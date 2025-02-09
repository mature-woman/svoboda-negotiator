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
	svoboda\svoboder\models\telegram\processes\distribution\localization as process_distribution_localization;

// Framework for Telegram
use Zanzara\Zanzara as zanzara,
	Zanzara\Context as context,
	Zanzara\Config as config;

// Baza database
use mirzaev\baza\record;

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
/* $robot->onCommand('svoboda', [commands::class, 'svoboda']);
$robot->onCommand('members', [commands::class, 'members']); */
$robot->onCommand('language', [commands::class, 'language'])->middleware([middlewares::class, 'settings']);
$robot->onCommand('society', [commands::class, 'society']);
$robot->onCommand('repository', [commands::class, 'repository']);
/* $robot->onCommand('projects', [commands::class, 'projects']); */
$robot->onCommand('author', [commands::class, 'author']);

// Initializing the robot distrubitions menu handlers
$robot->onCommand('distributions', [commands::class, 'distributions']);
$robot->onCbQueryData(['distributions'], [commands::class, 'distributions']); // Remake to buttons?

// Initializing the robot distributions search button handlers
$robot->onCbQueryData(['distribution_search_start'], [process_distribution_search::class, 'start']);
$robot->onCbQueryData(['distribution_search_text'], [button_distribution_search::class, 'text']);
/* $robot->onCbQueryData(['distribution_search_next'], [process_distribution_search::class, 'text']); */
$robot->onCbQueryData(['distribution_search_end'], [process_distribution_search::class, 'end']);
$robot->onCbQueryData(['distribution_search_confirmed'], [process_distribution_search::class, 'confirmed']);
$robot->onCbQueryData(['distribution_search_location'], [button_distribution_search::class, 'location']);
$robot->onCbQueryData(['distribution_search_distance'], [button_distribution_search::class, 'distance']);

// Initializing the robot distribution search location send button handler
$robot->onCbQueryData(['distribution_search_location'], [button_distribution_search::class, 'location']);

// Initializing the robot distributions registration buttons handlers
$robot->onCbQueryData(['distribution_registration_start'], [process_distribution_registration::class, 'start']);
$robot->onCbQueryData(['distribution_registration_cancel'], [process_distribution_registration::class, 'cancel']);
$robot->onCbQueryData(['distribution_registration_end'], [process_distribution_registration::class, 'end']);

// Initializing the robot distribution localization language select button handler
$robot->onCbQueryData(['distribution_registration_language'], [button_distribution_registration::class, 'language']);

// Initializing the robot distribution registration language buttons handlers
foreach (language::cases() as $language) {
	// Iterating over languages

	// Initializing language buttons
	$robot->onCbQueryData(['distribution_registration_select_language_' . $language->name], fn(context $context) => process_distribution_registration::language($context, $language));
};

// Initializing the robot distribution registration localization name enter button handler
$robot->onCbQueryData(['distribution_registration_name'], [button_distribution_registration::class, 'name']);

// Initializing the robot distribution registration location send button handler
$robot->onCbQueryData(['distribution_registration_location'], [button_distribution_registration::class, 'location']);


// Initializing the robot distributions localization buttons handlers
/* $robot->onCbQueryData(['distribution_localization_start'], [process_distribution_localization::class, 'start']); */
/* $robot->onCbQueryData(['distribution_localization_language'], [distribution_localization::class, 'language']);
$robot->onCbQueryData(['distribution_localization_name'], [distribution_localization::class, 'name']); */

/* // Initializing the robot distribution localization language select button handler
$robot->onCbQueryData(
	['distribution_localization_language'],
	fn(context $context) => selections::language(
		context: $context,
		prefix: 'distribution_localization_language_',
		title: '🌏 *' . $localization['distribution_localization_select_language_title'] . '*',
		description: '🌏 *' . $localization['distribution_localization_select_language_description'] . '*'
	)
);

// Initializing the robot distribution localization language buttons handlers
foreach (language::cases() as $language) {
	// Iterating over languages

	// Initializing language buttons
	$robot->onCbQueryData(['distribution_localization_language_' . $language->name], fn(context $context) => settings::language($context, $language));
}; */


// Initializing the robot distributions menu buttons handlers
/* $robot->onCbQueryData(['distributions_search_start'], [process_distribution_search::class, 'start']); */

// Initializing the robot settings language buttons handlers
foreach (language::cases() as $language) {
	// Iterating over languages

	// Initializing language buttons
	$robot->onCbQueryData(['settings_language_' . $language->name], fn(context $context) => settings::language($context, $language));
};

// Initializing the robot protected commands handlers
/* $robot->onCommand('system_settings', [commands::class, 'system_settings'])->middleware([middlewares::class, 'system_settings']); */

// Starting chat-robot
$robot->run();
