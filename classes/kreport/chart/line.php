<?php defined('SYSPATH') or die('No direct script access.');

// TODO KReport_Chart_Line and KReport_Chart_Area could extend a class that contains WIDTH and DOT functions

/**
 * Contains methods for OFC2 Line Charts
 */
class KReport_Chart_Line extends KReport_Chart
{
	const WIDTH     = 21001;
	const DOT_SIZE  = 21002;

	/**
	 * Instantiate a new OFC2 OFC_Charts_Line object and assign properties to it
	 * 
	 * @return KReport_Chart_Line The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_Charts_Line;

		if (!array_key_exists(self::COLOUR, $this->_config))
		{
			$this->get_colour();
		}

		parent::execute();

		foreach($this->_config as $var=>$value)
		{
			if (!is_int($var))
				continue;

			switch($var)
			{
				case self::WIDTH:
					if (!is_int($value))
						throw new Exception ('Width for ' . __CLASS__ . ' must be an integer');

					$this->ofc_chart->set_width($value);
				break;
				case self::DOT_SIZE:
					if (!is_int($value))
						throw new Exception ('Dot size for ' . __CLASS__ . ' must be an integer');
					
					$this->ofc_chart->set_dot_size($value);
				break;
				case self::KEY:
				case self::COLOUR:
				case self::HALO_SIZE:
				case self::VALUES:
				break;
				default:
					throw new Exception('Cannot set values for variable "' . $var . '" in ' . __CLASS__);
			}
		}

		return $this;
	}

	/**
	 * Set the width of the line
	 * 
	 * @param integer $width The width to set
	 * @return KReport_Chart_Line The instance being operated on
	 * @access public
	 */
	function width($width)
	{
		return $this->set(self::WIDTH, $width);
	}

	/**
	 * Set the size of the dot for each point
	 * 
	 * @param integer $dot_size The size of the dot
	 * @return KReport_Chart_Line The instance being operated on
	 * @access public
	 */
	function dot_size($dot_size)
	{
		return $this->set(self::DOT_SIZE, $dot_size);
	}
}
