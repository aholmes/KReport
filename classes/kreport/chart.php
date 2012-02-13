<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Coordinate instances of chart types. Contains methods that are generic to all chart types.
 */
class KReport_Chart
{
	// Chart types
	const LINE      = 20001;
	const BAR       = 20002;
	const HBAR      = 20003;
	const STACKBAR  = 20004;
	const AREA      = 20005;
	const PIE       = 20006;
	const TAG       = 20007;
	// Generic chart-configuration constants
	const KEY       = 20008;
	const COLOUR    = 20009;
	const HALO_SIZE = 20010;
	const VALUES    = 20011;
	const TOOLTIP   = 20012;
	const ON_CLICK  = 20013;
	const AXIS      = 20014;

	/**
	 * @var string The default instance name
	 * @access public
	 * @static
	 */
	public static $default = 'default';

	/**
	 * @var array KReport_Chart instances
	 * @access public
	 * @static
	 */
	public static $instances = array();

	/**
	 * @var string The name of the current instance
	 * @access protected
	 */
	protected $_instance;

	/**
	 * @var array The configuration values of the current instance
	 * @access protected
	 */
	protected $_config = array();

	/**
	 * @var OFC_Chart The instantiated OFC2 object
	 * @access protected
	 */
	protected $ofc_chart;

	/**
	 * Return a new or already created KReport_Chart instance
	 * 
	 * @param string $group The name of the group this instance belongs to. Defaults to KReport::$default
	 * @param string $name The name of the instance. Defaults to KReport_Chart::$default
	 * @param array $config Any pre-configuration variables to assign to the instance
	 * @return KReport the new or previously instantiated KReport_Chart instance
	 * @access public
	 * @static
	 */
	public static function instance($group = null, $name = null, array $config = null)
	{
		if (!isset(self::$instances[$group]))
		{
			if ($group === NULL)
			{
				$group = KReport::$default;
			}

			self::$instances[$group] = array();
		}

		if (!isset(self::$instances[$group][$name]))
		{
			if ($name === NULL)
			{
				$name = self::$default;
			}

			new KReport_Chart($group, $name, $config);
		}

		return self::$instances[$group][$name];
	}

	/**
	 * Set KReport_Chart::$instances[$name] to the current KReport_Chart instance
	 * 
	 * @param string $group The name of the group this instance belongs to
	 * @param string $name The name of the instance
	 * @param array $config Any pre-configuration variables to assign to the instance
	 */
	function __construct($group, $name, array $config = null)
	{
		$this->_instance = $name;
		$this->_config   = $config;

		// only instantiate a new child chart if KReport_Chart was just called, otherwise __construct is being called from a child
		if (get_class($this) === __CLASS__)
		{
			switch($config['type'])
			{
				case self::LINE:
					self::$instances[$group][$name] = new KReport_Chart_Line($name, $config);
				break;
				case self::BAR:
					self::$instances[$group][$name] = new KReport_Chart_Bar($name, $config);
				break;
				case self::HBAR:
					self::$instances[$group][$name] = new KReport_Chart_HBar($name, $config);
				break;
				case self::STACKBAR:
					self::$instances[$group][$name] = new KReport_Chart_StackBar($name, $config);
				break;
				case self::AREA:
					self::$instances[$group][$name] = new KReport_Chart_Area($name, $config);
				break;
				case self::PIE:
					self::$instances[$group][$name] = new KReport_Chart_Pie($name, $config);
				break;
				case self::TAG:
					self::$instances[$group][$name] = new KReport_Chart_Tag($name, $config);
				break;
				default:
					self::$instances[$group][$name] = new KReport_Chart_Line($name, $config);
			}
		
			// mark ourselves for unsetting because we are returning a child and not ourselves
			unset($this);

			// return the child instance so no one can reference $this
			return self::$instances[$group][$name];
		}
	}

	function __destruct()
	{
	}

	/**
	 * Get the name of the instance
	 * 
	 * @return string The name of the instance
	 */
	function __toString()
	{
		return $this->_instance;
	}

	/**
	 * Pop a colour off the KReport_Chart::$default_colours array and make it our colour if a colour has not already been set. Otherwise, return the already set colour
	 *
	 * @param bool $set Whether to set the chart colour or only to pop one off the default colours array
	 * @return string The colour popped off the default colours array or a random colour if there are no colours left
	 * @access public
	 */
	function get_colour()
	{
		return isset($this->_config[self::COLOUR]) ? $this->_config[self::COLOUR] : null;
	}

	/**
	 * Set variables for the KReport_Chart::$ofc_chart instance that are shared by all chart types
	 * 
	 * @return KReport_Chart The KReport_Chart instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->sanity(__FUNCTION__);

		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::KEY:
					if (!is_array($value) || !(array_key_exists('text', $value) && array_key_exists('font_size', $value)))
						throw new Exception ('Key value must be an array with indexes "text" and "font_size"');

					$this->ofc_chart->set_key($value['text'], $value['font_size']);
				break;
				case self::COLOUR:
					$this->ofc_chart->set_colour($value);
				break;
				case self::HALO_SIZE:
					$this->ofc_chart->set_halo_size($value);
				break;
				case self::VALUES:
					if (!is_array($value))
						throw new Exception ('Values must be a numerically indexed array');

					$this->ofc_chart->set_values($value);
				break;
				case self::TOOLTIP:
					$this->ofc_chart->set_tooltip($value);
				break;
				case self::ON_CLICK:
					$this->ofc_chart->set_on_click($value);
				break;
				case self::AXIS:
					$this->ofc_chart->set_axis($value);
				break;
			}
		}

		return $this;
	}

	/**
	 * Verify particulars are set before executing certain steps
	 * @access private
	 */
	private function sanity($from = null)
	{
		// values should always be set before executing or before retrieving min/max x/y values
		if (!isset($this->_config[self::VALUES]))
			throw new Exception('Chart "' . $this->_instance . '" does not have any values set' . (!is_null($from) ? '. Called from "' . $from . '"' : '.'));
	}

	/**
	 * Get the data for the chart
	 * 
	 * @return array The data that has been set for the chart
	 */
	function get_values()
	{
		return $this->_config[self::VALUES];
	}

	/**
	 * Return the charts x, y, and label values as an array
	 * 
	 * @return array The x, y, and label values
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
			$arr['x'][]     = $value;
			$arr['y'][]     = $index;
			$arr['label'][] = null;
		}

		return $arr;
	}

	/**
	 * Return the chart x and y value as index $x as a CSV string
	 * 
	 * @param integer $x The index of the value to retrieve
	 * @return string A CSV string containing the x and y values at index $x
	 * @access public
	 */
	function to_csv($x)
	{
		if (isset($this->_config[self::VALUES]) && isset($this->_config[self::VALUES][$x]))
			return $x . ',' . $this->_config[self::VALUES][$x] . "\n";
	}

	/**
	 * Set the chart data from an array
	 * 
	 * @param array $data An array containing the data. The format of this array differs between chart types.
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function data(array $data)
	{
		$this->set(self::VALUES, $data);

		return $this;
	}

	function axis($axis)
	{
		$this->set(self::AXIS, $axis);

		return $this;
	}

	/**
	 * Set a KReport_Chart::$var variable
	 * 
	 * @param integer $var The variable to set
	 * @param mixed $value The value of the variabke
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function set($var, $value = null)
	{
		if (!is_int($var))
			throw new Exception ('Var must be int from class constants ' . __CLASS__);

		$this->_config[$var] = $value;

		return $this;
	}

	/**
	 * Get the OFC_Chart object
	 * 
	 * @return OFC_Chart The OFC2 object that has been instantiated
	 * @access public
	 */
	function get()
	{
		return $this->ofc_chart;
	}

	/**
	 * Get the minimum X value of the chart
	 * 
	 * @return integer The lowest X value
	 * @access public
	 */
	function get_x_min()
	{
		$this->sanity(__FUNCTION__);

		$x = array_keys($this->_config[self::VALUES]);
		rsort($x);
		return array_pop($x);
	}

	/**
	 * Get the maximum X value of the chart
	 * 
	 * @return integer The highest X value
	 * @access public
	 */
	function get_x_max()
	{
		$this->sanity(__FUNCTION__);

		$x = array_keys($this->_config[self::VALUES]);
		sort($x);
		return array_pop($x);
	}

	/**
	 * Get the minimum Y value of the chart
	 * 
	 * @return integer The lowest Y value
	 * @access public
	 */
	function get_y_min()
	{
		$this->sanity(__FUNCTION__);

		$y = array_values($this->_config[self::VALUES]);
		rsort($y);
		return array_pop($y);
	}

	/**
	 * Get the maximum Y value of the chart
	 * 
	 * @return integer The highest Y value
	 * @access public
	 */
	function get_y_max()
	{
		$this->sanity(__FUNCTION__);

		$y = array_values($this->_config[self::VALUES]);
		sort($y);
		return array_pop($y);
	}

	/**
	 * Set the key of the chart for displaying in the report legend
	 *
	 * @param string $text The key to display
	 * @param integer $font_size The size of the text
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function key($text, $font_size = 10)
	{
		return $this->set(self::KEY, array(
			'text'      => $text,
			'font_size' => (float)$font_size
		));
	}

	/**
	 * Set the colour of the chart
	 * 
	 * @param string $colour A hexidecimal value to set the colour to
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function colour($colour)
	{
		return $this->set(self::COLOUR, $colour);
	}

	/**
	 * Set the halo size of the chart
	 * 
	 * @param integer $halo_size The size of the halo
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function halo_size($halo_size)
	{
		return $this->set(self::HALO_SIZE, $halo_size);
	}

	/**
	 * Set the hover-over tooltip for each data point
	 *
	 * @param string $tooltip The tooltip
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function tooltip($tooltip)
	{
		return $this->set(self::TOOLTIP, $tooltip);
	}

	/**
	 * Set a data point on the report
	 * 
	 * @param integer $x The X value of the data point
	 * @param integer $y The Y value of the data point
	 * @return KReport_Chart The instance being operated on
	 * @access public
	 */
	function point($x, $y)
	{
		$points = isset($this->_config[self::VALUES]) ? $this->_config[self::VALUES] : array();

		$points[(float)$x] = (float)$y;

		return $this->set(self::VALUES, $points);
	}

	/**
	 * Set an onclick() javascript handler for the stack
	 * 
	 * @param string $click The javascript handler
	 * @return KReport_Chart_StackBar The instance being operated on
	 * @access public
	 */
	function on_click($click)
	{
		return $this->set(self::ON_CLICK, $click);
	}
}
