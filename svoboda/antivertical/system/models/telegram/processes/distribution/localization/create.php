<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\processes\distribution;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\telegram\commands;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Distribution localization process
 *
 * @package svoboda\antivertical\models\telegram\processes\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class localization extends core
{
	/**
	 * Start
	 *
	 * Starting the distribution localization process
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function start(context $context): void
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

					// Reading from the telegram user buffer
					$context->getUserDataItem('distribution_localization')
						->then(function ($distribution_localization) use ($context, $account, $localization) {
							// Readed from the telegram user buffer

							if ($distribution_localization) {
								// Found started localization process

								// Sending the message
								$context->sendMessage('🌏 *' . $localization['distribution_localization_continiued'] . '*')
									->then(function (message $message) use ($context, $localization) {
										// Sended the message

										// Sending the localization generation menu
										static::generation($context);
									});
							} else {
								// Not found started localization process

								// Reading from the telegram user buffer
								$context->getUserDataItem('distribution_registration')
									->then(function ($distribution) use ($context, $account, $localization) {
										// Readed from the telegram user buffer

										if ($distribution instanceof record) {
											// Found started registration process

											// Writing to the telegram user buffer
											$context->setUserDataItem('distribution_localization', ['distribution' => $distribution])
												->then(function () use ($context, $localization) {
													// Writed to the telegram user buffer

													// Sending the message
													$context->sendMessage('🌏 *' . $localization['distribution_localization_started'] . '*')
														->then(function (message $message) use ($context, $localization) {
															// Sended the message

															// Sending the localization generation menu
															static::generation($context);
														});
												});
										} else {
											// Not found started registration process

											// Здесь надо просить выбрать дистрибутив
											
											// Проверять на то, что он creator дистрибутива, иначе посылать
										}
									});
							}
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
	 * Generation
	 *
	 * Send the generation menu
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	protected static function generation(context $context): array
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
					$context->getUserDataItem('distribution_localization')
						->then(function ($distribution_localization) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($distribution_localization) {
								// Found started localization process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => (empty($distribution_localization['language']) ? language::{$distribution_localization['language']}->flag() : '🟢') . ' ' . $localization['distribution_localization_button_language'],
											'callback_data' => 'distribution_localization_language'
										]
									],
									[
										[
											'text' => (empty($distribution_localization['name']) ? '🔴 ' : '🟢 ') . $localization['distribution_localization_button_name'],
											'callback_data' => 'distribution_localization_name'
										]
									],
								];

								// Sending the message
								$context->sendMessage(
									'🌏 *' . $localization['distribution_localization_generation_menu'] . '*',
									[
										'reply_markup' => [
											'inline_keyboard' => $keyboard
										],
										'disable_notification' => true
									]
								);
							}
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
		// Exit (success)
		return [];
	}
}
