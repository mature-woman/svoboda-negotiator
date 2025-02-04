<?php

declare(strict_types=1);

namespace svoboda\negotiator\models;

// Files of the project
use svoboda\negotiator\models\core;

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
 * Account
 *
 * @package svoboda\negotiator\models
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class account extends core
{
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
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Initializing the database
		$this->database = new database()
			->encoding(encoding::utf8)
			->columns(
				new column('identifier', type::integer_unsigned),
				new column('identifier_telegram', type::integer),
				new column('domain', type::string, ['length' => 32]),
				new column('name_first', type::string, ['length' => 64]),
				new column('name_second', type::string, ['length' => 64]),
				new column('language', type::string, ['length' => 2]),
				new column('robot', type::char),
				new column('authorized_system', type::char),
				new column('authorized_contact', type::char),
				new column('authorized_request', type::char),
				new column('authorized_settings', type::char),
				new column('authorized_system_settings', type::char),
				new column('created', type::integer_unsigned),
				new column('updated', type::integer_unsigned)
			)
			->connect($this->file);
	}

	/**
	 * Initialize
	 *
	 * Searches for the account record in the database, and if it does not find it, it creates it
	 *
	 * @param telegram $telegram The telegram account
	 *
	 * @throws exception_runtime if update the account record in the database by the telegram account values
	 * @throws exception_runtime if failed to find the registered account
	 * @throws exception_runtime if failed to registrate the account
	 *
	 * @return record The account record from the database
	 */
	public function initialize(telegram $telegram): record
	{
		// Searching for the account in the database
		$account = $this->database->read(filter: fn(record $record) => $record->identifier_telegram === $telegram->getId(), amount: 1)[0] ?? null;

		if ($account instanceof record) {
			// Found the account record

			if (
					$account->name_first !== $telegram->getFirstName() ||
					$account->name_second !== $telegram->getLastName() ||
					$account->domain !== $telegram->getUsername()
			) {
				// The telegram account was updated
				
				// Updating the account in the database
				$updated = $this->database->read(
					filter: fn(record $record) => $record->identifier_telegram === $telegram->getId(),
					update: function (record &$record) use ($telegram){
						// Writing new values into the record
						$record->name_first = $telegram->getFirstName();
						$record->name_second = $telegram->getLastName();
						$record->domain = $telegram->getUsername();
						$record->updated = svoboda::timestamp();
					},
					amount: 1
				)[0] ?? null;

				if ($updated instanceof record && $updated->values() !== $account->values()) {
					// Updated the account in the database

					// Exit (success)
					return $updated;
				} else {
					// Not updated the account in the database

					// Exit (fail)
					throw new exception_runtime('Failed to update the account record in the database by the telegram account values');
				}
			}

			// Exit (success)
			return $account;
		} else {
			// Not found the account record

			if ($this->registrate($telegram)) {
				// Registered the account

				// Searching for the registered account in the database
				$account = $this->database->read(filter: fn(record $record) => $record->identifier_telegram === $telegram->getId(), amount: 1)[0] ?? null;

				if ($account instanceof record) {
					// Found the registered account

					// Exit (success)
					return $account;
				} else {
					// Not found the registered account

					// Exit (fail)
					throw new exception_runtime('Failed to find the registered account');
				}
			} else {
				// Not registered the account

				// Exit (fail)
				throw new exception_runtime('Failed to registrate the account');
			}
		}
	}

	/**
	 * Registrate
	 *
	 * Creates the account record in the database
	 *
	 * @param telegram $telegram The telegram account
	 *
	 * @return int|false The record identifier, if created
	 */
	public function registrate(telegram $telegram): int|false
	{
		// Initializing the identifier
		$identifier = $this->database->count() + 1;

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			(int) $telegram->getId(),
			$telegram->getFirstName(),
			$telegram->getLastName(),
			$telegram->getUsername(),
			$telegram->getLanguageCode(),
			(int) $telegram->isBot(),
			1,
			1,
			1,
			1,
			0,
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the accound record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}
}
