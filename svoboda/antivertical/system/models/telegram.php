<?php

declare(strict_types=1);

namespace svoboda\antivertical\models;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\account\localization,
	svoboda\antivertical\models\interfaces\ar as active_record,
	svoboda\antivertical\models\traits\ar as active_record_trait;

// Svoboda time
use svoboda\time\statement as svoboda;

// Baza database
use mirzaev\baza\database,
	mirzaev\baza\column,
	mirzaev\baza\record,
	mirzaev\baza\enumerations\encoding,
	mirzaev\baza\enumerations\type;

// Framework for Telegram
use Zanzara\Telegram\Type\User as model;

// Built-in libraries
use Exception as exception,
	RuntimeException as exception_runtime;

/**
 * Telegram account
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
final class telegram extends core implements active_record
{
	use active_record_trait;

	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'telegram.baza';

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
				new column('domain', type::string, ['length' => 32]),
				new column('name_first', type::string, ['length' => 64]),
				new column('name_second', type::string, ['length' => 64]),
				new column('language', type::string, ['length' => 2]),
				new column('robot', type::char),
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
	 * Searches for the telegram account record in the database, and if it does not find it, then create
	 *
	 * @param model $telegram The telegram account
	 *
	 * @throws exception_runtime if update the telegram account record in the database by the telegram account values
	 * @throws exception_runtime if failed to find the created telegram account
	 * @throws exception_runtime if failed to create the telegram account
	 *
	 * @return static The telegram account record implementator
	 */
	public function initialize(model $telegram): static
	{
		// Searching for the account in the database
		$instance = $this->read(filter: fn(record $record) => $record->identifier === $telegram->getId());

		if ($instance instanceof static) {
			// Found the telegram account record

			if (
				$instance->name_first !== $telegram->getFirstName() ||
				$instance->name_second !== $telegram->getLastName() ||
				$instance->domain !== $telegram->getUsername()
			) {
				// The telegram account was updated

				// Updating the account in the database
				$updated = $this->update(
					filter: fn(record $record) => $record->identifier === $telegram->getId(),
					update: function (record &$record) use ($telegram) {
						// Writing new values into the record
						$record->name_first = $telegram->getFirstName();
						$record->name_second = $telegram->getLastName();
						$record->domain = $telegram->getUsername();
						$record->updated = svoboda::timestamp();
					}
				);

				if ($updated instanceof record && $updated->values() !== $instance->record->values()) {
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
			return $instance;
		} else {
			// Not found the account record

			if ($this->create($telegram)) {
				// Created the account

				// Searching for the created telegram account in the database
				$account = $this->read(filter: fn(record $record) => $record->identifier === $telegram->getId());

				if ($account instanceof static) {
					// Found the created telegram account

					// Exit (success)
					return $account;
				} else {
					// Not found the created telegram account

					// Exit (fail)
					throw new exception_runtime('Failed to find the created telegram account');
				}
			} else {
				// Not created the telegram account

				// Exit (fail)
				throw new exception_runtime('Failed to create the telegram account');
			}
		}
	}

	/**
	 * Create
	 *
	 * Creates the account record in the database
	 *
	 * @param model $telegram The telegram account
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(model $telegram): int|false
	{
		// Initializing the identifier
		$identifier = (int) $telegram->getId();

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			$telegram->getUsername(),
			$telegram->getFirstName(),
			$telegram->getLastName(),
			$telegram->getLanguageCode(),
			(int) $telegram->isBot(),
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}
}
