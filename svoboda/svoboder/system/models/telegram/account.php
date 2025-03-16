<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account as model,
	svoboda\svoboder\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

// Built-in libraries
use Error as error;

/**
 * Telegram account
 *
 * @package svoboda\svoboder\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class account extends core
{
	/**
	 * Localizations
	 *
	 * Sends a message with the localizations menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function localizations(context $context): void
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
				$title = '🗺 *' . $localization['account_localization_title'] . '*';

				// Initializing the account model
				$model_account = new model;

				// Initializing existed account localizations
				$existed = $model_account->localization->database->read(
					filter: fn(record $localization) => $localization->account === $account->identifier,
					amount: ACCOUNT_LOCALIZATION_ACCOUNT_LOCALIZATIONS_AMOUNT
				);

				// Initializing localizations amount
				$amount = '*' . $localization['account_localization_amount'] . ':* ' . count($existed);

				// Declaring the buffer of generated keyboard with languages
				$keyboard = [];

				// Initializing the iterator of rows
				$row = 0;

				foreach ($existed as $record) {
					// Iterating over existed account localizations

					try {
						// Initializing the localization language
						$language = language::{$record->language};

						// Initializing the row
						$keyboard[$row] ??= [];

						// Writing the language choose button into the buffer of generated keyboard with languages
						$keyboard[$row][] = [
							'text' => ($language->flag() ? $language->flag() . ' ' : '') . $language->label($language),
							'callback_data' => "account_localization_update_$language->name"
						];

						// When reaching 4 buttons in a row, move to the next row
						if (count($keyboard[$row]) === 4) ++$row;
					} catch (error $error) {
						// Sending the message
						$context->sendMessage('⚠️ *' . $localization['account_localization_create_failted_to_initialize_language'] . '*')
							->then(function (message $message) use ($context) {
								// Sended the message

								// Ending the conversation process
								$context->endConversation();
							});
					}
				}

				if (count($existed) !== count(language::cases())) {
					// Not all languages in the registry have localizations created (expected)

					// Writing the button for helping lozalizing
					$keyboard[$row === 0 && empty($keyboard[0]) ? 0 : ++$row] = [
						[
							'text' => '✏️ ' . $localization['account_localization_create'],
							'callback_data' => 'account_localization_create_start'
						]
					];
				}

				// Sending the message
				$context->sendMessage(
					<<<TXT
					$title

					$amount
					TXT,
					[
						'reply_markup' => [
							'inline_keyboard' => $keyboard,
							'disable_notification' => true,
							'remove_keyboard' => true
						],
					]
				)->then(function (message $message) use ($context) {
					// Sended the message
				});
			} else {
				// Not initialized localization

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize localization*')
					->then(function (message $message) use ($context) {
						// Sended the message

						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Sended the message

					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
