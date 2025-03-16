<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\telegram\buttons\distribution;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\distribution,
	svoboda\svoboder\models\member,
	svoboda\svoboder\models\telegram\processes\distribution\search as process_distribution_search,
	svoboda\svoboder\models\enumerations\member\status;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Svoboda time
use svoboda\time\statement as svoboda;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution search buttons
 *
 * @package svoboda\svoboder\models\telegram\buttons\distribution
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class search extends core
{
	/**
	 * Name
	 *
	 * Request to enter search name
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function name(context $context)
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
				$context->getUserDataItem(process_distribution_search::PROCESS)
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing title for the message
							$title = '📄 *' . $localization['distribution_search_name_request_title'] . '*';

							// Initializing description for the message
							$description = $localization['distribution_search_name_request_description'];

							// Sending the message
							$context->sendMessage(<<<TXT
								$title

								$description
								TXT)
								->then(function (message $message) use ($context) {
									// Sended the message

									// Writing into the distribution search buffer
									$context->nextStep([process_distribution_search::class, 'name']);
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
	 * Location
	 *
	 * Request to send location
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function location(context $context)
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
				$context->getUserDataItem(process_distribution_search::PROCESS)
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the message title
							$title = '🗺 *' . $localization['distribution_search_location_send_title'] . '*';

							// Initializing the message description
							$description = $localization['distribution_search_location_send_description'];

							// Sending the message
							$context->sendMessage(
								<<<TXT
								$title

								$description
								TXT,
								[
									'reply_markup' => [
										'keyboard' => [
											[
												[
													'text' => '🗺 ' . $localization['distribution_search_button_location_send'],
													'request_location' => true
												]
											],
										],
										'disable_notification' => true
									]
								]
							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution search buffer
								$context->nextStep([process_distribution_search::class, 'location']);
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
	 * Distance 
	 *
	 * Request to send distance
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function distance(context $context)
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
				$context->getUserDataItem(process_distribution_search::PROCESS)
					->then(function ($search) use ($context, $account, $localization) {
						// Readed from the telegram user buffer

						if ($search) {
							// Found started search process

							// Initializing the message title
							$title = '🔭 *' . $localization['distribution_search_distance_request_title'] . '* \(' . $localization['distribution_search_km'] . '\)';

							// Initializing the message description
							$description = $localization['distribution_search_distance_request_description'];

							// Sending the message
							$context->sendMessage(
								<<<TXT
								$title

								$description
								TXT
							)->then(function (message $message) use ($context) {
								// Sended the message

								// Writing into the distribution search buffer
								$context->nextStep([process_distribution_search::class, 'distance']);
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
	 * Plan 
	 *
	 * Request to plan to join to the distribution
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function plan(context $context)
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the message
				$message = $context->getCallbackQuery()->getMessage();

				if ($message instanceof message) {
					// Initialized the message

					// Initializing the distribution information
					preg_match('/^(\d+)(?:\s)?(\w+\s*\w*\s*\w*)(?:\s)?(🪽)?$/muU', $message->getText(), $matches);
					$identifier = (int) $matches[1];
					$name = $matches[2];
					$recognized = isset($matches[3]) && $matches[3] === '🪽';

					if (!empty($identifier) && $identifier !== 0) {
						// Initialized the distribution identifier

						// Initializing the distribution model
						$model_distribution = new distribution;

						// Initializing the distribution
						$distribution = $model_distribution->database->read(
							filter: fn(record $record) => $record->identifier === $identifier,
							amount: 1
						)[0] ?? null;

						if ($distribution instanceof record) {
							// Initialized the distribution

							// Initializing the member model
							$model_member = new member;

							// Initializing function of the message join buttom updating
							$update = function (context $context, record $member) use ($account, $localization, $distribution, $name, $recognized, $model_member): void {
								// Searching for the another member records
								$another = $model_member->database->read(
									filter: fn(record $record) => $record->identifier !== $member->identifier && $record->account === $member->accoount && $record->status !== status::unknown->value,
									amount: 1
								)[0] ?? null;

								// Initializing the updated inline keyboard of the message
								$keyboard = process_distribution_search::keyboard(
									distribution: $distribution,
									member: $another ?? $member,
									localization: $localization,
									messages: $account->authorized_messages === 1,
									joins: $account->authorized_joins === 1,
								);

								// Initializing the updated text of the message
								$text = process_distribution_search::message(
									distribution: $distribution,
									localization: $localization,
									name: $name,
									recognized: $recognized
								);

								// Sending the updated message inline keyboard
								$context->editMessageText(
									$text,
									[
										'message_inline_id' => $context->getCallbackQuery()->getInlineMessageId(),
										'reply_markup' => ['inline_keyboard' => $keyboard]
									]
								);
							};

							if (!empty($model_member->database->read(
								filter: fn(record $member) => $member->distribution !== $distribution->identifier && $member->account === $account->identifier && $member->status === status::joined->value,
								amount: 1
							))) {
								// Found joining to another distribution

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_search_another_joined'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();
									});
							} else {
								// Not found joining to another distribution


								if (!empty($model_member->database->read(
									filter: fn(record $member) => $member->distribution !== $distribution->identifier && $member->account === $account->identifier && $member->status === status::planned->value,
									amount: 1
								))) {
									// Found planning to join to another distribution

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_search_another_planned'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								} else {
									// Not found planned to join to another distribution

									// Searching for the member record
									$member = $model_member->database->read(
										filter: fn(record $record) => $record->distribution === $distribution->identifier && $record->account === $account->identifier,
										amount: 1
									)[0] ?? null;

									if ($member instanceof record) {
										// Found the member of the distribution 

										if ($member->status === status::unknown->value) {
											// The account has leaved the distribution

											// Updating the member record
											$updated = $model_member->database->read(
												filter: fn(record $record) => $record->identifier === $member->identifier,
												update: function (record &$record) {
													$record->status = status::planned->value;
													$record->updated = svoboda::timestamp();
												},
												amount: 1
											)[0] ?? null;

											if ($updated) {
												// Updated the member record

												// Deprecating other records
												$model_member->database->read(
													filter: fn(record $record) => $record->identifier !== $updated->identifier && $record->account === $updated->account,
													update: function (record &$record) {
														$record->status = status::unknown->value;
														$record->updated = svoboda::timestamp();
													},
													amount: DISTRIBUTIONS_SEARCH_MEMBER_DEPRECATING_RECORDS_AMOUNT
												)[0] ?? null;

												// Sending the message
												$context->sendMessage('📅 *' . $localization['distribution_search_planned'] . '*')
													->then(function (message $message) use ($context, $update, $updated) {
														// Sended the message

														// Updating the message with the plan button
														$update($context, $updated);

														// Ending the conversation process
														$context->endConversation();
													});
											} else {
												// Not updated the member record

												// Sending the message
												$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_updated'] . '*')
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else if ($member->status === status::planned->value) {
											// The account has already planned to join to the distribution

											// Sending the message
											$context->sendMessage('⚠️ *' . $localization['distribution_search_already_planned'] . '*')
												->then(function (message $message) use ($context, $update, $member) {
													// Sended the message

													// Updating the message with the plan button
													$update($context, $member);

													// Ending the conversation process
													$context->endConversation();
												});
										} else if ($member->status === status::joined->value) {
											// The account has already joined to the distribution

											// Sending the message
											$context->sendMessage('⚠️ *' . $localization['distribution_search_already_joined'] . '*')
												->then(function (message $message) use ($context, $update, $member) {
													// Sended the message

													// Updating the message with the plan button
													$update($context, $member);

													// Ending the conversation process
													$context->endConversation();
												});
										}
									} else {
										// Not found the member of the distribution 

										// Creating the member record
										$record = $model_member->create(
											distribution: $distribution->identifier,
											account: $account->identifier,
											status: status::planned
										);

										if ($record) {
											// Created the member record

											// Searching for the member record
											$member = $model_member->database->read(
												filter: fn(record $member) => $member->identifier === $record,
												amount: 1
											)[0] ?? null;

											if ($member instanceof record) {
												// Found the member of the distribution 

												// Deprecating other records
												$model_member->database->read(
													filter: fn(record $record) => $record->identifier !== $member->identifier && $record->account === $member->account,
													update: function (record &$record) {
														$record->status = status::unknown->value;
														$record->updated = svoboda::timestamp();
													},
													amount: DISTRIBUTIONS_SEARCH_MEMBER_DEPRECATING_RECORDS_AMOUNT
												)[0] ?? null;

												// Sending the message
												$context->sendMessage('📅 *' . $localization['distribution_search_planned'] . '*')
													->then(function (message $message) use ($context, $update, $member) {
														// Sended the message

														// Updating the message with the plan button
														$update($context, $member);

														// Ending the conversation process
														$context->endConversation();
													});
											} else {
												// Not found the member of the distribution 

												// Sending the message
												$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
													->then(function (message $message) use ($context) {
														// Sended the message

														// Ending the conversation process
														$context->endConversation();
													});
											}
										} else {
											// Not created the member record

											// Sending the message
											$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();
												});
										}
									}
								}
							}
						} else {
							// Not initialized the distribution

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();
								});
						}
					} else {
						// Not initialized the distribution identifier

						// Sending the message
						$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
							->then(function (message $message) use ($context) {
								// Sended the message

								// Ending the conversation process
								$context->endConversation();
							});
					}
				} else {
					// Not initialized the message

					// Sending the message
					$context->sendMessage('⚠️ *' . $localization['distribution_search_message_not_initialized'] . '*')
						->then(function (message $message) use ($context) {
							// Sended the message

							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * Unplan 
	 *
	 * Request to unplan to join to the distribution
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function unplan(context $context)
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the message
				$message = $context->getCallbackQuery()->getMessage();

				if ($message instanceof message) {
					// Initialized the message

					// Initializing the distribution information
					preg_match('/^(\d+)(?:\s)?(\w+\s*\w*\s*\w*)(?:\s)?(🪽)?$/muU', $message->getText(), $matches);
					$identifier = (int) $matches[1];
					$name = $matches[2];
					$recognized = isset($matches[3]) && $matches[3] === '🪽';

					if (!empty($identifier) && $identifier !== 0) {
						// Initialized the distribution identifier

						// Initializing the distribution model
						$model_distribution = new distribution;

						// Initializing the distribution
						$distribution = $model_distribution->database->read(
							filter: fn(record $record) => $record->identifier === $identifier,
							amount: 1
						)[0] ?? null;

						if ($distribution instanceof record) {
							// Initialized the distribution

							// Initializing the member model
							$model_member = new member;

							// Initializing function of the message join buttom updating
							$update = function (context $context, record $member) use ($account, $localization, $distribution, $name, $recognized): void {
								// Initializing the updated inline keyboard of the message
								$keyboard = process_distribution_search::keyboard(
									distribution: $distribution,
									member: $member,
									localization: $localization,
									messages: $account->authorized_messages === 1,
									joins: $account->authorized_joins === 1,
								);

								// Initializing the updated text of the message
								$text = process_distribution_search::message(
									distribution: $distribution,
									localization: $localization,
									name: $name,
									recognized: $recognized
								);

								// Sending the updated message inline keyboard
								$context->editMessageText(
									$text,
									[
										'message_inline_id' => $context->getCallbackQuery()->getInlineMessageId(),
										'reply_markup' => ['inline_keyboard' => $keyboard]
									]
								);
							};

							// Searching for the member record
							$member = $model_member->database->read(
								filter: fn(record $record) => $record->distribution === $distribution->identifier && $record->account === $account->identifier,
								amount: 1
							)[0] ?? null;

							if ($member instanceof record) {
								// Found the member of the distribution 

								if ($member->status === status::unknown->value) {
									// The account has leaved the distribution

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_search_already_unplanned'] . '*')
										->then(function (message $message) use ($context, $update, $member) {
											// Sended the message

											// Updating the message with the plan button
											$update($context, $member);

											// Ending the conversation process
											$context->endConversation();
										});
								} else if ($member->status === status::planned->value) {
									// The account has  planned to join to the distribution

									// Updating the member record
									$updated = $model_member->database->read(
										filter: fn(record $record) => $record->identifier === $member->identifier,
										update: function (record &$record) {
											$record->status = status::unknown->value;
											$record->updated = svoboda::timestamp();
										},
										amount: 1
									)[0] ?? null;

									if ($updated) {
										// Updated the member record

										// Sending the message
										$context->sendMessage('❌ *' . $localization['distribution_search_unplanned'] . '*')
											->then(function (message $message) use ($context, $update, $updated) {
												// Sended the message

												// Updating the message with the plan button
												$update($context, $updated);

												// Ending the conversation process
												$context->endConversation();
											});
									} else {
										// Not updated the member record

										// Sending the message
										$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_updated'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();
											});
									}
								} else if ($member->status === status::joined->value) {
									// The account has already joined to the distribution

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_search_already_joined'] . '*')
										->then(function (message $message) use ($context, $update, $member) {
											// Sended the message

											// Updating the message with the plan button
											$update($context, $member);

											// Ending the conversation process
											$context->endConversation();
										});
								}
							} else {
								// Not found the member of the distribution 

								// Creating the member record
								$record = $model_member->create(
									distribution: $distribution->identifier,
									account: $account->identifier,
									status: status::unknown
								);

								if ($record) {
									// Created the member record

									// Searching for the member record
									$member = $model_member->database->read(
										filter: fn(record $member) => $member->identifier === $record,
										amount: 1
									)[0] ?? null;

									if ($member instanceof record) {
										// Found the member of the distribution 

										// Sending the message
										$context->sendMessage('❌ *' . $localization['distribution_search_unplanned'] . '*')
											->then(function (message $message) use ($context, $update, $member) {
												// Sended the message

												// Updating the message with the plan button
												$update($context, $member);

												// Ending the conversation process
												$context->endConversation();
											});
									} else {
										// Not found the member of the distribution 

										// Sending the message
										$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();
											});
									}
								} else {
									// Not created the member record

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								}
							}
						} else {
							// Not initialized the distribution

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();
								});
						}
					} else {
						// Not initialized the distribution identifier

						// Sending the message
						$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
							->then(function (message $message) use ($context) {
								// Sended the message

								// Ending the conversation process
								$context->endConversation();
							});
					}
				} else {
					// Not initialized the message

					// Sending the message
					$context->sendMessage('⚠️ *' . $localization['distribution_search_message_not_initialized'] . '*')
						->then(function (message $message) use ($context) {
							// Sended the message

							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * Join 
	 *
	 * Request to join to the distribution
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function join(context $context)
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the message
				$message = $context->getCallbackQuery()->getMessage();

				if ($message instanceof message) {
					// Initialized the message

					// Initializing the distribution information
					preg_match('/^(\d+)(?:\s)?(\w+\s*\w*\s*\w*)(?:\s)?(🪽)?$/muU', $message->getText(), $matches);
					$identifier = (int) $matches[1];
					$name = $matches[2];
					$recognized = isset($matches[3]) && $matches[3] === '🪽';

					if (!empty($identifier) && $identifier !== 0) {
						// Initialized the distribution identifier

						// Initializing the distribution model
						$model_distribution = new distribution;

						// Initializing the distribution
						$distribution = $model_distribution->database->read(
							filter: fn(record $record) => $record->identifier === $identifier,
							amount: 1
						)[0] ?? null;

						if ($distribution instanceof record) {
							// Initialized the distribution

							// Initializing the distribution model
							$model_member = new member;

							// Initializing function of the message join buttom updating
							$update = function (context $context, record $member) use ($account, $localization, $distribution, $name, $recognized, $model_member): void {
								// Searching for the another member records
								$another = $model_member->database->read(
									filter: fn(record $record) => $record->identifier !== $member->identifier && $record->account === $member->accoount && $record->status !== status::unknown->value,
									amount: 1
								)[0] ?? null;

								// Initializing the updated inline keyboard of the message
								$keyboard = process_distribution_search::keyboard(
									distribution: $distribution,
									member: $another ?? $member,
									localization: $localization,
									messages: $account->authorized_messages === 1,
									joins: $account->authorized_joins === 1,
								);

								// Initializing the updated text of the message
								$text = process_distribution_search::message(
									distribution: $distribution,
									localization: $localization,
									name: $name,
									recognized: $recognized
								);

								// Sending the updated message inline keyboard
								$context->editMessageText(
									$text,
									[
										'message_inline_id' => $context->getCallbackQuery()->getInlineMessageId(),
										'reply_markup' => ['inline_keyboard' => $keyboard]
									]
								);
							};

							if (!empty($model_member->database->read(
								filter: fn(record $member) => $member->distribution !== $distribution->identifier && $member->account === $account->identifier && $member->status === status::joined->value,
								amount: 1
							))) {
								// Found joining to another distribution

								// Sending the message
								$context->sendMessage('⚠️ *' . $localization['distribution_search_another_joined'] . '*')
									->then(function (message $message) use ($context) {
										// Sended the message

										// Ending the conversation process
										$context->endConversation();
									});
							} else {
								// Not found joining to another distribution

								// Searching for the member record
								$member = $model_member->database->read(
									filter: fn(record $member) => $member->distribution === $distribution->identifier && $member->account === $account->identifier,
									amount: 1
								)[0] ?? null;

								if ($member instanceof record) {
									// Found the member of the distribution 

									if ($member->status === status::unknown->value || $member->status === status::planned->value) {
										// The account has planned to join to the distribution or leaved the distribution

										// Updating the member record
										$updated = $model_member->database->read(
											filter: fn(record $record) => $record->identifier === $member->identifier,
											update: function (record &$record) {
												$record->status = status::joined->value;
												$record->updated = svoboda::timestamp();
											},
											amount: 1
										)[0] ?? null;

										if ($updated instanceof record) {
											// Updated the member record

											// Deprecating other records
											$model_member->database->read(
												filter: fn(record $record) => $record->identifier !== $updated->identifier && $record->account === $updated->account,
												update: function (record &$record) {
													$record->status = status::unknown->value;
													$record->updated = svoboda::timestamp();
												},
												amount: DISTRIBUTIONS_SEARCH_MEMBER_DEPRECATING_RECORDS_AMOUNT
											)[0] ?? null;

											// Initializing the message title
											$title = '🤝 *' . $localization['distribution_search_joined_title'] . '*';

											// Initializing the message description
											$description = $localization['distribution_search_joined_description'];

											// Sending the message
											$context->sendMessage(
												<<<TXT
											$title

											$description
											TXT
											)->then(function (message $message) use ($context, $update, $updated) {
												// Sended the message

												// Updating the message with the join button
												$update($context, $updated);

												// Ending the conversation process
												$context->endConversation();
											});
										} else {
											// Not updated the member record

											// Sending the message
											$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_updated'] . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();
												});
										}
									} else if ($member->status === status::joined->value) {
										// The account has already joined to the distribution

										// Sending the message
										$context->sendMessage('⚠️ *' . $localization['distribution_search_already_joined'] . '*')
											->then(function (message $message) use ($context, $update, $member) {
												// Sended the message

												// Updating the message with the join button
												$update($context, $member);

												// Ending the conversation process
												$context->endConversation();
											});
									}
								} else {
									// Not found the member of the distribution 

									// Creating the member record
									$record = $model_member->create(
										distribution: $distribution->identifier,
										account: $account->identifier,
										status: status::joined
									);

									if ($record) {
										// Created the member record

										// Searching for the member record
										$member = $model_member->database->read(
											filter: fn(record $member) => $member->identifier === $record,
											amount: 1
										)[0] ?? null;

										if ($member instanceof record) {
											// Found the member of the distribution

											// Deprecating other records
											$model_member->database->read(
												filter: fn(record $record) => $record->identifier !== $member->identifier && $record->account === $member->account,
												update: function (record &$record) {
													$record->status = status::unknown->value;
													$record->updated = svoboda::timestamp();
												},
												amount: DISTRIBUTIONS_SEARCH_MEMBER_DEPRECATING_RECORDS_AMOUNT
											)[0] ?? null;

											// Initializing the message title
											$title = '🤝 *' . $localization['distribution_search_joined_title'] . '*';

											// Initializing the message description
											$description = $localization['distribution_search_joined_description'];

											// Sending the message
											$context->sendMessage(
												<<<TXT
											$title

											$description
											TXT
											)->then(function (message $message) use ($context, $update, $member) {
												// Sended the message

												// Updating the message with the join button
												$update($context, $member);

												// Ending the conversation process
												$context->endConversation();
											});
										} else {
											// Not found the member of the distribution 

											// Sending the message
											$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
												->then(function (message $message) use ($context) {
													// Sended the message

													// Ending the conversation process
													$context->endConversation();
												});
										}
									} else {
										// Not created the member record

										// Sending the message
										$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();
											});
									}
								}
							}
						} else {
							// Not initialized the distribution

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();
								});
						}
					} else {
						// Not initialized the distribution identifier

						// Sending the message
						$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
							->then(function (message $message) use ($context) {
								// Sended the message

								// Ending the conversation process
								$context->endConversation();
							});
					}
				} else {
					// Not initialized the message

					// Sending the message
					$context->sendMessage('⚠️ *' . $localization['distribution_search_message_not_initialized'] . '*')
						->then(function (message $message) use ($context) {
							// Sended the message

							// Ending the conversation process
							$context->endConversation();
						});
				}
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
	 * Leave
	 *
	 * Request to leave from the distribution
	 *
	 * @param context $context Request data from Telegram
	 *
	 * @return void
	 */
	public static function leave(context $context)
	{
		// Initializing the account
		$account = $context->get('account');

		if ($account instanceof record) {
			// Initialized the account

			// Initializing localization 
			$localization = $context->get('localization');

			if ($localization) {
				// Initialized localization

				// Initializing the message
				$message = $context->getCallbackQuery()->getMessage();

				if ($message instanceof message) {
					// Initialized the message

					// Initializing the distribution information
					preg_match('/^(\d+)(?:\s)?(\w+\s*\w*\s*\w*)(?:\s)?(🪽)?$/muU', $message->getText(), $matches);
					$identifier = (int) $matches[1];
					$name = $matches[2];
					$recognized = isset($matches[3]) && $matches[3] === '🪽';

					if (!empty($identifier) && $identifier !== 0) {
						// Initialized the distribution identifier

						// Initializing the distribution model
						$model_distribution = new distribution;

						// Initializing the distribution
						$distribution = $model_distribution->database->read(
							filter: fn(record $record) => $record->identifier === $identifier,
							amount: 1
						)[0] ?? null;

						if ($distribution instanceof record) {
							// Initialized the distribution

						// Initializing the member model
						$model_member = new member;

							// Initializing function of the message join buttom updating
							$update = function (context $context, record $member) use ($account, $localization, $distribution, $name, $recognized): void {
								// Initializing the updated inline keyboard of the message
								$keyboard = process_distribution_search::keyboard(
									distribution: $distribution,
									member: $member,
									localization: $localization,
									messages: $account->authorized_messages === 1,
									joins: $account->authorized_joins === 1,
								);

								// Initializing the updated text of the message
								$text = process_distribution_search::message(
									distribution: $distribution,
									localization: $localization,
									name: $name,
									recognized: $recognized
								);

								// Sending the updated message inline keyboard
								$context->editMessageText(
									$text,
									[
										'message_inline_id' => $context->getCallbackQuery()->getInlineMessageId(),
										'reply_markup' => ['inline_keyboard' => $keyboard]
									]
								);
							};

							// Searching for the member record
							$member = $model_member->database->read(
								filter: fn(record $record) => $record->distribution === $distribution->identifier && $record->account === $account->identifier,
								amount: 1
							)[0] ?? null;

							if ($member instanceof record) {
								// Found the member of the distribution 

								if ($member->status === status::unknown->value || $member->status === status::planned->value) {
									// The account has planned to join to the distribution or leaved the distribution

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_search_already_leaved'] . '*')
										->then(function (message $message) use ($context, $update, $member) {
											// Sended the message

											// Updating the message with the join button
											$update($context, $member);

											// Ending the conversation process
											$context->endConversation();
										});
								} else if ($member->status === 2) {
									// The account has joined to the distribution

									// Updating the member record
									$updated = $model_member->database->read(
										filter: fn(record $record) => $record->identifier === $member->identifier,
										update: function (record &$record) {
											$record->status = status::unknown->value;
											$record->updated = svoboda::timestamp();
										},
										amount: 1
									)[0] ?? null;

									if ($updated instanceof record) {
										// Updated the member record

										// Sending the message
										$context->sendMessage('👋 *' . $localization['distribution_search_leaved'] . '*')
											->then(function (message $message) use ($context, $update, $updated) {
												// Sended the message

												// Updating the message with the join button
												$update($context, $updated);

												// Ending the conversation process
												$context->endConversation();
											});
									} else {
										// Not updated the member record

										// Sending the message
										$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_updated'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();
											});
									}
								}
							} else {
								// Not found the member of the distribution 

								// Creating the member record
								$record = $model_member->create(
									distribution: $distribution->identifier,
									account: $account->identifier,
									status: status::unknown
								);

								if ($record) {
									// Created the member record

									// Searching for the member record
									$member = $model_member->database->read(
										filter: fn(record $member) => $member->identifier === $record,
										amount: 1
									)[0] ?? null;

									if ($member instanceof record) {
										// Found the member of the distribution 

										// Sending the message
										$context->sendMessage('👋 *' . $localization['distribution_search_leaved'] . '*')
											->then(function (message $message) use ($context, $update, $member) {
												// Sended the message

												// Updating the message with the join button
												$update($context, $member);

												// Ending the conversation process
												$context->endConversation();
											});
									} else {
										// Not found the member of the distribution 

										// Sending the message
										$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
											->then(function (message $message) use ($context) {
												// Sended the message

												// Ending the conversation process
												$context->endConversation();
											});
									}
								} else {
									// Not created the member record

									// Sending the message
									$context->sendMessage('⚠️ *' . $localization['distribution_search_member_not_created'] . '*')
										->then(function (message $message) use ($context) {
											// Sended the message

											// Ending the conversation process
											$context->endConversation();
										});
								}
							}
						} else {
							// Not initialized the distribution

							// Sending the message
							$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
								->then(function (message $message) use ($context) {
									// Sended the message

									// Ending the conversation process
									$context->endConversation();
								});
						}
					} else {
						// Not initialized the distribution identifier

						// Sending the message
						$context->sendMessage('⚠️ *' . $localization['distribution_search_distribution_not_initialized'] . '*')
							->then(function (message $message) use ($context) {
								// Sended the message

								// Ending the conversation process
								$context->endConversation();
							});
					}
				} else {
					// Not initialized the message

					// Sending the message
					$context->sendMessage('⚠️ *' . $localization['distribution_search_message_not_initialized'] . '*')
						->then(function (message $message) use ($context) {
							// Sended the message

							// Ending the conversation process
							$context->endConversation();
						});
				}
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
