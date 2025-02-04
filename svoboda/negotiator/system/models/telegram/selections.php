<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram selections
 *
 * @package svoboda\negotiator\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class selections extends core
{
	/**
	 * Language
	 *
	 * The language choose menu
	 *
	 * @param context $context Request data from Telegram
	 * @param string $prefix Prefix for 'callback_data' (`$prefix . $language->name`)
	 * @param string $title Title of the message
	 * @param string $description Description of the message
	 *
	 * @return void
	 */
	public static function language(context $context, string $prefix, string $title, string $description): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing language 
			$language = $context->get('language');

			if ($language) {
				// Initialized language

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Declaring the buffer of generated keyboard with languages
					$keyboard = [];

					// Initializing the iterator of rows
					$row = 0;

					// Initializing buffer of languages
					$languages = language::cases();

					// Deleting the actual language from buffer of languages
					unset($languages[array_search($language, $languages, strict: true)]);

					// Sorting buffer of languages by the actual language
					$languages = [$language, ...$languages];

					foreach ($languages as $language) {
						// Iterating over languages

						// Initializing the row
						$keyboard[$row] ??= [];

						// Writing the language choose button into the buffer of generated keyboard with languages
						$keyboard[$row][] = [
							'text' => ($language->flag() ? $language->flag() . ' ' : '') . $language->label($language),
							'callback_data' => $prefix . $language->name
						];

						// When reaching 4 buttons in a row, move to the next row
						if (count($keyboard[$row]) === 4) ++$row;
					}

					// Writing the button for helping lozalizing
					$keyboard[++$row] = [
						[
							'text' => '🗂 ' . $localization['select_language_button_add'],
							'url' => 'https://git.svoboda.works/svoboda/negotiator/src/branch/stable/svoboda/negotiator/system/localizations'
						]
					];

					// Sending the message
					$context->sendMessage(
						$title ?? '🌏 *' . $localization['select_language_title'] . "*\n" . ($description ?? $localization['select_language_description']),
						[
							'reply_markup' => [
								'inline_keyboard' => $keyboard,
								'disable_notification' => true
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
}
