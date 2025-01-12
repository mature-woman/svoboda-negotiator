<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models;

// Framework for PHP
use mirzaev\minimal\model,
	mirzaev\minimal\http\enumerations\status;

// Framework for ArangoDB
use mirzaev\arangodb\connection as arangodb,
	mirzaev\arangodb\collection,
	mirzaev\arangodb\document;

// Library for ArangoDB
use ArangoDBClient\Document as _document,
	ArangoDBClient\DocumentHandler as _document_handler;

// Built-in libraries
use exception;

/**
 * Models core
 *
 * @package svoboda\negotiatior\models
 *
 * @param public ARANGODB Path to the file with ArangoDB session connection data
 * @param arangodb $arangodb Instance of the ArangoDB session
 *
 * @method void __construct(bool $initialize, ?arangodb $arangodb) Constructor
 * @method _document|static|array|null read(string $filter, string $sort, int $amount, int $page, string $return, array $parameters, array &$errors) Read document from ArangoDB
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
class core extends model
{
	/**
	 * ArangoDB connection daa
	 *
	 * @var string ARANGODB Path to the file with ArangoDB session connection data	 
	 */
	final public const string ARANGODB = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'arangodb.php';

	/**
	 * ArangoDB
	 *
	 * @var arangodb $arangodb Instance of the ArangoDB session
	 */
	protected static arangodb $arangodb;

	/**
	 * Constructor
	 *
	 * @param bool $initialize Initialize a model?
	 * @param ?arangodb $arangodb Instance of the ArangoDB session
	 *
	 * @return void
	 */
	public function __construct(bool $initialize = true, ?arangodb $arangodb = null)
	{
		// For the extends system
		parent::__construct($initialize);

		if ($initialize) {
			// Initializing is requested

			// Writing an instance of a session of ArangoDB to the property
			self::$arangodb = $arangodb ?? new arangodb(require static::ARANGODB);
		}
	}

	/**
	 * Read document from ArangoDB
	 *
	 * @param string $filter Expression for filtering (AQL)
	 * @param string $sort Expression for sorting (AQL)
	 * @param int $amount Amount of documents for collect
	 * @param int $page Page
	 * @param string $return Expression describing the parameters to return (AQL)
	 * @param array $parameters Binded parameters for placeholders ['placeholder' => parameter]
	 * @param array &$errors Registry of errors
	 *
	 * @return mixed An array of instances of documents from ArangoDB, if they are found
	 */
	public static function _read(
		string $filter = '',
		string $sort = 'd.created DESC, d._key DESC',
		int $amount = 1,
		int $page = 1,
		string $return = 'd',
		array $parameters = [],
		array &$errors = []
	): _document|static|array|null {
		try {
			if (collection::initialize(static::COLLECTION, static::TYPE)) {
				// Initialized the collection

				// Read from ArangoDB
				$result = collection::execute(
					sprintf(
						<<<'AQL'
							FOR d IN @@collection
								%s
								%s
								LIMIT @offset, @amount
								RETURN %s
						AQL,
						empty($filter) ? '' : "FILTER $filter",
						empty($sort) ? '' : "SORT $sort",
						empty($return) ? 'd' : $return
					),
					[
						'@collection' => static::COLLECTION,
						'offset' => --$page <= 0 ? 0 : $page * $amount,
						'amount' => $amount
					] + $parameters,
					errors: $errors
				);

				if ($amount === 1 && $result instanceof _document) {
					// Received only 1 document and @todo rebuild 

					// Initializing the object
					$object = new static;

					if (method_exists($object, '__document')) {
						// Object can implement a document from ArangoDB

						// Writing the instance of document from ArangoDB to the implement object
						$object->__document($result);

						// Exit (success)
						return $object;
					}
				}

				// Exit (success)
				return $result;
			} else throw new exception('Failed to initialize ' . static::TYPE . ' collection: ' . static::COLLECTION);
		} catch (exception $e) {
			// Writing to registry of errors
			$errors[] = [
				'text' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'stack' => $e->getTrace()
			];
		}

		// Exit (fail)
		return null;
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
			'arangodb' => (function () use ($value) {
				if (isset(static::$arangodb)) throw new exception('Forbidden to reinitialize the ArangoDB session($this::$arangodb)', status::internal_server_error->value);
				else if ($value instanceof arangodb) self::$arangodb = $value;
				else throw new exception('Session of connection to ArangoDB ($this::$arangodb) is need to be mirzaev\arangodb\connection', status::internal_server_error->value);
			})(),
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
			'arangodb' => (new static)->__get('arangodb'),
			default => throw new exception("Not found: $name", 500)
		};
	}
}

