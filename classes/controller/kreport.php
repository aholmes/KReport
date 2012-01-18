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

		if (version_compare(Kohana::VERSION, '3.2.0') >= 0)
			$this->config = Kohana::$config->load('kreport')->get('default');
		else
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

		if (version_compare(Kohana::VERSION, '3.2.0') >= 0)
			$response = Request::initial()->response();
		else
			$response = Request::instance();

		// If Kohana can't send the file, it might be due to incompatible or missing libs, so try sending it another way
		try
		{
			$response->send_file($path, false, array(
				'inline' => true,
				'delete' => false
			));
		}
		catch(Exception $e)
		{
			if (version_compare(Kohana::VERSION, '3.2.0') >= 0)
				Log::instance()->add(Log::WARNING, 'Kohana failed to send file, trying ourselves: ' . $e->getMessage());
			else
				Kohana_Log::instance()->add('warning', 'Kohana failed to send file, trying ourselves: ' . $e->getMessage());

			// non-static zlib module
			if ($e->getCode() === 8)
			{
				header('Content-Length: ' . filesize($path));

				if (function_exists('mime_content_type'))
				{
					header('Content-Type: ' . mime_content_type($path));
				}
				else
				{
					// Log a warning because it is possible this will fail in the client browser
					if (version_compare(Kohana::VERSION, '3.2.0') >= 0)
						Log::instance()->add(Log::WARNING, 'Function "mime_content_type" is undefined. Client browser may not interpret file contents correctly.');
					else
						Kohana_Log::instance()->add('warning', 'Function "mime_content_type" is undefined. Client browser may not interpret file contents correctly.');
				}

				$handler = fopen($path, 'rb');
				fpassthru($handler);
			}
		}
	}
}
