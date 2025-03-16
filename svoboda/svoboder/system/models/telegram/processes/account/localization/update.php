<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\processes\account\localization;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\telegram\account as telegram_account,
	svoboda\svoboder\models\telegram\buttons\account\localization\update as button_account_localization_update,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\telegram\commands;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Svoboda time
use svoboda\time\statement as svoboda;

// Baza database
use mirzaev\baza\record;

// Build-in libraries
use Error as error;

/**
 * Account localization update process
 *
 * @package svoboda\svoboder\models\telegram\processes\account\localization
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class update extends core
{
	/**
	 * Process
	 *
	 * @var const string PROCESS Name of the process in the telegram user buffer
	 */
	public const string PROCESS = 'account_localization_update';

	/**
	 * Start
	 *
	 * Starting the account localization creating process
	 *
	 * @param context $context Request data from Telegram
	 * @param language $target The language
	 *
	 * @return void
	 */
	public static function start(context $context, language $target): void
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

					// Reading from the telegram user buffer
					$context->getUserDataItem(static::PROCESS)
						->then(function (?array $process) use ($context, $account, $language, $target, $localization) {
							// Readed from the telegram user buffer

							if ($process) {
								// Found started account localization update process

								// Sending the message
								$context->sendMessage('📂 *' . $localization['account_localization_update_continiued'] . '*')
									->then(function (message $message) use ($context, $account, $language, $localization) {
										// Sended the message

										// Sending the account localization update menu
										static::menu($context);
									});
							} else {
								// Not found started account localization update process

								// Initializing the account model
								$model_account = new account;

								// Initializing the account localization
								$record = $model_account->localization->database->read(
									filter: fn(record $localization) => $localization->account === $account->identifier && $localization->language === $target->name,
									amount: 1
								)[0] ?? null;

								if ($record instanceof record) {
									// Initialized the account localization

									// Initializing the account localization update buffer
									$process = [
										'record' => $record,
										'language' => $target,
										'name' => $record?->name,
									];

									// Writing to the telegram user buffer
									$context->setUserDataItem(static::PROCESS, $process)
										->then(function () use ($context, $account, $localization) {
											// Writed to the telegram user buffer

											// Sending the message
											$context->sendMessage('📂 *' . $localization['account_localization_update_started'] . '*')
												->then(function (message $message) use ($context, $account, $localization) {
													// Sended the message

													// Sending the account localization update menu
													static::menu($context);
												});
										});
								} else {
									// Not initialized the account localization

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_initialized_localization'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								}
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
				->then(function (message $message) use ($context) {
					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Cancel
	 *
	 * Ending the account localization update process
	 * without creating the localization record in the database
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function cancel(context $context): void
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
				$context->getUserDataItem(static::PROCESS)
					->then(function (?array $process) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($process) {
							// Found started account localization update process

							// Deleting in the telegram user buffer
							$context->deleteUserDataItem(static::PROCESS)
								->then(function () use ($context, $localization) {
									// Deleted in the telegram user buffer

									// Sending the message
									$context->sendMessage('🗑 *' . $localization['account_localization_update_canceled'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Sending the account localizations menu
											telegram_account::localizations($context);
										});
								});
						} else {
							// Not found started account localization update process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_started'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();

									// Sending the account localizations menu
									telegram_account::localizations($context);
								});
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

	/**
	 * End
	 *
	 * Ending the account localization update process
	 * and creating the localization record in the database
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function end(context $context): void
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

					// Reading from the telegram user buffer
					$context->getUserDataItem(static::PROCESS)
						->then(function (?array $process) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($process) {
								// Found started account localization update process

								// Initializing the account model
								$model_account = new account;

								// Updating the account localization
								$updated_localization = $model_account->localization->database->read(
									filter: fn(record $localization) => $localization->identifier === $process['record']->identifier,
									update: function (record &$localization) use ($process) {
										$localization->name = $process['name'];
										$localization->updated = svoboda::timestamp();
									},
									amount: 1
								);

								if ($updated_localization) {
									// Updated the account localization

									// Sending the message
									$context->sendMessage('✏️ *' . $localization['account_localization_update_updated'] . '*')
										->then(function (message $message) use ($context, $account, $language, $localization) {
											// Sended the message

											// Deleting from the telegram user buffer
											$context->deleteUserDataItem(static::PROCESS)
												->then(function () use ($context, $localization) {
													// Deleted from the telegram user buffer

													// Sending the message
													$context->sendMessage('✅ *' . $localization['account_localization_update_completed'] . '*')
														->then(function (message $message) use ($context) {
															// Sended the message

															// Ending the conversation process
															$context->endConversation();

															// Sending the account localizations menu
															telegram_account::localizations($context);
														});
												});
										});
								} else {
									// Not updated the account localization

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_updated'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not found started account localization update process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_started'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Sending the account localizations menu
										telegram_account::localizations($context);
									});
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
	 * Menu
	 *
	 * Sends the account localization update menu with parameters: language, name
	 * When all parameters was initialized then sends the complete button
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	protected static function menu(context $context): void
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
					$context->getUserDataItem(static::PROCESS)
						->then(function (?array $process) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($process) {
								// Found started account localization update process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => empty($process['name']) ? '🔴 ' . $localization['account_localization_update_button_name'] : '🟢 ' . $localization['account_localization_update_button_name'] . ': ' . $process['name'],
											'callback_data' => 'account_localization_update_name'
										]
									],
								];

								// Initializing the index of last row
								$last = count($keyboard);

								// Initializing the last row
								$keyboard[$last] ??= [];

								// Initializing the button for canceling the generation process
								$keyboard[$last][] = [
									'text' => '❎ ' . $localization['account_localization_update_button_cancel'],
									'callback_data' => 'account_localization_update_cancel'
								];

								if (
									!empty($process['record']) &&
									!empty($process['name'])
								) {
									// Initialized all requeired parameters

									// Initializing the button for completing the generation process
									$keyboard[$last][] = [
										'text' => '✅ ' . $localization['account_localization_update_button_confirm'],
										'callback_data' => 'account_localization_update_end'
									];
								}

								// Ending the conversation process
								$context->endConversation()
									->then(function () use ($context, $language, $localization, $keyboard, $process) {
										// Deinitialized the conversation process

										// Initializing the message title
										$title = '📀 *' . $localization['account_localization_update_generation'] . '*';

										// Initializing the message target
										$target = '*' . $localization['account_localization_update_generation_target'] . ':* ' . $process['language']->flag() . ' *' . $process['language']->label($language) . '* \(' . $process['record']->identifier . '\)';

										// Sending the message
										$context->sendMessage(
											<<<TXT
											$title

											$target
											TXT,
											[
												'reply_markup' => [
													'inline_keyboard' => $keyboard,
													'disable_notification' => true,
													'remove_keyboard' => true
												],
											]
										);
									});
							} else {
								// Not found started account localization update process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_started'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Sending the account localizations menu
										telegram_account::localizations($context);
									});
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
	 * Name
	 *
	 * Write name into the account localization update buffer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function name(context $context): void
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
				$context->getUserDataItem(static::PROCESS)
					->then(function (?array $process) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($process) {
							// Found started account localization update process

							// Initializing the new name
							$new = $context->getMessage()->getText();

							if (!empty($new)) {
								// Initialized the new name

								if (mb_strlen($new) >= 2) {
									// Passed minimum length check

									if (mb_strlen($new) <= 128) {
										// Passed maximum length check

										// Search for restricted characters
										preg_match_all('/[\W\d]/u', $new, $matches);

										// Declaring the buffer of found restricted characters
										$characters = [];

										foreach ($matches[0] as $match) {
											// Iterating over found restricted characters 

											if (match ($match) {
												' ', '-' => false,
												default => true
											}) {
												// Found a restricted character

												// Writing into the buffer of found restricted characters
												$characters[] = $match;
											}
										}

										if (empty($characters)) {
											// Not found restricted characters

											try {
												// Initializing the old name
												$old = empty($process['name']) ? '_' . $localization['empty'] . '_' : $process['name'];

												// Writing into the account localization update process buffer
												$process['name'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem(static::PROCESS, $process)
													->then(function () use ($context, $account, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Escaping characters for markdown
														$escaped = str_replace('-', '\\-', $new);

														// Sending the message
														$context->sendMessage('✅ *' . $localization['account_localization_update_name_update_success'] . "* $old → *$escaped*")
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the account localization update menu
																static::menu($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about name update

												// Sending the message
												$context->sendMessage('❎ *' . $localization['account_localization_update_name_update_fail'])
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else {
											// Found restricted characters

											// Initializing title of the message
											$title = '⚠️  *' . $localization['account_localization_update_name_request_restricted_characters_title'] . '*';

											// Initializing description of the message
											$description = '*' . $localization['account_localization_update_name_request_restricted_characters_description'] . '* \\' . implode(', \\', $characters);

											// Sending the message
											$context->sendMessage(
												<<<TXT
												$title

												$description
												TXT
											)
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();

													// Requesting to enter name again
													button_account_localization_update::name($context);
												});
										}
									} else {
										// Not passed maximum length check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization['account_localization_update_name_request_too_long'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter name again
												button_account_localization_update::name($context);
											});
									}
								} else {
									// Not passed minimum length check

									// Sending the message
									$context->sendMessage('⚠️  *' . $localization['account_localization_update_name_request_too_short'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Requesting to enter name again
											button_account_localization_update::name($context);
										});
								}
							} else {
								// Failed to initialize the new name

								// Sending the message
								$context->sendMessage('📄 *' . $localization['account_localization_update_name_request_not_acceptable'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter name again
										button_account_localization_update::name($context);
									});
							}
						} else {
							// Not found started account localization update process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['account_localization_update_not_started'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();

									// Sending the account localizations menu
									telegram_account::localizations($context);
								});
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
