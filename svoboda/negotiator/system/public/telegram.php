<?php

declare(strict_types=1);

namespace svoboda\negotiator;

// Files of the project
use svoboda\negotiator\models\telegram\middlewares,
	svoboda\negotiator\models\telegram\commands,
	svoboda\negotiator\models\telegram\buttons,
	svoboda\negotiator\models\telegram\settings,
	svoboda\negotiator\models\enumerations\language;

// Framework for Telegram
use Zanzara\Zanzara as zanzara,
	Zanzara\Context as context,
	Zanzara\Config as config;

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

// Initializing the configuration
$config = new config();
$config->setParseMode(config::PARSE_MODE_MARKDOWN);
$config->useReactFileSystem(true);

// Initializing the robot
$robot = new Zanzara(TELEGRAM_KEY, $config);

// Initializing the updates listener
$robot->onUpdate(function (Context $context): void {});

// Initializing the robot middlewares
$robot->middleware([middlewares::class, 'account']);
$robot->middleware([middlewares::class, 'language']);
$robot->middleware([middlewares::class, 'localization']);
$robot->middleware([middlewares::class, 'system']);

// Initializing the robot commands handlers
$robot->onCommand('start', [commands::class, 'menu']);
$robot->onCommand('menu', [commands::class, 'menu']);
/* $robot->onCommand('request', [telegram::class, 'request_start'])->middleware([telegram::class, 'request']); */
$robot->onCommand('language', [commands::class, 'language'])->middleware([middlewares::class, 'settings']);
$robot->onCommand('society', [commands::class, 'society']);

// Initializing the robot protected commands handlers
/* $robot->onCommand('system_settings', [commands::class, 'system_settings'])->middleware([middlewares::class, 'system_settings']); */

// Initializing the robot buttons handlers
foreach (language::cases() as $language) {
	// Iterating over languages

	// Initializing language buttons
	$robot->onCbQueryData(['settings_language_' . $language->name], fn(context $context) => settings::language($context, $language));
};


// Starting chat-robot
$robot->run();
