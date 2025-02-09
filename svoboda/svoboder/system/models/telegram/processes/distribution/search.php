<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\processes\distribution;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\account,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\localizations\distribution as distribution_localization,
	svoboda\svoboder\models\telegram\buttons\distribution\search as button_distribution_search,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\telegram\commands,
	svoboda\svoboder\models\traits\coordinates;

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
 * @package svoboda\svoboder\models\telegram\processes\distribution
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
					$context->getUserDataItem('distribution_search')
						->then(function ($distribution) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started search process

								// Sending the message
								$context->sendMessage('🗂 *' . $localization['distribution_search_continiued'] . '*')
									->then(function (message $message) use ($context, $account, $language, $localization) {
										// Sended the message

										// Sending the list of found distributions and menu
										static::search($context);
									});
							} else {
								// Not found started search process

								// Initializing the distribution search buffer
								$search = [
									'text' => null,
									'location' => [
										'latitude' => null,
										'longitude' => null,
										'distance' => 300
									],
									'page' => 0,
									'confirmed' => true
								];

								// Writing to the telegram user buffer
								$context->setUserDataItem('distribution_search', $search)
									->then(function () use ($context, $account, $localization) {
										// Writed to the telegram user buffer

										// Sending the message
										$context->sendMessage('🗂 *' . $localization['distribution_search_started'] . '*')
											->then(function (message $message) use ($context, $account, $localization) {
												// Sended the message

												// Sending the list of found distributions and menu
												static::search($context);
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

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem('distribution_search')
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Deleting from the telegram user buffer
							$context->deleteUserDataItem('distribution_search', $search)
								->then(function () use ($context, $search, $localization) {
									// Deleted from the telegram user buffer

									// Sending the message
									$context->sendMessage('🗂 *' . $localization['distribution_search_ended'] . '*')
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

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*');
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
	 * Sends the list of found distributions and menu with parameters: text
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	protected static function search(context $context): void
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
					$context->getUserDataItem('distribution_search')
						->then(function ($search) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($search) {
								// Found started search process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => empty($search['text']) ? '🔴 ' . $localization['distribution_search_button_text'] : '🟢 ' . $localization['distribution_search_button_text'] . ': ' . $search['text'],
											'callback_data' => 'distribution_search_text'
										]
									],
									[
										[
											'text' => $search['confirmed'] ? '🪽 ' . $localization['distribution_search_button_confirmed'] : '🏘 ' . $localization['distribution_search_button_confirmed_all'],
											'callback_data' => 'distribution_search_confirmed'
										]
									],
									[
										[
											'text' => empty($search['location']['latitude']) || empty($search['location']['longitude']) ? '🔴 ' . $localization['distribution_search_button_location'] : '🟢 ' . $localization['distribution_search_button_location'] . ': ' . $search['location']['latitude'] . ', ' . $search['location']['longitude'],
											'callback_data' => 'distribution_search_location'
										]
									],
									[
										[
											'text' => empty($search['location']['distance']) ? '🔴 ' . $localization['distribution_search_button_distance'] : '🟢 ' . $localization['distribution_search_button_distance'] . ': ' . $search['location']['distance'] . ' ' . $localization['distribution_search_km'],
											'callback_data' => 'distribution_search_distance'
										]
									]
								];

								// Ending the conversation process
								$context->endConversation()
									->then(function () use ($context, $language, $localization, $search, $keyboard) {
										// Deinitialized the conversation process

										// Initializing the distribution model
										$model = new distribution;

										// Initializing amount of readed distributions per page
										$page = 3;

										if (
											empty($search['text']) &&
											empty($search['location']['latitude']) &&
											empty($search['location']['longitude']) &&
											$search['confirmed'] === false
										) {
											// Each search parameter is empty

											// Search for distributions
											$distributions = $model->database->read(
												amount: $page + 1,
												offset: $search['page'] < 1 ? 0 : $page * $search['page']
											);
										} else {
											// The search buffer has at least one parameter

											// Search for distributions
											$distributions = $model->database->read(
												filter: function (record $record) use ($search) {
													// Initializing the matched buffer
													$matched = false;

													if ($search['confirmed']) {
														// Requested only confirmed

														if ($record->confirmed === 1) {
															// The distribution is confirmed

															// Reinitializing the matched buffer
															$matched = true;
														} else {
															// The distribution is not confirmed

															// Exit (success)
															return false;
														}
													}

													if (!empty($search['text'])) {
														// Requested search by text

														// Initializing the distribution localization model
														$model = new distribution_localization;

														// Initializing localizations
														$localizations = $model->database->read(
															filter: fn(record $_record) => $_record->distribution === $record->identifier,
															amount: 100
														);

														foreach ($localizations as $localization) {
															// Iterating over localizations

															if (levenshtein($localization->name, $search['text']) <= 4) {
																// Names matched by Levenshtein function

																// Reinitializing the matched buffer
																$matched = true;

																// Exit (success)
																break;
															} else {
																// Names not matched by Levenshtein function

																// Exit (success)
																return false;
															}
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
																$record->latitude,
																$record->longitude
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
										$title = '🔎 *' . $localization['distribution_search_title'] . '*';

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
										)->then(function (message $message) use ($context, $language, $localization, $search, $page, $next, $distributions) {
											// Sended the message

											if (count($distributions) > 0) {
												// Initialized distributions

												// Initializing the distributions localization model
												$model = new distribution_localization;

												foreach ($distributions as $distribution) {
													// Iterating over found distributions

													// Declaring the buffer of localized values
													$values = null;

													// Initializing localizations
													$localizations = $model->database->read(
														filter: function (record $record) use ($distribution) {
															// Exit (success)
															return $record->distribution === $distribution->identifier;
														},
														amount: 300
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
																$model = new account;

																// Initializing the distribution creator account
																$creator = $model->database->read(
																	filter: function (record $record) use ($distribution) {
																		// Exit (success)
																		return $record->identifier === $distribution->creator;
																	},
																	amount: 1
																)[0] ?? null;

																if ($creator instanceof record) {
																	// Initialized the distribution creator account

																	foreach ($localizations as $record) {
																		// Iterating over localizations

																		if ($record->language === $creator->language) {
																			// Found localization by the distribution creator language

																			// Initializing localization by the distribution creator language
																			$values = $record;

																			// Exit (success)
																			break;
																		}
																	}
																}

																if (is_null($values)) {
																	// Not initialized localization by the distribution creator language

																	// Initializing localization by the distribution creator language
																	$values = $localizations[0];
																}
															}
														}
													} else {
														// Not initialized the distributions localizations

														// Sending the message
														$context->sendMessage('⚠️ *' . $localization['distribution_search_not_localized'] . '*')
															->then(function (message $message) use ($context) {
																// Sended the message

																// Ending the conversation process
																$context->endConversation();
															});
													}

													// Initializing name for the message
													$name = $values instanceof record ? $values->name : $localization['distribution_search_not_named'];

													// Initializing accepting status for the message
													$accepted = $distribution->accepted ? '🪽' : '';

													// Initializing members of the distribution
													$members = [];

													// Initializing amount of members for the message
													$amount = '*' . $localization['distribution_search_members'] . ':* ' . count($members);

													// Initializing location for the message
													$location = '*' . $localization['distribution_search_location'] . ':* ' . str_replace('.', '\\.', (empty($distribution->latitude) ? '_' . $localization['empty'] . '_' : round($distribution->latitude, 6)) . ', ' . (empty($distribution->longitude) ? '_' . $localization['empty'] . '_' : round($distribution->longitude, 6)));

													// Sending the message
													$context->sendMessage(
														<<<TXT
														*$distribution->identifier* \- $name $accepted

														$amount
														$location
														TXT,
														[
															'reply_markup' => [
																'inline_keyboard' => [
																	[
																		[
																			'text' => '🗺 ' . $localization['distribution_search_button_map'],
																			'web_app' => [
																				'url' => 'https://telegram.map.svoboda.works?distribution=' . $distribution->identifier
																			]
																		],
																		/* [
																			'text' => ' ' . $localization['distribution_search_button_vhod'],
																			'callback_data' => 'distribution_search_vhod'
																		], */
																		[
																			'text' => '🐣 ' . $localization['distribution_search_button_members'],
																			'callback_data' => 'distribution_search_members'
																		],
																	],
																	/* [
																		[
																			'text' => ' ' . $localization['distribution_search_button_telegram'],
																			'url' => 'https://t.me/'
																		],
																	], */
																	[
																		[
																			'text' => '✉️ ' . $localization['distribution_search_button_message'],
																			'callback_data' => 'distribution_search_message'
																		],
																	],
																],
																'disable_notification' => true,
																'remove_keyboard' => true
															],
														]
													)->then(function (message $message) use ($context, $next) {
														// Sended the message

													});
												}

												if ($next) {
													// Exists the next page 

													// Sending the message
													$context->sendMessage(
														'🔎 *' . $localization['distribution_search_page_next_exists'] . '*',
														[
															'reply_markup' => [
																'inline_keyboard' => [
																	[
																		[
																			'text' => $localization['distribution_search_button_end'],
																			'callback_data' => 'distribution_search_end'
																		]
																	],
																	[
																		[
																			'text' => $localization['distribution_search_button_page_next'],
																			'callback_data' => 'distribution_search_next'
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
														'🔎 *' . $localization['distribution_search_page_next_not_exists'] . '*',
														[
															'reply_markup' => [
																'inline_keyboard' => [
																	[
																		[
																			'text' => $localization['distribution_search_button_end'],
																			'callback_data' => 'distribution_search_end'
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
													'⚠️ *' . $localization['distribution_search_empty'] . '*',
													[
														'reply_markup' => [
															'inline_keyboard' => [
																[
																	[
																		'text' => $localization['distribution_search_button_end'],
																		'callback_data' => 'distribution_search_end'
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
								$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();
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
	 * Text
	 *
	 * Write search text into the distribution search buffer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function text(context $context): void
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
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the new search text
							$new = $context->getMessage()->getText();

							if (!empty($new)) {
								// Initialized the new text

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
												// Initializing the old search text
												$old = empty($search['text']) ? '_' . $localization['empty'] . '_' : $search['text'];

												// Writing into the distribution search process buffer
												$search['text'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem('distribution_search', $search)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Sending the message
														$context->sendMessage('✅ *' . $localization['distribution_search_text_update_success'] . "* $old → *$new*")
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the search menu
																static::search($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about search text update

												// Sending the message
												$context->sendMessage('❎ *' . $localization['distribution_search_text_update_fail'])
													->then(function (message $message) use ($context) {
														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else {
											// Found restricted characters

											// Initializing title of the message
											$title = '⚠️  *' . $localization['distribution_search_text_request_restricted_characters_title'] . '*';

											// Initializing description of the message
											$description = '*' . $localization['distribution_search_text_request_restricted_characters_description'] . '* \\' . implode(', \\', $characters);

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

													// Requesting to enter search text again
													button_distribution_search::text($context);
												});
										}
									} else {
										// Not passed maximum length check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization['distribution_search_text_request_too_long'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter search text again
												button_distribution_search::text($context);
											});
									}
								} else {
									// Not passed minimum length check

									// Sending the message
									$context->sendMessage('⚠️  *' . $localization['distribution_search_text_request_too_short'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Requesting to enter search text again
											button_distribution_search::text($context);
										});
								}
							} else {
								// Failed to initialize the new search text

								// Sending the message
								$context->sendMessage('📄 *' . $localization['distribution_search_text_request_not_acceptable'] . '*')
									->then(function (message  $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter search text again
										button_distribution_search::text($context);
									});
							}
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
	 * Confirmed
	 *
	 * Toggle filter by confirmation status into the distribution search buffer
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function confirmed(context $context): void
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
					->then(function ($search) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Iverting the "confirmed" parameter
							$search['confirmed'] = !$search['confirmed'];

							// Writing to the telegram user buffer
							$context->setUserDataItem('distribution_search', $search)
								->then(function () use ($context, $search, $localization) {
									// Writed to the telegram user buffer

									// Initializing the old value for the message
									$old = $search['confirmed'] ? $localization['distribution_search_button_confirmed_all'] : $localization['distribution_search_button_confirmed'];

									// Initializing the new value for the message
									$new = $search['confirmed'] ? $localization['distribution_search_button_confirmed'] : $localization['distribution_search_button_confirmed_all'];

									// Sending the message
									$context->sendMessage('✅ *' . $localization['distribution_search_confirmed_update_success'] . "* $old → *$new*")
										->then(function (message $message) use ($context) {
											// Sended the message

											// Sending the search menu
											static::search($context);
										});
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

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem('distribution_search')
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
														$context->setUserDataItem('distribution_search', $search)
															->then(function () use ($context, $localization, $latitude, $longitude, $old) {
																// Writed to the telegram user buffer

																// Initializing the new location
																$new = str_replace('.', '\\.', $latitude . ', ' . $longitude);

																// Sending the message
																$context->sendMessage('✅ *' . $localization['distribution_search_location_update_success'] . "*\n$old → *$new*")
																	->then(function (message $message) use ($context) {
																		// Sended the message

																		// Sending the search menu
																		static::search($context);
																	});
															});
													} catch (error $error) {
														// Failed to send the message about name update

														// Sending the message
														$context->sendMessage('❎ *' . $localization['distribution_search_location_update_fail'])
															->then(function (message $message) use ($context) {
																// Sended the message

																// Ending the conversation process
																$context->endConversation();
															});
													}
												} else {
													// Not passed longitude maximum value check

													// Sending the message
													$context->sendMessage('⚠️  *' . $localization['distribution_search_location_send_longitude_too_big'] . '*')
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
												$context->sendMessage('⚠️  *' . $localization['distribution_search_location_send_longitude_too_small'] . '*')
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
											$context->sendMessage('⚠️  *' . $localization['distribution_search_location_send_latitude_too_big'] . '*')
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
										$context->sendMessage('⚠️  *' . $localization['distribution_search_location_send_latitude_too_small'] . '*')
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
									$context->sendMessage('📄 *' . $localization['distribution_search_location_send_not_acceptable'] . '*')
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
								$context->sendMessage('📄 *' . $localization['distribution_search_location_send_not_acceptable'] . '*')
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
							$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*');
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
			$context->sendMessage('⚠️ *Failed to initialize your Telegram account*')
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

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Reading from the telegram user buffer
				$context->getUserDataItem('distribution_search')
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

								// Initializing the buffer of found restricted characters (except spaces)
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
												$context->setUserDataItem('distribution_search', $search)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Sending the message
														$context->sendMessage('✅ *' . $localization['distribution_search_distance_update_success'] . "* $old \(" . $localization['distribution_search_km'] . '\) ' . " → *$new* \(" . $localization['distribution_search_km'] . '\)')
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the search menu
																static::search($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about name update

												// Sending the message
												$context->sendMessage('❎ *' . $localization['distribution_search_distance_update_fail'])
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else {
											// Not passed maximum value check

											// Sending the message
											$context->sendMessage('⚠️  *' . $localization['distribution_search_distance_request_too_long_km'] . '*')
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
										$context->sendMessage('⚠️  *' . $localization['distribution_search_distance_request_too_short_km'] . '*')
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
									$title = '⚠️  *' . $localization['distribution_search_distance_request_restricted_characters_title'] . '*';

									// Initializing description of the message
									$description = '*' . $localization['distribution_search_distance_request_restricted_characters_description'] . '* \\' . implode(', \\', $characters);

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
								$context->sendMessage('📄 *' . $localization['distribution_search_distance_request_not_acceptable'] . '*')
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
							$context->sendMessage('⚠️ *' . $localization['distribution_search_not_started'] . '*');
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
