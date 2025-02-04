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
 * Distribution
 *
 * @package svoboda\negotiator\models
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
	protected string $file = DATABASES . DIRECTORY_SEPARATOR . 'distributions.baza';

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
			->encoding(encoding::ascii)
			->columns(
				new column('identifier', type::integer_unsigned),
				new column('creator', type::integer_unsigned),
				new column('latitude', type::float),
				new column('longitude', type::float),
				new column('created', type::integer_unsigned),
				new column('updated', type::integer_unsigned)
			)
			->connect($this->file);
	}

	/**
	 * Create
	 *
	 * Creates the distribution record in the database
	 *
	 * @param int $creator Identifier of the creator account
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

		// Creating the accound record in the database
		$created = $this->database->write($record);

		// Exit (success)
		return $created ? $identifier : false;
	}
}
