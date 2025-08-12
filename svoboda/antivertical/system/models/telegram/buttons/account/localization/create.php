<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\buttons\account\localization;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\telegram\selections,
	svoboda\antivertical\models\telegram\processes\account\localization\create as process_account_localization_create;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram account localization create buttons
 *
 * @package svoboda\antivertical\models\telegram\buttons\account\localization
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class create extends core
{
	/**
	 * Language
	 *
	 * Send the language selection menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function language(context $context)
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof account) {
			// Initialized the account

			// Initializing language 
			$language = $context->get('language');

			if ($language) {
				// Initialized language

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Reading from the telegram user buffer
					$context->getUserDataItem(process_account_localization_create::PROCESS)
						->then(function (?array $process) use ($context, $account, $localization) {
							// Readed from the telegram user buffer

							if ($process) {
								// Found started account localization create process

								// Initializing the account model
								$model_account = new account;

								// Initializing the account localizations
								$existed = $model_account->localization->database->read(
									filter: fn(record $localization) => $localization->account === $account->identifier,
									amount: ACCOUNT_LOCALIZATION_CREATE_ACCOUNT_LOCALIZATIONS_AMOUNT
								);

								// Declaring the buffer of languages to exclude
								$exclude = [];

								// Initializing languages to exclude
								foreach ($existed as $record) $exclude[] = $record->language;

								if (count($exclude) !== count(language::cases())) {
									// Not all languages in the registry have localizations created (expected)

									// Sending the language selection
									selections::language(
										context: $context,
										prefix: 'account_localization_create_select_language_',
										title: '🌏 *' . $localization['account_localization_create_select_language_title'] . '*',
										description: '🌏 *' . $localization['account_localization_create_select_language_description'] . '*',
										exclude: $exclude
									);
								} else {
									// All languages in the registry have localizations created (expected)

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['account_localization_create_every_language_created'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not found started account localization create process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['account_localization_create_not_started'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();
									});
							}
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
				// Not initialized language

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize language*')
					->then(function (message $message) use ($context) {
						// Sended the message

						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function (message $message) use ($context) {
					// Sended the message

					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Name
	 *
	 * Request to enter name
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function name(context $context)
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof account) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem(process_account_localization_create::PROCESS)
					->then(function (?array $process) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($process) {
							// Found started account localization create process

							// Sending the message
							$context->sendMessage('📄 *' . $localization['account_localization_create_name_request'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the account localization create buffer
									$context->nextStep([process_account_localization_create::class, 'name']);
								});
						} else {
							// Not found started account localization create process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['account_localization_create_not_started'] . '*');
						}
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function (message $message) use ($context) {
					// Sended the message

					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
