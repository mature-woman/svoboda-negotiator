<?php

declare(strict_types=1);

namespace svoboda\antivertical\models;

// Files of the project
use svoboda\antivertical\models\core,
	svoboda\antivertical\models\enumerations\entity,
	svoboda\antivertical\models\enumerations\member\status;

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
 * Relation
 *
 * @package svoboda\antivertical\models
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class relation extends core
{
	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'relations.baza';

	/**
	 * Database
	 *
	 * Identifier: The record identifier
	 * From: The account identifier
	 * To: The account identifier
	 * Type: Type
	 * Updated: Timestamp of the last the record update
	 * Created: Timestamp of the record creating
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
			->encoding(encoding::ascii)
			->columns(
				new column('identifier', type::long_long_unsigned),
				new column('from', type::long_long_unsigned),
				new column('to', type::long_long_unsigned),
				new column('type', type::char),
				new column('updated', type::integer_unsigned),
				new column('created', type::integer_unsigned)
			)
			->connect($this->file);
	}

	/**
	 * Create
	 *
	 * Creates the member record in the database
	 *
	 * @param int $from Identifier of the account (svoboda\antivertical\models\account)
	 * @param int $to Identifier of the account (svoboda\antivertical\models\account)
	 * @param type $type Type of the relation
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(int $from, int $to, type $type): int|false
	{
		// Initializing the identifier
		$identifier = $this->database->count() + 1;

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			$distribution,
			$account,
			$status->value,
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}
}
