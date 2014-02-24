<?php
/**
 * Algorithm execution
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */

// It could take a lot of time
set_time_limit(0);

// PHP configuration
ini_set("memory_limit", "256M");
ini_set("auto_detect_line_endings", true);

// Define start time for duration calculation
define("TIME_START", microtime(true));

// Show debug information
define("DEBUG", false);

// Include application classes and functions
require_once('app/functions.php');
require_once('app/city_manager.php');
require_once('app/city.php');
require_once('app/solution.php');
require_once('app/population.php');

// Random initialization
srand();

/*
 * Maximum execution time
 * -> null if calculate until stagnation is detected
 * -> otherwise calculate until maximumTime is reached
 */
$maximumTime = 15;

// Load city list from data set
CityManager::getInstance()->loadFromFile('data/pb020.txt');

// Create and initialize population
$population = new Population();
$population->initialization(5000);
if (DEBUG) echo 'initialization <br />';

do 
{
	$population->selectionElitist(0.5);
	if (DEBUG) echo 'selectionElitist <br />';
	if (DEBUG) echo 'size : '.$population->getSize().' <br />';
	
	$population->mutationAll(0.01);
	if (DEBUG) echo 'mutationAll <br />';
	if (DEBUG) echo 'size : '.$population->getSize().' <br />';
	
	$population->crossoverAll(0.49);
	if (DEBUG) echo 'crossoverAll <br />';
	if (DEBUG) echo 'size : '.$population->getSize().' <br />';
	
	echo '<p>'.$population->bestSolution().'</p>';
	
	$currentTime = microtime(true) - TIME_START;
}
while ($currentTime < $maximumTime OR ($maximumTime == null AND $population->isStagnant(0.01)));

?>