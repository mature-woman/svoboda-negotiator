<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\processes\member;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\member,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\telegram\buttons\member\search as button_member_search,
	svoboda\svoboder\models\enumerations\member\status,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\telegram\commands,
	svoboda\svoboder\models\traits\coordinates;

// Event-driven library for PHP
use function React\Async\await;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

// Built-in libraries
use Exception as exception,
	Error as error;

/**
 * Member search process
 *
 * @package svoboda\svoboder\models\telegram\processes\member
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class search extends core
{
	use coordinates {
		coordinates::distance as vincenty;
	}

	/**
	 * Process
	 *
	 * @var const string PROCESS Name of the process in the telegram user buffer
	 */
	public const string PROCESS = 'member_search';

	/**
	 * Start
	 *
	 * Starting the member search process
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function start(context $context): void
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
						->then(function (?array $search) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							// Declaring the target distribution 
							$distribution = [
								'distribution' => null,
								'localization' => null
							];

							// Initializing the message text
							$text = $context->getCallbackQuery()?->getMessage()?->getText();

							if (!empty($text)) {
								// Initialized the message text

								// Searching for the distribution identifier
								preg_match('/^(\d)+[\w\s]+$/mu', $text, $matches);

								// Initializing the distribution identifier
								$identifier = $matches[1] ?? null;

								if (!empty($identifier)) {
									// Initialized the distribution identifier

									// Type conversion
									$identifier = (int) $identifier;

									// Initializing the distribution model
									$model_distribution = new distribution;

									// Initializing the distribution
									$distribution['distribution'] = $model_distribution->database->read(
										filter: fn(record $record) => $record->identifier === $identifier,
										amount: 1
									)[0] ?? null;

									if ($distribution['distribution'] instanceof record) {
										// Initialized the distribution

										// Searching for the distribution localizations records
										$distribution_localizations = $model_distribution->localization->database->read(
											filter: fn(record $localization) => $localization->distribution === $distribution['distribution']->identifier,
											amount: MEMBERS_SEARCH_DISTRIBUTION_LOCALIZATIONS_AMOUNT
										);

										if (count($distribution_localizations) > 0) {
											// Initialized the distributions localizations

											foreach ($distribution_localizations as $record) {
												// Iterating over localizations

												if ($record->language === $language->name) {
													// Found localization by the account language

													// Initializing localization by the account language
													$distribution['localization'] = $record;

													// Exit (success)
													break;
												}
											}
											if (is_null($distribution['localization'])) {
												// Not initialized localization by the account language

												foreach ($distribution_localizations as $record) {
													// Iterating over localizations

													if ($record->language === 'en') {
														// Found localization by english language

														// Initializing localization by english language
														$distribution['localization'] = $record;

														// Exit (success)
														break;
													}
												}

												if (is_null($distribution['localization'])) {
													// Not initialized localization by english language

													// Initializing the account model
													$model_account = new account;

													// Initializing the distribution creator account
													$creator = $model_account->database->read(
														filter: fn(record $account) => $account->identifier === $distribution['distribution']->creator,
														amount: 1
													)[0] ?? null;

													if ($creator instanceof record) {
														// Initialized the distribution creator account

														foreach ($distribution_localizations as $record) {
															// Iterating over localizations

															if ($record->language === $creator->language) {
																// Found localization by the distribution creator account language

																// Initializing localization by the distribution creator account language
																$distribution['localization'] = $record;

																// Exit (success)
																break;
															}
														}
													}

													if (is_null($distribution['localization'])) {
														// Not initialized localization by the distribution creator account language

														// Initializing localization by the first found record
														$distribution['localization'] = $distribution_localizations[0];
													}
												}
											}
										}
									}
								}
							}

							if ($search) {
								// Found started search process

								// Sending the message
								$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_continiued'] . '*')
									->then(function (message $message) use ($context, $account, $language, $localization, $search, $distribution) {
										// Sended the message

										if (!empty($distribution['distribution']) && !empty($distribution['localization'])) {
											// Initialized the distribution

											// Writing into the process buffer
											$search['distribution'] = $distribution;

											// Writing into the telegram user buffer
											$context->setUserDataItem(static::PROCESS, $search)
												->then(function () use ($context) {
													// Writing into the telegram user buffer

													// Sending the list of found member and menu
													static::menu($context);
												});
										} else {
											// Not initialized the distribution

											// Sending the list of found member and menu
											static::menu($context);
										}
									});
							} else {
								// Not found started search process

								// Initializing the member search buffer
								$search = [
									'name' => null,
									'distribution' => $distribution,
									'status' => status::joined,
									'page' => 0
								];

								// Writing to the telegram user buffer
								$context->setUserDataItem(static::PROCESS, $search)
									->then(function () use ($context, $account, $localization) {
										// Writed to the telegram user buffer

										// Sending the message
										$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_started'] . '*')
											->then(function (message $message) use ($context, $account, $localization) {
												// Sended the message

												// Sending the list of found member and menu
												static::menu($context);
											});
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
	 * End
	 *
	 * Ending the member search process
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

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem(static::PROCESS)
					->then(function (?array $search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Deleting from the telegram user buffer
							$context->deleteUserDataItem(static::PROCESS, $search)
								->then(function () use ($context, $search, $localization) {
									// Deleted from the telegram user buffer

									// Sending the message
									$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_ended'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Sending the members menu
											commands::members($context);
										});
								});
						} else {
							// Not found started search process

							// Ending the conversation process
							$context->endConversation();

							// Sending the members menu
							commands::members($context);
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
	 * Search
	 *
	 * Sends the list of found members and menu with parameters: name, distribution, status
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 *
	 * @todo rename to "search"?
	 */
	public static function menu(context $context): void
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
						->then(function (?array $search) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($search) {
								// Found started search process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => empty($search['name']) ? '🔴 ' . $localization[static::PROCESS . '_button_name'] : '🟢 ' . $localization['member_search_button_name'] . ': ' . $search['name'],
											'callback_data' => static::PROCESS . '_name'
										]
									],
									[
										[
											'text' => empty($search['distribution']['distribution']) || empty($search['distribution']['localization']) ? '🔴 ' . $localization[static::PROCESS . '_button_distribution'] : '🟢 ' . $localization['member_search_button_distribution'] . ': ' . $search['distribution']['localization']->name,
											'callback_data' => static::PROCESS . '_distribution'
										]
									],
									[
										[
											'text' => ($search['status']->emoji() ?? status::unknown->emoji()) .	' ' . $localization['member_search_button_status'] . ': ' . $localization['member_search_button_status_' . ($search['status']->value ?? status::unknown->value)],
											'callback_data' => static::PROCESS . '_status'
										]
									],
								];

								// Ending the conversation process
								$context->endConversation()
									->then(function () use ($context, $account, $language, $localization, $search, $keyboard) {
										// Deinitialized the conversation process

										// Initializing the member model
										$model_member = new member;

										// Initializing amount of readed members per page
										$page = MEMBERS_SEARCH_PAGE;

										if (
											empty($search['name']) &&
											empty($search['distribution']['distribution']) &&
											empty($search['distribution']['localization']) &&
											empty($search['status'])
										) {
											// Each search parameter is empty

											// Search for members
											$members = $model_member->database->read(
												amount: $page + 1,
												offset: $search['page'] < 1 ? 0 : $page * $search['page']
											);
										} else {
											// The search buffer has at least one parameter

											// Initializing the account model
											$model_account = new account;

											// Search for members
											$members = $model_member->database->read(
												filter: function (record $member) use ($search, $model_account) {
													// Initializing the matched buffer
													$matched = false;

													if (!empty($search['name'])) {
														// Requested search by name

														// Initializing the account localizations
														$localizations = $model_account->localization->database->read(
															filter: fn(record $localization) => $localization->account === $member->account,
															amount: MEMBERS_SEARCH_ACCOUNT_LOCALIZATIONS_AMOUNT
														);

														// Initializing the result status
														$result = false;

														foreach ($localizations as $localization) {
															// Iterating over localizations

															// Splitting the localization name into parts
															$parts = preg_split('/[\-\s]/', $localization->name);

															foreach ($parts as $part) {
																// Iterating over localization name parts

																if (levenshtein($part, $search['name']) <= MEMBERS_SEARCH_MEMBER_NAME_LEVENSHTEIN_DISTANCE) {
																	// Names matched by Levenshtein function

																	// Reinitializing the matched buffer
																	$result = true;

																	// Exit (success)
																	break 2;
																}
															}
														}

														if ($result) {
															// Names matched by Levenshtein function

															// Reinitializing the matched buffer
															$matched = true;
														} else {
															// Names not matched by Levenshtein function

															// Exit (success)
															return false;
														}
													}

													if (!empty($search['distribution']['distribution'])) {
														// Requested search by distribution

														if ($member->distribution === $search['distribution']['distribution']?->identifier) {
															// Distributions matched

															// Reinitializing the matched buffer
															$matched = true;
														} else {
															// Distributions not matched

															// Exit (success)
															return false;
														}
													}

													if ($search['status'] instanceof status) {
														// Requested search by status

														if ($member->status === $search['status']->value) {
															// Statuses matched

															// Reinitializing the matched buffer
															$matched = true;
														} else {
															// Statuses not matched

															// Exit (success)
															return false;
														}
													}

													// Exit (success)
													return $matched;
												},
												amount: $page + 1,
												offset: $search['page'] < 1 ? 0 : $page * $search['page']
											);
										}

										// Initializing the next page existence status
										$next = count($members) > $page;

										// Deleting the additional readed members
										unset($members[$page]);

										// Initializing the title
										$title = '🔍 *' . $localization[static::PROCESS . '_title'] . '*';

										// Sending the message
										$context->sendMessage(
											<<<TXT
											$title
											TXT,
											[
												'reply_markup' => [
													'inline_keyboard' => $keyboard,
													'disable_notification' => true,
													'remove_keyboard' => true
												],
											]
										)->then(function (message $message) use ($context, $account, $language, $localization, $search, $page, $next, $members) {
											// Sended the message

											if (count($members) > 0) {
												// Initialized members

												foreach ($members as $member) {
													// Iterating over found members

													// Declaring the buffer of localized values
													$values = null;

													// Initializing the account model
													$model_account = new account;

													// Initializing localizations
													$localizations = $model_account->localization->database->read(
														filter: fn(record $localization) => $localization->account === $member->account,
														amount: MEMBERS_SEARCH_ACCOUNT_LOCALIZATIONS_AMOUNT
													);

													if (count($localizations) > 0) {
														// Initialized the members localizations

														foreach ($localizations as $record) {
															// Iterating over localizations

															if ($record->language === $language->name) {
																// Found localization by the account language

																// Initializing localization by the account language
																$values = $record;

																// Exit (success)
																break;
															}
														}

														if (is_null($values)) {
															// Not initialized localization by the account language

															foreach ($localizations as $record) {
																// Iterating over localizations

																if ($record->language === 'en') {
																	// Found localization by english language

																	// Initializing localization by english language
																	$values = $record;

																	// Exit (success)
																	break;
																}
															}

															if (is_null($values)) {
																// Not initialized localization by english language

																// Initializing the account model
																$model_account = new account;

																// Initializing the member account
																$member_account = $model_account->database->read(
																	filter: fn(record $account) => $account->identifier === $member->account,
																	amount: 1
																)[0] ?? null;

																if ($member_account instanceof record) {
																	// Initialized the member account

																	foreach ($localizations as $record) {
																		// Iterating over localizations

																		if ($record->language === $member_account->language) {
																			// Found localization by the member account language

																			// Initializing localization by the member account language
																			$values = $record;

																			// Exit (success)
																			break;
																		}
																	}
																}

																if (is_null($values)) {
																	// Not initialized localization by the member account language

																	// Initializing localization by the first found record
																	$values = $localizations[0];
																}
															}
														}
													} else {
														// Not initialized the members localizations

														// Sending the message
														$context->sendMessage('⚠️ *' . $localization[static::PROCESS . '_not_localized'] . '*')
															->then(function (message $message) use ($context) {
																// Sended the message

																// Ending the conversation process
																$context->endConversation();
															});
													}

													// Initializing buffer of keyboard
													$keyboard = static::keyboard(
														member: $member,
														localization: $localization,
														messages: $account->authorized_messages === 1,
													);

													/* if ($account->authorized_system_distributions) {
														// Authorized access to distribution administration

														// Initializing identifier of the row for administration buttons
														$identifier = count($keyboard);

														// Initializing the row for administration buttons
														$keyboard[$identifier] ??= [];

														// Initializing the distribution accepting toggle button
														$keyboard[$identifier][] = [
															'text' => $distribution->recognized ? '🏠 ' . $localization[static::PROCESS . '_button_recognized'] : '🏚 ' . $localization['distribution_search_button_not_recognized'],
															'callback_data' => static::PROCESS . '_trust_toggle'
														];
													} */

													// Sending the message
													await($context->sendMessage(
														static::message(
															context: $context,
															language: $language,
															member: $member,
															localization: $localization,
															name: $values?->name,
															recognized: false
														),
														[
															'reply_markup' => [
																'inline_keyboard' => $keyboard,
																'disable_notification' => true,
																'remove_keyboard' => true
															],
														]
													));
												}

												if ($next) {
													// Exists the next page 

													// Sending the message
													$context->sendMessage(
														'🔎 *' . $localization[static::PROCESS . '_page_next_exists'] . '*',
														[
															'reply_markup' => [
																'inline_keyboard' => [
																	[
																		[
																			'text' => '🔒 ' . $localization[static::PROCESS . '_button_end'],
																			'callback_data' => static::PROCESS . '_end'
																		]
																	],
																	[
																		[
																			'text' => $localization[static::PROCESS . '_button_page_next'],
																			'callback_data' => static::PROCESS . '_next'
																		]
																	]
																],
																'disable_notification' => true,
																'remove_keyboard' => true
															],
														]
													)->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
												} else {
													// Not exists the next page

													// Sending the message
													$context->sendMessage(
														'🔎 *' . $localization[static::PROCESS . '_page_next_not_exists'] . '*',
														[
															'reply_markup' => [
																'inline_keyboard' => [
																	[
																		[
																			'text' => '🔒 ' . $localization[static::PROCESS . '_button_end'],
																			'callback_data' => static::PROCESS . '_end'
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

															// Ending the conversation process
															$context->endConversation();
														});
												}
											} else {
												// Not initialized members

												// Sending the message
												$context->sendMessage(
													'⚠️ *' . $localization[static::PROCESS . '_empty'] . '*',
													[
														'reply_markup' => [
															'inline_keyboard' => [
																[
																	[
																		'text' => '🔒 ' . $localization[static::PROCESS . '_button_end'],
																		'callback_data' => static::PROCESS . '_end'
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

														// Ending the conversation process
														$context->endConversation();
													});
											}
										});
									});
							} else {
								// Not found started search process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization[static::PROCESS . '_not_started'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Sending the members menu
										commands::members($context);
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
	 * Write name into the member search buffer
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
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

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
												$old = empty($search['name']) ? '_' . $localization['empty'] . '_' : $search['name'];

												// Writing into the member search process buffer
												$search['name'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem(static::PROCESS, $search)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Escaping characters for markdown
														$escaped = str_replace('-', '\\-', $new);

														// Sending the message
														$context->sendMessage('✅ *' . $localization[static::PROCESS . '_name_update_success'] . "* $old → *$escaped*")
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the search menu
																static::menu($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about name update

												// Sending the message
												$context->sendMessage('❎ *' . $localization[static::PROCESS . '_name_update_fail'])
													->then(function (message $message) use ($context) {
														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else {
											// Found restricted characters

											// Initializing title of the message
											$title = '⚠️  *' . $localization[static::PROCESS . '_name_request_restricted_characters_title'] . '*';

											// Initializing description of the message
											$description = '*' . $localization[static::PROCESS . '_name_request_restricted_characters_description'] . '* \\' . implode(', \\', $characters);

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
													button_member_search::name($context);
												});
										}
									} else {
										// Not passed maximum length check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_name_request_too_long'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter name again
												button_member_search::name($context);
											});
									}
								} else {
									// Not passed minimum length check

									// Sending the message
									$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_name_request_too_short'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Requesting to enter name again
											button_member_search::name($context);
										});
								}
							} else {
								// Failed to initialize the new name

								// Sending the message
								$context->sendMessage('📄 *' . $localization[static::PROCESS . '_name_request_not_acceptable'] . '*')
									->then(function (message  $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter name again
										button_member_search::name($context);
									});
							}
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[static::PROCESS . '_not_started'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();

									// Sending the members menu
									commands::members($context);
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
	 * Status
	 *
	 * Write status into the member search buffer
	 *
	 * @param context $context Request data from Telegram
	 * @param status $status The member status
	 *
	 * @return void
	 */
	public static function status(context $context, status $status): void
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
					->then(function ($search) use ($context, $localization, $status) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process
							try {
								// Initializing the old status
								$old = empty($search['status']) ? '_' . $localization['empty'] . '_' : $search['status']->emoji() . ' ' . $localization[static::PROCESS . '_status_' . $search['status']->value];

								// Writing into the member search process buffer
								$search['status'] = $status;

								// Writing to the telegram user buffer
								$context->setUserDataItem(static::PROCESS, $search)
									->then(function () use ($context, $localization, $status, $old) {
										// Writed to the telegram user buffer

										// Sending the message
										$context->sendMessage('✅ *' . $localization[static::PROCESS . '_status_update_success'] . "* $old → *" . $status->emoji() . ' ' . $localization[static::PROCESS . "_status_$status->value"] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Sending the search menu
												static::menu($context);
											});
									});
							} catch (error $error) {
								// Failed to send the message about status update

								// Sending the message
								$context->sendMessage('❎ *' . $localization[static::PROCESS . '_status_update_fail'])
									->then(function (message $message) use ($context) {
										// Ending the conversation process
										$context->endConversation();
									});
							}
						} else {
							// Not found started search process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization[static::PROCESS . '_not_started'] . '*');
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
	 * Message text
	 *
	 * Generate text with the distribution member infornation
	 *
	 * @param context $context Request data from Telegram
	 * @param record $member The distribution member
	 * @param language $language The account language
	 * @param array $localization The account localization
	 * @param string|null $name Localized name of the member
	 * @param bool $recognized Add the icon for recognized distribution member?
	 *
	 * @return string Generated text
	 */
	public static function message(
		context $context,
		record $member,
		language $language,
		array $localization,
		?string $name = null,
		bool $recognized = false
	): string {
		// Initializing name for the message
		if (!empty($name)) $name = str_replace('-', '\\-', $name);
		else $name = $localization[static::PROCESS . '_not_named'];

		// Initializing accepting status for the message
		$recognized = $recognized ? '🪽' : '';

		// Initializing the distribution model
		$model_distribution = new distribution;

		// Searching for the distribution
		$distribution = $model_distribution->database->read(
			filter: fn(record $distribution) => $distribution->identifier === $member->distribution,
			amount: 1
		)[0] ?? null;

		if ($distribution instanceof record) {
			// Initialized the distribution 

			// Searching for the distribution localizations records
			$distribution_localizations = $model_distribution->localization->database->read(
				filter: fn(record $localization) => $localization->distribution === $distribution->identifier,
				amount: MEMBERS_SEARCH_DISTRIBUTION_LOCALIZATIONS_AMOUNT
			);

			// Declaring the buffer of the distribution localization
			$distribution_localization = null;

			if (count($distribution_localizations) > 0) {
				// Initialized the distributions localizations

				foreach ($distribution_localizations as $record) {
					// Iterating over localizations

					if ($record->language === $language->name) {
						// Found localization by the account language

						// Initializing localization by the account language
						$distribution_localization = $record;

						// Exit (success)
						break;
					}
				}
				if (is_null($distribution_localization)) {
					// Not initialized localization by the account language

					foreach ($distribution_localizations as $record) {
						// Iterating over localizations

						if ($record->language === 'en') {
							// Found localization by english language

							// Initializing localization by english language
							$distribution_localization = $record;

							// Exit (success)
							break;
						}
					}

					if (is_null($distribution_localization)) {
						// Not initialized localization by english language

						// Initializing the account model
						$model_account = new account;

						// Initializing the distribution creator account
						$creator = $model_account->database->read(
							filter: fn(record $account) => $account->identifier === $distribution->creator,
							amount: 1
						)[0] ?? null;

						if ($creator instanceof record) {
							// Initialized the distribution creator account

							foreach ($distribution_localizations as $record) {
								// Iterating over localizations

								if ($record->language === $creator->language) {
									// Found localization by the distribution creator account language

									// Initializing localization by the distribution creator account language
									$distribution_localization = $record;

									// Exit (success)
									break;
								}
							}
						}

						if (is_null($distribution_localization)) {
							// Not initialized localization by the distribution creator account language

							// Initializing localization by the first found record
							$distribution_localization = $distribution_localizations[0];
						}
					}
				}
			} else {
				// Not initialized the distributions localizations

				// Sending the message
				$context->sendMessage('⚠️ *' . $localization[static::PROCESS . '_not_localized'] . '*')
					->then(function (message $message) use ($context) {
						// Sended the message

						// Ending the conversation process
						$context->endConversation();
					});
			}

			if ($distribution_localization instanceof record) {
				// Initialized the distribution localization

				// Initializing relation for the message
				$relation = '*' . $localization[static::PROCESS . '_distribution'] . ':* ' . $distribution_localization->name;

				// Initializing the member status
				$status = status::from($member->status) ?? status::unknown;

				// Initializing status for the message
				$_status = '*' . $localization[static::PROCESS . '_status'] . ':* ' . $status->emoji() . ' ' . $localization[static::PROCESS . "_status_$status->value"];

				/* // Initializing amount of recognized members for the message
		$members_recognized = '*' . $localization[static::PROCESS . '_recognized'] . ':* ' . 0;

		// Initializing amount of members for the message
		$amount = '*' . $localization[static::PROCESS . '_members'] . ':* ' . count(array_filter($members, fn(record $member) => $member?->status === 2));

		// Initializing planners
		$planners = '*' . $localization[static::PROCESS . '_planners'] . ':* ' . count(array_filter($members, fn(record $member) => $member?->status === 1));

		// Initializing volunteers
		$volunteers = '*' . $localization[static::PROCESS . '_volunteers'] . ':* ' . 0;

		// Initializing messages
		$messages = '*' . $localization[static::PROCESS . '_messages'] . ':* ' . 0;

		// Initializing location for the message
		$location = '*' . $localization[static::PROCESS . '_location'] . ':* ' . str_replace('.', '\\.', (empty($distribution->latitude) ? '_' . $localization['empty'] . '_' : round($distribution->latitude, 6)) . ', ' . (empty($distribution->longitude) ? '_' . $localization['empty'] . '_' : round($distribution->longitude, 6))); */

				// Exit (success)
				return <<<TXT
					$distribution->identifier:$member->identifier:$member->account *$name* $recognized

					$relation
					$_status
					TXT;
			} else {
				// Not initialized the distribution localization

			}
		} else {
			// Not initialized the distribution

		}
	}

	/**
	 * Keyboard (inline)
	 *
	 * Generate inline keyboard for the member
	 *
	 * @param record $member The member
	 * @param array $localization The account localization
	 * @param bool $messages Generate messages buttons? (is the account allowed to send messages?)
	 *
	 * @return array Generated inline keyboard
	 */
	public static function keyboard(
		record $member,
		array $localization,
		bool $messages = false,
	): array {
		// Initializing the buffer of keyboard 
		$keyboard = [];

		if ($messages) {
			// Requested messages buttons

			// Writing the messages buttons into the buffer of joinings
			$keyboard[] = [
				[
					'text' => '✉️ ' . $localization[static::PROCESS . '_button_message'],
					'callback_data' => static::PROCESS . '_message'
				]
			];
		}

		// Exit (success)
		return $keyboard;
	}
}
