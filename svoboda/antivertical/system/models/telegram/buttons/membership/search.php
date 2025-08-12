<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\buttons\membership;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\telegram\processes\distribution\select as process_distribution_select,
	svoboda\antivertical\models\telegram\processes\membership\search as process_membership_search,
	svoboda\antivertical\models\enumerations\membership\status,
	svoboda\antivertical\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message,
	Zanzara\Telegram\Type\Keyboard\InlineKeyboardMarkup as keyboard_inline,
	Zanzara\Telegram\Type\Keyboard\InlineKeyboardButton as keyboard_inline_button;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram membership search buttons
 *
 * @package svoboda\antivertical\models\telegram\buttons\membership
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class search extends core
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
				$context->getUserDataItem(process_membership_search::PROCESS)
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing title for the message
							$title = '📄 *' . $localization[process_membership_search::PROCESS . '_name_request_title'] . '*';

							// Initializing description for the message
							$description = $localization[process_membership_search::PROCESS . '_name_request_description'];

							// Sending the message
							$context->sendMessage(<<<TXT
								$title

								$description
								TXT)
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the membership search buffer
									$context->nextStep([process_membership_search::class, 'name']);
								});
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[process_membership_search::PROCESS . '_not_started'] . '*');
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
	 * Distribution
	 *
	 * Request to choose distribution
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function distribution(context $context)
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
				$context->getUserDataItem(process_membership_search::PROCESS)
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Starting the distribution selection process
							process_distribution_select::start(
								context: $context,
								select: function (context $context, array $distribution) use ($search) {

									if (!empty($distribution['distribution']) && !empty($distribution['localization'])) {
										// Initialized the distribution

										// Writing the distribution into the process buffer
										$search['distribution'] = $distribution;

										// Writing to the telegram user buffer
										$context->setUserDataItem(process_membership_search::PROCESS, $search)
											->then(function () use ($context) {
												// Writed into the telegram user buffer

												// Sending the list of found distributions and menu
												process_membership_search::menu($context);
											});
									} else {
										// Not initialized the distribution
									}
								},
								delete: function (context $context) use ($search) {
									// Deleting the distribution from the process buffer
									$search['distribution'] = [
										'distribution' => null,
										'localization' => null
									];

									// Writing to the telegram user buffer
									$context->setUserDataItem(process_membership_search::PROCESS, $search)
										->then(function () use ($context) {
											// Writed into the telegram user buffer

											// Sending the list of found distributions and menu
											process_membership_search::menu($context);
										});
								},
								cancel: function (context $context) {
									// Sending the list of found distributions and menu
									process_membership_search::menu($context);
								},
								description: $localization[process_membership_search::PROCESS . '_distribution_selection_description']
							);
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[process_membership_search::PROCESS . '_not_started'] . '*');
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
	 * Status
	 *
	 * Request to choose status
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function status(context $context)
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
				$context->getUserDataItem(process_membership_search::PROCESS)
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Declaring the keyboard buffer
							$keyboard = [[]];

							// Initializing the amount of buttons in one row
							$amount = MEMBERSHIPS_SEARCH_STATUS_ROW_AMOUNT ?? 8;

							// Initializing the rows iterator
							$row = 0;

							foreach (status::cases() as $status) {
								// Iterating over statuses

								// Skipping the unknown status
								if ($status === status::unknown) continue;

								// Moving to the next row
								if (count($keyboard[$row]) >= $amount) ++$row;
								
								// Initializing the row
								$keyboard[$row] ??= [];

								// Generating the button and writing into the keyboard buffer
								$keyboard[$row][] = [
									'text' => $status->emoji() . ' ' . $localization[process_membership_search::PROCESS . "_button_status_$status->value"],
									'callback_data' => process_membership_search::PROCESS . "_status_$status->name"
								];
							}

							// Sending the message
							$context->sendMessage(
								'👤 *' . $localization[process_membership_search::PROCESS . '_status_select_title'] . '*',
								[
									'reply_markup' => [
										'inline_keyboard' => $keyboard,
										'disable_notification' => true
									]
								]
							);
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[process_membership_search::PROCESS . '_not_started'] . '*');
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
