<?php

// It could take a lot of time
set_time_limit(0);

ini_set("memory_limit", "256M");
ini_set("auto_detect_line_endings", true);

function swap(&$array, $element1, $element2)
{
	$temp = $array[$element1];
	$array[$element1] = $array[$element2];
	$array[$element2] = $temp;
}

function rotate(&$array, $number)
{
	for ($i = 0; $i < $number; $i++)
	{
		array_push($array, array_shift($array));
	}
}

class CityManager
{
	protected $cityList;
	protected static $instance = null;
	
	public function __construct()
	{
		$this->cityList = array();
	}
	
	public static function getInstance()
	{
		if (static::$instance == null)
			static::$instance = new CityManager();
		
		return static::$instance;
	}
	
	public function &getCityList()
	{
		return $this->cityList;
	}
	
	public function getCityListCopy()
	{
		return $this->cityList;
	}
	
	public function loadFromFile($fileName)
	{
		if (file_exists($fileName)) 
		{
			$file = fopen($fileName, 'r');
			
			while (!feof($file)) 
			{
				/**
				 * Expected format :
				 * cityName positionX positionY
				 */
				$cityString = fgets($file);
				$cityDetail = explode(" ", $cityString);
				$this->cityList[] = new City($cityDetail[0], $cityDetail[1], $cityDetail[2]);
			}
			
			fclose($file);
		}
	}
}

class City
{
	protected $name;
	protected $x;
	protected $y;
	
	public function __construct($name, $x, $y)
	{
		$this->name = $name;
		$this->x = $x;
		$this->y = $y;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getX()
	{
		return $this->x;
	}
	
	public function getY()
	{
		return $this->y;
	}
	
	public function getLocation()
	{
		return array(
			'x' => $this->getX(),
			'y' => $this->getY()
		);
	}
	
	public function getDistance(City $otherCity)
	{
		$deltaX = $this->getX() - $otherCity->getX();
		$deltaY = $this->getY() - $otherCity->getY();
		return sqrt($deltaX * $deltaX + $deltaY * $deltaY);
	}
}

class Solution
{
	protected $cityList;
	protected $distance;
	
	public function __construct()
	{
		$this->cityList = array();
		$this->distance = 0;
	}
	
	public function addCity(City $city, $distance = -1)
	{
		if ($distance != -1)
			$this->distance += $distance;
		else if (count($this->cityList) > 0)
			$this->distance += $city->getDistance($this->getLastCity());
		
		//echo 'addCity (name='.$city->getName().', distance='.$this->distance.')<br />';
		
		$this->cityList[] = $city;
	}
	
	/*
	 * Index start at 0
	 */
	public function addCityAt(City $city, $index)
	{
		$this->distance += $city->getDistance($this->getLastCity());
		array_splice($this->cityList, $index, 0, $city); 
	}
	
	public function addCityGroup(array $cityList)
	{
		foreach ($cityList as $city)
		{
			$this->addCity($city);
		}
	}
	
	public function joinFirstAndLast()
	{
		$this->distance += $this->cityList[0]->getDistance($this->getLastCity());
	}
	
	public function &getCityList()
	{
		return $this->cityList;
	}
	
	public function getCityListCopy()
	{
		return $this->cityList;
	}
	
	public function setCityList($cityList)
	{
		$this->cityList = $cityList;
		$this->calculateDistance();
	}
	
	public function getLastCity()
	{
		if (count($this->cityList) > 0)
			return $this->cityList[count($this->cityList) - 1];
		
		return null;
	}
	
	public function getDistance()
	{
		return $this->distance;
	}
	
	public function rotate($stepNumber)
	{
		rotate($this->cityList, $stepNumber);
	}
	
	protected function calculateDistance()
	{
		$lastCity = null;
		
		foreach ($this->cityList AS $city)
		{
			// First loop, we add distance between first and last city
			if ($lastCity == null)
				$this->distance += $city->getDistance($this->getLastCity());
			
			// Otherwise calculate distance between last and current city
			else
				$this->distance += $lastCity->getDistance($city);
			
			// Save current city to be used as last city in next loop
			$lastCity = $city;
		}
	}
	
	public function getCityNumber()
	{
		return count($this->cityList);
	}
	
	public function __toString()
	{
		$toString = 'Solution(distance='.$this->distance.')';
		
		foreach ($this->cityList AS $city)
		{
			$toString .= '->'.$city->getName();
		}
		
		return $toString.'<br />';
	}
}

class Population
{
	protected $solutionList;
	
	public function __construct()
	{
		$this->solutionList = array();
	}
	
	// Greedy algorithm
	public function initialization($populationSize)
	{
		// Create population size
		for ($i = 0; $i < $populationSize; $i++)
		{
			// Get city list (copy)
			$cityList = CityManager::getInstance()->getCityListCopy();
			
			// Choose random city to start
			$cityIndex = rand(0, count($cityList) - 1);
			
			// Save last city used
			$lastCity = $cityList[$cityIndex];
			
			// Add first city to solution and delete it from list
			$solution = new Solution();
			$solution->addCity($lastCity, 0);
			unset($cityList[$cityIndex]);
			
			while (count($cityList) > 0)
			{
				$minDistance = -1;
				
				foreach ($cityList AS $index => $city)
				{
					$currentDistance = $lastCity->getDistance($city);
					
					//echo 'distance('.$lastCity->getName().'->'.$city->getName().') = '.$currentDistance.' <br />';
					
					if ($minDistance == -1 OR $currentDistance < $minDistance)
					{
						$cityIndex = $index;
						$minDistance = $currentDistance;
					}
				}
				
				$lastCity = $cityList[$cityIndex];
				$solution->addCity($lastCity, $minDistance);
				unset($cityList[$cityIndex]);
			}
			
			$solution->joinFirstAndLast();
			$this->solutionList[] = $solution;
		}
	}
	
	/**
	 * Pourcent [0, 1]
	 */
	public function selectionRoulette($pourcent)
	{
		uasort($this->solutionList, function($a, $b) {
			if ($a == $b) 
				return 0;
			
			return ($a->getDistance() < $b->getDistance()) ? -1 : 1;
		});
		
		$this->solutionList = array_slice($this->solutionList, 0, round($this->getSize() * $pourcent));
	}
	
	public function mutationAll($factor = null)
	{
		$populationSize = $this->getSize();
		
		if ($factor == null)
		{
			foreach ($this->solutionList as $solution)
			{
				$this->mutation($solution);
			}
		}
		else
		{
			for ($i = 0; $i < round($populationSize * $factor); $i++)
			{
				$randomIndex = rand(0, $populationSize - 1);
				$this->mutation($this->solutionList[$randomIndex]);
			}
		}
	}
	
	public function mutation(Solution $solution)
	{
		$this->solutionList[] = $newSolution = new Solution();
		
		$cityList = $solution->getCityListCopy();
		
		$maxIndex = $solution->getCityNumber() - 1;
		$randomIndex1 = rand(0, $maxIndex);
		$randomIndex2 = rand(0, $maxIndex);
		
		swap($cityList, $randomIndex1, $randomIndex2);
		
		$newSolution->setCityList($cityList);
	}
	
	public function crossoverAll($factor = null)
	{
		$populationSize = $this->getSize();
		
		if ($factor == null)
		{
			foreach (range(0, $populationSize) as $index1) 
			{
				foreach (range($index1, $populationSize) as $index2)
				{
					$this->crossover($this->solutionList[$index1], $this->solutionList[$index2]);
				}
			}
		}
		else
		{
			for ($i = 0; $i < round($populationSize * $factor); $i++)
			{
				$randomIndex1 = rand(0, $populationSize - 1);
				$randomIndex2 = rand(0, $populationSize - 1);
				$this->crossover($this->solutionList[$randomIndex1], $this->solutionList[$randomIndex2]);
			}
		}
	}
	
	public function crossover(Solution $solution1, Solution $solution2)
	{
		$this->solutionList[] = $newSolution1 = new Solution();
		$this->solutionList[] = $newSolution2 = new Solution();
		
		$cityList1 = &$solution1->getCityList();
		$cityList2 = &$solution2->getCityList();
		
		$size = $solution1->getCityNumber();
		
		$indexPart2 = floor($size / 3);
		$indexPart3 = ceil(2 * $size / 3);

		$cityCross1 = array_slice($cityList1, $indexPart2, $indexPart3 - $indexPart2);
		$cityCross2 = array_slice($cityList2, $indexPart2, $indexPart3 - $indexPart2);
		
		for ($i = 0; $i < $size; $i++)
		{
			$currentIndex = $indexPart3 + $i;
			
			if (!in_array($cityList1[$currentIndex % $size], $cityCross2))
			{
				$newSolution1->addCity($cityList1[$currentIndex % $size]);
			}
			
			if (!in_array($cityList2[$currentIndex % $size], $cityCross1))
			{
				$newSolution2->addCity($cityList2[$currentIndex % $size]);
			}
		}
		
		$newSolution1->addCityGroup($cityCross2);
		$newSolution2->addCityGroup($cityCross1);
		
		$newSolution1->rotate($size - $indexPart3);
		$newSolution2->rotate($size - $indexPart3);
		
		$newSolution1->joinFirstAndLast();
		$newSolution2->joinFirstAndLast();
	}
	
	public function bestSolution()
	{
		$bestSolution = null;
		
		foreach ($this->solutionList AS $solution)
		{
			if ($bestSolution == null OR $solution->getDistance() < $bestSolution->getDistance())
			{
				$bestSolution = $solution;
			}
		}
		
		return $bestSolution;
	}
	
	public function isStagnant($deltaAccepted)
	{
		static $distance = 0;
		
		$deltaCalculate = $distance / $this->bestSolution()->getDistance();
		
		if ($deltaCalculate > 1)
			$deltaCalculate -= 1;
		
		$distance = $this->bestSolution()->getDistance();
		
		return ($deltaCalculate <= $deltaAccepted);
	}
	
	public function getSolutionList()
	{
		return $this->solutionList;
	}
	
	public function getSize()
	{
		return count($this->solutionList);
	}
}

srand();

define("TIME_START", microtime(true));

$maximumTime = 5;

CityManager::getInstance()->loadFromFile('cityList_8.txt');

$population = new Population();
$population->initialization(5000);
echo 'initialization <br />';

do 
{
	$population->selectionRoulette(0.5);
	echo 'selectionRoulette <br />';
	echo 'size : '.$population->getSize().' <br />';
	
	$population->mutationAll(0.01);
	echo 'mutationAll <br />';
	echo 'size : '.$population->getSize().' <br />';
	
	$population->crossoverAll(0.49);
	echo 'crossoverAll <br />';
	echo 'size : '.$population->getSize().' <br />';
	
	echo $population->bestSolution();
	
	$currentTime = microtime(true) - TIME_START;
}
while ($currentTime < $maximumTime OR ($currentTime == null AND $population->isStagnant(0.01)));

?>