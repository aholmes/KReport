<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains method for the Right Y axis of the graph
 */
class KReport_Element_Axis_YRight extends KReport_Element_Axis
{
	/**
	 * Instantiate a new OFC2 OFC_Elements_Axis_Y_Right object and assign properties to it
	 * 
	 * @return KReport_Element_Axis_Y_Right The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_element = new OFC_Elements_Axis_Y_Right();

		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::LABELS:
					if (!is_array($value))
						throw new Exception('Labels must be an array');

					$this->ofc_element->set_labels($value);
				break;
				case self::TICK_LENGTH:
					if (!is_int($value))
						throw new Exception('Tick Length must be an integer');

					$this->ofc_element->set_tick_length($value);
				break;
			}
		}

		parent::execute();

		return $this;
	}
}