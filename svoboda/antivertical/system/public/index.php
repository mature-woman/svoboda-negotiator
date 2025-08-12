<?php

declare(strict_types=1);

namespace svoboda\antivertical;

// Files of the project
use svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\localization\distribution as distribution_localization,
	svoboda\antivertical\models\telegram\middlewares,
	svoboda\antivertical\models\telegram\commands,
	svoboda\antivertical\models\telegram\buttons,
	svoboda\antivertical\models\telegram\selections,
	svoboda\antivertical\models\telegram\settings,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\telegram\processes\distribution\registration as process_distribution_registration,
	svoboda\antivertical\models\telegram\processes\distribution\search as process_distribution_search,
	svoboda\antivertical\models\telegram\buttons\distribution\registration as button_distribution_registration,
	svoboda\antivertical\models\telegram\buttons\distribution\search as button_distribution_search,
	svoboda\antivertical\models\telegram\buttons\distribution\administration as button_distribution_administration,
	svoboda\antivertical\models\telegram\processes\distribution\localization as process_distribution_localization;

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
