<?php

declare(strict_types=1);

namespace svoboda\antivertical\models;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\enumerations\entity,
	svoboda\antivertical\models\enumerations\membership\status,
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
use Zanzara\Telegram\Type\User as telegram;

// Built-in libraries
use Exception as exception,
	RuntimeException as exception_runtime;

/**
 * Connection between account::class and telegram::class
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
final class connection extends core implements active_record
{
	use active_record_trait;

	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'connection.baza';

	/**
	 * Database
	 *
	 * Identifier: The record identifier
	 * Account: The account identifier
	 * Telegram: The telegram account identifier
	 * Updated: Timestamp of the last the record update
	 * Created: Timestamp of the record creating
	 *
	 * @var database $database The database
	 */
	public protected(set) database $database;

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
				new column('account', type::long_long_unsigned),
				new column('telegram', type::long_long_unsigned),
				new column('active', type::char),
				new column('updated', type::integer_unsigned),
				new column('created', type::integer_unsigned)
			)
			->connect($this->file);

		// Initializing the database record
		if ($record) $this->record = $record;
	}

	/**
	 * Create
	 *
	 * Creates the member record in the database
	 *
	 * @param int $account Identifier of the account
	 * @param int $telegram Identifier of the telegram account
	 * @param bool $active Is the connection active?
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(int $account, int $telegram, bool $active = true): int|false
	{
		// Initializing the identifier
		$identifier = $this->database->count() + 1;

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			$account,
			$telegram,
			(int) $active,
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}
}
