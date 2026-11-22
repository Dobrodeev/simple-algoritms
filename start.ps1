<#
.SYNOPSIS
    Starts the Laravel project locally: MariaDB (port 3307) + PHP built-in server.

.DESCRIPTION
    - Initializes the MariaDB data directory in .mariadb\data on first run (root without password).
    - Starts MariaDB if it is not already listening on port 3307.
    - Installs Composer dependencies and creates laravel\.env if they are missing.
    - Runs migrations; seeds courses.json when the courses table is empty.
    - Runs the site at http://127.0.0.1:8000 until Ctrl+C, then stops MariaDB if this script started it.

.EXAMPLE
    .\start.ps1
    .\start.ps1 -Port 8080 -KeepDatabase
#>
param(
    [int]$Port = 8000,
    [string]$PhpDir = 'C:\OSPanel\modules\php\PHP_8.1',
    [string]$MariaDbDir = 'C:\OSPanel\modules\database\MariaDB-10.8-Win10',
    [string]$Composer = 'C:\OSPanel\userdata\composer\composer.phar',
    [switch]$KeepDatabase   # do not stop MariaDB when the site stops
)

$ErrorActionPreference = 'Stop'

$root    = $PSScriptRoot
$app     = Join-Path $root 'laravel'
$dataDir = Join-Path $root '.mariadb\data'
$dbPort  = 3307
$php     = Join-Path $PhpDir 'php.exe'

function Write-Step([string]$Text) { Write-Host "==> $Text" -ForegroundColor Cyan }

function Test-Port([int]$Number) {
    $client = New-Object Net.Sockets.TcpClient
    try { $client.ConnectAsync('127.0.0.1', $Number).Wait(500) -and $client.Connected } catch { $false } finally { $client.Dispose() }
}

function Invoke-Php {
    & $php @args
    if ($LASTEXITCODE -ne 0) { throw "php $($args -join ' ') failed with exit code $LASTEXITCODE" }
}

foreach ($path in $php, (Join-Path $MariaDbDir 'bin\mariadbd.exe')) {
    if (-not (Test-Path $path)) { throw "Not found: $path. Check the -PhpDir / -MariaDbDir parameters." }
}

$env:PHPRC = Join-Path $root 'php-dev.ini'   # PHP settings for this project

# --- MariaDB -------------------------------------------------------------
if (-not (Test-Path (Join-Path $dataDir 'my.ini'))) {
    Write-Step 'Initializing MariaDB data directory'
    New-Item -ItemType Directory -Force (Split-Path $dataDir) | Out-Null
    & (Join-Path $MariaDbDir 'bin\mariadb-install-db.exe') "--datadir=$dataDir" "--port=$dbPort"
    if ($LASTEXITCODE -ne 0) { throw 'mariadb-install-db failed' }
    Add-Content (Join-Path $dataDir 'my.ini') "[mysqld]`nbind-address=127.0.0.1`ncharacter-set-server=utf8mb4`ncollation-server=utf8mb4_unicode_ci"
}

$mariadb = $null
if (Test-Port $dbPort) {
    Write-Step "MariaDB is already running on port $dbPort"
} else {
    Write-Step "Starting MariaDB on port $dbPort"
    $mariadb = Start-Process (Join-Path $MariaDbDir 'bin\mariadbd.exe') `
        -ArgumentList "--defaults-file=`"$dataDir\my.ini`"" -WindowStyle Hidden -PassThru
    for ($i = 0; $i -lt 30 -and -not (Test-Port $dbPort); $i++) { Start-Sleep -Milliseconds 500 }
    if (-not (Test-Port $dbPort)) { throw "MariaDB did not start; see $dataDir\*.err" }
}

try {
    # --- Laravel ---------------------------------------------------------
    Push-Location $app
    try {
        if (-not (Test-Path 'vendor')) {
            Write-Step 'Installing Composer dependencies'
            Invoke-Php $Composer install --no-interaction
        }
        if (-not (Test-Path '.env')) {
            Write-Step 'Creating .env'
            (Get-Content '.env.example') -replace '^DB_PORT=.*', "DB_PORT=$dbPort" -replace '^DB_PASSWORD=.*', 'DB_PASSWORD=' |
                Set-Content '.env'
            Invoke-Php artisan key:generate --ansi
        }

        & (Join-Path $MariaDbDir 'bin\mariadb.exe') --host=127.0.0.1 --port=$dbPort --user=root `
            --execute='CREATE DATABASE IF NOT EXISTS binance_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        if ($LASTEXITCODE -ne 0) { throw 'Could not create database binance_test' }

        Write-Step 'Running migrations'
        Invoke-Php artisan migrate --force

        $count = & (Join-Path $MariaDbDir 'bin\mariadb.exe') --host=127.0.0.1 --port=$dbPort --user=root `
            --batch --skip-column-names --execute='SELECT COUNT(*) FROM binance_test.courses'
        if ([int]$count -eq 0) {
            Write-Step 'Loading courses.json into the database'
            Invoke-Php artisan db:seed --force
        }
    } finally {
        Pop-Location
    }

    # --- Web server ------------------------------------------------------
    if (Test-Port $Port) { throw "Port $Port is already in use. Run with -Port <other port>." }

    Write-Host ''
    Write-Host "Site: http://127.0.0.1:$Port   (Ctrl+C to stop)" -ForegroundColor Green
    Write-Host ''
    Push-Location (Join-Path $app 'public')   # Laravel's router uses the current directory as public path
    try {
        & $php -S "127.0.0.1:$Port" (Join-Path $app 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php')
    } finally {
        Pop-Location
    }
} finally {
    if ($mariadb -and -not $KeepDatabase -and -not $mariadb.HasExited) {
        Write-Step 'Stopping MariaDB'
        Stop-Process -Id $mariadb.Id
    }
}
