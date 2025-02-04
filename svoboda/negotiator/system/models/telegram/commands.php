<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\distribution,
	svoboda\negotiator\models\telegram\selections,
	svoboda\negotiator\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message,
	Zanzara\Telegram\Type\Input\InputFile as file_input;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram commands
 *
 * @package svoboda\negotiator\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class commands extends core
{
	/**
	 * Menu
	 *
	 * Responce for the commands: "/start", '/menu'
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function menu(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the title
				$title = '*' . $localization['svoboda'] . '*';

				// Sending the message
				$context->sendMessage(<<<TXT
				$title
				TXT)
					->then(function (message $message) use ($context) {
						// 
					});
			} else {
				// Not initialized localization

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize localization*')
					->then(function (message $message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Message
	 *
	 * Responce for the command: "/message"
	 *
	 * Start a process for creating message
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function message(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing the title
			$title = '';

			// Sending the message
			$context->sendMessage(<<<TXT
				*⚠️ Failed to initialize your Telegram account*
				TXT)
				->then(function (message $message) use ($context) {
					// 
				});
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Distributions
	 *
	 * Responce for the command: "/distributions"
	 * 
	 * Sends the distributions menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function distributions(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing language 
			$language = $context->get('language');

			if ($language instanceof language) {
				// Initialized language

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Initializing the message title
					$title = '🏘 *' . $localization['distributions_title'] . '*';

					// Initializing the message description
					$description = $localization['distributions_description'];

					// Initializing the distribution model
					$model = new distribution;

					// Initializing the message "registered" row
					$registered = '*' . $localization['distributions_registered'] . ':* ' . $model->database->count();

					// Sending the message
					$context->sendMessage(
						<<<TXT
						$title
						
						$registered

						$description
						TXT,
						[
							'reply_markup' => [
								'inline_keyboard' => [
									[
										[
											'text' => '📋 ' . $localization['distributions_button_register'],
											'callback_data' => 'distribution_registration_start'
										],
										[
											'text' => '🔎 ' . $localization['distributions_button_search'],
											'callback_data' => 'distribution_search_start'
										]
									]
								],
								'disable_notification' => true,
								'remove_keyboard' => true
							],
						]
					);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function (message $message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
			} else {
				// Not initialized language

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize language*')
					->then(function (message $message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Language
	 *
	 * Responce for the command: "/language"
	 * 
	 * Send the language selection menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function language(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing language 
			$language = $context->get('language');

			if ($language instanceof language) {
				// Initialized language

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the language selection
					selections::language(
						context: $context,
						prefix: 'settings_language_',
						title: '🌏 *' . $localization['settings_select_language_title'] . '*',
						description: '🌏 *' . $localization['settings_select_language_description'] . '*'
					);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function (message $message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
			} else {
				// Not initialized language

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize language*')
					->then(function (message $message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Society
	 *
	 * Responce for the command: "/society"
	 *
	 * Sends the "mushroom" image and the localized text "why so shroomious"
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function society(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Sending the message
				$context->sendPhoto(
					new file_input(STORAGE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'mushroom.jpg'),
					[
						'caption' => $localization['why_so_shroomious'],
						'disable_notification' => true
					]
				);
			} else {
				// Not initialized localization

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize localization*')
					->then(function (message $message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
