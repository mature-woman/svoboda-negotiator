<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models\traits;

// Files of the project
use svoboda\negotiatior\models\interfaces\document as document_interface,
	svoboda\negotiatior\models\interfaces\collection as collection_interface,
	svoboda\negotiatior\models\connect;

// Library для ArangoDB
use ArangoDBClient\Document as _document;

// Framework for ArangoDB
use mirzaev\arangodb\connection as arangodb,
	mirzaev\arangodb\document as framework_document,
	mirzaev\arangodb\collection;

// Built-in libraries
use exception;

/**
 * Trait for implementing a document instance from ArangoDB
 *
 * @uses document_interface
 *
 * @var protected readonly _document|null $document An instance of the ArangoDB document
 *
 * @package svoboda\negotiatior\models\traits
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
trait document
{
	/**
	 * Document
	 *
	 * @var _document $document An instance of the document from ArangoDB
	 */
	protected readonly _document $document;

	/**
	 * Constructor
	 *
	 * @param bool $initialize Initialize a model?
	 * @param ?arangodb $arangodb Instance of a session of ArangoDB
	 * @param _document|null|false $document An instance of the ArangoDB document
	 *
	 * @return void
	 */
	public function __construct(
		bool $initialize = true,
		?arangodb $arangodb = null,
		_document|null|false $document = false
	) {
		// For the extends system
		parent::__construct($initialize, $arangodb);

		// Writing to the property
		if ($document instanceof _document) $this->__document($document);
		else if ($document === null) throw new exception('Failed to initialize an instance of the document from ArangoDB');
	}

	/**
	 * Write or read document
	 *
	 * @param _document|null $document Instance of document from ArangoDB
	 *
	 * @return _document|null Instance of document from ArangoDB
	 */
	public function __document(?_document $document = null): ?_document
	{
		// Writing a property storing a document instance to ArangoDB
		if ($document) $this->document ??= $document;

		// Read a property storing a document instance to ArangoDB and exit (success)
		return $this->document ?? null;
	}

	/**
	 * Connect
	 *
	 * @param collecton_interface $document Document
	 * @param array &$errors Registry of errors
	 *
	 * @return string|null The identifier of the created edge of the "connect" collection, if created
	 */
	public function connect(collection_interface $document, array &$errors = []): ?string
	{
		try {
			if (collection::initialize(static::COLLECTION, static::TYPE, errors: $errors)) {
				if (collection::initialize(connect::COLLECTION, connect::TYPE, errors: $errors)) {
					if (collection::initialize($document::COLLECTION, $document::TYPE, errors: $errors)) {
						// Initialized collections 

						if ($this->document instanceof _document) {
							// Initialized instance of the document from ArangoDB

							// Writing document and exit (success)
							return framework_document::write(
								connect::COLLECTION,
								[
									'_from' => $document->getId(),
									'_to' => $this->document->getId()
								],
								errors: $errors
							);
						} else  throw new exception('The instance of the document from ArangoDB is not initialized');
					} else throw new exception('Failed to initialize ' . $document::TYPE . ' collection: ' . $document::COLLECTION);
				} else throw new exception('Failed to initialize ' . connect::TYPE . ' collection: ' . connect::COLLECTION);
			} else throw new exception('Failed to initialize ' . static::TYPE . ' collection: ' . static::COLLECTION);
		} catch (exception $e) {
			// Writing to the registry of errors
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
	 * Write a property into an instance of the ArangoDB document
	 *
	 * @param string $name Name of the property
	 * @param mixed $value Content of the property
	 *
	 * @return void
	 */
	public function __set(string $name, mixed $value = null): void
	{
		// Writing to the property into an instance of the ArangoDB document and exit (success)
		$this->document->{$name} = $value;
	}

	/**
	 * Read
	 *
	 * Read a property from an instance of the ArangoDB docuemnt
	 * 
	 * @param string $name Name of the property
	 *
	 * @return mixed Content of the property
	 */
	public function __get(string $name): mixed
	{
		// Read a property from an instance of the ArangoDB document and exit (success)
		return match ($name) {
			default => $this->document->{$name}
		};
	}

	/**
	 * Delete
	 *
	 * Deinitialize the property in an instance of the ArangoDB document
	 *
	 * @param string $name Name of the property
	 *
	 * @return void
	 */
	public function __unset(string $name): void
	{
		// Delete the property in an instance of the ArangoDB document and exit (success)
		unset($this->document->{$name});
	}

	/**
	 * Check of initialization
	 *
	 * Check of initialization of the property into an instance of the ArangoDB document
	 *
	 * @param string $name Name of the property
	 *
	 * @return bool The property is initialized?
	 */
	public function __isset(string $name): bool
	{
		// Check of initializatio nof the property and exit (success)
		return isset($this->document->{$name});
	}

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
	public function __call(string $name, array $arguments = []): mixed
	{
		// Execute the method and exit (success)
		return method_exists($this->document, $name) ? $this->document->{$name}($arguments) ?? null : null;
	}
}
