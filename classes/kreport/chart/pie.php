<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for OFC2 Pie Charts
 */
class KReport_Chart_Pie extends KReport_Chart
{
	const ALPHA          = 24001;
	const ANGLE          = 24002;
	const ANIMATION      = 24003;
	const COLOURS        = 24004;
	const BORDER         = 24005;
	const SLICE_ON_CLICK = 24006;

	/**
	 * Instantiate a new OFC2 OFC_Charts_Pie object and assign properties to it
	 * 
	 * @return KReport_Chart_Pie The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_Charts_Pie;

		parent::execute();

		foreach($this->_config as $var=>$value)
		{
			if (!is_int($var))
				continue;

			switch($var)
			{
				case self::ALPHA:
					$this->ofc_chart->set_alpha($value);
				break;
				case self::ANGLE:
					$this->ofc_chart->set_start_angle($value);
				break;
				case self::ANIMATION:
				break;
				case self::BORDER:
					$this->ofc_chart->set_border($value);
				break;
				case self::COLOURS:
					if (!is_array($value))
						throw new Exception ('Colours must be an array');

					$this->ofc_chart->add_colours($value);
				break;
				// FIXME the parent still calls set_values, and we are just overwritting its work. The parent should instead not process values to save us processing time
				// A thought: we can use set_CONSTNAME functions to overload the default behavior from the parent instead of a switch statement, and this foreach loop can
				// instead just call $this->set_CONSTNAME($value);
				// this will also fix the need to have a huge switch statement in the OFC class, though some other solution for that still needs to be found
				case self::VALUES:
					foreach($this->_config[self::VALUES] as $index=>$value)
					{
						if (is_array($value) && isset($value['value']) && isset($value['text']))
						{
							$this->_config[self::VALUES][$index] = new OFC_Charts_Pie_Value((float)$value['value'], $value['text']);
							if (isset($this->_config[self::SLICE_ON_CLICK]) && isset($this->_config[self::SLICE_ON_CLICK][$index]))
							{
								$this->_config[self::VALUES][$index]->set_on_click($this->_config[self::SLICE_ON_CLICK][$index]['click']);
							}
						}
						else if (is_array($value) && isset($value['value']))
						{
							$this->_config[self::VALUES][$index] = $value['value'];
						}
					}

					$this->ofc_chart->set_values($this->_config[self::VALUES]);
				break;
			}
		}

		return $this;
	}

	function get_colours()
	{
		return isset($this->_config[self::COLOURS]) ? $this->_config[self::COLOURS] : null;
	}

	function slice_on_click($slice_number, $click)
	{
		$slices = isset($this->_config[self::SLICE_ON_CLICK]) ? $this->_config[self::SLICE_ON_CLICK] : array();

		$slices[$slice_number]['click'] = $click;

		return $this->set(self::SLICE_ON_CLICK, $slices);
	}

	/**
	 * Override KReport_Chart::as_array() to support the differing format of pie chart data
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
			if ($value instanceof OFC_Charts_Pie_Value)
			{
				$arr['x'][] = $value->value;
				$arr['y'][] = $index;
				$arr['label'][] = $value->label;
			}
			else if (is_array($value))
			{
				$arr['x'][] = $value['value'];
				$arr['y'][] = $index;
				$arr['label'][] = $value['label'];
			}
			else
			{
				$arr['x'][] = $value;
				$arr['y'][] = $index;
				$arr['label'][] = null;
			}
		}

		return $arr;
	}

	/**
	 * Override KReport_Chart::to_csv() to support the differing format of pie chart data
	 * 
	 * @param integer $x The index of the value to retrieve
	 * @return string A CSV string containing the x and y values at index $x
	 * @access public
	 */
	function to_csv($x)
	{
		if (isset($this->_config[self::VALUES]) && $this->_config[self::VALUES][$x] instanceof OFC_Charts_Pie_Value)
		{
			return $x . ',' . $this->_config[self::VALUES][$x]->value . ',' . $this->_config[self::VALUES][$x]->label . "\n";
		}
		else if (isset($this->_config[self::VALUES]) && is_array($this->_config[self::VALUES][$x]))
		{
			return $x . ','. $this->_config[self::VALUES][$x]['value'] . ',' . $this->_config[self::VALUES][$x]['text'] . "\n";
		}
		else if (isset($this->_config[self::VALUES]))
		{
			return $x . ',' . $this->_config[self::VALUES][$x] . "\n";
		}
	}

	/**
	 * Add a new slice to the pie chart
	 * 
	 * @param integer $number The value of the slice
	 * @param string $text The label of the slice. Defaults to null, which will display the number instead
	 * @return KReport_Chart_Pie The instance being operated on
	 * @access public
	 */
	function slice($number, $text = null, $on_click = null)
	{
		$slices = isset($this->_config[self::VALUES]) ? $this->_config[self::VALUES] : array();
		$index = count($slices);

		$slices[$index]['value'] = (float)$number;

		if (!is_null($text))
			$slices[$index]['text'] = $text;

		if (!is_null($on_click))
			$this->slice_on_click($index, $on_click);

		return $this->set(self::VALUES, $slices);
	}

	/**
	 * Override KReport_Chart::point(). Throws an exception.
	 */
	function point($x, $y)
	{
		throw new Exception('Cannot call ' . __CLASS__ . '::point(). Use ' . __CLASS__ . '::slice()');
	}

	/**
	 * Set the border of each slice
	 * 
	 * @param integer $border The width of the border
	 * @return KReport_Chart_Pie The instance being operated on
	 * @access public
	 */
	function border($border)
	{
		return $this->set(self::BORDER, $border);
	}

	/**
	 * Set the alpha of the pie chart
	 * 
	 * @param float $alpha A number between 0 and 1 representing the alpha
	 * @return KReport_Chart_Pie The instance being operated on
	 * @access public
	 */
	function alpha($alpha)
	{
		return $this->set(self::ALPHA, $alpha);
	}

	/**
	 * Set the initial angle of the pie chart
	 * 
	 * @param integer $angle A number repsenting the initiali angle of the pie chart
	 * @return KReport_Chart_Pie The instance being operated on
	 * @access public
	 */
	function angle($angle)
	{
		return $this->set(self::ANGLE, $angle);
	}

	/**
	 * Set the colours of the pie chart. The index of the colour will set the colour of the value that contains the same index.
	 * 
	 * @param array $colours An array of hexidecimal strings that repsent the colour of the slice that share the same index
	 * @return KReport_Chart_Pie The instance being operated on
	 * @access public
	 */
	function colours($colours)
	{
		return $this->set(self::COLOURS, $colours);
	}

	/**
	 * Override KReport_Chart::get_x_min() to support the differing format of pie chart data. Get the minimum X value of the chart
	 * 
	 * @return integer The lowest X value
	 * @access public
	 */
	function get_x_min()
	{
		$values = array();

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			$values[] = (is_array($value) ? $value['value'] : $value);
		}

		$x = array_values($values);
		rsort($x);
		return array_pop($x);
	}

	/**
	 * Override KReport_Chart::get_x_mmax() to support the differing format of pie chart data. Get the maximum X value of the chart
	 * 
	 * @return integer The highest X value
	 * @access public
	 */
	function get_x_max()
	{
		$values = array();

		foreach($this->_config[self::VALUES] as $index=>$value)
		{
			$values[] = (is_array($value) ? $value['value'] : $value);
		}

		$x = array_values($values);
		sort($x);
		return array_pop($x);
	}

	/**
	 * Override KReport_Chart::get_y_min() to support the differing format of pie chart data. Get the minimum Y value of the chart
	 * 
	 * @return integer The lowest Y value
	 * @access public
	 */
	function get_y_min()
	{
		return $this->get_x_min();
	}

	/**
	 * Override KReport_Chart::get_y_max() to support the differing format of pie chart data. Get the maximum Y value of the chart.
	 * 
	 * @return integer The highest Y value
	 * @access public
	 */
	function get_y_max()
	{
		return $this->get_x_max();
	}
}
