<?php
/**
 * Solution
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */
class Solution
{
	protected $cityList;
	protected $distance;
	protected $insertMode;
	
	public function __construct()
	{
		$this->cityList = array();
		$this->distance = 0;
		$this->insertMode = false;
	}
	
	public function addCity(City $city, $distance = -1)
	{
		$this->insertMode = true;
		
		if ($distance != -1)
			$this->distance += $distance;
		else if (count($this->cityList) > 0)
			$this->distance += $city->getDistance($this->getLastCity());
		
		$this->cityList[] = $city;
	}
	
	/*
	 * Index start at 0
	 */
	public function addCityAt(City $city, $index)
	{
		$this->insertMode = true;
		
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
		$this->insertMode = false;
		
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
		if ($this->insertMode)
			throw new Exception("You must call Solution::joinFirstAndLast after adding cities.");
		
		return $this->distance;
	}
	
	public function rotate($stepNumber)
	{
		rotate($this->cityList, $stepNumber);
	}
	
	protected function calculateDistance()
	{
		// First loop, we add distance between first and last city
		$lastCity = $this->getLastCity();
		
		foreach ($this->cityList AS $city)
		{
			// Otherwise calculate distance between last and current city
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
		if ($this->insertMode)
			throw new Exception("You must call Solution::joinFirstAndLast after adding cities.");
			
		$toString = 'Solution(distance='.$this->distance.')';
		
		foreach ($this->cityList AS $city)
		{
			$toString .= '->'.$city->getName();
		}
		
		return $toString.'<br />';
	}
}
