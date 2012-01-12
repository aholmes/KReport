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

}

?>
