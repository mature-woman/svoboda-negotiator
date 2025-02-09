<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\processes\distribution;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\localizations\distribution as distribution_localization,
	svoboda\svoboder\models\enumerations\language,
	svoboda\svoboder\models\telegram\commands,
	svoboda\svoboder\models\telegram\processes\distribution\localization,
	svoboda\svoboder\models\telegram\buttons\distribution\registration as button_distribution_registration;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

// Build-in libraries
use Error as error;

/**
 * Distribution registration process
 *
 * @package svoboda\svoboder\models\telegram\processes\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class registration extends core
{
	/**
	 * Start
	 *
	 * Starting the distribution registration process
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
					$context->getUserDataItem('distribution_registration')
						->then(function ($distribution) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started registration process

								// Sending the message
								$context->sendMessage('📂 *' . $localization['distribution_registration_continiued'] . '*')
									->then(function (message $message) use ($context, $account, $language, $localization) {
										// Sended the message

										// Sending the generation menu
										static::generation($context);
									});
							} else {
								// Not found started registretion process

								// Initializing the distribution registration buffer
								$distribution = [
									'latitude' => null,
									'longitude' => null,
									'localization' => [
										'language' => $language,
										'name' => ''
									]
								];

								// Writing to the telegram user buffer
								$context->setUserDataItem('distribution_registration', $distribution)
									->then(function () use ($context, $account, $localization) {
										// Writed to the telegram user buffer

										// Sending the message
										$context->sendMessage('📂 *' . $localization['distribution_registration_started'] . '*')
											->then(function (message $message) use ($context, $account, $localization) {
												// Sended the message

												// Sending the generation menu
												static::generation($context);
											});
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
	 * Ending the distribution registration process
	 * without creating the distribution record in the database
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
				$context->getUserDataItem('distribution_registration')
					->then(function ($distribution) use ($context, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started registration process

							// Deleting in the telegram user buffer
							$context->deleteUserDataItem('distribution_registration')
								->then(function () use ($context, $localization) {
									// Deleted in the telegram user buffer

									// Sending the message
									$context->sendMessage('🗑 *' . $localization['distribution_registration_canceled'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Sending the distributions menu
											commands::distributions($context);
										});
								});
						} else {
							// Not found started registretion process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
	 * Ending the distribution registration process
	 * and creating the distribution record in the database
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
					$context->getUserDataItem('distribution_registration')
						->then(function ($distribution) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started registration process

								// Creating the distribution
								/* $created_distribution = new distribution->create( */
								$created_distribution = (new distribution)->create(
									creator: $account->identifier,
									latitude: $distribution['latitude'],
									longitude: $distribution['longitude']
								);

								if ($created_distribution) {
									// Created the distribution

									// Sending the message
									$context->sendMessage('✏️ *' . $localization['distribution_registration_created_distribution'] . '*')
										->then(function (message $message) use ($context, $account, $language, $localization, $distribution, $created_distribution) {
											// Sended the message

											// Initializing the distribution localization
											/* $created_localization = new distribution_localization->create( */
											$created_localization = (new distribution_localization)->create(
												distribution: $created_distribution,
												language: $distribution['localization']['language'],
												name: $distribution['localization']['name']
											);

											if ($created_localization) {
												// Created the localization

												// Sending the message
												$context->sendMessage('✏️ *' . $localization['distribution_registration_created_localization'] . '*')
													->then(function (message $message) use ($context, $localization) {
														// Sended the message

														// Deleting from the telegram user buffer
														$context->deleteUserDataItem('distribution_registration')
															->then(function () use ($context, $localization) {
																// Deleted from the telegram user buffer

																// Sending the message
																$context->sendMessage('✅ *' . $localization['distribution_registration_completed'] . '*')
																	->then(function (message $message) use ($context) {
																		// Sended the message

																		// Ending the conversation process
																		$context->endConversation();

																		// Sending the distributions menu
																		commands::distributions($context);
																	});
															});
													});
											} else {
												// Not created the distribution localization

												// Sending the message
												$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_created_localization'] . '*')
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
											}
										});
								} else {
									// Not created the distribution

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_created_distribution'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not found started registretion process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
	 * Generation
	 *
	 * Sends the generation menu with parameters: language, name, location
	 * When all parameters was initialized then sends the complete button
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	protected static function generation(context $context): void
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
					$context->getUserDataItem('distribution_registration')
						->then(function ($distribution) use ($context, $account, $language, $localization) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started registration process

								// Initializing the buffer of generated keyboard with languages
								$keyboard = [
									[
										[
											'text' => empty($distribution['localization']['language']) ? '🟢 ' . $localization['distribution_registration_button_language'] : '🟢 ' . $localization['distribution_registration_button_language'] . ': ' . $distribution['localization']['language']->flag() . ' ' . $distribution['localization']['language']->label($language),
											'callback_data' => 'distribution_registration_language'
										]
									],
									[
										[
											'text' => empty($distribution['localization']['name']) ? '🔴 ' . $localization['distribution_registration_button_name'] : '🟢 ' . $localization['distribution_registration_button_name'] . ': ' . $distribution['localization']['name'],
											'callback_data' => 'distribution_registration_name'
										]
									],
									[
										[
											'text' => empty($distribution['latitude']) || empty('longitude') ? '🔴 ' . $localization['distribution_registration_button_location'] : '🟢 ' . $localization['distribution_registration_button_location'] . ': ' . $distribution['latitude'] . ', ' . $distribution['longitude'],
											'callback_data' => 'distribution_registration_location'
										]
									],
								];

								// Initializing the index of last row
								$last = count($keyboard);

								// Initializing the last row
								$keyboard[$last] ??= [];

								// Initializing the button for canceling the generation process
								$keyboard[$last][] = [
									'text' => '❎ ' . $localization['distribution_registration_button_cancel'],
									'callback_data' => 'distribution_registration_cancel'
								];

								if (
									!empty($distribution['localization']['language']) &&
									!empty($distribution['localization']['name']) &&
									!empty($distribution['latitude']) &&
									!empty($distribution['longitude'])
								) {
									// Initialized all requeired parameters

									// Initializing the button for completing the generation process
									$keyboard[$last][] = [
										'text' => '✅ ' . $localization['distribution_registration_button_confirm'],
										'callback_data' => 'distribution_registration_end'
									];
								}

								// Ending the conversation process
								$context->endConversation()
									->then(function () use ($context, $localization, $keyboard) {
										// Deinitialized the conversation process

										// Sending the message
										$context->sendMessage(
											'📀 *' . $localization['distribution_registration_generation'] . '*',
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
								// Not found started registretion process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
							}
						});
				} else {
					// Not initialized localization

					// Sending the message
					$context->sendMessage('⚠️ *Failed to initialize localization*')
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
	 * Language
	 *
	 * Write language into the distribution registration buffer
	 *
	 * @param context $context Request data from Telegram
	 * @param language $new The language
	 *
	 * @return void
	 */
	public static function language(context $context, language $new): void
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
					$context->getUserDataItem('distribution_registration')
						->then(function ($distribution) use ($context, $account, $language, $localization, $new) {
							// Readed from the telegram user buffer

							if ($distribution) {
								// Found started registration process

								try {
									// Initializing the old language
									$old = $distribution['localization']['language'];

									// Writing into the distribution registration process buffer
									$distribution['localization']['language'] = $new;

									// Writing to the telegram user buffer
									$context->setUserDataItem('distribution_registration', $distribution)
										->then(function () use ($context, $account, $language, $localization, $new, $old) {
											// Writed to the telegram user buffer

											// Sending the message
											$context->sendMessage('✅ *' . $localization['distribution_registration_language_update_success'] . '* ' . ($old->flag() ? $old->flag() . ' ' : '') . $old->label($language) . ' → *' . ($new->flag() ? $new->flag() . ' ' : '') . $new->label($language) . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Sending the generation menu
													static::generation($context);
												});
										});
								} catch (error $error) {
									// Failed to send the message about language update

									// Sending the message
									$context->sendMessage('❎ *' . $localization['distribution_registration_language_update_fail'])
										->then(function (message $message) use ($context) {
											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not found started registretion process

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
					->then(function ($message) use ($context) {
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
	 * Name
	 *
	 * Write name into the distribution registration buffer
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
				$context->getUserDataItem('distribution_registration')
					->then(function ($distribution) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started registration process

							// Initializing the new name
							$new = $context->getMessage()->getText();

							if (!empty($new)) {
								// Initialized the new name

								if (mb_strlen($new) >= 3) {
									// Passed minimum length check

									if (mb_strlen($new) <= 32) {
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

											if ($spaces <= 2) {
												// Number of spaces is not more than 2

												try {
													// Initializing the old name
													$old = empty($distribution['localization']['name']) ? '_' . $localization['empty'] . '_' : $distribution['localization']['name'];

													// Writing into the distribution registration process buffer
													$distribution['localization']['name'] = $new;

													// Writing to the telegram user buffer
													$context->setUserDataItem('distribution_registration', $distribution)
														->then(function () use ($context, $account, $localization, $new, $old) {
															// Writed to the telegram user buffer

															// Sending the message
															$context->sendMessage('✅ *' . $localization['distribution_registration_name_update_success'] . "* $old → *$new*")
																->then(function (message $message) use ($context) {
																	// Sended the message

																	// Sending the generation menu
																	static::generation($context);
																});
														});
												} catch (error $error) {
													// Failed to send the message about name update

													// Sending the message
													$context->sendMessage('❎ *' . $localization['distribution_registration_name_update_fail'])
														->then(function (message $message) use ($context) {
															// Ending the conversation process
															$context->endConversation();
														});
												}
											} else {
												// Number of spaces is more than 2

												// Sending the message
												$context->sendMessage('⚠️  *' . $localization['distribution_registration_name_request_spaces'] . '*')
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();

														// Requesting to enter name again
														button_distribution_registration::name($context);
													});
											}
										} else {
											// Found restricted characters

											// Initializing title of the message
											$title = '⚠️  *' . $localization['distribution_registration_name_request_restricted_characters_title'] . '*';

											// Initializing description of the message
											$description = '*' . $localization['distribution_registration_name_request_restricted_characters_description'] . '* \\' . implode(', \\', $characters);

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
													button_distribution_registration::name($context);
												});
										}
									} else {
										// Not passed maximum length check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization['distribution_registration_name_request_too_long'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter name again
												button_distribution_registration::name($context);
											});
									}
								} else {
									// Not passed minimum length check

									// Sending the message
									$context->sendMessage('⚠️  *' . $localization['distribution_registration_name_request_too_short'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Requesting to enter name again
											button_distribution_registration::name($context);
										});
								}
							} else {
								// Failed to initialize the new name

								// Sending the message
								$context->sendMessage('📄 *' . $localization['distribution_registration_name_request_not_acceptable'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to enter name again
										button_distribution_registration::name($context);
									});
							}
						} else {
							// Not found started registretion process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
	 * Location
	 *
	 * Write latitude and longitude into the distribution registration buffer
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
				$context->getUserDataItem('distribution_registration')
					->then(function ($distribution) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($distribution) {
							// Found started registration process

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
														$old = str_replace('.', '\\.', (empty($distribution['latitude']) ? '_' . $localization['empty'] . '_' : $distribution['latitude']) . ', ' . (empty($distribution['longitude']) ? '_' . $localization['empty'] . '_' : $distribution['longitude']));

														// Writing into the distribution registration process buffer
														$distribution['latitude'] = $latitude;
														$distribution['longitude'] = $longitude;

														// Writing to the telegram user buffer
														$context->setUserDataItem('distribution_registration', $distribution)
															->then(function () use ($context, $account, $localization, $latitude, $longitude, $old) {
																// Writed to the telegram user buffer

																// Initializing the new location
																$new = str_replace('.', '\\.', $latitude . ', ' . $longitude);

																// Sending the message
																$context->sendMessage('✅ *' . $localization['distribution_registration_location_update_success'] . "*\n$old → *$new*")
																	->then(function (message $message) use ($context) {
																		// Sended the message

																		// Sending the generation menu
																		static::generation($context);
																	});
															});
													} catch (error $error) {
														// Failed to send the message about name update

														// Sending the message
														$context->sendMessage('❎ *' . $localization['distribution_registration_name_update_fail'])
															->then(function (message $message) use ($context) {
																// Ending the conversation process
																$context->endConversation();
															});
													}
												} else {
													// Not passed longitude maximum value check

													// Sending the message
													$context->sendMessage('⚠️  *' . $localization['distribution_registration_location_send_longitude_too_big'] . '*')
														->then(function (message $message) use ($context) {
															// Sended the message

															// Ending the conversation process
															$context->endConversation();

															// Requesting to enter locaztion again
															button_distribution_registration::location($context);
														});
												}
											} else {
												// Not passed longitude minimum value check

												// Sending the message
												$context->sendMessage('⚠️  *' . $localization['distribution_registration_location_send_longitude_too_small'] . '*')
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();

														// Requesting to enter locaztion again
														button_distribution_registration::location($context);
													});
											}
										} else {
											// Not passed latitude maximum value check

											// Sending the message
											$context->sendMessage('⚠️  *' . $localization['distribution_registration_location_send_latitude_too_big'] . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();

													// Requesting to enter locaztion again
													button_distribution_registration::location($context);
												});
										}
									} else {
										// Not passed latitude minimum value check

										// Sending the message
										$context->sendMessage('⚠️  *' . $localization['distribution_registration_location_send_latitude_too_small'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();

												// Requesting to enter locaztion again
												button_distribution_registration::location($context);
											});
									}
								} else {
									// Failed to initialize the new name

									// Sending the message
									$context->sendMessage('📄 *' . $localization['distribution_registration_location_send_not_acceptable'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();

											// Requesting to send location again
											button_distribution_registration::location($context);
										});
								}
							} else {
								// Not initialized the new location

								// Sending the message
								$context->sendMessage('📄 *' . $localization['distribution_registration_location_send_not_acceptable'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();

										// Requesting to send locaztion again
										button_distribution_registration::location($context);
									});
							}
						} else {
							// Not found started registretion process

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_registration_not_started'] . '*');
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
}
