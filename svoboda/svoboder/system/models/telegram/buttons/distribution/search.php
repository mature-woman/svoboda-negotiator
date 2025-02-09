<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\buttons\distribution;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\telegram\selections,
	svoboda\svoboder\models\telegram\processes\distribution\search as process_distribution_search,
	svoboda\svoboder\models\localization\distribution as distribution_localization,
	svoboda\svoboder\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution search buttons
 *
 * @package svoboda\svoboder\models\telegram\buttons\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class search extends core
{
	/**
	 * Text
	 *
	 * Request to enter search text
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function text(context $context)
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
				$context->getUserDataItem('distribution_search')
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing title for the message
							$title = '📄 *' . $localization['distribution_search_text_request_title'] . '*';

							// Initializing description for the message
							$description = $localization['distribution_search_text_request_description'];

							// Sending the message
							$context->sendMessage(<<<TXT
								$title

								$description
								TXT)
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the distribution search buffer
									$context->nextStep([process_distribution_search::class, 'text']);
								});
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*');
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
				$context->getUserDataItem('distribution_search')
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the message title
							$title = '🗺 *' . $localization['distribution_search_location_send_title'] . '*';

							// Initializing the message description
							$description = $localization['distribution_search_location_send_description'];

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
													'text' => '🗺 ' . $localization['distribution_search_button_location_send'],
													'request_location' => true
												]
											],
										],
										'disable_notification' => true
									]
								]
							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution search buffer
								$context->nextStep([process_distribution_search::class, 'location']);
							});
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*');
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

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem('distribution_search')
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the message title
							$title = '🔭 *' . $localization['distribution_search_distance_request_title'] . '* \(' . $localization['distribution_search_km'] . '\)';

							// Initializing the message description
							$description = $localization['distribution_search_distance_request_description'];

							// Sending the message
							$context->sendMessage(
								<<<TXT
								$title

								$description
								TXT
							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution search buffer
								$context->nextStep([process_distribution_search::class, 'distance']);
							});
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*');
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
