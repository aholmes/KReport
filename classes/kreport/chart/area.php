<?php defined('SYSPATH') or die('No direct script access.');

// TODO KReport_Chart_Line and KReport_Chart_Area could extend a class that contains WIDTH and DOT functions

/**
 * Contains methods for OFC2 Area charts
 */
class KReport_Chart_Area extends KReport_Chart_Line
{
	const FILL_COLOUR = 22005;
	const FILL_ALPHA  = 22006;

	/**
	 * Instantiate a new OFC2 OFC_Charts_Area object and assign properties to it
	 * 
	 * @return KReport_Chart_Area The instance being operated on
	 * @access public
	 */
	function execute()
	{
		if (get_class($this) === __CLASS__)
			$this->ofc_chart = new OFC_Charts_Area;

		parent::execute();

		foreach($this->_config as $var=>$value)
		{
			if (!is_int($var))
				continue;

			switch($var)
			{
				case self::FILL_COLOUR:
					$this->ofc_chart->set_fill_colour($value);
				break;
				case self::FILL_ALPHA:
					$this->ofc_chart->set_fill_alpha($value);
				break;
			}
		}

		return $this;
	}

	/**
	 * Set the colour of the filled-in area of the chart
	 *
	 * @param integer $colour A hexidecimal number to set the colour to
	 * @param integer $dot_size The size of the dot
	 * @access public
	 */
	function fill_colour($colour)
	{
		return $this->set(self::FILL_COLOUR, $colour);
	}

	/**
	 * Set the alpha of the filled-in area of the chart
	 * 
	 * @param float $alpha A number from 0 to 1 that represents the alpha
	 * @param integer $dot_size The size of the dot
	 * @access public
	 */
	function fill_alpha($alpha)
	{
		return $this->set(self::FILL_ALPHA, $alpha);
	}
}
