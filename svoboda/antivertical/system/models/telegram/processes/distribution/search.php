<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\processes\distribution;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\membership,
	svoboda\antivertical\models\telegram\buttons\distribution\search as button_distribution_search,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\telegram\commands,
	svoboda\antivertical\models\traits\coordinates;

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
 * Distribution search process
 *
 * @package svoboda\antivertical\models\telegram\processes\distribution
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
	public const string PROCESS = 'distribution_search';

	/**
	 * Start
	 *
	 * Starting the distribution search process
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
					$context->getUserDataItem(static::PROCESS)
						->then(function (?array $search) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($search) {
								// Found started search process

								// Sending the message
								$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_continiued'] . '*')
									->then(function (message $message) use ($context, $account, $language, $localization) {
										// Sended the message

										// Sending the list of found distributions and menu
										static::menu($context);
									});
							} else {
								// Not found started search process

								// Initializing the distribution search buffer
								$search = [
									'name' => null,
									'location' => [
										'latitude' => null,
										'longitude' => null,
										'distance' => DISTRIBUTIONS_SEARCH_DISTRIBUTION_DISTANCE
									],
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

												// Sending the list of found distributions and menu
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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
	 * Ending the distribution search process
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function end(context $context): void
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

											// Sending the distributions menu
											commands::distributions($context);
										});
								});
						} else {
							// Not found started search process

							// Ending the conversation process
							$context->endConversation();

							// Sending the distributions menu
							commands::distributions($context);
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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
	 * Sends the list of found distributions and menu with parameters: text, location, distance
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	protected static function menu(context $context): void
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
					$context->getUserDataItem(static::PROCESS)
						->then(function (?array $search) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($search) {
								// Found started search process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => empty($search['name']) ? '🔴 ' . $localization[static::PROCESS . '_button_name'] : '🟢 ' . $localization['distribution_search_button_name'] . ': ' . $search['name'],
											'callback_data' => static::PROCESS . '_name'
										]
									],
									[
										[
											'text' => empty($search['location']['latitude']) || empty($search['location']['longitude']) ? '🔴 ' . $localization[static::PROCESS . '_button_location'] : '🟢 ' . $localization['distribution_search_button_location'] . ': ' . $search['location']['latitude'] . ', ' . $search['location']['longitude'],
											'callback_data' => static::PROCESS . '_location'
										]
									],
									[
										[
											'text' => empty($search['location']['distance']) ? '🔴 ' . $localization[static::PROCESS . '_button_distance'] : '🟢 ' . $localization['distribution_search_button_distance'] . ': ' . $search['location']['distance'] . ' ' . $localization['distribution_search_km'],
											'callback_data' => static::PROCESS . '_distance'
										]
									]
								];

								// Ending the conversation process
								$context->endConversation()
									->then(function () use ($context, $account, $language, $localization, $search, $keyboard) {
										// Deinitialized the conversation process

										// Initializing the distribution model
										$model_distribution = new distribution;

										// Initializing amount of readed distributions per page
										$page = DISTRIBUTIONS_SEARCH_PAGE;

										if (
											empty($search['name']) &&
											empty($search['location']['latitude']) &&
											empty($search['location']['longitude'])
										) {
											// Each search parameter is empty

											// Search for distributions
											$distributions = $model_distribution->database->read(
												amount: $page + 1,
												offset: $search['page'] < 1 ? 0 : $page * $search['page']
											);
										} else {
											// The search buffer has at least one parameter

											// Search for distributions
											$distributions = $model_distribution->database->read(
												filter: function (record $distribution) use ($search, $model_distribution) {
													// Initializing the matched buffer
													$matched = false;

													if (!empty($search['name'])) {
														// Requested search by name

														// Initializing localizations
														$localizations = $model_distribution->localization->database->read(
															filter: fn(record $localization) => $localization->distribution === $distribution->identifier,
															amount: DISTRIBUTIONS_SEARCH_DISTRIBUTION_LOCALIZATIONS_AMOUNT
														);

														// Initializing the result status
														$result = false;

														foreach ($localizations as $localization) {
															// Iterating over localizations

															// Splitting the localization name into parts
															$parts = preg_split('/[\s]/', $localization->name);

															foreach ($parts as $part) {
																// Iterating over localization name parts

																if (levenshtein($part, $search['name']) <= DISTRIBUTIONS_SEARCH_DISTRIBUTION_NAME_LEVENSHTEIN_DISTANCE) {
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

													if (
														!empty($search['location']['latitude']) &&
														!empty($search['location']['longitude']) &&
														!empty($search['location']['distance'])
													) {
														// Requested search by location

														if (
															static::vincenty(
																$search['location']['latitude'],
																$search['location']['longitude'],
																$distribution->latitude,
																$distribution->longitude
															) <= $search['location']['distance'] * 1000
														) {
															// Matched by distance to distribution

															// Reinitializing the matched buffer
															$matched = true;
														} else {
															// Not matched by distance to distribution

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
										$next = count($distributions) > $page;

										// Deleting the additional readed distribution
										unset($distributions[$page]);

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
										)->then(function (message $message) use ($context, $account, $language, $localization, $search, $page, $next, $distributions, $model_distribution) {
											// Sended the message

											if (count($distributions) > 0) {
												// Initialized distributions

												// Initializing the membership model
												$model_membership = new membership;

												foreach ($distributions as $distribution) {
													// Iterating over found distributions

													// Declaring the buffer of localized values
													$values = null;

													// Initializing localizations
													$localizations = $model_distribution->localization->database->read(
														filter: fn(record $localization) => $localization->distribution === $distribution->identifier,
														amount: DISTRIBUTIONS_SEARCH_DISTRIBUTION_LOCALIZATIONS_AMOUNT
													);

													if (count($localizations) > 0) {
														// Initialized the distributions localizations

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

																// Initializing the distribution creator account
																$creator = $model_account->database->read(
																	filter: fn(record $account) => $account->identifier === $distribution->creator,
																	amount: 1
																)[0] ?? null;

																if ($creator instanceof record) {
																	// Initialized the distribution creator account

																	foreach ($localizations as $record) {
																		// Iterating over localizations

																		if ($record->language === $creator->language) {
																			// Found localization by the distribution creator account language

																			// Initializing localization by the distribution creator account language
																			$values = $record;

																			// Exit (success)
																			break;
																		}
																	}
																}

																if (is_null($values)) {
																	// Not initialized localization by the distribution creator account language

																	// Initializing localization by the first found record
																	$values = $localizations[0];
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

													// Searching for the membership record
													$membership = $model_membership->database->read(
														filter: fn(record $membership) => $membership->distribution === $distribution->identifier && $membership->account === $account->identifier,
														amount: 1
													)[0] ?? null;

													// Searching for the another membership records
													$another = $model_membership->database->read(
														filter: fn(record $membership) => $membership->distribution !== $distribution->identifier && $membership->account === $account->identifier && $membership->status !== 0,
														amount: 1
													)[0] ?? null;

													// Initializing buffer of keyboard
													$keyboard = static::keyboard(
														distribution: $distribution,
														membership: $another ?? $membership,
														localization: $localization,
														messages: $account->authorized_messages === 1,
														memberships: $account->authorized_memberships === 1,
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
															distribution: $distribution,
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
												// Not initialized distributions

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

										// Sending the distributions menu
										commands::distributions($context);
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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
	 * Write search name into the distribution search buffer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function name(context $context): void
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
				$context->getUserDataItem(static::PROCESS)
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the new search name
							$new = $context->getMessage()->getText();

							if (!empty($new)) {
								// Initialized the new search name

								if (mb_strlen($new) >= 3) {
									// Passed minimum length check

									if (mb_strlen($new) <= 64) {
										// Passed maximum length check

										// Search for restricted characters
										preg_match_all('/[\W\d]/u', $new, $matches);

										// Declaring the buffer of found restricted characters (except spaces)
										$characters = [];

										// Declaring the counter of found spaces
										$spaces = 0;

										foreach ($matches[0] as $match) {
											// Iterating over found restricted characters 

											if ($match === ' ') {
												// Space-character

												// Increasing the counter of found spaces
												++$spaces;
											} else {
												// Not space-character

												// Writing into the buffer of found restricted characers (except spaces)
												$characters[] = $match;
											}
										}

										if (empty($characters)) {
											// Not found restricted characters

											try {
												// Initializing the old search name
												$old = empty($search['name']) ? '_' . $localization['empty'] . '_' : $search['name'];

												// Writing into the distribution search process buffer
												$search['name'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem(static::PROCESS, $search)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Sending the message
														$context->sendMessage('✅ *' . $localization[static::PROCESS . '_name_update_success'] . "* $old → *$new*")
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the distribution search menu
																static::menu($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about search name update

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

													// Requesting to enter search name again
													button_distribution_search::name($context);
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

												// Requesting to enter search name again
												button_distribution_search::name($context);
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

											// Requesting to enter search name again
											button_distribution_search::name($context);
										});
								}
							} else {
								// Failed to initialize the new search name

								// Sending the message
								$context->sendMessage('📄 *' . $localization[static::PROCESS . '_name_request_not_acceptable'] . '*')
									->then(function (message  $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter search name again
										button_distribution_search::name($context);
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

									// Sending the distributions menu
									commands::distributions($context);
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
				->then(function (message $message) use ($context) {
					// Sended the message

					// Ending the conversation process
					$context->endConversation();
				});
		}
	}

	/**
	 * Location
	 *
	 * Write location into the distribution search buffer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function location(context $context): void
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
				$context->getUserDataItem(static::PROCESS)
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the new location
							preg_match_all('/(\-?\d{1,2})\.?(\d*)/', $context->getMessage()->getText(), $matches);

							if ($matches[0]) {
								// Initialized the new location

								// Initializing the new latitude
								$latitude = round((float) $matches[0][0], 6);

								// Initializing the new longitude
								$longitude = round((float) $matches[0][1], 6);

								if (!empty($latitude) && !empty($longitude)) {
									// Initialized the new latitude and the new longitude

									if ($latitude >= 0) {
										// Passed latitude minimum value check

										if ($latitude <= 90) {
											// Passed latitude maximum value check

											if ($longitude >= 0) {
												// Passed longitude minimum value check

												if ($longitude <= 180) {
													// Passed longitude maximum value check

													try {
														// Initializing the old location
														$old = str_replace('.', '\\.', (empty($search['location']['latitude']) ? '_' . $localization['empty'] . '_' : $search['location']['latitude']) . ', ' . (empty($search['location']['longitude']) ? '_' . $localization['empty'] . '_' : $search['location']['longitude']));

														// Writing into the distribution search process buffer
														$search['location']['latitude'] = $latitude;
														$search['location']['longitude'] = $longitude;

														// Writing to the telegram user buffer
														$context->setUserDataItem(static::PROCESS, $search)
															->then(function () use ($context, $localization, $latitude, $longitude, $old) {
																// Writed to the telegram user buffer

																// Initializing the new location
																$new = str_replace('.', '\\.', $latitude . ', ' . $longitude);

																// Sending the message
																$context->sendMessage('✅ *' . $localization[static::PROCESS . '_location_update_success'] . "*\n$old → *$new*")
																	->then(function (message $message) use ($context) {
																		// Sended the message

																		// Sending the distribution search menu
																		static::menu($context);
																	});
															});
													} catch (error $error) {
														// Failed to send the message about name update

														// Sending the message
														$context->sendMessage('❎ *' . $localization[static::PROCESS . '_location_update_fail'])
															->then(function (message $message) use ($context) {
																// Sended the message

																// Ending the conversation process
																$context->endConversation();
															});
													}
												} else {
													// Not passed longitude maximum value check

													// Sending the message
													$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_location_send_longitude_too_big'] . '*')
														->then(function (message $message) use ($context) {
															// Sended the message

															// Ending the conversation process
															$context->endConversation();

															// Requesting to enter locaztion again
															button_distribution_search::location($context);
														});
												}
											} else {
												// Not passed longitude minimum value check

												// Sending the message
												$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_location_send_longitude_too_small'] . '*')
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();

														// Requesting to enter locaztion again
														button_distribution_search::location($context);
													});
											}
										} else {
											// Not passed latitude maximum value check

											// Sending the message
											$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_location_send_latitude_too_big'] . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();

													// Requesting to enter locaztion again
													button_distribution_search::location($context);
												});
										}
									} else {
										// Not passed latitude minimum value check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_location_send_latitude_too_small'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter locaztion again
												button_distribution_search::location($context);
											});
									}
								} else {
									// Failed to initialize the new location

									// Sending the message
									$context->sendMessage('📄 *' . $localization[static::PROCESS . '_location_send_not_acceptable'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Requesting to send location again
											button_distribution_search::location($context);
										});
								}
							} else {
								// Not initialized the new location

								// Sending the message
								$context->sendMessage('📄 *' . $localization[static::PROCESS . '_location_send_not_acceptable'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to send locaztion again
										button_distribution_search::location($context);
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

									// Sending the distributions menu
									commands::distributions($context);
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
	 * Distance
	 *
	 * Write location distance into the distribution search buffer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function distance(context $context): void
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
				$context->getUserDataItem(static::PROCESS)
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the new distance
							$new = $context->getMessage()->getText();

							if (!empty($new)) {
								// Initialized the new distance

								// Search for restricted characters
								preg_match_all('/[^\d]/u', $new, $matches);

								// Initializing found restricted characters
								$characters = $matches[0];

								if (empty($characters)) {
									// Not found restricted characters

									// Converting the new value from string to integer
									$new = (int) $new;

									if ($new >= 0) {
										// Passed minimum value check

										if ($new <= 600) {
											// Passed maximum value check

											try {
												// Initializing the old name
												$old = empty($search['location']['distance']) ? '_' . $localization['empty'] . '_' : $search['location']['distance'];

												// Writing into the distribution search process buffer
												$search['location']['distance'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem(static::PROCESS, $search)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Sending the message
														$context->sendMessage('✅ *' . $localization[static::PROCESS . '_distance_update_success'] . "* $old \(" . $localization['distribution_search_km'] . '\) ' . " → *$new* \(" . $localization['distribution_search_km'] . '\)')
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the distribution search menu
																static::menu($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about name update

												// Sending the message
												$context->sendMessage('❎ *' . $localization[static::PROCESS . '_distance_update_fail'])
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else {
											// Not passed maximum value check

											// Sending the message
											$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_distance_request_too_long_km'] . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();

													// Requesting to enter distance again
													button_distribution_search::distance($context);
												});
										}
									} else {
										// Not passed minimum value check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization[static::PROCESS . '_distance_request_too_short_km'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter ditance again
												button_distribution_search::distance($context);
											});
									}
								} else {
									// Found restricted characters

									// Initializing title of the message
									$title = '⚠️  *' . $localization[static::PROCESS . '_distance_request_restricted_characters_title'] . '*';

									// Initializing description of the message
									$description = '*' . $localization[static::PROCESS . '_distance_request_restricted_characters_description'] . '* \\' . implode(', \\', $characters);

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

											// Requesting to enter distance again
											button_distribution_search::distance($context);
										});
								}
							} else {
								// Failed to initialize the new distance

								// Sending the message
								$context->sendMessage('📄 *' . $localization[static::PROCESS . '_distance_request_not_acceptable'] . '*')
									->then(function (message  $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter distance again
										button_distribution_search::distance($context);
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

									// Sending the distributions menu
									commands::distributions($context);
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
			$context->sendMessage('⚠️ *Failed to initialize the account*')
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
	 * Generate text with the distribution infornation
	 *
	 * @param record $distribution The distribution
	 * @param array $localization The account localization
	 * @param string|null $name Localized name of the distribution
	 * @param bool $recognized Add the icon for recognized distributions?
	 *
	 * @return string Generated text
	 */
	public static function message(
		record $distribution,
		array $localization,
		?string $name = null,
		bool $recognized = false
	): string {
		// Initializing name for the message
		$name = $name ?? $localization[static::PROCESS . '_not_named'];

		// Initializing accepting status for the message
		$recognized = $recognized ? '🪽' : '';

		// Initializing the membership model
		$model_membership = new membership;

		// Searching for memberships records
		$memberships = $model_membership->database->read(
			filter: fn(record $record) => $record->distribution === $distribution->identifier,
			amount: DISTRIBUTIONS_SEARCH_MEMBERSHIPS_AMOUNT
		) ?? [];

		// Initializing amount of recognized memberships for the message
		$memberships_recognized = '*' . $localization[static::PROCESS . '_recognized'] . ':* ' . 0;

		// Initializing amount of memberships for the message
		$amount = '*' . $localization[static::PROCESS . '_memberships'] . ':* ' . count(array_filter($memberships, fn(record $membership) => $membership?->status === 2));

		// Initializing planners
		$planners = '*' . $localization[static::PROCESS . '_planners'] . ':* ' . count(array_filter($memberships, fn(record $membership) => $membership?->status === 1));

		// Initializing volunteers
		$volunteers = '*' . $localization[static::PROCESS . '_volunteers'] . ':* ' . 0;

		// Initializing messages
		$messages = '*' . $localization[static::PROCESS . '_messages'] . ':* ' . 0;

		// Initializing location for the message
		$location = '*' . $localization[static::PROCESS . '_location'] . ':* ' . str_replace('.', '\\.', (empty($distribution->latitude) ? '_' . $localization['empty'] . '_' : round($distribution->latitude, 6)) . ', ' . (empty($distribution->longitude) ? '_' . $localization['empty'] . '_' : round($distribution->longitude, 6)));

		// Exit (success)
		return <<<TXT
			$distribution->identifier *$name* $recognized

			$amount
			$memberships_recognized
			$planners
			$volunteers

			$messages

			$location
		TXT;
	}

	/**
	 * Keyboard (inline)
	 *
	 * Generate inline keyboard for the distribution
	 *
	 * @param record $distribution The distribution
	 * @param record|null $membership The distribution membership
	 * @param array $localization The account localization
	 * @param bool $planned Is the account planned to join to the distribution?
	 * @param bool $joined Is the account joined to the distribution?
	 * @param bool $messages Generate messages buttons? (is the account allowed to send messages?)
	 * @param bool $memberships Generate memberships buttons? (is the account allowed to join?)
	 *
	 * @return array Generated inline keyboard
	 */
	public static function keyboard(
		record $distribution,
		?record $membership,
		array $localization,
		bool $messages = false,
		bool $memberships = false,
	): array {
		// Initializing the buffer of keyboard 
		$keyboard = [
			[
				[
					'text' => '🗺 ' . $localization[static::PROCESS . '_button_map'],
					'web_app' => [
						/* 'url' => 'https://telegram.map.svoboda.works?distribution=' . $distribution->identifier */
						'url' => "https://www.openstreetmap.org/#map=12/$distribution->latitude/$distribution->longitude"
					]
				]
			],
			[
				/* [
					'text' => ' ' . $localization[static::PROCESS . '_button_vhod'],
					'callback_data' => static::PROCESS . '_vhod'
				], */
				[
					'text' => '🤟 ' . $localization[static::PROCESS . '_button_volunteers'],
					'callback_data' => static::PROCESS . '_volunteers'
				],
				[
					'text' => '🐣 ' . $localization[static::PROCESS . '_button_memberships'],
					'callback_data' => static::PROCESS . '_memberships'
				]
			]
		]
			/* [
				[
					'text' => ' ' . $localization[static::PROCESS . '_button_telegram'],
					'url' => 'https://t.me/'
				],
			], */;

		if ($memberships) {
			// Requested memberships buttons

			if ($membership instanceof record) {
				// Initialized the membership

				if ($membership->distribution === $distribution->identifier) {
					// The membership distribution matched the distribution

					if ($membership->status === 2) {
						// The membership was joined to the distribution

						// Writing the joining buttons into the buffer of keyboard
						$keyboard[] = [
							[
								'text' => '🧳 ' . $localization[static::PROCESS . '_button_leave'],
								'callback_data' => static::PROCESS . '_leave'
							]
						];
					} else if ($membership->status === 1) {
						// The membership was planning to join to the distribution

						// Writing the joining buttons into the buffer of keyboard
						$keyboard[] = [
							[
								'text' => '❌ ' . $localization[static::PROCESS . '_button_unplan'],
								'callback_data' => static::PROCESS . '_unplan'
							],
							[
								'text' => '🧳 ' . $localization[static::PROCESS . '_button_join'],
								'callback_data' => static::PROCESS . '_join'
							]
						];
					} else {
						// The membership status is unknown

						// Writing the joining buttons into the buffer of keyboard
						$keyboard[] = [
							[
								'text' => '📅 ' . $localization[static::PROCESS . '_button_plan'],
								'callback_data' => static::PROCESS . '_plan'
							],
							[
								'text' => '🧳 ' . $localization[static::PROCESS . '_button_join'],
								'callback_data' => static::PROCESS . '_join'
							]
						];
					}
				} else {
					// The membership distribution not matched the distribution

					if ($membership->status === 2) {
						// The membership was joined to the distribution

					} else if ($membership->status === 1) {
						// The membership was planning to join to the distribution

					} else {
						// The membership status is unknown

						// Writing the joining buttons into the buffer of keyboard
						$keyboard[] = [
							[
								'text' => '📅 ' . $localization[static::PROCESS . '_button_plan'],
								'callback_data' => static::PROCESS . '_plan'
							],
							[
								'text' => '🧳 ' . $localization[static::PROCESS . '_button_join'],
								'callback_data' => static::PROCESS . '_join'
							]
						];
					}
				}
			} else {
				// Not initialized the membership

				// Writing the joining buttons into the buffer of keyboard
				$keyboard[] = [
					[
						'text' => '📅 ' . $localization[static::PROCESS . '_button_plan'],
						'callback_data' => static::PROCESS . '_plan'
					],
					[
						'text' => '🧳 ' . $localization[static::PROCESS . '_button_join'],
						'callback_data' => static::PROCESS . '_join'
					]
				];
			}
		}

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
