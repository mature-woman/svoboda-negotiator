<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\account,
	svoboda\negotiator\models\enumerations\language;

// Framework for Telegram
use Zanzara\Zanzara,
	Zanzara\Context as context,
	Zanzara\Telegram\Type\Input\InputFile as file_input,
	Zanzara\Telegram\Type\File\Document as document,
	Zanzara\Middleware\MiddlewareNode as node,
	Zanzara\Telegram\Type\User as user;

// Baza database
use mirzaev\baza\record;

// Built-in libraries
use Exception as exception,
	Error as error;

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
					->then(function ($message) use ($context) {
						// 
					});
			} else {
				// Not initialized localization

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize localization*')
					->then(function ($message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function ($message) use ($context) {
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
				->then(function ($message) use ($context) {
					// 
				});
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function ($message) use ($context) {
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
	 * Sends a language selection menu
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

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Declaring the buffer of generated keyboard with languages
				$keyboard = [];

				// Initializing the iterator of rows
				$row = 0;

				foreach (language::cases() as $language) {
					// Iterating over languages

					// Initializing the row
					$keyboard[$row] ??= [];

					// Writing the language choose button into the buffer of generated keyboard with languages
					$keyboard[$row][] = [
						'text' => ($language->flag() ? $language->flag() . ' ' : '') . $language->label($language),
						'callback_data' => 'settings_language_' . $language->name
					];

					// When reaching 4 buttons in a row, move to the next row
					if (count($keyboard[$row]) === 4) ++$row;
				}

				// Writing the button for helping lozalizing
				$keyboard[++$row] = [
					[
						'text' => '🗂 ' . $localization['settings_language_add'],
						'url' => 'https://git.svoboda.works/svoboda/negotiator/src/branch/stable/svoboda/negotiator/system/localizations'
					]
				];

				// Sending the message
				$context->sendMessage(
					'🌏 *' . $localization['settings_language_title'] . '*',
					[
						'reply_markup' => [
							'inline_keyboard' =>  $keyboard
						],
						'disable_notification' => true
					]
				);
			} else {
				// Not initialized localization

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize localization*')
					->then(function ($message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function ($message) use ($context) {
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
					->then(function ($message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function ($message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
