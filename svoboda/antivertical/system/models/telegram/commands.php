<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\telegram,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\membership,
	svoboda\antivertical\models\telegram\processes\language\select as process_language_select,
	svoboda\antivertical\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message,
	Zanzara\Telegram\Type\Input\InputFile as file_input;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram commands
 *
 * @package svoboda\antivertical\models\telegram
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

		if ($account instanceof account) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing title for the message
				$title = '📡 *' . $localization['menu_title'] . '*';

				// Initializing accounts
				$accounts = '*' . $localization['menu_accounts'] . ':* ' . ((new account)->database->count() ?? 0);

				// Initializing the membership model
				$model_membership = new membership;

				// Searching for memberships records
				$records = $model_membership->database->read(
					filter: function (record $membership, array $records = []) {
						if ($membership->status === 2) {
							// The account joined to the distribution

							foreach ($records as $record) {
								// Iterating over readed records

								if ($record->identifier === $membership->identifier) {
									// Found a dublicate of the membership

									// Exit (success)
									return false;
								}
							}

							// Exit (success)
							return true;
						}

						// Exit (success)
						return false;
					},
					amount: MENU_MEMBERSHIPS_AMOUNT
				) ?? [];


				// Initializing distributions
				$distributions = '*' . $localization['menu_distributions'] . ':* ' . ((new distribution)->database->count() ?? 0);

				// Initializing amount of planned
				$planned =  count(account::planned(PHP_INT_MAX));

				// Initializing amount of structors
				$structors =  '*' . $localization['menu_structors'] . ':* ' . count(account::structors(PHP_INT_MAX)) . ($planned > 0 ? " + $planned" : '');

				// Initializing amount of autonoms
				$autonoms =  '*' . $localization['menu_autonoms'] . ':* ' . count(account::autonoms(PHP_INT_MAX));

				// Initializing amount of volunteers 
				$volunteers =  '*' . $localization['menu_volunteers'] . ':* ' . count(account::volunteers(PHP_INT_MAX));

				// Initializing amount of investors
				$investors =  '*' . $localization['menu_investors'] . ':* ' . count(account::investors(PHP_INT_MAX));

				// Initializing amount of recruiters
				$recruiters =  '*' . $localization['menu_recruiters'] . ':* ' . count(account::recruiters(PHP_INT_MAX));

				// Initializing the data syncronization for the message
				$syncronization = '⛓️‍💥 ' . $localization['menu_not_syncronized'];

				// Initializing the database syncronization for the message
				$database = '⚠️ ' . $localization['menu_warning_database'];

				// Initializing channels
				$channels = [];

				// Initializing chats
				$chats = [];

				// Initializing projects
				$projects = [];

				// Initializing events
				$events = [];

				// Thinking stuff
				$thing_about_index = rand(1, 1);
				$thing_about_it = $localization['thing_about_it_' . $thing_about_index] ?? null;
				$thing_about_author = $localization['thing_about_it_' . $thing_about_index . '_author'] ?? null;
				$thinking_stuff = !empty($thing_about_it) ? "\n\n**>\"" . ($thing_about_it . (!empty($thing_about_author) ? "\"\n>\n>_*" . preg_replace('/\./', '\.', $thing_about_author) . '*_||' : '')) : '';

				// Sending the message
				$context->sendMessage(
					<<<TXT
					$title

					$accounts

					$distributions
					$structors
					$autonoms
					$volunteers
					$investors
					$recruiters

					$syncronization
					$database$thinking_stuff
					TXT,
					[
						'reply_markup' => [
							'inline_keyboard' => [
								[
									[
										'text' => '🔥 ' . $localization['menu_button_channel'],
										'url' => 'https://t.me/antivertical'
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
									[
										'text' => '🚸 ' . $localization['menu_button_chats'] . ': ' . count($chats),
										'url' => 'https://t.me/chats_antivertical_bot'
									],
									[
										'text' => '📣 ' . $localization['menu_button_channels'] . ': ' . count($channels),
										'url' => 'https://t.me/channels_antivertical_bot'
									],
								],
								[
									[
										'text' => '🏗 ' . $localization['menu_button_projects'] . ': ' . count($projects),
										'callback_data' => 'projects'
									],
									[
										'text' => '📅 ' . $localization['menu_button_events'] . ': ' . count($events),
										'url' => 'https://t.me/events_antivertical_bot'
									],
								],
								[
									[
										'text' => '🐣 ' . $localization['menu_button_memberships'],
										'callback_data' => 'memberships'
									],
									[
										'text' => '🏘 ' . $localization['menu_button_distributions'],
										'callback_data' => 'distributions'
									]
								],
								[
									[
										'text' => '🌏 ' . $localization['menu_button_organisation'],
										'callback_data' => 'organisation'
									],
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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

		if ($account instanceof account) {
			// Initialized the account

			// Initializing the title
			$title = '';

			// Sending the message
			$context->sendMessage(<<<TXT
				*⚠️ Failed to initialize the account*
				TXT)
				->then(function (message $message) use ($context) {
					// 
				});
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

	/**
	 * Memberships
	 *
	 * Responce for the command: "/memberships"
	 * 
	 * Sends the memberships menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function memberships(context $context): void
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof account) {
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
					$title = '🐣 *' . $localization['memberships_title'] . '*';

					// Initializing the message description
					$structor = $localization['memberships_structor'];

					// Sending the message
					$context->sendMessage(
						<<<TXT
						$title

						$structor
						TXT,
						[
							'reply_markup' => [
								'inline_keyboard' => [
									[
										[
											'text' => '🔎 ' . $localization['memberships_button_search'],
											'callback_data' => 'membership_search_start'
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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

		if ($account instanceof account) {
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

					// Initializing the message "declared" row
					$declared = '*' . $localization['distributions_declared'] . ':* ' . $model->database->count();

					// Initializing the message "confirmed" row
					$recognized = '*' . $localization['distributions_recognized'] . ':* ' . 0;

					// Sending the message
					$context->sendMessage(
						<<<TXT
						$title
						
						$declared
						$recognized

						$description
						TXT,
						[
							'reply_markup' => [
								'inline_keyboard' => [
									[
										[
											'text' => '📋 ' . $localization['distributions_button_declare'],
											'callback_data' => 'distribution_declaration_start'
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Organisation
	 *
	 * Responce for the command: "/organisation"
	 *
	 * Sends information about organisation with menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function organisation(context $context): void
	{
		// Initializing the telegram account
		$telegram = $context->get('telegram');

		if ($telegram instanceof telegram) {
			// Initialized the telegram account

			// Initializing the account
			$account = $context->get('account');

			if ($account instanceof account) {
				// Initialized the account

				// Initializing language 
				$language = $context->get('language');

				if ($language instanceof language) {
					// Initialized language


					// Initializing localization 
					$localization = $context->get('localization');

					if ($localization) {
						// Initialized localization

						// Initializing title for the message
						$title = match (rand(1, 3)) {
							1 => '🌏 ',
							2 => '🌍 ',
							3 => '🌎 '
						} . '*' . $localization['organisation_title'] . '*';

						// Initializing the organisation description for the message
						$description = $localization['organisation_description'];

						// Initializing the structor description for the message
						$structor = $localization['organisation_structor'];

						// Initializing the autonom description for the message
						$autonom = $localization['organisation_autonom'];

						// Initializing the warning for the message
						$warning_legal = '⚠️ ' . $localization['organisation_warning_legal'];

						// Initializing the warning for the message
						$warning_administration =  '⚠️ ' . $localization['organisation_warning_administration'];

						// Initiailzing the account distribution
						$distribution = $account->distribution();

						// Declaring the membership keyboard
						$keyboard_membership = [];

						if ($distribution instanceof distribution) {
							// Structor

							// Initializing the account distribution localization
							$distribution_localization = $distribution->localization(language: $language);

							// Initializing the distribution structors
							$distribution_structors = count($distribution->structors(PHP_INT_MAX) ?? []);

							// Initializing the distribution plannert 
							$distribution_planners = count($distribution->planners(PHP_INT_MAX) ?? []);

							// Initializing the membership keyboard
							$keyboard_membership = [
								[
									[
										'text' => '🏘 ' . $localization['organisation_button_structor_distribution'] . ': ' . $distribution_localization->name . ' (' . $distribution_structors . ($distribution_planners > 0 ? ' + ' . $distribution_planners : '') . ')',
										'callback_data' => 'distributions'
									]
								]
							];
						} else if (false) {
							// Autonom 
						} else if (false) {
							// Volunteer 
						} else {
							// Stranger

							// Initializing the membership keyboard
							$keyboard_membership = [
								[
									[
										'text' => '🧱 ' . $localization['organisation_button_structor'],
										'callback_data' => 'distributions'
									],
									[
										'text' => '🐣 ' . $localization['organisation_button_autonom'],
										'callback_data' => 'autonom'
									]
								]
							];
						}

						// Sending the message
						$context->sendMessage(
							<<<TXT
							$title

							$description

							$structor
							$autonom

							$warning_legal
						
							$warning_administration
							TXT,
							[
								'reply_markup' => [
									'inline_keyboard' => $keyboard_membership,
									'remove_keyboard' => true,
									'disable_notification' => true
								],
								'link_preview_options' => [
									'is_disabled' => true
								]
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
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the telegram account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize the telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}


	/**
	 * Account
	 *
	 * Responce for the command: "/account"
	 *
	 * Sends information about account with menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function account(context $context): void
	{
		// Initializing the telegram account
		$telegram = $context->get('telegram');

		if ($telegram instanceof telegram) {
			// Initialized the telegram account

			// Initializing the account
			$account = $context->get('account');

			if ($account instanceof account) {
				// Initialized the account

				// Initializing localization 
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Initializing title for the message
					$title = '🫵 ' . $localization['account_title'];

					// Initializing the account identifier row for the message
					$identifier = '*' . $localization['account_identifier'] . ":* $account->identifier \($telegram->identifier\)";

					// Declaring the buffer of authorizations
					$authorizations = '';

					// Initializing rows about authorization
					foreach ($account->record->values() as $key => $value) {
						// Iterating over account parameters

						if (str_starts_with($key, 'authorized_')) {
							// Iterating over account authorizations

							// Skipping system authorizations
							if (str_starts_with($key, 'authorized_system_')) continue;

							// Writing into the buffer of authorizations
							$authorizations .= ($value ? '✅' : '❎') . ' *' . ($localization["account_$key"] ?? preg_replace('/_/', '\\_', $key)) . ':* ' . ($value ? $localization['yes'] : $localization['no']) . "\n";
						}
					}

					// Trimming the last line break character
					$authorizations = trim($authorizations, "\n");

					// Declaring the buffer of system authorizations
					$authorizations_system = '';

					// Initializing rows about authorization
					foreach ($account->record->values() as $key => $value) {
						// Iterating over account parameters

						if (str_starts_with($key, 'authorized_')) {
							// Iterating over account authorizations

							if (str_starts_with($key, 'authorized_system_') && $value) {
								// System authorization

								// Writing into the buffer of system authorizations
								$authorizations_system .= ($value ? '🔐' : '🔒') . ' *' . ($localization["account_$key"] ?? preg_replace('/_/', '\\_', $key)) . ':* ' . ($value ? $localization['yes'] : $localization['no']) . "\n";
							}
						}
					}

					// Trimming the last line break character
					$authorizations_system = trim($authorizations_system, "\n");

					// Initializing the line break for the buffer of system authorizations
					if (!empty($authorizations_system)) $authorizations_system = "\n\n" . $authorizations_system;

					// Initializing the data export for the message
					$export = '📤 ' . $localization['account_export'];

					// Sending the message
					$context->sendMessage(
						<<<TXT
						$title

						$identifier

						$authorizations$authorizations_system

						$export
						TXT,
						[
							'reply_markup' => [
								'inline_keyboard' => [
									[
										[
											'text' => '🗺 ' . $localization['account_button_localizations'],
											'callback_data' => 'account_localizations'
										]
									]
								],
								'remove_keyboard' => true,
								'disable_notification' => true
							],
							'link_preview_options' => [
								'is_disabled' => true
							]
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
				$context->sendMessage('⚠️ *Failed to initialize the account*')
					->then(function (message $message) use ($context) {
						// Ending the conversation process
						$context->endConversation();
					});
			}
		} else {
			// Not initialized the telegram account

			// Sending the message
			$context->sendMessage('⚠️ *Failed to initialize the telegram account*')
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

		if ($account instanceof account) {
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
					process_language_select::menu(
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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

		if ($account instanceof account) {
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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

		if ($account instanceof account) {
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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

		if ($account instanceof account) {
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}
}
