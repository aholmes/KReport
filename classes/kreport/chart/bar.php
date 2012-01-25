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

		parent::execute();

		return $this;
	}

	/**
	 * Set a point with an optional tooltip for the bar
	 * 
	 * @param integer $x The position of the bar on the X axis
	 * @param integer $y1 The minimum value of the bar
	 * @param integer $y2 The maximum value of the bar
	 * @param type $tooltip An optional tooltip for the bar
	 * @return array The values for the chart
	 */
	function bar($x, $y1, $y2 = null, $tooltip = null)
	{
		$points = isset($this->_config[self::VALUES]) ? $this->_config[self::VALUES] : array();

		if (!is_null($tooltip))
		{
			$value = new OFC_Charts_Bar_Value($y1, $y2);
			$value->set_tooltip($tooltip);
			$points[(float)$x] = $value;
		}
		elseif (!is_null($y2))
		{
			$value = new OFC_Charts_Bar_Value($y1, $y2);
			$points[(float)$x] = $value;
		}
		else
		{
			$points[(float)$x] = (float)$y1;
		}

		return $this->set(self::VALUES, $points);
	}

	function to_csv($x)
	{
		if ($this->_config[self::VALUES][$x] instanceof OFC_Charts_Bar_Value)
			return $this->_config[self::VALUES][$x]->bottom . ',' . $this->_config[self::VALUES][$x]->top . "\n";
		else
			return $x . ',' . $this->_config[self::VALUES][$x] . "\n";
	}

	/**
	 * Get the minimum Y value of the chart
	 * 
	 * @return integer The lowest Y value
	 * @access public
	 */
	function get_y_min()
	{
		$lowest_y = null;

		$y = array_values($this->_config[self::VALUES]);

		foreach($y as $index=>$y_value)
		{
			if ($y_value instanceof OFC_Charts_Bar_Value)
			{
				if (is_null($lowest_y) || $y_value->bottom < $lowest_y)
					$lowst_y = $y_value->bottom;
			}
			else
			{
				if (is_null($lowest_y) || $y_value < $lowest_y)
					$lowst_y = $y_value;
			}
		}

		return $lowest_y;
	}

	/**
	 * Get the maximum Y value of the chart
	 * 
	 * @return integer The highest Y value
	 * @access public
	 */
	function get_y_max()
	{
		$highest_y = 0;

		$y = array_values($this->_config[self::VALUES]);

		foreach($y as $index=>$y_value)
		{
			if ($y_value instanceof OFC_Charts_Bar_Value)
			{
				if ($y_value->top > $highest_y)
					$highest_y = $y_value->top;
			}
			else
			{
				if ($y_value > $highest_y)
					$highest_y = $y_value;
			}
		}

		return $highest_y;
	}
}
