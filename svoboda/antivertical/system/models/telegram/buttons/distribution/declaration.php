<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\buttons\distribution;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\telegram\processes\language\select as process_language_select,
	svoboda\antivertical\models\telegram\processes\distribution\declaration as process_distribution_declaration,
	svoboda\antivertical\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution declaration buttons
 *
 * @package svoboda\antivertical\models\telegram\buttons\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class declaration extends core
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
					$context->getUserDataItem(process_distribution_declaration::PROCESS)
						->then(function ($distribution) use ($context, $account, $localization) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started declaration process

								// Sending the language selection
								process_language_select::menu(
									context: $context,
									prefix: 'distribution_declaration_select_language_',
									title: '🌏 *' . $localization['distribution_declaration_select_language_title'] . '*',
									description: '🌏 *' . $localization['distribution_declaration_select_language_description'] . '*'
								);
							} else {
								// Not found started declaration process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_declaration_not_started'] . '*');
							}
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function ($message) use ($context) {
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
				$context->getUserDataItem(process_distribution_declaration::PROCESS)
					->then(function ($distribution) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started declaration process

							// Sending the message
							$context->sendMessage('📄 *' . $localization['distribution_declaration_name_request'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the distribution declaration buffer
									$context->nextStep([process_distribution_declaration::class, 'name']);
								});
						} else {
							// Not found started declaration process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_declaration_not_started'] . '*');
						}
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function ($message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Location
	 *
	 * Request to send location
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function location(context $context)
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
				$context->getUserDataItem(process_distribution_declaration::PROCESS)
					->then(function ($distribution) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started declaration process

							// Initializing the message title
							$title = '🗺 *' . $localization['distribution_declaration_location_send_title'] . '*';

							// Initializing the message description
							$description = $localization['distribution_declaration_location_send_description'];

							// Sending the message
							$context->sendMessage(
								<<<TXT
								$title

								$description
								TXT,
								[
									'reply_markup' => [
										'keyboard' => [
											[
												[
													'text' => '🗺 ' . $localization['distribution_declaration_button_location_send'],
													'request_location' => true
												]
											],
										],
										'disable_notification' => true
									]
								]

							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution declaration buffer
								$context->nextStep([process_distribution_declaration::class, 'location']);
							});
						} else {
							// Not found started declaration process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_declaration_not_started'] . '*');
						}
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function ($message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
