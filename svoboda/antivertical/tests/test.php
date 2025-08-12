<?php

declare(strict_types=1);

namespace svoboda\svoboder\tests;

// Files of the project
use svoboda\svoboder\models\account,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\telegram;

// Enabling debugging
/* ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

// Initializing path to the public directory 
define('INDEX', __DIR__ . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'public');

// Initializing path to the root directory
define('ROOT',  INDEX . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR);

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

// Initializing system settings
require SETTINGS . DIRECTORY_SEPARATOR . 'system.php';

echo <<<TXT
###############################
########### ACCOUNT ###########
###############################
\n
TXT;

// Initializing the account model
$model = new account;

var_dump($model->database->read(
	/* filter: fn (record $record) => $record-), */
	amount: 1000000
));

echo <<<TXT
\n\n
###############################
###### TELEGRAM ACCOUNT #######
###############################
\n
TXT;

// Initializing the telegram account model
$model = new telegram;

var_dump($model->database->read(
	/* filter: fn (record $record) => $record-), */
	amount: 1000000
));

echo <<<TXT
\n\n
###############################
######## DISTRIBUTION #########
###############################
\n
TXT;

// Initializing the distribution model
$model = new distribution;

var_dump($model->database->read(
	/* filter: fn (record $record) => $record-), */
	amount: 1000000
));
