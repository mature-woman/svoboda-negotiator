<?php

declare(strict_types=1);

namespace svoboda\antivertical;

// Files of the project
use svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\localization\distribution as distribution_localization,
	svoboda\antivertical\models\telegram\middlewares,
	svoboda\antivertical\models\telegram\commands,
	svoboda\antivertical\models\telegram\buttons,
	svoboda\antivertical\models\telegram\account,
	svoboda\antivertical\models\telegram\settings,
	svoboda\antivertical\models\enumerations\membership\status,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\telegram\processes\membership\search as process_membership_search,
	svoboda\antivertical\models\telegram\buttons\membership\search as button_membership_search,
	svoboda\antivertical\models\telegram\processes\distribution\declaration as process_distribution_declaration,
	svoboda\antivertical\models\telegram\processes\distribution\search as process_distribution_search,
	svoboda\antivertical\models\telegram\processes\distribution\select as process_distribution_select,
	svoboda\antivertical\models\telegram\buttons\distribution\declaration as button_distribution_declaration,
	svoboda\antivertical\models\telegram\buttons\distribution\search as button_distribution_search,
	svoboda\antivertical\models\telegram\buttons\distribution\select as button_distribution_select,
	svoboda\antivertical\models\telegram\processes\account\localization\create as process_account_localization_create,
	svoboda\antivertical\models\telegram\processes\account\localization\update as process_account_localization_update,
	svoboda\antivertical\models\telegram\buttons\account\localization\create as button_account_localization_create,
	svoboda\antivertical\models\telegram\buttons\account\localization\update as button_account_localization_update,
	svoboda\antivertical\models\telegram\buttons\distribution\administration as button_distribution_administration,
	svoboda\antivertical\models\telegram\processes\distribution\localization as process_distribution_localization;

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

// Initializing system settings
require SETTINGS . DIRECTORY_SEPARATOR . 'system.php';

// Initializing the configuration
$config = new config();
$config->setParseMode(config::PARSE_MODE_MARKDOWN);
$config->useReactFileSystem(true);

// Initializing the robot
$robot = new Zanzara(TELEGRAM_KEY, $config);

// Initializing the updates listener
$robot->onUpdate(function (Context $context): void {});

// Initializing the robot middlewares
$robot->middleware([middlewares::class, 'telegram']);
$robot->middleware([middlewares::class, 'account']);
$robot->middleware([middlewares::class, 'language']);
$robot->middleware([middlewares::class, 'localization']);
$robot->middleware([middlewares::class, 'system']);

// Initializing the robot commands handlers
$robot->onCommand('start', [commands::class, 'menu']);
$robot->onCommand('menu', [commands::class, 'menu']);
$robot->onCommand('memberships', [commands::class, 'memberships']);
$robot->onCommand('account', [commands::class, 'account']);
$robot->onCommand('distributions', [commands::class, 'distributions']);
/* $robot->onCommand('request', [telegram::class, 'request_start'])->middleware([telegram::class, 'request']); */
/* $robot->onCommand('svoboda', [commands::class, 'svoboda']);
$robot->onCommand('memberships', [commands::class, 'memberships']); */
$robot->onCommand('language', [commands::class, 'language'])->middleware([middlewares::class, 'settings']);
$robot->onCommand('repository', [commands::class, 'repository']);
/* $robot->onCommand('projects', [commands::class, 'projects']); */
$robot->onCommand('author', [commands::class, 'author']);
$robot->onCommand('society', [commands::class, 'society']);
$robot->onCommand('organisation', [commands::class, 'organisation']);
/* $robot->onCommand('system_settings', [commands::class, 'system_settings'])->middleware([middlewares::class, 'system_settings']); */

// Initializing the robot buttons handlers
$robot->onCbQueryData(['distributions'], [commands::class, 'distributions']); // Remake to buttons?
$robot->onCbQueryData(['distribution_search_start'], [process_distribution_search::class, 'start']);
$robot->onCbQueryData(['distribution_search_name'], [button_distribution_search::class, 'name']);
/* $robot->onCbQueryData(['distribution_search_next'], [process_distribution_search::class, 'text']); */
$robot->onCbQueryData(['distribution_search_end'], [process_distribution_search::class, 'end']);
$robot->onCbQueryData(['distribution_search_location'], [button_distribution_search::class, 'location']);
$robot->onCbQueryData(['distribution_search_distance'], [button_distribution_search::class, 'distance']);
$robot->onCbQueryData(['distribution_search_plan'], [button_distribution_search::class, 'plan'])->middleware([middlewares::class, 'memberships']);
$robot->onCbQueryData(['distribution_search_unplan'], [button_distribution_search::class, 'unplan'])->middleware([middlewares::class, 'memberships']);
$robot->onCbQueryData(['distribution_search_join'], [button_distribution_search::class, 'join'])->middleware([middlewares::class, 'memberships']);
$robot->onCbQueryData(['distribution_search_leave'], [button_distribution_search::class, 'leave'])->middleware([middlewares::class, 'memberships']);
$robot->onCbQueryData(['distribution_search_location'], [button_distribution_search::class, 'location']);
$robot->onCbQueryData(['distribution_search_memberships'], [process_membership_search::class, 'start']);

$robot->onCbQueryData(['distribution_select_name'], [button_distribution_select::class, 'name']);
$robot->onCbQueryData(['distribution_select_location'], [button_distribution_select::class, 'location']);
$robot->onCbQueryData(['distribution_select_distance'], [button_distribution_select::class, 'distance']);
/* $robot->onCbQueryData(['distribution_select_next'], [process_distribution_select::class, 'next']); */
$robot->onCbQueryData(['distribution_select_select'], [process_distribution_select::class, 'select']);
$robot->onCbQueryData(['distribution_select_delete'], [process_distribution_select::class, 'delete']);
$robot->onCbQueryData(['distribution_select_cancel'], [process_distribution_select::class, 'cancel']);

$robot->onCbQueryData(['distribution_declaration_start'], [process_distribution_declaration::class, 'start']);
$robot->onCbQueryData(['distribution_declaration_cancel'], [process_distribution_declaration::class, 'cancel']);
$robot->onCbQueryData(['distribution_declaration_end'], [process_distribution_declaration::class, 'end']);
$robot->onCbQueryData(['distribution_declaration_language'], [button_distribution_declaration::class, 'language']);
foreach (language::cases() as $language)
	$robot->onCbQueryData(['distribution_declaration_select_language_' . $language->name], fn(context $context) => process_distribution_declaration::language($context, $language));
$robot->onCbQueryData(['distribution_declaration_name'], [button_distribution_declaration::class, 'name']);
$robot->onCbQueryData(['distribution_declaration_location'], [button_distribution_declaration::class, 'location']);
/* $robot->onCbQueryData(['distribution_accept_toggle'], [button_distribution_administration::class, 'accept'])->middleware([middlewares::class, 'distributions_administration']); */
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

$robot->onCbQueryData(['memberships'], [commands::class, 'memberships']); // Remake to buttons?
$robot->onCbQueryData(['membership_search_start'], [process_membership_search::class, 'start']);
$robot->onCbQueryData(['membership_search_name'], [button_membership_search::class, 'name']);
$robot->onCbQueryData(['membership_search_distribution'], [button_membership_search::class, 'distribution']);
foreach (status::cases() as $status)
	$robot->onCbQueryData(['membership_search_status_' . $status->name], fn(context $context) => process_membership_search::status($context, $status));
$robot->onCbQueryData(['membership_search_status'], [button_membership_search::class, 'status']);
$robot->onCbQueryData(['membership_search_end'], [process_membership_search::class, 'end']);

$robot->onCbQueryData(['account_localizations'], [account::class, 'localizations']);
$robot->onCbQueryData(['account_localization_create_start'], [process_account_localization_create::class, 'start']);
$robot->onCbQueryData(['account_localization_create_cancel'], [process_account_localization_create::class, 'cancel']);
$robot->onCbQueryData(['account_localization_create_end'], [process_account_localization_create::class, 'end']);
$robot->onCbQueryData(['account_localization_create_language'], [button_account_localization_create::class, 'language']);
$robot->onCbQueryData(['account_localization_update_cancel'], [process_account_localization_update::class, 'cancel']);
$robot->onCbQueryData(['account_localization_update_end'], [process_account_localization_update::class, 'end']);
foreach (language::cases() as $language) {
	$robot->onCbQueryData(['account_localization_create_select_language_' . $language->name], fn(context $context) => process_account_localization_create::language($context, $language));
	$robot->onCbQueryData(['account_localization_update_' . $language->name], fn(context $context) => process_account_localization_update::start($context, $language));
}
$robot->onCbQueryData(['account_localization_create_name'], [button_account_localization_create::class, 'name']);
$robot->onCbQueryData(['account_localization_update_name'], [button_account_localization_update::class, 'name']);
/* $robot->onCbQueryData(['account_localizations_search'], [process_account_localization_search::class, 'start']); */


$robot->onCbQueryData(['organisation'], [commands::class, 'organisation']); // Remake to buttons?

// Initializing the robot settings language buttons handlers
foreach (language::cases() as $language) {
	// Iterating over languages

	// Initializing language buttons
	$robot->onCbQueryData(['settings_language_' . $language->name], fn(context $context) => settings::language($context, $language));
};

// Starting chat-robot
$robot->run();
