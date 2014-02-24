<?php
/**
 * Population
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */
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
	public function selectionElitist($pourcent)
	{
		uasort($this->solutionList, function($a, $b) {
			if ($a == $b) 
				return 0;
			
			return ($a->getDistance() < $b->getDistance()) ? -1 : 1;
		});
		
		$this->solutionList = array_slice($this->solutionList, 0, round($this->getSize() * $pourcent));
	}
	
	public function selectionRoulette($poucent)
	{
		// TODO
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
				foreach (range($index1 + 1, $populationSize) as $index2)
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
