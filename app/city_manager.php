<?php
/**
 * City Management
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */
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
				
				if (count($cityDetail) == 3)
					$this->cityList[] = new City($cityDetail[0], intval($cityDetail[1]), intval($cityDetail[2]));
			}
			
			fclose($file);
		}
		else
			throw new Exception("City file not found ($fileName).");
	}
}
