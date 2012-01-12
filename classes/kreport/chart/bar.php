<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for OFC2 Bar Charts
 */
class KReport_Chart_Bar extends KReport_Chart
{
	/**
	 * Instantiate a new OFC2 OFC_Charts_Bar object and assign properties to it
	 * 
	 * @return KReport_Chart_Bar The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_Charts_Bar;

		if (!array_key_exists(self::COLOUR, $this->_config))
		{
			$this->get_colour();
		}

		parent::execute();

		return $this;
	}
}
