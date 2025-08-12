<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\telegram\processes\distribution;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\account,
	svoboda\antivertical\models\distribution,
	svoboda\antivertical\models\membership,
	svoboda\antivertical\models\telegram\buttons\distribution\select as button_distribution_select,
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
 * Distribution select process
 *
 * @package svoboda\antivertical\models\telegram\processes\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class select extends core
{
	use coordinates {
		coordinates::distance as vincenty;
	}

	/**
	 * Process
	 *
	 * @var const string PROCESS Name of the process in the telegram user buffer
	 */
	public const string PROCESS = 'distribution_select';

	/**
	 * Start
	 *
	 * Starting the distribution select process
	 *
	 * @param context $context Request data from Telegram
	 * @param callable $select The distribution selection function (context $context, array $distribution = ['distribution', 'localization'])
	 * @param callable $delete The distribution deletion function (context $context)
	 * @param callable $cancel The process canceling function (context $context)
	 * @param string|null $description Description of the message
	 *
	 * @return void
	 */
	public static function start(context $context, callable $select, callable $delete, callable $cancel, ?string $description = null): void
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
						->then(function (?array $_select) use ($context, $account, $language, $localization, $select, $delete, $cancel, $description) {
							// Readed from the telegram user buffer

							if ($_select) {
								// Found started select process

								// Sending the message
								$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_continiued'] . '*')
									->then(function (message $message) use ($context, $account, $language, $localization) {
										// Sended the message

										// Sending the list of found distributions and menu
										static::menu($context);
									});
							} else {
								// Not found started select process

								// Initializing the distribution select buffer
								$_select = [
									'name' => null,
									'location' => [
										'latitude' => null,
										'longitude' => null,
										'distance' => DISTRIBUTIONS_SELECT_DISTRIBUTION_DISTANCE
									],
									'events' => [
										'select' => $select,
										'delete' => $delete,
										'cancel' => $cancel
									],
									'page' => 0
								];

								// Writing to the telegram user buffer
								$context->setUserDataItem(static::PROCESS, $_select)
									->then(function () use ($context, $account, $localization, $description) {
										// Writed to the telegram user buffer

										// Initializing title for the message
										$title = '🗂 *' . $localization[static::PROCESS . '_started'] . '*';

										// Sending the message
										$context->sendMessage(
											empty($description)
												?
												$title
												:
												<<<TXT
												$title

												$description
												TXT
										)->then(function (message $message) use ($context, $account, $localization) {
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
	 * Select
	 *
	 * End the distribution select process 
	 * and process the distribution selection telegram button listener
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function select(context $context): void
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
						->then(function (?array $select) use ($context, $language, $localization) {
							// Readed from the telegram user buffer

							if ($select) {
								// Found started select process

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
												amount: MEMBERSHIPS_SEARCH_DISTRIBUTION_LOCALIZATIONS_AMOUNT
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

										// Deleting from the telegram user buffer
										$context->deleteUserDataItem(static::PROCESS)
											->then(function () use ($context, $select, $language, $localization, $distribution) {
												// Deleted from the telegram user buffer

												// Sending the message
												$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_ended'] . '*')
													->then(function (message $message) use ($context, $select, $distribution) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();

														// Processing the event
														$select['events']['select'](context: $context, distribution: $distribution);
													});
											});
									} else {
										// Not initialized the distribution identifier

										// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
									}
								} else {
									// Not initialized the message text

									// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
								}
							} else {
								// Not found started select process

								// Ending the conversation process
								$context->endConversation();
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
	 * Delete
	 *
	 * End the distribution select process 
	 * and process the distribution deletion telegram button listener
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function delete(context $context): void
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
					->then(function (?array $select) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

							// Deleting from the telegram user buffer
							$context->deleteUserDataItem(static::PROCESS, $select)
								->then(function () use ($context, $select, $localization) {
									// Deleted from the telegram user buffer

									// Sending the message
									$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_ended'] . '*')
										->then(function (message $message) use ($context, $select) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Processing the event
											$select['events']['delete'](context: $context);
										});
								});
						} else {
							// Not found started select process

							// Ending the conversation process
							$context->endConversation();
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
	 * Cancel
	 *
	 * End the distribution select process 
	 * and process the distribution canceling telegram button listener
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function cancel(context $context): void
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
					->then(function (?array $select) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

							// Deleting from the telegram user buffer
							$context->deleteUserDataItem(static::PROCESS, $select)
								->then(function () use ($context, $select, $localization) {
									// Deleted from the telegram user buffer

									// Sending the message
									$context->sendMessage('🗂 *' . $localization[static::PROCESS . '_ended'] . '*')
										->then(function (message $message) use ($context, $select) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Processing the event
											$select['events']['cancel'](context: $context);
										});
								});
						} else {
							// Not found started select process

							// Ending the conversation process
							$context->endConversation();
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
						->then(function (?array $select) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($select) {
								// Found started select process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => empty($select['name']) ? '🔴 ' . $localization[static::PROCESS . '_button_name'] : '🟢 ' . $localization['distribution_select_button_name'] . ': ' . $select['name'],
											'callback_data' => static::PROCESS . '_name'
										]
									],
									[
										[
											'text' => empty($select['location']['latitude']) || empty($select['location']['longitude']) ? '🔴 ' . $localization[static::PROCESS . '_button_location'] : '🟢 ' . $localization['distribution_select_button_location'] . ': ' . $select['location']['latitude'] . ', ' . $select['location']['longitude'],
											'callback_data' => static::PROCESS . '_location'
										]
									],
									[
										[
											'text' => empty($select['location']['distance']) ? '🔴 ' . $localization[static::PROCESS . '_button_distance'] : '🟢 ' . $localization['distribution_select_button_distance'] . ': ' . $select['location']['distance'] . ' ' . $localization['distribution_select_km'],
											'callback_data' => static::PROCESS . '_distance'
										]
									]
								];

								// Ending the conversation process
								$context->endConversation()
									->then(function () use ($context, $account, $language, $localization, $select, $keyboard) {
										// Deinitialized the conversation process

										// Initializing the distribution model
										$model_distribution = new distribution;

										// Initializing amount of readed distributions per page
										$page = DISTRIBUTIONS_SELECT_PAGE;

										if (
											empty($select['name']) &&
											empty($select['location']['latitude']) &&
											empty($select['location']['longitude'])
										) {
											// Each select parameter is empty

											// Search for distributions
											$distributions = $model_distribution->database->read(
												amount: $page + 1,
												offset: $select['page'] < 1 ? 0 : $page * $select['page']
											);
										} else {
											// The select buffer has at least one parameter

											// Search for distributions
											$distributions = $model_distribution->database->read(
												filter: function (record $distribution) use ($select, $model_distribution) {
													// Initializing the matched buffer
													$matched = false;

													if (!empty($select['name'])) {
														// Requested select by name

														// Initializing localizations
														$localizations = $model_distribution->localization->database->read(
															filter: fn(record $localization) => $localization->distribution === $distribution->identifier,
															amount: DISTRIBUTIONS_SELECT_DISTRIBUTION_LOCALIZATIONS_AMOUNT
														);

														// Initializing the result status
														$result = false;

														foreach ($localizations as $localization) {
															// Iterating over localizations

															// Splitting the localization name into parts
															$parts = preg_split('/[\s]/', $localization->name);

															foreach ($parts as $part) {
																// Iterating over localization name parts

																if (levenshtein($part, $select['name']) <= DISTRIBUTIONS_SELECT_DISTRIBUTION_NAME_LEVENSHTEIN_DISTANCE) {
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
														!empty($select['location']['latitude']) &&
														!empty($select['location']['longitude']) &&
														!empty($select['location']['distance'])
													) {
														// Requested select by location

														if (
															static::vincenty(
																$select['location']['latitude'],
																$select['location']['longitude'],
																$distribution->latitude,
																$distribution->longitude
															) <= $select['location']['distance'] * 1000
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
												offset: $select['page'] < 1 ? 0 : $page * $select['page']
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
										)->then(function (message $message) use ($context, $account, $language, $localization, $select, $page, $next, $distributions, $model_distribution) {
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
														amount: DISTRIBUTIONS_SELECT_DISTRIBUTION_LOCALIZATIONS_AMOUNT
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

													// Search for the membership record
													$membership = $model_membership->database->read(
														filter: fn(record $membership) => $membership->distribution === $distribution->identifier && $membership->account === $account->identifier,
														amount: 1
													)[0] ?? null;

													// Initializing buffer of keyboard
													$keyboard = static::keyboard(
														localization: $localization
													);

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
																			'text' => '🔥 ' . $localization[static::PROCESS . '_button_delete'],
																			'callback_data' => static::PROCESS . '_delete'
																		],
																		[
																			'text' => '❌ ' . $localization[static::PROCESS . '_button_cancel'],
																			'callback_data' => static::PROCESS . '_cancel'
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
																			'text' => '🔥 ' . $localization[static::PROCESS . '_button_delete'],
																			'callback_data' => static::PROCESS . '_delete'
																		],
																		[
																			'text' => '❌ ' . $localization[static::PROCESS . '_button_cancel'],
																			'callback_data' => static::PROCESS . '_cancel'
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
																		'text' => '🔥 ' . $localization[static::PROCESS . '_button_delete'],
																		'callback_data' => static::PROCESS . '_delete'
																	],

																	[
																		'text' => '❌ ' . $localization[static::PROCESS . '_button_cancel'],
																		'callback_data' => static::PROCESS . '_cancel'
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
								// Not found started select process

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
	 * Write select name into the distribution select buffer
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
					->then(function ($select) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

							// Initializing the new select name
							$new = $context->getMessage()->getText();

							if (!empty($new)) {
								// Initialized the new select name

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
												// Initializing the old select name
												$old = empty($select['name']) ? '_' . $localization['empty'] . '_' : $select['name'];

												// Writing into the distribution select process buffer
												$select['name'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem(static::PROCESS, $select)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Sending the message
														$context->sendMessage('✅ *' . $localization[static::PROCESS . '_name_update_success'] . "* $old → *$new*")
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the distribution select menu
																static::menu($context);
															});
													});
											} catch (error $error) {
												// Failed to send the message about select name update

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

													// Requesting to enter select name again
													button_distribution_select::name($context);
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

												// Requesting to enter select name again
												button_distribution_select::name($context);
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

											// Requesting to enter select name again
											button_distribution_select::name($context);
										});
								}
							} else {
								// Failed to initialize the new select name

								// Sending the message
								$context->sendMessage('📄 *' . $localization[static::PROCESS . '_name_request_not_acceptable'] . '*')
									->then(function (message  $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter select name again
										button_distribution_select::name($context);
									});
							}
						} else {
							// Not found started select process

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
	 * Write location into the distribution select buffer
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
					->then(function ($select) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

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
														$old = str_replace('.', '\\.', (empty($select['location']['latitude']) ? '_' . $localization['empty'] . '_' : $select['location']['latitude']) . ', ' . (empty($select['location']['longitude']) ? '_' . $localization['empty'] . '_' : $select['location']['longitude']));

														// Writing into the distribution select process buffer
														$select['location']['latitude'] = $latitude;
														$select['location']['longitude'] = $longitude;

														// Writing to the telegram user buffer
														$context->setUserDataItem(static::PROCESS, $select)
															->then(function () use ($context, $localization, $latitude, $longitude, $old) {
																// Writed to the telegram user buffer

																// Initializing the new location
																$new = str_replace('.', '\\.', $latitude . ', ' . $longitude);

																// Sending the message
																$context->sendMessage('✅ *' . $localization[static::PROCESS . '_location_update_success'] . "*\n$old → *$new*")
																	->then(function (message $message) use ($context) {
																		// Sended the message

																		// Sending the distribution select menu
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
															button_distribution_select::location($context);
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
														button_distribution_select::location($context);
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
													button_distribution_select::location($context);
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
												button_distribution_select::location($context);
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
											button_distribution_select::location($context);
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
										button_distribution_select::location($context);
									});
							}
						} else {
							// Not found started select process

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
	 * Write location distance into the distribution select buffer
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
					->then(function ($select) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($select) {
							// Found started select process

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
												$old = empty($select['location']['distance']) ? '_' . $localization['empty'] . '_' : $select['location']['distance'];

												// Writing into the distribution select process buffer
												$select['location']['distance'] = $new;

												// Writing to the telegram user buffer
												$context->setUserDataItem(static::PROCESS, $select)
													->then(function () use ($context, $localization, $new, $old) {
														// Writed to the telegram user buffer

														// Sending the message
														$context->sendMessage('✅ *' . $localization[static::PROCESS . '_distance_update_success'] . "* $old \(" . $localization['distribution_select_km'] . '\) ' . " → *$new* \(" . $localization['distribution_select_km'] . '\)')
															->then(function (message $message) use ($context) {
																// Sended the message

																// Sending the distribution select menu
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
													button_distribution_select::distance($context);
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
												button_distribution_select::distance($context);
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
											button_distribution_select::distance($context);
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
										button_distribution_select::distance($context);
									});
							}
						} else {
							// Not found started select process

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

		// Exit (success)
		return "$distribution->identifier *$name* $recognized";
	}

	/**
	 * Keyboard (inline)
	 *
	 * Generate inline keyboard for the distribution
	 *
	 * @param array $localization The account localization
	 *
	 * @return array Generated inline keyboard
	 */
	public static function keyboard(array $localization): array
	{
		// Exit (success)
		return [
			[
				[
					'text' => '✅ ' . $localization[static::PROCESS . '_button_select'],
					'callback_data' => static::PROCESS . '_select'
				]
			]
		];
	}
}
