<?php

declare(strict_types=1);

namespace svoboda\svoboder;

// Files of the project
use svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\localization\distribution as distribution_localization,
	svoboda\svoboder\models\telegram\middlewares,
	svoboda\svoboder\models\telegram\commands,
	svoboda\svoboder\models\telegram\buttons,
	svoboda\svoboder\models\telegram\selections,
	svoboda\svoboder\models\telegram\settings,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\telegram\processes\distribution\registration as process_distribution_registration,
	svoboda\svoboder\models\telegram\processes\distribution\search as process_distribution_search,
	svoboda\svoboder\models\telegram\buttons\distribution\registration as button_distribution_registration,
	svoboda\svoboder\models\telegram\buttons\distribution\search as button_distribution_search,
	svoboda\svoboder\models\telegram\buttons\distribution\administration as button_distribution_administration,
	svoboda\svoboder\models\telegram\processes\distribution\localization as process_distribution_localization;

// Framework for PHP
use mirzaev\minimal\core,
	mirzaev\minimal\route;

// Enabling debugging
/* ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

// Initializing path to the public directory 
define('INDEX', __DIR__);

// Initializing path to the root directory
define('ROOT',  INDEX . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

// Initializing path to the settings directory
define('SETTINGS', INDEX . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings');

// Initializing path to the storage directory
define('STORAGE', INDEX . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'storage');

// Initializing path to the databases directory
define('DATABASES', INDEX . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'databases');

// Initializing path to the localizations directory
define('LOCALIZATIONS', INDEX . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'localizations');

// Initiailizing telegram key
define('TELEGRAM_KEY', require(SETTINGS . DIRECTORY_SEPARATOR . 'telegram.php'));

// Initializing dependencies
require ROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Initializing core
$$core = new core(namespace: __NAMESPACE__);

// Initializing routes
$$core->router
	->write('/', new route('index', 'index'), 'GET')
	->write('/map', new route('map', 'index'), 'GET')
;

// Handling request
$$core->start();
