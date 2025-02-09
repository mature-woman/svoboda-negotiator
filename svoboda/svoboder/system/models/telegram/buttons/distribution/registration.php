<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\buttons\distribution;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\telegram\selections,
	svoboda\svoboder\models\telegram\processes\distribution\registration as process_distribution_registration,
	svoboda\svoboder\models\localization\distribution as distribution_localization,
	svoboda\svoboder\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution registration buttons
 *
 * @package svoboda\svoboder\models\telegram\buttons\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class registration extends core
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

					// Reading from the telegram user buffer
					$context->getUserDataItem('distribution_registration')
						->then(function ($distribution) use ($context, $account, $localization) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started registration process

								// Sending the language selection
								selections::language(
									context: $context,
									prefix: 'distribution_registration_select_language_',
									title: '🌏 *' . $localization['distribution_registration_select_language_title'] . '*',
									description: '🌏 *' . $localization['distribution_registration_select_language_description'] . '*'
								);
							} else {
								// Not found started registration process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
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

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem('distribution_registration')
					->then(function ($distribution) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started registration process

							// Sending the message
							$context->sendMessage('📄 *' . $localization['distribution_registration_name_request'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the distribution registration buffer
									$context->nextStep([process_distribution_registration::class, 'name']);
								});
						} else {
							// Not found started registration process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
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

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem('distribution_registration')
					->then(function ($distribution) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started registration process

							// Initializing the message title
							$title = '🗺 *' . $localization['distribution_registration_location_send_title'] . '*';

							// Initializing the message description
							$description = $localization['distribution_registration_location_send_description'];

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
													'text' => '🗺 ' . $localization['distribution_registration_button_location_send'],
													'request_location' => true
												]
											],
										],
										'disable_notification' => true
									]
								]

							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution registration buffer
								$context->nextStep([process_distribution_registration::class, 'location']);
							});
						} else {
							// Not found started registration process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function ($message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
