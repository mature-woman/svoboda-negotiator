<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\account,
	svoboda\negotiator\models\enumerations\language;

// Framework for Telegram
use Zanzara\Zanzara,
	Zanzara\Context as context,
	Zanzara\Telegram\Type\Input\InputFile as file_input,
	Zanzara\Telegram\Type\File\Document as document,
	Zanzara\Middleware\MiddlewareNode as node,
	Zanzara\Telegram\Type\User as user;

// Baza database
use mirzaev\baza\record;

// Built-in libraries
use Exception as exception,
	Error as error;

/**
 * Telegram middlewares
 *
 * @package svoboda\negotiator\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class middlewares extends core
{
	/**
	 * Account (middleware)
	 *
	 * Initialize or registrate the account and write it to the `account` variable inside the `$context`
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function account(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the telegram account
		$telegram = $context->getEffectiveUser();

		// Initializing the account
		/* $account = new account()->initialize($telegram); */
		$account = (new account())->initialize($telegram);

		if ($account instanceof record) {
			// Initialized the account

			// Writing the account into the context variable
			$context->set('account', $account);

			// Continuation of the process
			$next($context);
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
	 * Language (middleware)
	 *
	 * Implement the account language
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function language(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			if ($account->language) {
				// Initialized the language parameter

				try {
					// Writing the account language into the context variable
					$context->set('language', language::{$account->language});
				} catch (error $error) {
					// Not initialized the language

					// Writing the default language into the context variable
					$context->set('language', language::en);
				}
			} else {
				// Not initialized the language parameter

				// Writing the default language into the context variable
				$context->set('language', language::en);
			}

			// Continuation of the process
			$next($context);
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
	 * Localization (middleware)
	 *
	 * Implement the account language and initialize the localization file
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function localization(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing the language
			$language = $context->get('language');

			if ($language instanceof language) {
				// Initialized the language

				// Initializing path to the localization file
				$file = LOCALIZATIONS . DIRECTORY_SEPARATOR . strtolower($language->label()) . '.php';

				if (file_exists($file) && is_readable($file)) {
					// Found the localization file

					// Initializing localization
					$localization = require($file);

					if (is_array($localization)) {
						// Initializae localization

						// Writing localization into the context variable
						$context->set('localization', $localization);

						// Continuation of the process
						$next($context);
					} else {

						// Sending the message
						$context->sendMessage('⚠️ *Failed to initialize localization*')
							->then(function ($message) use ($context) {
								// Ending the conversation process
								$context->endConversation();
							});
					}
				} else {
					// Not found the localization file

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize the localization file*')
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
	 * System (middleware)
	 *
	 * Check the account for access to the system
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function system(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			if ($account->authorized_system) {
				// Authorized the account to the system

				// Continuation of the process
				$next($context);
			} else {
				// Not authorized the account to the system

				// Initializing localization
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the message
					$context->sendMessage('⛔ *' . $localization['not_authorized_system'] . '*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});

					// Stopping the process
					$context->set('stop', true);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * Contact (middleware)
	 *
	 * Check the account for access to contact with the organization
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function contact(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			if ($account->authorized_contact) {
				// Authorized the account to contact with the organization

				// Continuation of the process
				$next($context);
			} else {
				// Not authorized the account to contact with the organization

				// Initializing localization
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the message
					$context->sendMessage('⛔ *' . $localization['not_authorized_contact'] . '*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});

					// Stopping the process
					$context->set('stop', true);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * Request (middleware)
	 *
	 * Check the account for access to request to the organization
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function request(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			if ($account->authorized_request) {
				// Authorized the account to request to the organization

				// Continuation of the process
				$next($context);
			} else {
				// Not authorized the account to request to the organization

				// Initializing localization
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the message
					$context->sendMessage('⛔ *' . $localization['not_authorized_request'] . '*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});

					// Stopping the process
					$context->set('stop', true);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * Settings (middleware)
	 *
	 * Check the account for access to the settings
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function settings(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			if ($account->authorized_settings) {
				// Authorized the account to the settings

				// Continuation of the process
				$next($context);
			} else {
				// Not authorized the account to the settings

				// Initializing localization
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the message
					$context->sendMessage('⛔ *' . $localization['not_authorized_settings'] . '*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});

					// Stopping the process
					$context->set('stop', true);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * System settings (middleware)
	 *
	 * Check the account for access to the system settings
	 *
	 * @param context $context
	 * @param node $next
	 *
	 * @return void
	 */
	public static function system_settings(context $context, node $next): void
	{
		// Is the process stopped?
		if ($context->get('stop')) return;

		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			if ($account->authorized_system_settings) {
				// Authorized the account to the system settings

				// Continuation of the process
				$next($context);
			} else {
				// Not authorized the account to the system settings

				// Initializing localization
				$localization = $context->get('localization');

				if ($localization) {
					// Initialized localization

					// Sending the message
					$context->sendMessage('⛔ *' . $localization['not_authorized_system_settings'] . '*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});

					// Stopping the process
					$context->set('stop', true);
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
						->then(function ($message) use ($context) {
							// Ending the conversation process
							$context->endConversation();
						});
				}
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
