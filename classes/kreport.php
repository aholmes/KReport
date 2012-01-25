<?php defined('SYSPATH') or die('No direct script access.');

// FIXME When a series has the same name among multiple KReport instances, the original will be overwritten.
// You should be allowed to have the same series name among multiple KReport instances

/**
 * Integrate KReport_Chart and KReport_Element classes together to create a KReport object
 */
class KReport
{
	const X_AXIS              = 10001;
	const Y_AXIS              = 10002;
	const Y_AXIS_RIGHT        = 10003;
	const X_LEGEND            = 10004;
	const Y_LEGEND            = 10005;
	const X_LABELS            = 10006;
	const Y_LABELS            = 10007;
	const Y_LABELS_RIGHT      = 10008;
	const X_STEPS             = 10009;
	const Y_STEPS             = 10010;
	const Y_STEPS_RIGHT       = 10011;
	const TITLE               = 10012;
	const BG_COLOUR           = 10013;
	const X_GRID_COLOUR       = 10014;
	const Y_GRID_COLOUR       = 10015;
	const X_COLOUR            = 10016;
	const Y_COLOUR            = 10017;
	const Y_COLOUR_RIGHT      = 10018;
	const X_STROKE            = 10019;
	const Y_STROKE            = 10020;
	const Y_STROKE_RIGHT      = 10021;
	const X_TICK_LENGTH       = 10022;
	const Y_TICK_LENGTH       = 10023;
	const Y_TICK_LENGTH_RIGHT = 10024;
	const EXPORTABLE          = 10025;
	const WIDTH               = 10026;
	const HEIGHT              = 10027;
	const X_ALIAS             = 10028;
	const Y_ALIAS             = 10029;

	/**
	 * @var string default instance name
	 * @access public
	 * @static
	 */
	public static $default = 'default';

	/**
	 * @var array FC instances
	 * @access public
	 * @static
	 */
	public static $instances = array();

	/**
	 * @var string the name of the currently operated-on instance
	 * @access protected
	 */
	protected $_instance;

	/**
	 * @var array the configuration of the currently operated-on instance
	 * @access protected
	 */
	protected $_config = array();

	/**
	 * @var array KReport_Chart classes that are to be added to the KReport instance. Stored in groups if instances
	 * @access private
	 */
	private $chart = array();

	/**
	 * @var array KReport_Chart instances that have been executed. Intention is to save reprocessing of an already processed chart, but currently has no effect.
	 * @access private
	 */
	private $charts_added = array();

	/**
	 * @var type KReport_Element classes that are to be added to the KReport instance
	 * @access private
	 */
	private $element = array();

	/**
	 * @var type KReport_Element instances that have been executed. Intention is to save reprocessing of an already processed element, but currently has no effect.
	 * @access private
	 */
	private $elements_added = array();

	/**
	 * @var OFC_Chart the instance of the OFC2 chart class
	 * @access protected
	 */
	protected $ofc_chart;

	/**
	 * @var KReport_Chart the series that was just created or is currently being operated on
	 * @access private
	 */
	private $series_instance;

	/**
	 * @var bool determine whether calls to series() should chain the newly created series or continue to chain the KReport object
	 * @access private
	 */
	private $chain_series = false;

	public $default_chart_colours = array(
		'#FFCC99',
		'#CC6699',
		'#FFCC00',
		'#CCCC00',
		'#339999',
		'#996699',
		'#CC6666',
		'#99CC00',
		'#FF9933',
		'#3366CC'
	);

	/**
	 * Return a new or already created KReport instance
	 * 
	 * @param string $name The name of the instance. Defaults to KReport::$default
	 * @param array $config Any pre-configuration variables to assign to the instance
	 * @return KReport the new or previously instantiated KReport instance
	 * @access public
	 * @static
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if (!isset(self::$instances[$name]))
		{
			if ($name === NULL)
			{
				$name = self::$default;
			}

			new KReport($name, $config);
		}

		return self::$instances[$name];
	}

	/**
	 * Set KReport::$instances[$name] to the current KReport instance
	 * 
	 * @param string $name The name of the instance
	 * @param array $config Any pre-configuration variables to assign to the instance
	 */
	function __construct($name, array $config = null)
	{
		$this->_instance = $name;
		$this->_config   = (!is_null($config)) ? $config : array();

		self::$instances[$name] = $this;
	}

	/**
	 * Unset our instance
	 */
	function __destruct()
	{
		unset(self::$instances[$this->_instance]);
	}

	/**
	 * Instantiate the KReport libraries and set the configuration settings.
	 * 
	 * Call each KReport_Chart and KReport_Element execute functions.
	 * Fill in default settings that haven't been set by the user.
	 * @return KReport the current KReport instance 
	 * @access public
	 */
	function execute()
	{
		// create the OFC2 object
		$this->ofc_chart = new OFC_Chart();

		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::X_LEGEND:
					$this->ofc_chart->set_x_legend($value);
				break;
				case self::Y_LEGEND:
					$this->ofc_chart->set_y_legend($value);
				break;
				case self::BG_COLOUR:
					$this->ofc_chart->set_bg_colour($value);
				break;
				// other properties
				case self::EXPORTABLE:
				case self::WIDTH:
				case self::HEIGHT:
				case self::X_ALIAS:
				case self::Y_ALIAS:
				break;
				// Instantiate KReport_Elements on-the-fly and set any element-specific settings 
				// FIXME KReport_Element needs to be pulled from KReport_Element_Properties (or some other class). This switch statement of elements and element properties is getting silly.
				case self::TITLE:
				case self::X_AXIS:
				case self::Y_AXIS:
				case self::Y_AXIS_RIGHT:
				case self::X_LABELS:
				case self::Y_LABELS:
				case self::Y_LABELS_RIGHT:
				case self::X_GRID_COLOUR:
				case self::Y_GRID_COLOUR:
				case self::X_STEPS:
				case self::Y_STEPS:
				case self::Y_STEPS_RIGHT:
				case self::X_COLOUR:
				case self::Y_COLOUR:
				case self::Y_COLOUR_RIGHT:
				case self::X_STROKE:
				case self::Y_STROKE:
				case self::Y_STROKE_RIGHT:
				case self::X_TICK_LENGTH:
				case self::Y_TICK_LENGTH:
				case self::Y_TICK_LENGTH_RIGHT:
				case KReport_Element_Axis_X::LABEL_COLOUR:
				case KReport_Element_Axis_X::LABEL_SIZE:
				case KReport_Element_Axis_X::LABEL_STEPS:
				case KReport_Element_Axis_X::LABEL_ROTATE:
					// TODO it would be nice if we can avoid having to re-execute elements after subsequent execute() calls
					//if (in_array($var, $this->elements_added))
					//	break;

					//$this->elements_added[] = $var;

					if ($var == self::TITLE)
						$this->element[self::TITLE] = KReport_Element::instance($this->__toString(), KReport_Element::TITLE, array('title' => $value));

					if ($var == self::X_AXIS)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::RANGE, $value);

					if ($var == self::Y_AXIS)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::RANGE, $value);

					if ($var == self::Y_AXIS_RIGHT)
						$this->element[self::Y_AXIS_RIGHT] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS_RIGHT)
							->set(KReport_Element_Axis::RANGE, $value);

					if ($var == self::X_LABELS)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::LABELS, $value);

					if ($var == self::Y_LABELS)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::LABELS, $value);

					if ($var == self::Y_LABELS_RIGHT)
						$this->element[self::Y_AXIS_RIGHT] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS_RIGHT)
							->set(KReport_Element_Axis::LABELS, $value);

					if ($var == self::X_GRID_COLOUR)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::GRID_COLOUR, $value);

					if ($var == self::Y_GRID_COLOUR)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::GRID_COLOUR, $value);

					if ($var == self::X_STEPS)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::STEPS, $value);

					if ($var == self::Y_STEPS)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::STEPS, $value);	

					if ($var == self::Y_STEPS_RIGHT)
						$this->element[self::Y_AXIS_RIGHT] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS_RIGHT)
							->set(KReport_Element_Axis::STEPS, $value);	

					if ($var == self::X_COLOUR)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::COLOUR, $value);

					if ($var == self::Y_COLOUR)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::COLOUR, $value);

					if ($var == self::Y_COLOUR_RIGHT)
						$this->element[self::Y_AXIS_RIGHT] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS_RIGHT)
							->set(KReport_Element_Axis::COLOUR, $value);

					if ($var == self::X_STROKE)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::STROKE, $value);

					if ($var == self::Y_STROKE)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::STROKE, $value);

					if ($var == self::Y_STROKE_RIGHT)
						$this->element[self::Y_AXIS_RIGHT] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS_RIGHT)
							->set(KReport_Element_Axis::STROKE, $value);

					if ($var == self::X_TICK_LENGTH)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis::TICK_LENGTH, $value);
					
					if ($var == self::Y_TICK_LENGTH)
						$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
							->set(KReport_Element_Axis::TICK_LENGTH, $value);

					if ($var == self::Y_TICK_LENGTH_RIGHT)
						$this->element[self::Y_AXIS_RIGHT] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS_RIGHT)
							->set(KReport_Element_Axis::TICK_LENGTH, $value);

					if ($var == KReport_Element_Axis_X::LABEL_COLOUR)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis_X::LABEL_COLOUR, $value);

					if ($var == KReport_Element_Axis_X::LABEL_SIZE)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis_X::LABEL_SIZE, $value);

					if ($var == KReport_Element_Axis_X::LABEL_STEPS)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis_X::LABEL_STEPS, $value);

					if ($var == KReport_Element_Axis_X::LABEL_ROTATE)
						$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
							->set(KReport_Element_Axis_X::LABEL_ROTATE, $value);
				break;
				default:
					throw new Exception('Cannot set values for variable "' . $var . '" in ' . __CLASS__);
			}
		}

		// execute KReport_Chart instances if they haven't yet been executed
		foreach($this->chart[$this->_instance] as $chart_name=>$chart)
		{
			// TODO it would be nice if we can avoid re-executing KReport_Chart_* instances if they have already been executed.
			//if (in_array($chart_name, $this->charts_added))
			//	continue;

			//$this->charts_added[] = $chart_name;

			// some chart types require an array of colours, while other have one colour type
			if (defined(get_class($chart) . '::COLOURS'))
			{
				if (!$chart->get_colours())
				{
					$colours = array();

					foreach($chart->get_values() as $index=>$value)
					{
						$colours[$index] = $this->get_colour();
					}

					$chart->colours($colours);
				}
			}
			else
			{
				if (!$chart->get_colour())
				{
					$chart->colour($this->get_colour());
				}
			}

			$this->ofc_chart->add_element($chart->execute()->get());
		}

		// determine default settings
		// set the X Axis to the min and max x-values
		if (!array_key_exists(self::X_AXIS, $this->_config))
		{
			$min = 0;
			$max = 0;
			foreach($this->chart[$this->_instance] as $chart_name=>$chart)
			{
				// pie charts don't have a X/Y axis. KReport_Chart_Pie will tell us the min/max x/y values, but we don't really need them
				if ($chart instanceof KReport_Chart_Pie)
					continue;

				if (($num = $chart->get_x_min()) < $min)
					$min = $num;

				if(($num = $chart->get_x_max()) > $max)
					$max = $num;
			}

			// set the X_AXIS element whether or not it's already been set. The instance will be the same either way
			$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
				->set(KReport_Element_Axis::RANGE, array('min' => $min, 'max' => $max));
		}

		// set the Y Axis to the min and max y-values
		if (!array_key_exists(self::Y_AXIS, $this->_config))
		{
			$min = 0;
			$max = 0;
			foreach($this->chart[$this->_instance] as $chart_name=>$chart)
			{
				// see above about pie
				if ($chart instanceof KReport_Chart_Pie)
					continue;

				if (($num = $chart->get_y_min()) < $min)
					$min = $num;

				if(($num = $chart->get_y_max()) > $max)
					$max = $num;
			}

			// see above about instances
			$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
				->set(KReport_Element_Axis::RANGE, array('min' => $min, 'max' => $max + 1)); // +1 buffer at the top of the graph
		}

		// default to a pleasing gray instead of the default yellow that OFC2 uses
		if (!array_key_exists(self::X_GRID_COLOUR, $this->_config))
		{
			$this->element[self::X_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::X_AXIS)
				->set(KReport_Element_Axis::GRID_COLOUR, '#C0C0C0');
		}

		if (!array_key_exists(self::Y_GRID_COLOUR, $this->_config))
		{
			$this->element[self::Y_AXIS] = KReport_Element::instance($this->__toString(), KReport_Element::Y_AXIS)
				->set(KReport_Element_Axis::GRID_COLOUR, '#C0C0C0');
		}

		// default to white instead of the default off-white that OFC2 uses
		if (!array_key_exists(self::BG_COLOUR, $this->_config))
		{
			$this->set(self::BG_COLOUR, '#FFFFFF');
		}

		// add elements to the OFC_Chart object
		foreach($this->element as $type=>$element)
		{
			switch($type)
			{
				case self::X_AXIS:
					$this->ofc_chart->set_x_axis($element->execute()->get());
				break;
				case self::Y_AXIS:
					$this->ofc_chart->set_y_axis($element->execute()->get());
				break;
				case self::Y_AXIS_RIGHT:
					$this->ofc_chart->set_y_axis_right($element->execute()->get());
				break;
				case self::TITLE:
					$this->ofc_chart->set_title($element->execute()->get());
				break;
			}
		}

		return $this;
	}

	function get_colour()
	{
		if (!empty($this->default_chart_colours))
			return array_pop($this->default_chart_colours);
		else
			return rand(0, 0xFFFFFF);
	}

	/**
	 * Retrieve the OFC2 chart object
	 * 
	 * @return OFC_Chart The OFC2 chart object
	 */
	function get()
	{
		if (!isset($this->ofc_chart))
			return $this->execute()->get();

		return $this->ofc_chart;
	}

	/**
	 * Render the KReport as an OFC2 HTML object
	 * 
	 * @return string The HTML and JavaScript needed to render the KReport
	 * @access public
	 */
	function as_chart()
	{
		return View::factory('chart', array('chart' => $this))->render();
	}

	/**
	 * Render the KReport as JSON that can be passed to OFC2
	 * 
	 * @param bool $human_readable Whether to return indented and "prettified" JSON. Defaults to false.
	 * @return string The JSON of the OFC2 object that can be passed to OFC2
	 * @access public
	 */
	function as_json($human_readable = false)
	{
		return ($human_readable === false) ? $this->ofc_chart->toString() : $this->ofc_chart->toPrettyString();
	}

	/**
	 * Retrieve the values, x and y axes, and labels of each KReport_Chart that has been set up
	 * 
	 * @param string $chart_name_match A string or PCRE used to determine which charts should be included in the output of this function. Defaults to null.
	 * @param bool $name_match_include Whether to include charts that match $chart_name_match or to exclude them. Defaults to true.
	 * @return array The values, x and y axes, and labels of each KReport_Chart instance.
	 * @access public
	 */
	function as_array($chart_name_match = null, $name_match_include = true)
	{
		$arr = array();

		foreach($this->chart[$this->_instance] as $chart_name=>$chart)
		{
			if (!is_null($chart_name_match))
			{
				// Decide that the match name is a regular expression if the first and last characters are the same.
				if ((substr($chart_name_match, 0, 1) === substr($chart_name_match, -1, 1) && (preg_match($chart_name_match, $chart_name) ^ $name_match_include))
				|| $chart_name_match === $chart_name)
					continue;
			}

			$arr[$chart_name] = array();
			$arr[$chart_name]['x_axis'] = array();
			$arr[$chart_name]['y_axis'] = array();
			$arr[$chart_name]['x']      = array();
			$arr[$chart_name]['y']      = array();
			$arr[$chart_name]['label']  = array();
			$arr[$chart_name]['class']  = get_class($chart);

			$chart_values = $chart->as_array();
			$arr[$chart_name]['x']     = $chart_values['x'];
			$arr[$chart_name]['y']     = $chart_values['y'];
			$arr[$chart_name]['label'] = $chart_values['label'];

			// assign the KReport labels to the chart data array to make it more easily processable in other areas
			if (isset($this->_config[self::X_LABELS]))
			{
				foreach($this->_config[self::X_LABELS] as $index=>$label)
				{
					$arr[$chart_name]['x_axis'][] = ((isset($this->_config[self::X_LABELS]) && isset($this->_config[self::X_LABELS][$index])) ? $this->_config[self::X_LABELS][$index] : null);
				}
			}

			if (isset($this->_config[self::Y_LABELS]))
			{
				foreach($this->_config[self::Y_LABELS] as $index=>$label)
				{
					$arr[$chart_name]['y_axis'][] = ((isset($this->_config[self::Y_LABELS]) && isset($this->_config[self::Y_LABELS][$index])) ? $this->_config[self::Y_LABELS][$index] : null);
				}
			}
		}

		return $arr;
	}

	/**
	 * Render the KReport as an HTML table
	 * 
	 * @param bool $show_header Whether to insert a header with the KReport title at the top of the table
	 * @param string $chart_name_match A string or PCRE used to determine which charts should be included in the output of this function. Defaults to null.
	 * @param bool $name_match_include Whether to include charts that match $chart_name_match or to exclude them. Defaults to true.
	 * @return string An HTML table containing the chart data
	 * @access public
	 */
	function as_grid($show_header = true, $chart_name_match = null, $name_match_include = true)
	{
		return View::factory('grid', array(
			'chart'       => $this,
			'charts'      => $this->as_array($chart_name_match, $name_match_include),
			'show_header' => $show_header
		))->render();
	}

	/**
	 * Render the KReport as a CSV file
	 * 
	 * @param bool $as_file Whether to send the output as a downloadable file. Defaults to false.
	 * @param string $chart_name_match A string or PCRE used to determine which charts should be included in the output of this function. Defaults to null.
	 * @param bool $name_match_include Whether to include charts that match $chart_name_match or to exclude them. Defaults to true.
	 * @return string A CSV file containing the chart data. If $as_file is true, this function does not return and will call die().
	 * @access public
	 */
	function as_csv($as_file = false, $chart_name_match = null, $name_match_include = true)
	{
		// TODO update to use as_array so to_csv can be removed from the KReport_Chart_* classes.
		$csv = '';

		foreach($this->chart[$this->_instance] as $chart_name=>$chart)
		{
			if (!is_null($chart_name_match))
			{
				if ((substr($chart_name_match, 0, 1) === substr($chart_name_match, -1, 1) && (preg_match($chart_name_match, $chart_name) ^ $name_match_include))
				|| $chart_name_match === $chart_name)
					continue;
			}

			$csv .= $chart_name . "\nx_axis,y_axis,x,y,label\n";

			$c = 0;

			foreach($chart->get_values() as $x=>$y)
			{
				$csv .= ((isset($this->_config[self::X_LABELS]) && isset($this->_config[self::X_LABELS][$x])) ? $this->_config[self::X_LABELS][$x] : $x) . ',';
				$csv .= ((isset($this->_config[self::Y_LABELS]) && isset($this->_config[self::Y_LABELS][$c])) ? $this->_config[self::Y_LABELS][$c] : $c) . ',';
				$csv .= $chart->to_csv($x);

				$c++;
			}

			// did we set all the labels?
			if ((isset($this->_config[self::X_LABELS]) && ($c < count($this->_config[self::X_LABELS])))
			|| (isset($this->_config[self::Y_LABELS]) && ($c < count($this->_config[self::Y_LABELS]))))
			{
				foreach($this->_config[self::X_LABELS] as $index=>$label)
				{
					// don't show the labels we've already shown
					if ($index < $c)
						continue;

					$csv .= $label . ((isset($this->_config[self::Y_LABELS]) && isset($this->_config[self::Y_LABELS][$index])) ? ',' . $this->_config[self::Y_LABELS][$index] : '') . "\n";
				}
			}
		}

		if ($as_file === true)
		{
			if (ob_get_level())
				ob_clean();

			header('Content-Type: text/csv');
			header('Content-disposition: attachment;filename=chart_' . (isset($this->_config[self::TITLE]) ? str_replace('/[^a-zA-Z0-9_-]/', '', $this->_config[self::TITLE]) : '') . '_' . date('Y-m-dH-i-s', time()) . '.csv');

			die(rtrim($csv));
		}
		else
		{
			return rtrim($csv);
		}
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
	 * Instantiate a new KReport_Chart instance and add it to the KReport instances $chart variable
	 * 
	 * @param mixed $chart Can be either an instance of KReport_Chart or a string. If a string, a new KReport_Instance is created, otherwise the instance of KReport_Chart that was passed will be operated on.
	 * @param integer $type The KReport_Chart type that should be instantiated. See KReport_Chart chart types for available chart type names. Defaults to null.
	 * @param bool $chain_this Whether to return the instance of KReport_Chart that is instantiated, or the instance of KReport that that KReport_Chart is added to. Defaults to true.
	 * @return mixed If $chain_this is true, the instance of KReport_Chart is returned, otherwise the instance of KReport that the KReport_Chart instance was added to is returned.
	 * @access public
	 */
	function series($chart, $type = null, $chain_this = true)
	{
		// it's a string or an instace of a chart, so either create the new chart, or use an existing one
		if (is_string($chart))
		{
			if (!is_null($type))
				$type = array('type' => $type);

			if (!isset($this->chart[$this->_instance]))
				$this->chart[$this->_instance] = array();

			$this->series_instance = $this->chart[$this->_instance][$chart] = KReport_Chart::instance($this->_instance, $chart, $type);
		}

		if ($chart instanceof KReport_Chart)
		{
			$this->series_instance = $this->chart[$this->_instance][$chart->__toString()] = $chart;
		}

		if ($chain_this === true)
		{
			$this->chain_series = true;
			return $this->series_instance;
		}
		else
		{
			return $this;
		}
	}

	/**
	 * DEPRECATED
	 * Set the KReport_Chart data
	 * 
	 * @param array $data The data array that should be set in the currently operated on chart instance
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function data(array $data)
	{
		$this->series_instance->data($data);

		return $this;
	}

	/**
	 * Set a KReport or KReport_Chart instance variable.
	 * NOTE
	 * These function arguments have variable purposes.
	 *
	 * <code>
	 * // Set the background color of the report
	 * KReport::instance()->set(KReport::BG_COLOUR, '#FFFFFF');
	 * 
	 * // Create a line chart instance and set the width of 'Line Chart'
	 * KReport::instance()->set('Line Chart', KReport_Chart_Line::WIDTH, 2);
	 * 
	 * Create a line chart instance and set the key to 'Line'
	 * $chart = KReport::instance->series('Line Chart', KReport_Chart::LINE);
	 * KReport::instance()->set($chart, KReport_Chart::KEY, 'Line');
	 * </code>
	 * 
	 * @param mixed $chart If $chart is an integer, set a KReport variable that has the value assigned to $var. If $chart is a string or instance of KReport_Chart, set the variable $var to the value $value in the KReport_Chart instance.
	 * @param mixed $var If $chart is an integer, this is the value being assigned to the KReport configuration variable that is KReport::$chart. If $chart is a string, $var is an integer that represents the KReport_Chart variable to set.
	 * @param mixed $value If $chart is not an integer, $value is the value assigned to the KReport_Chart instance $var variable. If $chart is an integer, $value has no purpose. Defaults to null.
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function set($chart, $var, $value = null)
	{
		// $chart is an integer, so we need to set an KReport variable, not a KReport_Chart variable
		if (is_int($chart))
		{
			$this->_config[$chart] = $var;
		}
		else
		{
			$chart_instance = null;

			if ($chart instanceof KReport_Chart)
			{
				if (!in_array($chart, $this->chart[$this->_instance]))
					$this->series($chart);

				$chart_instance = $this->chart[$this->_instance][$chart->__toString()];
			}

			if (is_string($chart))
			{
				if (!array_key_exists($chart, $this->chart[$this->_instance]))
					$this->series($chart);

				$chart_instance = $this->chart[$chart][$this->_instance];
			}

			$chart_instance->set($var, $value);
		}

		return $this;
	}

	// TODO these "setters" can be handled from __call(), but perhaps it's better to have them here for documentations sake
	// The other solution is to document that constants instead

	/**
	 * Set the title of the report
	 * Default: no title
	 * 
	 * @param string $title The title of the report
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function title($title)
	{
		return $this->set(self::TITLE, $title);	
	}

	/**
	 * Set the background colour of the report
	 * Default: #FFFFFF
	 * 
	 * @param string $colour A hexidecimal value to set the background colour to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function bg_colour($colour)
	{
		return $this->set(self::BG_COLOUR, $colour);
	}

	/**
	 * For the X axis range instead of allowing KReport to figure it out
	 * 
	 * @param integer $min The minimum value of the X axis
	 * @param integer $max The maximum value of the X axis
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_range($min, $max)
	{
		return $this->set(self::X_AXIS, array(
			'min' => (float)$min,
			'max' => (float)$max
		));
	}
	
	/**
	 * For the Y axis range instead of allowing KReport to figure it out
	 * 
	 * @param integer $min The minimum value of the Y axis
	 * @param integer $max The maximum value of the Y axis
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_range($min, $max)
	{
		return $this->set(self::Y_AXIS, array(
			'min' => (float)$min,
			'max' => (float)$max
		));
	}

	/**
	 * Set the labels for the X axis instead of defaulting to the numerical values of the X axis
	 * 
	 * @param array $labels A numerically indexed array of labels for each X axis value
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_labels(array $labels)
	{
		return $this->set(self::X_LABELS, $labels);
	}

	/**
	 * Set the labels for the Y axis instead of defaulting to the numerical values of the Y axis
	 * 
	 * @param array $labels A numerically indexed array of labels for each Y axis value
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_labels(array $labels)
	{
		return $this->set(self::Y_LABELS, $labels);
	}

	/**
	 * Set the labels for the right Y axis instead of defaulting to the numerical values of the right Y axis
	 * 
	 * @param array $labels A numerically indexed array of labels for each right Y axis value
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_labels_right(array $labels)
	{
		return $this->set(self::Y_LABELS_RIGHT, $labels);
	}

	/**
	 * Set how many steps must pass before a value is printed on the X axis.
	 * Default: 1
	 * 
	 * @param integer $steps The number of steps
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_steps($steps)
	{
		return $this->set(self::X_STEPS, $steps);
	}

	/**
	 * Set how many steps must pass before a value is printed on the Y axis.
	 * Default: 1
	 * 
	 * @param integer $steps The number of steps
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_steps($steps)
	{
		return $this->set(self::Y_STEPS, $steps);
	}

	/**
	 * Set how many steps must pass before a value is printed on the right Y axis.
	 * Default: 1
	 * 
	 * @param integer $steps The number of steps
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_steps_right($steps)
	{
		return $this->set(self::Y_STEPS_RIGHT, $steps);
	}

	/**
	 * Set the grid colour of the X axis lines
	 * Default: #C0C0C0
	 * 
	 * @param string $colour A hexidecimal value to set the background colour to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_grid_colour($colour)
	{
		return $this->set(self::X_GRID_COLOUR, $colour);
	}

	/**
	 * Set the grid colour of the Y axis lines
	 * Default: #C0C0C0
	 * 
	 * @param string $colour A hexidecimal value to set the background colour to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_grid_colour($colour)
	{
		return $this->set(self::Y_GRID_COLOUR, $colour);
	}

	/**
	 * Set the grid colour of the X axis
	 * 
	 * @param string $colour A hexidecimal value to set the background colour to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_colour($colour)
	{
		return $this->set(self::X_COLOUR, $colour);
	}
	
	/**
	 * Set the grid colour of the Y axis
	 * 
	 * @param string $colour A hexidecimal value to set the background colour to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_colour($colour)
	{
		return $this->set(self::Y_COLOUR, $colour);
	}

	/**
	 * Set the grid colour of the right Y axis
	 * 
	 * @param string $colour A hexidecimal value to set the background colour to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_colour_right($colour)
	{
		return $this->set(self::Y_COLOUR_RIGHT, $colour);
	}

	/**
	 * Set the width of the X axis
	 * Default: 1
	 * 
	 * @param integer $stroke The width of the X axis
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_stroke($stroke)
	{
		return $this->set(self::X_STROKE, $stroke);
	}

	/**
	 * Set the width of the Y axis
	 * Default: 1
	 * 
	 * @param integer $stroke The width of the X axis
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_stroke($stroke)
	{
		return $this->set(self::Y_STROKE, $stroke);
	}

	/**
	 * Set the width of the right Y axis
	 * Default: 1
	 * 
	 * @param integer $stroke The width of the X axis
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_stroke_right($stroke)
	{
		return $this->set(self::Y_STROKE_RIGHT, $stroke);
	}

	/**
	 * Set the length of the tick marks on the X axis
	 * Default: 1
	 * 
	 * @param integer $tick_length The length of the tick marks
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_tick_length($tick_length)
	{
		return $this->set(self::X_TICK_LENGTH, $tick_length);
	}

	/**
	 * Set the length of the tick marks on the Y axis
	 * Default: 1
	 * 
	 * @param integer $tick_length The length of the tick marks
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_tick_length($tick_length)
	{
		return $this->set(self::Y_TICK_LENGTH, $tick_length);
	}

	/**
	 * Set the length of the tick marks on the right Y axis
	 * Default: 1
	 * 
	 * @param integer $tick_length The length of the tick marks
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_tick_length_right($tick_length)
	{
		return $this->set(self::Y_TICK_LENGTH_RIGHT, $tick_length);
	}

	/**
	 * Display a 'Export CSV' link at the bottom of the KReport
	 * Default: false
	 * 
	 * @param bool $toggle Whether to display the link or not. Defaults to true.
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function exportable($toggle = true)
	{
		return ($toggle === true) ? $this->set(self::EXPORTABLE, true) : $this->set(self::EXPORTABLE, false);
	}

	/**
	 * Set the width of the report
	 * 
	 * @param integer $width The width to set
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function width($width)
	{
		return $this->set(self::WIDTH, $width);
	}

	/**
	 * Set the height of the report
	 * 
	 * @param integer $height The height to set
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function height($height)
	{
		return $this->set(self::HEIGHT, $height);
	}

	/**
	 * Set an alias for the X axis name
	 * Default: X
	 * 
	 * @param string $alias The value to set the alias to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function x_alias($alias)
	{
		return $this->set(self::X_ALIAS, $alias);
	}

	/**
	 * Set an alias for the Y axis name
	 * Default: Y
	 * 
	 * @param string $alias The value to set the alias to
	 * @return KReport The KReport instance being operated on
	 * @access public
	 */
	function y_alias($alias)
	{
		return $this->set(self::Y_ALIAS, $alias);
	}

	/**
	 * This method serves two purposes.
	 * The first is as a "getter" for KReport.
	 * Calling KReport::get_CONSTNAME() will return the value of the KReport constant you replace CONSTNAME with.
	 * <code>
	 * KReport::instance()->bg_colour('#FFFFFF');
	 * KReport::instance()->get_bg_colour(); // returns '#FFFFFF'
	 * </code>
	 * 
	 * The second is as a "setter" for KReport_Chart instances stored in KReport::$chart.
	 * This method will validate whether $func is a valid function in the KReport_Chart instance and call it.
	 * The first argument to the function MUST be the chart name or this will return an exception.
	 * This method, in this context, is only useful if KReport_Chart chaining was set to false in the KReport::series() function call.
	 * <code>
	 * // Each of these methods will have the same effect of setting the width of a line chart to 2.
	 * 
	 * // returns KReport::instance()
	 * KReport::instance()->series('Line', OFC_Chart::LINE, false)
	 * ->width('Line', 2); 
	 * 
	 * // returns KReport::instance()
	 * KReport::instance()->width('Line', 2);
	 * 
	 * // returns KReport_Chart::instance()
	 * // In this case, __call() is NOT called, but this example is here for clarities sake
	 * KReport::instance()->series('Line', OFC_Chart::LINE)
	 * ->width('Line', 2);
	 * 
	 * // Returns KReport_Chart::instance()
	 * KReport::instance()->series('Line', OFC_Chart::LINE);
	 * KReport::instance()->width('Line', 2);
	 * </code>
	 * 
	 * @param string $func The function name that was called
	 * @param array $args The arguments for the function. If $func does not begin with 'get_', index 0 MUST be the chart name or this will fail.
	 * @return mixed If $func begins with 'get_', this will return a string or array depending of the value being gotten. Otherwise, this will return a KReport or KReport_Chart instance depending on how it is called. See above code example. 
	 */
	function __call($func, $args)
	{
		// get function
		if (substr($func, 0, 4) === 'get_')
		{
			$const = strtoupper(substr($func, 4));

			if (!defined('self::' . $const))
				throw new Exception ($const . ' is not a valid configuration variable for KReport');

			return (isset($this->_config[constant('self::' . $const)])) ? $this->_config[constant('self::' . $const)] : false;
		}

		if (empty($args))
			throw new Exception('Unexpected number of arguments supplied when attempting to configure a chart. Perhaps a bug exists in the KReport module?');

		if (is_array($args[0]) || is_object($args[0]))
			throw new Exception('Invalid chart name or number');

		if (!isset($this->chart[$this->_instance][$args[0]]))
			$this->series($args[0]);

		$chart_instance = $this->chart[$this->_instance][$args[0]];
		$callback = array($chart_instance, $func);

		array_shift($args);

		if (!is_callable($callback, false, $callback_name))
			throw new Exception ($callback_name . ' is not a configurable parameter for chart "' . $chart_instance->__toString() . '"');

		if (!call_user_func_array($callback, $args))
			throw new Exception ('Call to ' . $callback_name . ' failed');

		if ($this->chain_series === true)
			return $chart_instance;
		else
			return $this;
	}
}
