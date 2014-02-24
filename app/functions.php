<?php
/**
 * Tools functions
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */

/**
 * Swap two elements in an array
 *
 * @param mixed $array Array containing the elements to swap
 * @param mixed $key1 First key element to swap
 * @param mixed $key2 Second key element to swap 
 */
function swap(&$array, $key1, $key2)
{
	$temp = $array[$key1];
	$array[$key1] = $array[$key2];
	$array[$key2] = $temp;
}

/**
 * Rotate an array (left rotation)
 *
 * @param mixed $array Array containing the elements to swap
 * @param mixed $number Number of rotation to make
 */
function rotate(&$array, $number)
{
	for ($i = 0; $i < $number; $i++)
	{
		array_push($array, array_shift($array));
	}
}
