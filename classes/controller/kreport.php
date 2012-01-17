<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Handle requests for site/KReport
 */
class Controller_KReport extends Controller
{
	/**
	 * @var array The loaded KReport configuration file
	 * @access private
	 */
	private $config;

	/**
	 * Override parent. Retrieve the default configuration file
	 */
	function before()
	{
		parent::before();

		$this->config = (object)Kohana_Config::instance()->load('kreport')->get('default');
	}

	/**
	 * Link back to the github project if allowed, otherwise display the version if allowed
	 * @access public
	 */
	function action_index()
	{
		if ($this->config->allow_linkback === true)
			die(header('Location: https://github.com/aholmes/KReport'));

		$this->action_version();

		exit;
	}

	/**
	 * Display the version if allowed
	 * @access public
	 */
	function action_version()
	{
		if ($this->config->show_version === true)
			echo 'Kohana KReport module version ' . KREPORT_VERSION;

		exit;
	}

	/**
	 * Send the swfobject javascript to the browser and the default open_flash_chart_data() function
	 * @access public
	 */
	function action_js()
	{
		$path = MODPATH . 'KReport/ofc_lib/swfobject.js';

		echo " function open_flash_chart_data(){return JSON.stringify(kreport_kohana_data);} ";

		$this->send_file($path);
	}

	/**
	 * Send the SWF object to the browser
	 * @access public
	 */
	function action_swf()
	{
		$path = MODPATH . 'KReport/ofc_lib/open-flash-chart.swf'; 

		$this->send_file($path);
	}

	/**
	 * Send the binary file
	 * 
	 * @param string $path The path to the file to send
	 * @access private
	 */
	private function send_file($path)
	{
		if (!file_exists($path) || !filesize($path))
			throw new Exception('File does not exist in ' . $path);

		// zlib compressiong breaks sending binary files
		ini_set('zlib.output_compression', false);

		// don't let any output buffering mess with us
		if (ob_get_level())
			ob_clean();

		Request::instance()->send_file($path, false, array(
			'inline' => true,
			'delete' => false
		));

		throw new Exception('Failed to send file ' . $path);
	}
}
