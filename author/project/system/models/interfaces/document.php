<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models\interfaces;

// Library для ArangoDB
use ArangoDBClient\Document as _document;

/**
 * Document
 *
 * Interface for implementing a document instance from ArangoDB
 *
 * @param _document $document An instance of the ArangoDB document from ArangoDB (protected readonly)
 *
 * @package svoboda\negotiatior\models\interfaces
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
interface document
{
	/**
	 * Write
	 *
	 * Write a property into an instance of the ArangoDB document
	 *
	 * @param string $name Name of the property
	 * @param mixed $value Content of the property
	 *
	 * @return void
	 */
	public function __set(string $name, mixed $value = null): void;

	/**
	 * Read
	 *
	 * Read a property from an instance of the ArangoDB docuemnt
	 * 
	 * @param string $name Name of the property
	 *
	 * @return mixed Content of the property
	 */
	public function __get(string $name): mixed;


	/**
	 * Delete
	 *
	 * Deinitialize the property in an instance of the ArangoDB document
	 *
	 * @param string $name Name of the property
	 *
	 * @return void
	 */
	public function __unset(string $name): void;

	/**
	 * Check of initialization
	 *
	 * Check of initialization of the property into an instance of the ArangoDB document
	 *
	 * @param string $name Name of the property
	 *
	 * @return bool The property is initialized?
	 */
	public function __isset(string $name): bool;

	/**
	 * Execute a method
	 *
	 * Execute a method from an instance of the ArangoDB document
	 *
	 * @param string $name Name of the method
	 * @param array $arguments Arguments for the method
	 *
	 * @return mixed Result of execution of the method
	 */
	public function __call(string $name, array $arguments = []): mixed;
}
