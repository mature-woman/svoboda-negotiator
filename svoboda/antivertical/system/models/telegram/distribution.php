<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\distribution as model,
	svoboda\antivertical\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution
 *
 * @package svoboda\antivertical\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class distribution extends core
{
	/**
	 * Distributions
	 *
	 * Sends a message with a list of distributions to 
	 * which the account is a creator, membership, planned or volunteer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function list(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof account) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the title
				$title = '🏘 *' . $localization['distributions_list_title'] . '*';

				// Initializing the account model
				$model_account = new account;

				// Initializing accounts
				$accounts = '*' . $localization['menu_accounts'] . ':* ' . ($model_account->database->count() ?? 0);

				// Initializing memberships
				$memberships = '*' . $localization['menu_memberships'] . ':* ' . 0;

				// Initializing trusted memberships
				$memberships_trusted = '*' . $localization['menu_memberships_trusted'] . ':* ' . 0;

				// Initializing planners
				$planners = '*' . $localization['menu_planners'] . ':* ' . 0;

				// Initializing volunteers
				$volunteers = '*' . $localization['menu_volunteers'] . ':* ' . 0;

				// Initializing the distribution model
				$model_distribution = new model;

				// Initializing distributions
				$distributions = '*' . $localization['menu_distributions'] . ':* ' . ($model_distribution->database->count() ?? 0);

				// Initializing trusted distributions
				$distributions_trusted = '*' . $localization['menu_distributions_trusted'] . ':* ' . 0;

				// Initializing distributions messages
				$distributions_messages = '*' . $localization['menu_distributions_messages'] . ':* ' . ($model_distribution->message->database->count() ?? 0);

				// Sending the message
				$context->sendMessage(
					<<<TXT
					$title

					$accounts

					$memberships
					$memberships_trusted
					$planners
					$volunteers

					$distributions
					$distributions_trusted
					$distributions_messages
					TXT,
					[
						'reply_markup' => [
							'inline_keyboard' => [
								[
									[
										'text' => '🔥 ' . $localization['menu_button_projects'],
										'callback_data' => 'projects'
									],
									[
										'text' => '🗺 ' . $localization['menu_button_map'],
										'web_app' => [
											'url' => 'https://telegram.map.svoboda.works'
										]
									],
									[
										'text' => '📺 ' . $localization['menu_button_site'],
										'url' => 'https://svoboda.works'
									]
								],
								[
									/* [
										'text' => '🏗 ' . $localization['menu_button_projects'],
										'callback_data' => 'prjects'
									], */
									[
										'text' => '🐣 ' . $localization['menu_button_memberships'],
										'callback_data' => 'message'
									],
									[
										'text' => '🏘 ' . $localization['menu_button_distributions'],
										'callback_data' => 'distributions'
									]
								]
							],
							'disable_notification' => true,
							'remove_keyboard' => true
						],
					]
				)->then(function (message $message) use ($context) {
					// Sended the message
				});
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
