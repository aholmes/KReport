<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for OFC2 Stacked Bar Charts
 */
class KReport_Chart_StackBar extends KReport_Chart
{
	const COLOURS   = 25001;
	const STACK     = 25003;
	const KEYS      = 25004;

	/**
	 * Instantiate a new OFC2 OFC_Charts_Bar_Stack object and assign properties to it
	 * 
	 * @return KReport_Chart_StackBar The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_Charts_Bar_Stack;

		parent::execute();

		foreach($this->_config as $var=>$value)
		{
			if (!is_int($var))
				continue;

			switch($var)
			{
				case self::COLOURS:
					$this->ofc_chart->set_colours($value);
				break;
				case self::STACK:
					$this->ofc_chart->set_keys($value);
				break;
				case self::VALUES:
					// FIXME This commented code utilizes OFC_Charts_Bar_Stack_Value, allowing individual colours and tooltips
					// Unfortunately, it seems that OFC2 doesn't interpret this correctly, or I'm not using it correctly. Leave it alone for now.
//					$this->ofc_chart->set_values(null);
//
//					foreach($value as $index=>$values)
//					{
//						if (isset($values[self::TOOLTIP]))
//						{
//							$tooltip = $values[self::TOOLTIP];
//							unset($values[self::TOOLTIP]);
//						}
//
//						foreach($values as $value_index=>$values_value)
//						{
//							$v = new OFC_Charts_Bar_Stack_Value($values_value, '#000000');
//							$this->ofc_chart->append_value($v);
//						}
//					}
					$this->ofc_chart->set_values(null);

					foreach($value as $index=>$values)
					{
						$this->ofc_chart->append_value($values);
					}
				break;
			}
		}

		return $this;
	}

	/**
	 * Override KReport_Chart::as_array() to support the differing format of stacked bar chart data
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
			$arr['x'][]     = array($value[0], $value[1]);
			$arr['y'][]     = $index;
			$arr['label'][] = null;
		}

		return $arr;
	}

	/**
	 * Override KReport_Chart::to_csv() to support the differing format of stacked bar chart data
	 * 
	 * @param integer $x The index of the value to retrieve
	 * @return string A CSV string containing the x and y values at index $x
	 * @access public
	 */
	function to_csv($x)
	{
		if (isset($this->_config[self::VALUES]) && isset($this->_config[self::VALUES][$x]))
			return $this->_config[self::VALUES][$x][0] . ',' . $this->_config[self::VALUES][$x][1] . "\n";
	}

	/**
	 * Override KReport_Chart::point(). Throws an exception.
	 */
	function point($x, $y)
	{
		throw new Exception('Cannot call ' . __CLASS__ . '::point(). Use ' . __CLASS__ . '::bar()');
	}

	/**
	 * Configure a new stack.
	 * Note: Currently, stacked bar charts only support a maximum of two bars stacked together
	 * 
	 * @param string $colour A hexidecimal string to set the colour to
	 * @param string $text The tooltip text for the stack
	 * @param integer $font_size The fontsize of the tooltip
	 * @return KReport_Chart_StackBar The instance being operated on
	 * @access public
	 */
	function stack($colour, $text, $font_size)
	{
		$stacks = isset($this->_config[self::STACK]) ? $this->_config[self::STACK] : array();
		$index = count($stacks);

		$stacks[$index]['colour']    = $colour;
		$stacks[$index]['text']      = $text;
		$stacks[$index]['font-size'] = (float)$font_size;

		return $this->set(self::STACK, $stacks);
	}

	/**
	 * Set a new bar
	 * Note: Currently, stacked bar charts only support a maximum of two bars stacked together
	 * 
	 * @param integer $y1 The Y value of the first bar
	 * @param integer $y2 The Y value of the second (stacked) bar
	 * @return KReport_Chart_StackBar The instance being operated on
	 * @access public
	 */
	function bar($y1, $y2)
	{
		$bars = isset($this->_config[self::VALUES]) ? $this->_config[self::VALUES] : array();
		$index = count($bars);

		$bars[$index][] = (float)$y1;
		$bars[$index][] = (float)$y2;

		return $this->set(self::VALUES, $bars);
	}

	/**
	 * Override KReport_Chart::get_x_min() to support the differing format of stacked bar chart data. Get the minimum X value of the chart
	 * 
	 * @return integer The lowest X value
	 * @access public
	 */
	function get_x_min()
	{
		return 0;
	}

	/**
	 * Override KReport_Chart::get_x_mmax() to support the differing format of stacked bar chart data. Get the maximum X value of the chart
	 * 
	 * @return integer The highest X value
	 * @access public
	 */
	function get_x_max()
	{
		return count($this->_config[self::VALUES]) - 1; // minus 1 because the first is 0
	}

	/**
	 * Override KReport_Chart::get_y_min() to support the differing format of stacked bar chart data. Get the minimum Y value of the chart
	 * 
	 * @return integer The lowest Y value
	 * @access public
	 */
	function get_y_min()
	{
		$values = array();

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			rsort($value);
			$values[] = array_pop($value);
		}

		$x = array_values($values);
		rsort($x);
		return array_pop($x);
	}

	/**
	 * Override KReport_Chart::get_y_max() to support the differing format of stacked bar chart data. Get the maximum Y value of the chart.
	 * 
	 * @return integer The highest Y value
	 * @access public
	 */
	function get_y_max()
	{
		$values = array();

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			$values[] = array_sum($value);
		}

		$x = array_values($values);
		sort($x);
		return array_pop($x);
	}
}
