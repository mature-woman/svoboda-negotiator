<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\interfaces;

// Files of the project
use svoboda\antivertical\models\core;

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
 * AR
 *
 * The "Active Record" architectural pattern
 *
 * @see https://en.wikipedia.org/wiki/Active_record_pattern Active Record
 *
 * @package svoboda\antivertical\models\interfaces
 *
 * @method static|false read(callable $filter) Read from the database
 * @method static|false update() Update the record in the database
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
interface ar
{
	/**
	 * Constructor
	 *
	 * @throws exception_invalid_argument If not initialized the database columns parameters
	 *
	 * @return void
	 */
	public function __construct();

	/**
	 * Read
	 *
	 * Search for the record in the database
	 *
	 * @return static|false The record impementator object, if found
	 */
	public function read(callable $filter): static|false;

	/**
	 * Update
	 *
	 * Write the record new values into the database
	 *
	 * @return static|false The updated record, if updated (new instance)
	 */
	public function update(): static|false;
}
