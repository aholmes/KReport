<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for OFC2 Horizontal Bar Charts
 */
class KReport_Chart_HBar extends KReport_Chart
{
	/**
	 * Instantiate a new OFC2 OFC_Charts_Bar_Horizontal object and assign properties to it
	 * 
	 * @return KReport_Chart_HBar The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_Charts_Bar_Horizontal;

		parent::execute();

		foreach($this->_config as $var=>$value)
		{
			if (!is_int($var))
				continue;

			switch($var)
			{
				case self::KEY:
				case self::COLOUR:
				case self::HALO_SIZE:
				break;
				case self::VALUES:
					$this->ofc_chart->set_values(null);

					foreach($value as $index=>$values)
					{
						$v = new OFC_Charts_Bar_Horizontal_Value($values['left'], $values['right']);

						if (isset($values[self::TOOLTIP]))
							$v->set_tooltip($values[self::TOOLTIP]);

						$this->ofc_chart->append_value($v);
					}
				break;
				default:
					throw new Exception('Cannot set values for variable "' . $var . '" in ' . __CLASS__);
			}
		}

		return $this;
	}

	/**
	 * Override KReport_chart::as_array() to support the differing format of horizontal bar chart data
	 * 
	 * @return array The x, y, and label values of the chart 
	 * @access public
	 */
	function as_array()
	{
		$arr = array(
			'x'     => array(),
			'y'     => array(),
			'label' => array()
		);

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			$arr['x'][] = array($value['left'], $value['right']);
			$arr['y'][] = $index;
			$arr['label'][] = (isset($value[self::TOOLTIP]) ? $value[self::TOOLTIP] : null);
		}

		return $arr;
	}

	/**
	 * Override KReport_Chart::to_csv() to support the differing format of horizontal bar chart data
	 * 
	 * @param integer $x The index of the value to retrieve
	 * @return string A CSV string containing the x and y values at index $x
	 * @access public
	 */
	function to_csv($x)
	{
		if (isset($this->_config[self::VALUES]) && isset($this->_config[self::VALUES][$x]))
			return $this->_config[self::VALUES][$x]['left'] . ',' . $this->_config[self::VALUES][$x]['right'] . (isset($this->_config[self::VALUES][$x][self::TOOLTIP]) ? ',' . $this->_config[self::VALUES][$x][self::TOOLTIP] : '') . "\n";
	}

	/**
	 * Override KReport_Chart::point(). Throws an exception.
	 */
	function point($x, $y)
	{
		throw new Exception('Cannot call ' . __CLASS__ . '::point(). Use ' . __CLASS__ . '::bar()');
	}

	/**
	 * Set a new bar to display on the chart
	 * 
	 * @param integer $left The lower X value of the bar
	 * @param integer $right The higher X value of the bar
	 * @param string $tooltip The tooltip of the bar
	 * @return KReport_Chart_HBar The instance being operated on
	 * @access public
	 */
	function bar($left, $right, $tooltip = null)
	{
		$bars = isset($this->_config[self::VALUES]) ? $this->_config[self::VALUES] : array();
		$index = count($bars);

		$bars[$index]['left']  = intval($left);
		$bars[$index]['right'] = intval($right);

		if (!is_null($tooltip))
			$bars[$index][self::TOOLTIP] = $tooltip;

		return $this->set(self::VALUES, $bars);
	}

	/**
	 * Override KReport_Chart::get_x_min() to support the differing format of horizontal bar chart data. Get the mimimum X value of the chart
	 * 
	 * @return integer The lowest X value
	 * @access public
	 */
	function get_x_min()
	{
		$values = array();

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			$values[] = $value['left'];
		}

		$x = array_values($values);
		rsort($x);
		return array_pop($x);
	}

	/**
	 * Override KReport_Chart::get_x_mmax() to support the differing format of horizontal bar chart data. Get the maximum X value of the chart
	 * 
	 * @return integer The highest X value
	 * @access public
	 */
	function get_x_max()
	{
		$values = array();

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			$values[] = $value['right'];
		}

		$x = array_values($values);
		sort($x);
		return array_pop($x);
	}

	/**
	 * Override KReport_Chart::get_y_min() to support the differing format of horizontal bar chart data. Get the minimum Y value of the chart
	 * 
	 * @return integer The lowest Y value
	 * @access public
	 */
	function get_y_min()
	{
		return 0;
	}

	/**
	 * Override KReport_Chart::get_y_max() to support the differing format of horizontal bar chart data. Get the maximum Y value of the chart.
	 * 
	 * @return integer The highest Y value
	 * @access public
	 */
	function get_y_max()
	{
		return count($this->_config[self::VALUES]) -1;
	}
}
