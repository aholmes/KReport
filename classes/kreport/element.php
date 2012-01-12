<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods to instantiate OFC2 elements
 */
class KReport_Element
{
	// Element types
	const X_AXIS       = 30001;
	const Y_AXIS       = 30002;
	const Y_AXIS_RIGHT = 30003;
	const TITLE        = 30004;
	const TAGS         = 30005;

	/**
	 * @var string The name of the default instance
	 * @access public
	 * @static
	 */
	public static $default = 'default';

	/**
	 * @var array The instantiated KReport_Element classes
	 * @access public
	 * @static
	 */
	public static $instances = array();

	/**
	 * @var string The name of the instance
	 * @access protected
	 */
	protected $_instance;

	/**
	 * @var array The initial configuration of the instance 
	 * @access protected
	 */
	protected $_config;

	/**
	 * @var OFC_Elements The instantiated OFC2 element object
	 * @access protected
	 */
	protected $ofc_element;

	/**
	 * Return a new or already created KReport_Element instance
	 * 
	 * @param string $name The name of the instance. Defaults to KReport_Element::$default
	 * @param integer $type The type of the element
	 * @param array $config Any pre-configuration variables to assign to the instance
	 * @return KReport_Element the new or previously instantiated KReport_Element instance
	 * @access public
	 * @static
	 */
	public static function instance($name, $type, array $config = null)
	{
		if (!isset(self::$instances[$name]))
		{
			self::$instances[$name] = array();
		}

		if (!isset(self::$instances[$name][$type]))
		{
			if ($name === NULL)
			{
				$name = self::$default;
			}

			new KReport_Element($name, $type, $config);
		}

		return self::$instances[$name][$type];
	}

	/**
	 * Set KReport_Element::$instances[$name][$type] to the current KReport_Element instance
	 * 
	 * @param string $name The name of the instance
	 * @param integer $type The type of the instance
	 * @param array $config Any pre-configuration variables to assign to the instance
	 */
	function __construct($name, $type, array $config = null)
	{
		$this->_instance = $name;
		$this->_config   = $config;

		if (get_class($this) === __CLASS__)
		{
			switch($type)
			{
				case self::X_AXIS:
					self::$instances[$name][$type] = new KReport_Element_Axis_X($name, $type, $config);
				break;
				case self::Y_AXIS:
					self::$instances[$name][$type] = new KReport_Element_Axis_Y($name, $type, $config);
				break;
				case self::Y_AXIS_RIGHT:
					self::$instances[$name][$type] = new KReport_Element_Axis_YRight($name, $type, $config);
				break;
				case self::TITLE:
					self::$instances[$name][$type] = new KReport_Element_Title($name, $type, $config);
				break;
				case self::TAGS:
					self::$instances[$name][$type] = new OFC_Element_Tags($name, $type, $config);
				break;
				default:
					throw new Exception('Cannot instantiate new OFC Element of type "' . $type . '" in ' . __CLASS__);
			}

			// mark ourselves for unsetting because we are returning a child and not ourselves
			unset($this);

			return self::$instances[$name][$type];
		}
	}

	function __destruct()
	{
	}

	/**
	 * Get the element name
	 * 
	 * @return string The element name
	 */
	function __toString()
	{
		return $this->_instance;
	}

	/**
	 * Do any execution that needs to happen for each of the KReport_Element types. Currently only returns itself
	 * 
	 * @return KReport_Element the new or previously instantiated KReport_Element instance
	 * @access public
	 */
	function execute()
	{
		return $this;
	}

	/**
	 * Get the OFC_Element object
	 * 
	 * @return type OFC_Element The OFC2 element object
	 * @access public
	 */
	function get()
	{
		return $this->ofc_element;
	}
}
