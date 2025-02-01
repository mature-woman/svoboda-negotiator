<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\account,
	svoboda\negotiator\models\enumerations\language,
	svoboda\negotiator\models\telegram\middlewares;

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
 * Telegram settings
 *
 * @package svoboda\negotiator\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class settings extends core
{
	/**
	 * Language
	 *
	 * Write language into the account record
	 *
	 * @param context $context Request data from Telegram
	 * @param language $language The language
	 *
	 * @return void
	 */
	public static function language(context $context, language $language): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Updating the account in the database
				$updated = account::$database->read(
					filter: fn(record $record) => $record->identifier === $account->identifier,
					update: function (record &$record) use ($language) {
						// Writing new language value into the record
						$record->language = $language->name;
					},
					amount: 1
				)[0] ?? null;

				if ($updated instanceof record) {
					// Updated the account in the database

					// Writing the updated account into the context variable
					$context->set('account', $updated);

					middlewares::language($context, new node(function (context $context) use ($account, $updated) {
						// Updated language

						middlewares::localization($context, new node(function (context $context) use ($account, $updated) {
							// Updated localization

							// Initializing localization 
							$localization = $context->get('localization');

							if ($localization) {
								// Initialized localization
								try {
									// Initializing the old language
									$old = language::{$account->language};

									// Initializing the new language
									$new = language::{$updated->language};

									// Sending the message
									$context->sendMessage('✅ *' . $localization['settings_language_update_success'] . '* ' . ($old->flag() ? $old->flag() . ' ' : '') . $old->label($new) . ' » ' . ($new->flag() ? $new->flag() . ' ' : '') . $new->label($new))
										->then(function ($message) use ($context) {
											// Ending the conversation process
											$context->endConversation();
										});
								} catch (error $error) {
									// Failed to send the message about language update

									// Sending the message
									$context->sendMessage('❎ *' . $localization['settings_language_update_fail'])
										->then(function ($message) use ($context) {
											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not initialized localization

								// Sending the message
								$context->sendMessage('⚠️ *Failed to initialize localization*')
									->then(function ($message) use ($context) {
										// Ending the conversation process
										$context->endConversation();
									});
							}
						}));
					}));
				} else {
					// Not updated the account in the database

					// Sending the message
					$context->sendMessage('❎ *' . $localization['settings_language_update_fail'])
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
