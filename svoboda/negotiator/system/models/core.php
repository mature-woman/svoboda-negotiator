<?php

declare(strict_types=1);

namespace svoboda\negotiator\models;

// Framework for PHP
use mirzaev\minimal\model;

// Built-in libraries
use exception;

/**
 * Models core
 *
 * @package svoboda\negotiator\models
 *
 * @method void __construct(bool $initialize) Constructor
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
class core extends model
{
	/**
	 * Constructor
	 *
	 * @param bool $initialize Initialize a model?
	 *
	 * @return void
	 */
	public function __construct(bool $initialize = true)
	{
		// For the extends system
		parent::__construct($initialize);
	}

	/**
	 * Write
	 *
	 * @param string $name Name of the property
	 * @param mixed $value Value of the property
	 *
	 * @return void
	 */
	public function __set(string $name, mixed $value = null): void
	{
		match ($name) {
			default => parent::__set($name, $value)
		};
	}

	/**
	 * Read
	 *
	 * @param string $name Name of the property
	 *
	 * @return mixed Content of the property, if they are found
	 */
	public function __get(string $name): mixed
	{
		return match ($name) {
			default => parent::__get($name)
		};
	}

	/**
	 * Delete
	 *
	 * @param string $name Name of the property 
	 *
	 * @return void
	 */
	public function __unset(string $name): void
	{
		// Deleting a property and exit (success)
		parent::__unset($name);
	}

	/**
	 * Check of initialization
	 *
	 * @param string $name Name of the property
	 *
	 * @return bool The property is initialized?
	 */
	public function __isset(string $name): bool
	{
		// Check of initialization of the property and exit (success)
		return parent::__isset($name);
	}

	/**
	 * Call a static property or method
	 *
	 * @param string $name Name of the property or the method
	 * @param array $arguments Arguments for the method
	 */
	public static function __callStatic(string $name, array $arguments): mixed
	{
		return match ($name) {
			default => throw new exception("Not found: $name", 500)
		};
	}
}

