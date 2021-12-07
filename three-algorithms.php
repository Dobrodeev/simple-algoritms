<?php
/**
 * @package Simple algoritms
 * @author Dobrodeev Valera
 * @copyright 2021
 * @license GPL
 */
$someArray1 = [ 2, 5, 7 ];
$k1         = 5;
$k1         = 39;
$k1         = 2;
/**
 * Дана последовательность случайных цифр любой длины и «волшебное» положительное число, больше нуля.
 * Если можно разделить сумму квадратов последовательности на «волшебное» число без остатка.
 * В качестве ответа возвращается «Волшебство случается» в случае успеха или «Никакого волшебства»,
 * если разделить нельзя.
 *
 * @param $array
 * @param $divisor
 *
 * @return string
 */
function magic( $array, $divisor ) {
	$S = 0;
	for ( $i = 0; $i < count( $array ); $i ++ ) {
		$S += $array[ $i ] * $array[ $i ];
	}
//	echo 'Summa=' . $S . '<br>';
	$result = $S % $divisor;
//	echo 'Result=' . $result . '<br>';
	if ( $result == 0 ) {
		return 'Волшебство случается';
	} else {
		return 'Никакого волшебства';
	}
}

echo magic( [ 1, 2 ], 5 );
$someListing = [ 'крот', 'белка', 'выхухоль' ];
/**
 * @param $lst
 */
function all_eq( $lst ) {

}

$list_1 = [ 1, 1, 3, 3, 1 ];
$list_2 = [ 5, 5, 5, 5, 5, 5, 5 ];
$list_3 = [ 2, 2, 1, 2, 2, 5, 6, 7, 1, 3, 2, 2 ];
/**
 * @param $listing
 */
function set_gen( $listing ) {

}