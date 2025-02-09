<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\telegram\middlewares;

// Framework for Telegram
use Zanzara\Zanzara,
	Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message,
	Zanzara\Middleware\MiddlewareNode as node;

// Baza database
use mirzaev\baza\record;

// Built-in libraries
use Error as error;

/**
 * Telegram settings
 *
 * @package svoboda\svoboder\models\telegram
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
				
				// Initializing the account model
				$model = new account();

				// Updating the account in the database
				$updated = $model->database->read(
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
									$context->sendMessage('✅ *' . $localization['settings_language_update_success'] . '* ' . ($old->flag() ? $old->flag() . ' ' : '') . $old->label($new) . ' → *' . ($new->flag() ? $new->flag() . ' ' : '') . $new->label($new) . '*')
										->then(function (message $message) use ($context) {
											// Ending the conversation process
											$context->endConversation();
										});
								} catch (error $error) {
									// Failed to send the message about language update

									// Sending the message
									$context->sendMessage('❎ *' . $localization['settings_language_update_fail'])
										->then(function (message $message) use ($context) {
											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not initialized localization

								// Sending the message
								$context->sendMessage('⚠️ *Failed to initialize localization*')
									->then(function (message $message) use ($context) {
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
						->then(function (message $message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
