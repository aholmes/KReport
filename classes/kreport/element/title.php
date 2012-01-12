<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for the OFC_Elements_Title object
 */
class KReport_Element_Title extends KReport_Element
{
	/**
	 * Override KReport_Chart::__construct() and require a title
	 * 
	 * @param string $name The name of the instance
	 * @param integer $type The type of the instance
	 * @param array $config Any pre-configuration variables to assign to the instance. Must contain a 'title' index
	 */
	function __construct($name, $type, array $config = null)
	{
		if (!isset($config['title']))
			throw new Exception('Must supply a title');

		parent::__construct($name, $type, $config);
	}

	/**
	 * Instantiate a new OFC2 OFC_Elements_Title object and assign properties to it
	 * 
	 * @return KReport_Element_Title The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_element = new OFC_Elements_Title($this->_config['title']);

		parent::execute();

		return $this;
	}

	/**
	 * Return the title of the element
	 * 
	 * @return string The title of the element
	 */
	function __toString()
	{
		return $this->_config['title'];
	}
}