<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models\traits;

// Files of the project
use svoboda\negotiatior\models\traits\document as document_trait,
	svoboda\negotiatior\models\interfaces\document as document_interface,
	svoboda\negotiatior\models\interfaces\collection as collection_interface,
	svoboda\negotiatior\models\enumerations\language;

// Library for ArangoDB
use ArangoDBClient\Document as _document;

// Framework for ArangoDB
use mirzaev\arangodb\collection,
	mirzaev\arangodb\document;

// Built-in libraries
use exception;

/**
 * Buffer
 *
 * Storage of data in the document from ArangoDB
 *
 * @uses document
 * @uses document_interface
 * @uses collection_interface
 *
 * @param static COLLECTION Name of the collection in ArangoDB
 * @param static TYPE Type of the collection in ArangoDB
 *
 * @package svoboda\negotiatior\models\traits
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
trait buffer
{
	/**
	 * Write to buffer of the document
	 *
	 * @param array $data Data for writing (merge)
	 * @param array &$errors Registry of errors
	 *
	 * @return bool Is data has written into the document from ArangoDB?
	 */
	public function write(array $data, array &$errors = []): bool
	{
		try {
			if (collection::initialize(static::COLLECTION, static::TYPE, errors: $errors)) {
				// Initialized the collection

				// Is the instance of the document from ArangoDB are initialized?
				if (!isset($this->document)) throw new exception('The instance of the sessoin document from ArangoDB is not initialized');

				// Writing data into buffer of the instance of the document from ArangoDB
				$this->document->buffer = array_replace_recursive($this->document->buffer ?? [], $data);

				// Is the buffer of the instance of the document from ArangoDB exceed 10 megabytes?
				if (mb_strlen(json_encode($this->document->buffer)) > 10485760) throw new exception('The buffer size exceeds 10 megabytes');

				// Serializing parameters
				if ($this->document->language instanceof language) $this->document->language = $this->document->language->name;

				// Writing to ArangoDB and exit (success)
				return document::update($this->document, errors: $errors);
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
		return false;
	}
}
