<?php

declare(strict_types=1);

namespace svoboda\antivertical\models;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\connection,
	svoboda\antivertical\models\telegram,
	svoboda\antivertical\models\membership,
	svoboda\antivertical\models\interfaces\ar as active_record,
	svoboda\antivertical\models\traits\ar as active_record_trait,
	svoboda\antivertical\models\enumerations\language,
	svoboda\antivertical\models\enumerations\membership\status,
	svoboda\antivertical\models\account\localization;

// Svoboda time
use svoboda\time\statement as svoboda;

// Baza database
use mirzaev\baza\database,
	mirzaev\baza\column,
	mirzaev\baza\record,
	mirzaev\baza\enumerations\encoding,
	mirzaev\baza\enumerations\type;

// Built-in libraries
use Exception as exception,
	RuntimeException as exception_runtime,
	LogicException as exception_logic,
	InvalidArgumentException as exception_invalid_argument;
/**
 * Account
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
final class account extends core implements active_record
{
	use active_record_trait;

	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'accounts.baza';

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
			->encoding(encoding::utf8)
			->columns(
				new column('identifier', type::long_long_unsigned),
				new column('name', type::string, ['length' => 64]),
				new column('language', type::string, ['length' => 2]),
				new column('authorized_system', type::char),
				new column('authorized_messages', type::char),
				new column('authorized_authonom', type::char),
				new column('authorized_structor', type::char),
				new column('authorized_volunteer', type::char),
				new column('authorized_investor', type::char),
				new column('authorized_recruiter', type::char),
				new column('authorized_settings', type::char),
				new column('authorized_system_accounts', type::char),
				new column('authorized_system_distributions', type::char),
				new column('authorized_system_settings', type::char),
				new column('updated', type::integer_unsigned),
				new column('created', type::integer_unsigned)
			)
			->connect($this->file);

		// Initializing the localizations implementator
		$this->localization = new localization;

		// Initializing the database record
		if ($record) $this->record = $record;
	}

	/**
	 * Initialize
	 *
	 * Searches for the account record by the telegram account in the database, 
	 * and if it does not find it, then create the account record and the connection record
	 *
	 * @param telegram $telegram The telegram account
	 *
	 * @throws exception_runtime if failed to deactivate the connection between missing account and the telegram account
	 * @throws exception_runtime if failed to connect the account with the telegram account
	 * @throws exception_runtime if failed to find the created account
	 * @throws exception_runtime if failed to create the account
	 *
	 * @return static The account record implementator
	 */
	public function initialize(telegram $telegram): static
	{
		// Initializing the connection model
		$connection = new connection;

		// Searching for the connection record between theaccount and the telegram account in the database
		$connected = $connection->read(filter: fn(record $record) => $record->telegram === $telegram->identifier);

		if ($connected instanceof connection) {
			// Found the connection record between the account and the telegram account

			// Searching for the account in the database
			$account = $this->read(filter: fn(record $record) => $record->identifier === $connected->account);

			if ($account instanceof static) {
				// Found the account

				// Exit (success)
				return $account;
			} else {
				// Not found the account

				// Deactivating the connection between missing account and the telegram account
				$deactivated = $connected->update(
					filter: fn(record $record) => $record->identifier === $connected->identifier,
					update: function (record &$record) {
						$record->active = 0;
						$record->updated = svoboda::timestamp();
					}
				);

				if ($deactivated instanceof connection && $deactivated->active === 0) {
					// Deactivated the connection between missing account and the telegram account

					// Creating the account
					goto create;
				} else {
					// Failed to deactivate the connection between missing account and the telegram account

					// Exit (fail)
					throw new exception_runtime('Failed to deactivate the connection between missing account and the telegram account');
				}
			}
		} else {
			// Not found the connection record between the account and the telegram account

			// Creating the account process start
			create:

			// Creating the account
			$identifier = $this->create("$telegram->name_first $telegram->name_second", language::{$telegram->language ?? language::en->name} ?? language::en);

			if ($identifier) {
				// Created the account

				// Searching for the created account in the database
				$account = $this->read(filter: fn(record $record) => $record->identifier === $identifier);

				if ($account instanceof static) {
					// Found the created account

					// Connecting the created account with the telegram account
					$connected = $connection->create(account: $account->identifier, telegram: $telegram->identifier);

					if ($connected) {
						// Connected the created account with the telegram account

						// Exit (success)
						return $account;
					} else {
						// Not connected the created account with the telegram account

						// Exit (fail)
						throw new exception_runtime('Failed to connect the account with the telegram account');
					}
				} else {
					// Not found the created account

					// Exit (fail)
					throw new exception_runtime('Failed to find the created account');
				}
			} else {
				// Not created the account

				// Exit (fail)
				throw new exception_runtime('Failed to create the account');
			}
		}
	}

	/**
	 * Create
	 *
	 * Creates the account record in the database
	 *
	 * @param string $name Name
	 * @param language $language Language for generating views
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(string $name, language $language): int|false
	{
		// Initializing the identifier
		$identifier = $this->database->count() + 1;

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			$name,
			$language->name,
			ACCOUNT_ACCESS_SYSTEM,
			ACCOUNT_ACCESS_MESSAGES,
			ACCOUNT_ACCESS_AUTHONOM,
			ACCOUNT_ACCESS_STRUCTOR,
			ACCOUNT_ACCESS_VOLUNTEER,
			ACCOUNT_ACCESS_INVESTOR,
			ACCOUNT_ACCESS_RECRUITER,
			ACCOUNT_ACCESS_SETTINGS,
			ACCOUNT_ACCESS_SYSTEM_ACCOUNTS,
			ACCOUNT_ACCESS_SYSTEM_DISTRIBUTIONS,
			ACCOUNT_ACCESS_SYSTEM_SETTINGS,
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}

	/**
	 * Distribution
	 *
	 * Search for the account distribution
	 *
	 * @return distribution|false The account distribution, if found
	 */
	public function distribution(): distribution|false
	{
		// Searching for the distribution membership
		$membership = $this->memberships(amount: 1, offset: 0)[0] ?? false;

		if ($membership instanceof membership) {
			// Initialized the distribution membership

			// Initializing the distribution model
			$model = new distribution();

			// Searching for the distribution
			$distribution = $model->read(filter: fn(record $record) => $record->identifier === $membership->distribution);

			// Exit (success/fail)
			return $distribution;
		}

		// Exit (fail)
		return false;
	}

	/**
	 * Memberships
	 *
	 * @param status $status A membership status
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array The account memberships
	 */
	public function memberships(
		status $status = status::joined,
		int $amount = 100,
		int $offset = 0
	): array {
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->account === $this->identifier && $record->status === $status->value,
			amount: $amount,
			offset: $offset
		);

		// Declaring the buffer of implemented records
		$implemented = [];

		foreach ($memberships as $record) {
			// Iterating over memberships records

			// Initializing the implementator object
			$implementator = new membership();

			if ($implementator instanceof active_record) {
				// The implementator object implements the Active Record pattern

				// Writing the record into the implementator object
				$implementator->record = $record;
			} else {
				// The implementator object not implements the Active Record pattern

				// Exit (fail)
				throw new exception_logic('The implementator object not implements the Active Record pattern');
			}

			// Writing into the buffer of implemented record;
			$implemented[] = $implementator;
		}

		// Exit (success)
		return $implemented;
	}

	/**
	 * Structors
	 *
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array Structors
	 */
	public static function structors(int $amount = 100,	int $offset = 0): array
	{
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->status === status::joined->value,
			amount: $amount,
			offset: $offset
		);

		// Exit (success)
		return $memberships;
	}

	/**
	 * Planned
	 *
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array Planned
	 */
	public static function planned(int $amount = 100,	int $offset = 0): array
	{
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->status === status::planned,
			amount: $amount,
			offset: $offset
		);

		// Exit (success)
		return $memberships;
	}

	/**
	 * Autonoms 
	 *
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array Autonoms
	 */
	public static function autonoms(int $amount = 100,	int $offset = 0): array
	{
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->status === status::joined,
			amount: $amount,
			offset: $offset
		);

		// Exit (success)
		return [];
	}

	/**
	 * Volunteers
	 *
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array Volunteers
	 */
	public static function volunteers(int $amount = 100,	int $offset = 0): array
	{
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->status === status::joined,
			amount: $amount,
			offset: $offset
		);

		// Exit (success)
		return [];
	}

	/**
	 * Investors 
	 *
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array Investors
	 */
	public static function investors(int $amount = 100,	int $offset = 0): array
	{
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->status === status::joined,
			amount: $amount,
			offset: $offset
		);

		// Exit (success)
		return [];
	}

	/**
	 * Recruiters
	 *
	 * @param int $amount Amount
	 * @param int $offset Offset
	 *
	 * @return array Recruiters
	 */
	public static function recruiters(int $amount = 100,	int $offset = 0): array
	{
		// Initializing the membership model
		$model = new membership;

		// Initializing the account memberships
		$memberships = $model->database->read(
			filter: fn(record $record) => $record->status === status::joined,
			amount: $amount,
			offset: $offset
		);

		// Exit (success)
		return [];
	}
}
