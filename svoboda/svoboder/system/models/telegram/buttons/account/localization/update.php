<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\buttons\account\localization;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\telegram\selections,
	svoboda\svoboder\models\telegram\processes\account\localization\update as process_account_localization_update;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram account localization update buttons
 *
 * @package svoboda\svoboder\models\telegram\buttons\account\localization
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class update extends core
{
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
				$context->getUserDataItem(process_account_localization_update::PROCESS)
					->then(function (?array $process) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($process) {
							// Found started account localization update process

							// Sending the message
							$context->sendMessage('📄 *' . $localization['account_localization_update_name_request'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the account localization update buffer
									$context->nextStep([process_account_localization_update::class, 'name']);
								});
						} else {
							// Not found started account localization update process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_started'] . '*');
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Sended the message

					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
