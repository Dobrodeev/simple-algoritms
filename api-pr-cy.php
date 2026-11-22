<?php
declare( strict_types=1 );
/**
 * Как получить данные
 * Данные отдаются в формате JSON. Для получения данных необходимо сделать соответствующий запрос.
 *
 * Получение базового анализа GET-запрос
 * https://a.pr-cy.ru/api/v1.1.0/analysis/base/{DOMAIN}?key={KEY}
 *
 * Получение статуса базового анализа GET-запрос
 * https://a.pr-cy.ru/api/v1.1.0/analysis/status/base/{DOMAIN}?key={KEY}
 *
 * Обновление базового анализа POST запрос
 * https://a.pr-cy.ru/api/v1.1.0/analysis/update/base/{DOMAIN}?key={KEY}
 *
 *
 * Получение расширенного анализа GET-запрос
 * https://a.pr-cy.ru/api/v1.1.0/analysis/advanced/{DOMAIN}?key={KEY}
 *
 * Получение статуса расширенного анализа GET-запрос
 * https://a.pr-cy.ru/api/v1.1.0/analysis/status/advanced/{DOMAIN}?key={KEY}
 *
 * Обновление расширенного анализа POST-запрос
 * https://a.pr-cy.ru/api/v1.1.0/analysis/update/advanced/{DOMAIN}?key={KEY}
 *
 *
 * При необходимости можно ограничить список тестов только необходимыми, а также скрыть историю результатов. Для этого следует использовать параметры в GET-запросе: tests, excludeHistory.
 *
 * https://a.pr-cy.ru/api/v1.1.0/analysis/base/{DOMAIN}?key={KEY}&excludeHistory=1&tests={TESTS}
 *
 * {KEY} — ваш ключ. Который можно получить в настройках.
 *
 * {DOMAIN} — проверяемый домен.
 *
 * {TESTS} — список тестов через запятую. Например: page404StatusCode,robotsTxt.
 *
 * Swagger для тестирования запросов
 */
define( 'YOUR_API_KEY', '' );
$url    = 'https://a.pr-cy.ru/api/v1.1.0/analysis/base';
$params = [ 'key' => 'YOUR_API_KEY' ];
$domain = ! empty( $_POST['domain'] ) ? $_POST['domain'] : '';

if ( ! empty( $domain ) ) {
	try {
		$data = @file_get_contents( "{$url}/{$domain}&" . http_build_query( $params ) );
		$data = @json_decode( $data, true );
	} catch ( Exception $e ) {
		$data = [];
	}
	// echo "<pre>"; print_r($data); die();
}

/**
 * @param $rows
 * @param $key
 * @param  bool  $showHistroy
 *
 * @return string
 */
function formatValue( $rows, $key, $showHistroy = false ) {
	if ( empty( $rows ) || empty( $rows[ $key ] ) || empty( $rows[ $key ][ $key ] ) ) {
		return "&mdash;";
	}
	$val    = $rows[ $key ][ $key ];
	$values = [ number_format( $val ) ];
	if ( $showHistroy && ! empty( $rows[ $key ]["{$key}History"] ) ) {
		$prevValue = array_slice( $rows[ $key ]["{$key}History"]['days'], - 2, 1 )[0];
		$values[]  = history( $val, $prevValue );
	}

	return implode( $values, " " );
}

/**
 * @param $val
 * @param $history
 *
 * @return string|void
 */
function history( $val, $history ) {
	if ( $val == $history ) {
		return;
	}
	$sign  = '';
	$class = '';
	if ( $val < $history ) {
		$class = 'text-danger';
	} elseif ( $val > $history ) {
		$sign  = '+';
		$class = 'text-success';
	}
	$diff = $val - $history;
	$diff = number_format( $diff );

	return "<span class='{$class}'>{$sign}{$diff}</span>";
}

$tableMapping = [
	'days'   => 'История по дням',
	'weeks'  => 'История по неделям',
	'months' => 'История по годам',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Api Test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="ru"/>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container" style="margin-top: 25px;">
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="post" action="">
                <div class="form-group">
                    <label for="domain">Domain</label>
                    <input type="text" class="form-control" id="domain" name="domain" placeholder="Domain"
                           value="<?= $domain ?>">
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
        </div>
    </div>

	<?php if ( ! empty( $data ) ): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <dl>
                    <dt>Яндекс ТИЦ</dt>
                    <dd style="margin-bottom: 15px;">
						<?= formatValue( $data, 'yandexCitation' ) ?>
                    </dd>
                </dl>
                <dl>
                    <dt>Яндекс Rank</dt>
                    <dd style="margin-bottom: 15px;">
						<?= formatValue( $data, 'yandexRank' ) ?>
                    </dd>
                </dl>
                <dl>
                    <dt>Индексация Яндекс</dt>
                    <dd style="margin-bottom: 15px;">
						<?= formatValue( $data, 'yandexIndex', true ) ?>
                    </dd>
                </dl>
				<? if ( ! empty( $data['yandexCitation'] ) && ! empty( $data['yandexCitation']['yandexCitationHistory'] ) ): ?>
                    <div class="row">
						<? foreach ( $data['yandexCitation']['yandexCitationHistory'] as $type => $histories ): ?>
                            <div class="col-md-4">
                                <dl>
                                    <dt><?= $tableMapping[ $type ] ?></dt>
                                    <dd style="margin-bottom: 15px;">
                                        <table class="table">
                                            <thead>
                                            <th>Дата</th>
                                            <th>Значение</th>
                                            </thead>
                                            <tbody>
											<? foreach ( $histories as $date => $value ): ?>
                                                <tr>
                                                    <td><?= date( 'Y-m-d', strtotime( $date ) ) ?></td>
                                                    <td><?= number_format( $value ) ?></td>
                                                </tr>
											<? endforeach ?>
                                            </tbody>
                                        </table>
                                    </dd>
                                </dl>
                            </div>
						<? endforeach ?>
                    </div>
				<? endif ?>
                <dl>
                    <dt>Индексация Google</dt>
                    <dd style="margin-bottom: 15px;">
						<?= formatValue( $data, 'googleIndex', true ) ?>
                    </dd>
                </dl>
				<? if ( ! empty( $data['googleIndex'] ) && ! empty( $data['googleIndex']['googleIndexHistory'] ) ): ?>
                    <div class="row">
						<? foreach ( $data['googleIndex']['googleIndexHistory'] as $type => $histories ): ?>
                            <div class="col-md-4">
                                <dl>
                                    <dt><?= $tableMapping[ $type ] ?></dt>
                                    <dd style="margin-bottom: 15px;">
                                        <table class="table">
                                            <thead>
                                            <th>Дата</th>
                                            <th>Значение</th>
                                            </thead>
                                            <tbody>
											<? foreach ( $histories as $date => $value ): ?>
                                                <tr>
                                                    <td><?= date( 'Y-m-d', strtotime( $date ) ) ?></td>
                                                    <td><?= number_format( $value ) ?></td>
                                                </tr>
											<? endforeach ?>
                                            </tbody>
                                        </table>
                                    </dd>
                                </dl>
                            </div>
						<? endforeach ?>
                    </div>
				<? endif ?>
				<? if ( ! empty( $data['yandexCatalog'] ) && ! empty( $data['yandexCatalog']['yandexCategories'] ) ): ?>
                    <dl>
                        <dt>Яндекс.Каталог</dt>
                        <dd style="margin-bottom: 15px;">
							<? foreach ( $data['yandexCatalog']['yandexCategories'] as $path => $category ): ?>
                                <div>
                                    <a href="https://yandex.ru/yaca<?= $path ?>" target="_blank">
										<?= $category ?>
                                    </a>
                                </div>
							<? endforeach ?>
                        </dd>
                    </dl>
				<? endif ?>
            </div>
        </div>
	<?php endif; ?>
</div>
</body>
</html>

