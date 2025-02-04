<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram\processes\distribution;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\account,
	svoboda\negotiator\models\distribution,
	svoboda\negotiator\models\localization\distribution as distribution_localization,
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
 * Distribution search process
 *
 * @package svoboda\negotiator\models\telegram\processes\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class search extends core
{
	/**
	 * Registry
	 *
	 * Registrate the distribution
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function registry(context $context): void
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

					// Declaring the buffer of generated keyboard with languages
					$keyboard = [];

					// Initializing the iterator of rows
					$row = 0;

					// Initializing the distribution model
					$model = new distribution;

					// Initializing the distribution localization model
					$model_localization = new distribution_localization;

					foreach ($model->database->read(amount: PHP_INT_MAX) as $distribution) {
						// Iterating over distributions

						// Initializing the distribution localized values
						$localized = $model_localization->database->read(filter: fn($record) => $record->identifier === $distribution->identifier && $record->language === $language->name, amount: 1)[0] ?? null;

						// Initializing the row
						$keyboard[$row] ??= [];

						// Writing the language choose button into the buffer of generated keyboard with languages
						$keyboard[$row][] = [
							'text' => $localized->name,
							'callback_data' => 'distributions_open_' . $distribution->identifier
						];

						// When reaching 4 buttons in a row, move to the next row
						if (count($keyboard[$row]) === 3) ++$row;
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
				// Not initialized language

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize language*')
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
