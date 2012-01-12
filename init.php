<?php defined('SYSPATH') or die('No direct access allowed.');

// The base path of the OFC2 libraries
define('OFC_BASE_PATH',   dirname(__FILE__) . '/ofc_lib/');

// The version of the KReport module
define('KREPORT_VERSION', '1.0');

// initialize OFC2
require_once(OFC_BASE_PATH . 'OFC_Chart.php');

// Set a route to the controller which handles the SWF object
Route::set('KReport', 'KReport(/<action>)')
	->defaults(array(
		'controller' => 'kreport',
		'action' => 'index'
	));
