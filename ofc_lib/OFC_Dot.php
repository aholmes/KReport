<?php

class OFC_Dot{
	public function __construct($type){
		$this->type = $type;
	}

	public function set_dot_size($size){
		$this->dot_size = $size;
	}

	public function set_halo_size($size){
		$this->halo_size = $size;
	}

	public function set_tip($tip){
		$this->tip = $tip;
	}

	public function on_click($value){
		$prop = "on-click";	//to work around properties with hyphens
		$this->$prop = $value;
	}
	public function set_colour($colour){
		$this->colour = $colour;
	}

	/**
	 * For line charts that only require a Y position
	 * for each point.
	 * @param $value as integer, the Y position
	 */
	function value( $value )
	{
		$this->value = $value;
	}
	
	/**
	 * For scatter charts that require an X and Y position for
	 * each point.
	 * 
	 * @param $x as integer
	 * @param $y as integer
	 */
	function position( $x, $y )
	{
		$this->x = $x;
		$this->y = $y;
	}

	function type( $type )
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * Rotate the anchor object.
	 * @param $angle is an integer.
	 */
	function rotation($angle)
	{
		$this->rotation = $angle;
		return $this;
	}

	/**
	 * @param $sides is an integer. Number of sides this shape has.
	 */
	function sides($sides)
	{
		$this->sides = $sides;
		return $this;
	}
}
?>
