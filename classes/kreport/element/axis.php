<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for OFC_Element_Axis_* functions
 */
class KReport_Element_Axis extends KReport_Element
{
	// Axis element configuration constants
	const RANGE       = 31001;
	const LABELS      = 31002;
	const GRID_COLOUR = 31003;
	const STROKE      = 31004;
	const TICK_LENGTH = 31005;
	const COLOUR      = 31006;
	const STEPS       = 31007;

	/**
	 * Set an element variable
	 * 
	 * @param integer $var The KReport_Element_Axis constant to set
	 * @param mixed $value The value of the variable
	 * @return KReport_Element_Axis The instance being operated on
	 * @access public
	 */
	function set($var, $value = null)
	{
		if (!is_int($var))
			throw new Exception ('Var ' . $var . 'must be int from class constants ' . __CLASS__);

		$this->_config[$var] = $value;

		return $this;
	}

	/**
	 * Set variables for the KReport_Element::$ofc_chart instance that are shared by all chart types
	 * 
	 * @return KReport_Element_Axis The instance being operated on
	 */
	function execute()
	{
		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::RANGE:
					if (!isset($value['min']) || !isset($value['max']))
						throw new Exception('Must supply a min and max size');

					if (isset($this->_config[self::STEPS]))
						$this->ofc_element->set_range($value['min'], $value['max'], $this->_config[self::STEPS]);
					else
						$this->ofc_element->set_range($value['min'], $value['max'], isset($value['steps']) ? $value['steps'] : 1);
				break;
				case self::GRID_COLOUR:
					$this->ofc_element->set_grid_colour($value);
				break;
				case self::STROKE:
					if (!is_int($value))
						throw new Exception('Stroke must be an integer');

					$this->ofc_element->set_stroke($value);
				break;
				case self::COLOUR:
					$this->ofc_element->set_colour($value);
				break;
				case self::STEPS:
					if (!is_int($value))
						throw new Exception('Steps must be an integer');

					$this->ofc_element->set_steps($value);
				break;
			}
		}

		return $this;
	}
}