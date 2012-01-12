<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for the X axis of the graph
 */
class KReport_Element_Axis_X extends KReport_Element_Axis
{
	/**
	 * Instantiate a new OFC2 OFC_Elements_Axis_X object and assign properties to it
	 * 
	 * @return KReport_Element_Axis_X The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_element = new OFC_Elements_Axis_X();

		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::LABELS:
					if (!is_array($value))
						throw new Exception('Labels must be an array');

					$this->ofc_element->set_labels_from_array($value);
				break;
			}
		}

		parent::execute();

		return $this;
	}
}