<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Reads and writes the courses JSON file ({"SYMBOL": "price", ...}).
 * The working copy lives in storage; it is created from database/data/courses.json on first use.
 */
class CourseJsonStore
{
    public function __construct(
        private readonly string $path,
        private readonly string $sourcePath,
    ) {
    }

    /** @return array<string, string> */
    public function all(): array
    {
        $this->ensureExists();

        return json_decode(File::get($this->path), true, 512, JSON_THROW_ON_ERROR);
    }

    public function put(string $symbol, string $price): void
    {
        $data = $this->all();
        $data[$symbol] = $price;

        File::put($this->path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), true);
    }

    private function ensureExists(): void
    {
        if (! File::exists($this->path)) {
            File::ensureDirectoryExists(dirname($this->path));
            File::copy($this->sourcePath, $this->path);
        }
    }
}
