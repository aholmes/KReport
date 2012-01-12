<?php defined('SYSPATH') or die('No direct script access.');

// TODO KReport_Chart_Line and KReport_Chart_Area could extend a class that contains WIDTH and DOT functions

/**
 * Contains methods for OFC2 Area charts
 */
class KReport_Chart_Area extends KReport_Chart
{
	const WIDTH       = 22001;
	const DOT_COLOUR  = 22002;
	const DOT_SIZE    = 22003;
	const FILL_COLOUR = 22005;
	const FILL_ALPHA  = 22006;

	/**
	 * @var OFC_Dot The OFC2 dot object if a dot style has been configured for this chart
	 * @access private
	 */
	private $dot = null;

	/**
	 * Instantiate a new OFC2 OFC_Charts_Area object and assign properties to it
	 * 
	 * @return KReport_Chart_Area The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_Charts_Area;

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
				case self::DOT_COLOUR:
					if (is_null($this->dot))
						$this->dot = new OFC_Dot('dot');

					$this->dot->set_colour($value);
				break;
				case self::DOT_SIZE:
					if (!is_int($value))
						throw new Exception ('Dot size for ' . __CLASS__ . ' must be an integer');

					if (is_null($this->dot))
						$this->dot = new OFC_Dot('dot');

					$this->dot->set_dot_size($value);
				break;
				case self::FILL_COLOUR:
					$this->ofc_chart->set_fill_colour($value);
				break;
				case self::FILL_ALPHA:
					$this->ofc_chart->set_fill_alpha($value);
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

		if (!is_null($this->dot))
			$this->ofc_chart->set_default_dot_style($this->dot);

		return $this;
	}

	/**
	 * Set the width of the line
	 * 
	 * @param integer $width The width to set
	 * @return KReport_Chart_Area The instance being operated on
	 * @access public
	 */
	function width($width)
	{
		return $this->set(self::WIDTH, $width);
	}

	/**
	 * Set the dot colour of the point
	 * 
	 * @param integer $colour A hexidecimal number to set the colour to
	 * @return KReport_Chart_Area The instance being operated on
	 * @access public
	 */
	function dot_colour($colour)
	{
		return $this->set(self::DOT_COLOUR, $colour);
	}

	/**
	 * Set the size of the dot for each point
	 * 
	 * @param integer $dot_size The size of the dot
	 * @return KReport_Chart_Area The instance being operated on
	 * @access public
	 */
	function dot_size($dot_size)
	{
		return $this->set(self::DOT_SIZE, intval($dot_size));
	}

	/**
	 * Set the colour and dot size of each point
	 * 
	 * @param type $colour
	 * @param integer $colour A hexidecimal number to set the colour to
	 * @param integer $dot_size The size of the dot
	 * @return type 
	 * @access public
	 */
	function dot_style($colour, $dot_size)
	{
		$this->set(self::DOT_COLOUR, $colour);
		return $this->set(self::DOT_SIZE, $dot_size);
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
