<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\buttons\distribution;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\telegram\processes\distribution\select as process_distribution_select;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution select buttons
 *
 * @package svoboda\antivertical\models\telegram\buttons\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class select extends core
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

		if ($account instanceof account) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem(process_distribution_select::PROCESS)
					->then(function ($select) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

							// Initializing title for the message
							$title = '📄 *' . $localization[process_distribution_select::PROCESS . '_name_request_title'] . '*';

							// Initializing description for the message
							$description = $localization[process_distribution_select::PROCESS . '_name_request_description'];

							// Sending the message
							$context->sendMessage(<<<TXT
								$title

								$description
								TXT)
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the distribution select buffer
									$context->nextStep([process_distribution_select::class, 'name']);
								});
						} else {
							// Not found started select process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[process_distribution_select::PROCESS . '_not_started'] . '*');
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
				$context->getUserDataItem(process_distribution_select::PROCESS)
					->then(function ($select) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

							// Initializing the message title
							$title = '🗺 *' . $localization[process_distribution_select::PROCESS . '_location_send_title'] . '*';

							// Initializing the message description
							$description = $localization[process_distribution_select::PROCESS . '_location_send_description'];

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
													'text' => '🗺 ' . $localization[process_distribution_select::PROCESS . '_button_location_send'],
													'request_location' => true
												]
											],
										],
										'disable_notification' => true
									]
								]
							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution select buffer
								$context->nextStep([process_distribution_select::class, 'location']);
							});
						} else {
							// Not found started select process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[process_distribution_select::PROCESS . '_not_started'] . '*');
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
	 * Distance 
	 *
	 * Request to send distance
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function distance(context $context)
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
				$context->getUserDataItem(process_distribution_select::PROCESS)
					->then(function ($select) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

							// Initializing the message title
							$title = '🔭 *' . $localization[process_distribution_select::PROCESS . '_distance_request_title'] . '* \(' . $localization[process_distribution_select::PROCESS . '_km'] . '\)';

							// Initializing the message description
							$description = $localization[process_distribution_select::PROCESS . '_distance_request_description'];

							// Sending the message
							$context->sendMessage(
								<<<TXT
								$title

								$description
								TXT
							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution select buffer
								$context->nextStep([process_distribution_select::class, 'distance']);
							});
						} else {
							// Not found started select process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[process_distribution_select::PROCESS . '_not_started'] . '*');
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
