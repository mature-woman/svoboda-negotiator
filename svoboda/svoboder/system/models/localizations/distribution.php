<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\localizations;

// Files of the project
use svoboda\svoboder\models\core,
	svoboda\svoboder\models\enumerations\language;

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
 * @package svoboda\svoboder\models\localizations
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class distribution extends core
{
	/**
	 * File
	 *
	 * @var string $database Path to the database file
	 */
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'localizations' . DIRECTORY_SEPARATOR . 'distributions.baza';

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
				new column('distribution', type::integer_unsigned),
				new column('language', type::string, ['length' => 2]),
				new column('name', type::string, ['length' => 32]),
				new column('created', type::integer_unsigned),
				new column('updated', type::integer_unsigned)
			)
			->connect($this->file);
	}

	/**
	 * Create
	 *
	 * Creates the distribution localization record in the database
	 *
	 * @param int $distribution Identifier of the distribution
	 * @param language $language Language
	 * @param string $name Name
	 *
	 * @return int|false The record identifier, if created
	 */
	public function create(int $distribution, language $language, string $name): int|false
	{
		// Initializing the identifier
		$identifier = $this->database->count() + 1;

		// Initializing the record
		$record = $this->database->record(
			$identifier,
			$distribution,
			$language->name,
			$name,
			svoboda::timestamp(),
			svoboda::timestamp()
		);

		// Creating the accound record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}
}
