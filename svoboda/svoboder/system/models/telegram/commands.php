<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\telegram\selections,
	svoboda\svoboder\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message,
	Zanzara\Telegram\Type\Input\InputFile as file_input;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram commands
 *
 * @package svoboda\svoboder\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class commands extends core
{
	/**
	 * Menu
	 *
	 * Responce for the commands: "/start", '/menu'
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function menu(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the title
				$title = '📋 *' . $localization['menu_title'] . '*';

				// Initializing accounts
				$accounts = '*' . $localization['menu_accounts'] . '* ' . ((new account)->database->count() ?? 0);

				// Initializing members
				$members = '*' . $localization['menu_members'] . '* ' . 0;

				// Initializing distributions
				$distributions = '*' . $localization['menu_distributions'] . '* ' . ((new distribution)->database->count() ?? 0);

				// Sending the message
				$context->sendMessage(
					<<<TXT
					$title

					$accounts
					$members
					$distributions
					TXT,
					[
						'reply_markup' => [
							'inline_keyboard' => [
								[
									[
										'text' => '📺 ' . $localization['menu_button_site'],
										'url' => 'https://svoboda.works'
									],
									[
										'text' => '🗺 ' . $localization['menu_button_map'],
										'web_app' => [
											'url' => 'https://telegram.map.svoboda.works'
										]
									],
									/* [
										'text' => '🔥 ' . $localization['menu_button_blog'],
										'url' => 'https://t.me/svoboder_bot'
									] */
								],
								[
									/* [
										'text' => '🏗 ' . $localization['menu_button_projects'],
										'callback_data' => 'prjects'
									], */
									/* [
										'text' => '🐣 ' . $localization['menu_button_members'],
										'callback_data' => 'message'
									], */
									[
										'text' => '🏘 ' . $localization['menu_button_distributions'],
										'callback_data' => 'distributions'
									]
								],
								[
									/* [
										'text' => '🤟 ' . $localization['menu_button_volunteering'],
										'callback_data' => 'volunteering'
									], */
									[
										'text' => '✉️ ' . $localization['menu_button_message'],
										'callback_data' => 'message'
									]
								]
							],
							'disable_notification' => true,
							'remove_keyboard' => true
						],
					]
				)
					->then(function (message $message) use ($context) {
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Message
	 *
	 * Responce for the command: "/message"
	 *
	 * Start a process for creating message
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function message(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing the title
			$title = '';

			// Sending the message
			$context->sendMessage(<<<TXT
				*⚠️ Failed to initialize your Telegram account*
				TXT)
				->then(function (message $message) use ($context) {
					// 
				});
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

	/**
	 * Distributions
	 *
	 * Responce for the command: "/distributions"
	 * 
	 * Sends the distributions menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function distributions(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing language 
			$language = $context->get('language');

			if ($language instanceof language) {
				// Initialized language

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Initializing the message title
					$title = '🏘 *' . $localization['distributions_title'] . '*';

					// Initializing the message description
					$description = $localization['distributions_description'];

					// Initializing the distribution model
					$model = new distribution;

					// Initializing the message "registered" row
					$registered = '*' . $localization['distributions_registered'] . ':* ' . $model->database->count();

					// Initializing the message "confirmed" row
					$confirmed = '*' . $localization['distributions_confirmed'] . ':* ' . count($model->database->read(
						filter: fn(record $record) => $record->confirmed,
						amount: 1000
					));

					// Sending the message
					$context->sendMessage(
						<<<TXT
						$title
						
						$registered
						$confirmed

						$description
						TXT,
						[
							'reply_markup' => [
								'inline_keyboard' => [
									[
										[
											'text' => '📋 ' . $localization['distributions_button_register'],
											'callback_data' => 'distribution_registration_start'
										],
										[
											'text' => '🔎 ' . $localization['distributions_button_search'],
											'callback_data' => 'distribution_search_start'
										]
									]
								],
								'disable_notification' => true,
								'remove_keyboard' => true
							],
						]
					);
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
				// Not initialized language

				// Sending the message
				$context->sendMessage('⚠️ *Failed to initialize language*')
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

	/**
	 * Language
	 *
	 * Responce for the command: "/language"
	 * 
	 * Send the language selection menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function language(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing language 
			$language = $context->get('language');

			if ($language instanceof language) {
				// Initialized language

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the language selection
					selections::language(
						context: $context,
						prefix: 'settings_language_',
						title: '🌏 *' . $localization['settings_select_language_title'] . '*',
						description: '🌏 *' . $localization['settings_select_language_description'] . '*'
					);
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Sended the message

					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Repository
	 *
	 * Responce for the command: "/repository"
	 *
	 * Sends information about project and menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function repository(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing title of the message
				$title = '🏛️ ' . $localization['repository_title'];

				// Sending the message
				$context->sendMessage($title . "\n\n" . $localization['repository_text'], [
					'reply_markup' => [
						'inline_keyboard' => [
							[
								[
									'text' => '🏛️ ' . $localization['repository_button_code'],
									'url' => 'https://git.mirzaev.sexy/mirzaev/mashtrash'
								]
							],
							[
								[
									'text' => '⚠️  ' . $localization['repository_button_issues'],
									'url' => 'https://git.mirzaev.sexy/mirzaev/mashtrash/issues'
								],
								[
									'text' => '🌱  ' . $localization['repository_button_suggestions'],
									'url' => 'https://git.mirzaev.sexy/mirzaev/mashtrash/issues'
								]
							]
						],
						'remove_keyboard' => true,
						'disable_notification' => true
					],
					'link_preview_options' => [
						'is_disabled' => true
					]
				]);
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

	/**
	 * Author
	 *
	 * Responce for the command: "/author"
	 *
	 * Sends 
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function author(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing title of the message
				$title = '👽 ' . $localization['author_title'];

				// Sending the message
				$context->sendMessage($title . "\n\n" . $localization['author_text'], [
					'reply_markup' => [
						'inline_keyboard' => [
							[
								[
									'text' => '📚 ' . $localization['author_button_neurojournal'],
									'url' => 'https://mirzaev.sexy'
								],
								[
									'text' => '🤟 ' . $localization['author_button_projects'],
									'url' => 'https://git.svoboda.works/mirzaev?tab=activity'
								]
							],
							[
								[
									'text' => '✖️  ' . $localization['author_button_twitter'],
									'url' => 'https://x.com/mirzaev_sexy'
								],
								[
									'text' => '🦋 ' . $localization['author_button_bluesky'],
									'url' => 'https://bsky.app/profile/mirzaev.bsky.social'
								],
								[
									'text' => '⛓️ ' . $localization['author_button_bastyon'],
									'url' => 'https://bsky.app/profile/mirzaev.bsky.social'
								]
							],
							[
								[
									'text' => '🇺🇸 ' . $localization['author_button_youtube_english'],
									'url' => 'https://www.youtube.com/@MIRZAEV'
								],
								[
									'text' => '🇷🇺 ' . $localization['author_button_youtube_russian'],
									'url' => 'https://www.youtube.com/@MIRZAEV'
								]
							],
							[
								[
									'text' => '✉️ ' . $localization['author_button_message'],
									'url' => 'https://t.me/mirzaev_sexy'
								]
							]
						],
						'remove_keyboard' => true,
						'disable_notification' => true
					],
					'link_preview_options' => [
						'is_disabled' => true
					]
				]);
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

	/**
	 * Society
	 *
	 * Responce for the command: "/society"
	 *
	 * Sends the "mushroom" image and the localized text "why so shroomious"
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function society(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Sending the message
				$context->sendPhoto(
					new file_input(STORAGE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'mushroom.jpg'),
					[
						'caption' => $localization['why_so_shroomious'],
						'disable_notification' => true
					]
				);
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
