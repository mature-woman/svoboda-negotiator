<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\traits;

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
 * @package svoboda\antivertical\models\traits
 *
 * @method self __construct(?record $record) Constructor
 * @method static|false read(callable $filter) Read from the database
 * @method static|false update() Update the record in the database
 * @method void __set(string $name, mixed $value = null) Write into the database record property
 * @method mixed __get(string $name) Read from the database record property
 * @method bool __isset(string $name) Check that the database record property is initialized
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
trait ar
{
	/**
	 * Read
	 *
	 * Search for the record in the database
	 *
	 * @return static|false The record impementator object, if found
	 */
	public function read(callable $filter): static|false
	{
		// Reading from the database
		$record = $this->database->read(
			filter: $filter,
			amount: 1,
			offset: 0
		)[0] ?? false;

		if ($record instanceof record) {
			// Initialized the record

			// Exit (success)
			return new static($record);
		}

		// Exit (fail)
		return false;
	}

	/**
	 * Update
	 *
	 * Write the record new values into the database
	 *
	 * @return static|false The updated record, if updated (new instance)
	 */
	public function update(): static|false
	{
		// Writing into the database
		$record = $this->database->read(
			filter: fn(record $record) => $record->identifier === $this->record->identifier,
			update: function (record &$record) {
				$this->record->updated = svoboda::timestamp();
				$record = $this->record;
			},
			amount: 1,
			offset: 0
		)[0] ?? false;

		if ($record instanceof record) {
			// Initialized the record

			// Exit (success)
			return new static($record);
		}

		// Exit (fail)
		return false;
	}

	/**
	 * Write
	 *
	 * Write into the database record property
	 *
	 * @param string $name Name of the property
	 * @param mixed $value Value of the property
	 *
	 * @return void
	 */
	public function __set(string $name, mixed $value = null): void
	{
		match ($name) {
			'record' => $this->record = $value,
			default => $this->record->{$name} = $value
		};
	}

	/**
	 * Read
	 *
	 * Read from the database record property
	 *
	 * @param string $name Name of the property
	 *
	 * @return mixed Content of the property, if they are found
	 */
	public function __get(string $name): mixed
	{
		return match ($name) {
			'record' => $this->record,
			default => $this->record->{$name}
		};
	}

	/**
	 * Check for initialization
	 *
	 * Check that the database record property is initialized
	 *
	 * @param string $name Name of the property
	 *
	 * @return bool The property is initialized?
	 */
	public function __isset(string $name): bool
	{
		// Check of initialization of the property and exit (success)
		return match ($name) {
			'record' => isset($this->record),
			default => isset($this->record->{$name})
		};
	}
}
