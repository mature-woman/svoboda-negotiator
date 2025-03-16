<?php

declare(strict_types=1);

namespace svoboda\svoboder\models;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\enumerations\entity,
	svoboda\svoboder\models\enumerations\member\status;

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
 * Member
 *
 * @package svoboda\svoboder\models
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class member extends core
{
	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'members.baza';

	/**
	 * Database
	 *
	 * Identifier: The record identifier
	 * Distribution: The distribution
	 * Account: The account
	 * Status: 1 - planned; 2 - joined
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
				new column('identifier', type::integer_unsigned),
				new column('distribution', type::integer_unsigned),
				new column('account', type::integer_unsigned),
				new column('status', type::char),
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
	 * @param int $distribution Identifier of the distribution
	 * @param int $account Identifier of the account
	 * @param status $status The distribution memberhip status
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(int $distribution, int $account, status $status = status::unknown): int|false
	{var_dump($this->database->count());
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
