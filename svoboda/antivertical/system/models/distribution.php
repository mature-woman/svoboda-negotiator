<?php

declare(strict_types=1);

namespace svoboda\antivertical\models;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\distribution\localization,
	svoboda\antivertical\models\distribution\message,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\interfaces\ar as active_record,
	svoboda\antivertical\models\traits\ar as active_record_trait,
	svoboda\antivertical\models\enumerations\membership\status;

// Svoboda time
use svoboda\time\statement as svoboda;

// Baza database
use mirzaev\baza\database,
	mirzaev\baza\column,
	mirzaev\baza\record,
	mirzaev\baza\enumerations\encoding,
	mirzaev\baza\enumerations\type;

// Framework for Telegram
use Zanzara\Telegram\Type\User as telegram;

// Built-in libraries
use Exception as exception,
	RuntimeException as exception_runtime;

/**
 * Distribution
 *
 * @uses active_record
 * @uses active_record_trait
 *
 * @package svoboda\antivertical\models
 *
 * @property string $file Path to the database file
 * @property database $database The database
 * @property record $record The database record
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class distribution extends core implements active_record
{
	use active_record_trait;

	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'distributions.baza';

	/**
	 * Database
	 *
	 * @var database $database The database
	 */
	public protected(set) database $database;

	/**
	 * Localization
	 *
	 * @var localization $localization The localizations implementator
	 */
	public protected(set) localization $localization;

	/**
	 * Message
	 *
	 * @var localization $localization The messages implementator
	 */
	public protected(set) message $message;

	/**
	 * Constructor
	 *
	 * @param record|null $record The database record
	 *
	 * @return void
	 */
	public function __construct(?record $record = null)
	{
		// Initializing the database
		$this->database = new database()
			->encoding(encoding::ascii)
			->columns(
				new column('identifier', type::long_long_unsigned),
				new column('creator', type::long_long_unsigned),
				new column('latitude', type::float),
				new column('longitude', type::float),
				new column('updated', type::integer_unsigned),
				new column('created', type::integer_unsigned)
			)
			->connect($this->file);

		// Initializing the localizations implementator
		$this->localization = new localization;

		// Initializing the messages implementator
		$this->message = new message;

		// Initializing the database record
		if ($record) $this->record = $record;
	}

	/**
	 * Create
	 *
	 * Creates the distribution record in the database
	 *
	 * @param int $creator Identifier of the creator account (svoboda\antivertical\models\account)
	 * @param float $latitude Latitude
	 * @param float $longitude Longitude
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(int $creator, float $latitude = 0, float $longitude = 0): int|false
	{
		// Initializing the identifier
		$identifier = $this->database->count() + 1;

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			$creator,
			$latitude,
			$longitude,
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}

	/**
	 * Localization
	 *
	 * Initialize the distribution localization
	 * Priority: $language argument > english > the distribution creator language > first in the localizations registry
	 *
	 * @param language $language Language
	 *
	 * @return localization|false The distribtion localization, if initialized
	 */
	public function localization(language $language): localization|false
	{
		// Initializing localizations
		$localizations = $this->localization->database->read(
			filter: fn(record $localization) => $localization->distribution === $this->identifier,
			amount: DISTRIBUTIONS_SEARCH_DISTRIBUTION_LOCALIZATIONS_AMOUNT
		);


		if (count($localizations) > 0) {
			// Initialized the distributions localizations

			foreach ($localizations as $record) {
				// Iterating over localizations

				if ($record->language === $language->name) {
					// Found localization by the account language

					// Initializing localization by the account language
					$this->localization->record = $record;

					// Exit (success)
					break;
				}
			}

			if (!isset($this->localization->record)) {
				// Not initialized localization by the account language

				foreach ($localizations as $record) {
					// Iterating over localizations

					if ($record->language === 'en') {
						// Found localization by english language

						// Initializing localization by english language
						$this->localization->record = $record;

						// Exit (success)
						break;
					}
				}

				if (!isset($this->localization->record)) {
					// Not initialized localization by english language

					// Initializing the account model
					$model_account = new account;

					// Initializing the distribution creator account
					$creator = $model_account->read(filter: fn(record $account) => $account->identifier === $this->creator);

					if ($creator instanceof account) {
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

					if (!isset($this->localization->record)) {
						// Not initialized localization by the distribution creator account language

						// Initializing localization by the first found record
						$this->localization->record = $localizations[0];
					}
				}
			}
		}

		// Exit (success/fail)
		return $this->localization;
	}

	/**
	 * Structors
	 *
	 * Search for the distribution structors
	 *
	 * @param int $amount Amount
	 * @param int $offset Offest
	 *
	 * @return array The distribution structors
	 */
	public function structors(int $amount = 100, int $offset = 0): array
	{
		// Initializing the membership model
		$model_membership = new membership();

		// Searching for memberships
		$memberships = $model_membership->database->read(
			filter: fn(record $record) => $record->distribution === $this->identifier && $record->status === status::joined->value,
			amount: $amount,
			offset: $offset
		);

		// Initializing the account model
		$model_account = new account();

		// Declaring the buffer of found structors
		$structors = [];

		foreach ($memberships as $membership) {
			// Iterating over memberships

			// Searching for the structor account
			$structor = $model_account->read(filter: fn(record $record) => $record->identifier === $membership->account);

			if ($structor instanceof account) {
				// Initialized the structor account

				// Writing into the buffer of found structors
				$structors[] = $structor;
			}
		}

		// Exit (fail)
		return $structors;
	}

	/**
	 * Planners
	 *
	 * Search for the distribution planners
	 *
	 * @param int $amount Amount
	 * @param int $offset Offest
	 *
	 * @return array The distribution planners
	 */
	public function planners(int $amount = 100, int $offset = 0): array
	{
		// Initializing the membership model
		$model_membership = new membership();

		// Searching for memberships
		$memberships = $model_membership->database->read(
			filter: fn(record $record) => $record->distribution === $this->identifier && $record->status === status::planned->value,
			amount: $amount,
			offset: $offset
		);

		// Initializing the account model
		$model_account = new account();

		// Declaring the buffer of found planners
		$planners = [];

		foreach ($memberships as $membership) {
			// Iterating over memberships

			// Searching for the planner account
			$planner = $model_account->read(filter: fn(record $record) => $record->identifier === $membership->account);

			if ($planner instanceof account) {
				// Initialized the planner account

				// Writing into the buffer of found planners
				$planners[] = $planner;
			}
		}

		// Exit (fail)
		return $planners;
	}

}
