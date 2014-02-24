<?php
/**
 * City
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */
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
