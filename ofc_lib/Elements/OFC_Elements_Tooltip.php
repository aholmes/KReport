<?php

require_once(OFC_BASE_PATH . 'Elements/OFC_Elements_Base.php');

class OFC_Elements_Tooltip extends OFC_Elements_Base
{
	public function __construct(){
        	parent::OFC_Elements_Base();
	}

	public function set_shadow($value){
		$this->shadow = $value;
	}

	public function set_stroke($value){
		$this->stroke = $value;
	}

	public function set_colour($value){
		$this->colour = $value;
	}

	public function set_background_colour($value){
		$this->background = $value;
	}

	public function set_title_style($value){
		$this->title = $value;
	}

	public function set_body_style($value){
		$this->body = $value;
	}

	public function set_hover(){
		$this->mouse = 2;	//FIXME magic number
	}
}

?>
