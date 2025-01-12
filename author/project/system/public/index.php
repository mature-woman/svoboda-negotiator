<?php

declare(strict_types=1);

namespace svoboda\negotiatior;

// Framework for PHP
use mirzaev\minimal\core,
	mirzaev\minimal\route;

// Enabling debugging
/* ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

// Initializing path to the public directory 
define('INDEX', __DIR__);

// Initializing path to the project root directory
define('ROOT',  INDEX . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

// Initializing path to the directory of views 
define('VIEWS', realpath('..' . DIRECTORY_SEPARATOR . 'views'));

// Initializing path to the directory of settings 
define('SETTINGS', realpath('..' . DIRECTORY_SEPARATOR . 'settings'));

// Initializing path to the directory of the storage 
define('STORAGE', realpath('..' . DIRECTORY_SEPARATOR . 'storage'));

// Initializing default theme for the views templater
define('THEME', 'default');

// Initializing dependencies
require ROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Initializing core
$core = new core(namespace: __NAMESPACE__);

// Initializing routes
$core->router
	->write('/', new route('index', 'index'), 'GET')
;

// Handling request
$core->start();
