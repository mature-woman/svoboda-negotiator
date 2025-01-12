<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models;

// Files of the project
use svoboda\negotiatior\models\traits\status,
	svoboda\negotiatior\models\traits\buffer,
	svoboda\negotiatior\models\traits\document as document_trait,
	svoboda\negotiatior\models\interfaces\document as document_interface,
	svoboda\negotiatior\models\interfaces\collection as collection_interface,
	svoboda\negotiatior\models\enumerations\session as verification,
	svoboda\negotiatior\models\enumerations\language;

// Framework for ArangoDB
use mirzaev\arangodb\collection,
	mirzaev\arangodb\document;

// Library для ArangoDB
use ArangoDBClient\Document as _document;

// Built-in libraries
use exception;

/**
 * Session model
 *
 * @package svoboda\negotiatior\models
 *
 * @param string COLLECTION Name of the collection in ArangoDB
 * @param verification VERIFICATION Type of session verification
 *
 * @method void __construct(?string $hash, ?int $expires, array &$errors) Constructor
 * @method document|null hash(string $hash, array &$errors) Search by hash
 * @method document|null address(string $address, array &$errors) Search by IP-address
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
final class session extends core implements document_interface, collection_interface
{
	use status, document_trait, buffer, cart {
		buffer::write as write;
		cart::initialize as cart;
	}

	/**
	 * Collection name
	 * 
	 * @var string COLLECTION Name of the collection in ArangoDB
	 */
	final public const string COLLECTION = 'session';

	/**
	 * Session verification type
	 * 
	 * @var verification VERIFICATION Type of session verification
	 */
	final public const verification VERIFICATION = verification::hash_else_address;

	/**
	 * Constructor
	 *
	 * Initialize session and write into the $this->document property
	 *
	 * @param ?string $hash Hash of the session in ArangoDB
	 * @param ?int $expires Date of expiring of the session (used for creating a new session)
	 * @param array &$errors Registry of errors
	 *
	 * @return void
	 */
	public function __construct(?string $hash = null, ?int $expires = null, array &$errors = [])
	{
		try {
			if (collection::initialize(static::COLLECTION, static::TYPE, errors: $errors)) {
				// Initialized the collection

				if (isset($hash) && $document = $this->hash($hash, errors: $errors)) {
					// Found the instance of the ArangoDB document of session and received a session hash
 
					// Writing document instance of the session from ArangoDB to the property of the implementing object 
					$this->__document($document);
				} else if (static::VERIFICATION === verification::hash_else_address && $document = $this->address($_SERVER['REMOTE_ADDR'], errors: $errors)) {
					// Found the instance of the ArangoDB document of session and received a session hash

					// Writing document instance of the session from ArangoDB to the property of the implementing object 
					$this->__document($document);
				} else {
					// Not found the instance of the ArangoDB document of session

					// Initializing a new session and write they into ArangoDB
					$_id = document::write(
						static::COLLECTION,
						[
							'active' => true,
							'expires' => $expires ?? time() + 604800,
							'address' => $_SERVER['REMOTE_ADDR'],
							'x-forwarded-for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
							'referer' => $_SERVER['HTTP_REFERER'] ?? null,
							'useragent' => $_SERVER['HTTP_USER_AGENT'] ?? null
						]
					);

					if ($session = collection::execute(
						<<<'AQL'
							FOR d IN @@collection
								FILTER d._id == @_id && d.expires > @time && d.active == true
								RETURN d
						AQL,
						[
							'@collection' => static::COLLECTION,
							'_id' => $_id,
							'time' =>	time()
						],
						errors: $errors
					)) {
						// Found the instance of just created new session

						// Generating a hash and write into the instance of the ArangoDB document of session property
						$session->hash = sodium_bin2hex(sodium_crypto_generichash($_id));

						if (document::update($session, errors: $errors)) {
							// Writed to ArangoDB

							// Writing instance of the session document from ArangoDB to the property of the implementing object 
							$this->__document($session);
						} else throw new exception('Failed to write the session data');
					} else throw new exception('Failed to create or find just created session');
				}
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
	}

	/**
	 * Search by hash
	 *
	 * Search for the session in ArangoDB by hash
	 *
	 * @param string $hash Hash of the session in ArangoDB
	 * @param array &$errors Registry of errors
	 *
	 * @return _document|null instance of document of the session in ArangoDB
	 */
	public static function hash(string $hash, array &$errors = []): ?_document
	{
		try {
			if (collection::initialize(static::COLLECTION, static::TYPE, errors: $errors)) {
				// Collection initialized

				// Search the session data in ArangoDB
				return collection::execute(
					<<<'AQL'
					FOR d IN @@collection
						FILTER d.hash == @hash && d.expires > @time && d.active == true
						RETURN d
					AQL,
					[
						'@collection' => static::COLLECTION,
						'hash' => $hash,
						'time' => time()
					],
					errors: $errors
				);
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
	 * Search by IP-address
	 *
	 * Search for the session in ArangoDB by IP-address
	 *
	 * @param string $address IP-address writed to the session in ArangoDB
	 * @param array &$errors Registry of errors
	 *
	 * @return _document|null instance of document of the session in ArangoDB
	 */
	public static function address(string $address, array &$errors = []): ?_document
	{
		try {
			if (collection::initialize(static::COLLECTION, static::TYPE, errors: $errors)) {
				// Collection initialized

				// Search the session data in ArangoDB
				return collection::execute(
					<<<'AQL'
					FOR d IN @@collection
						FILTER d.address == @address && d.expires > @time && d.active == true
						SORT d.updated DESC
						LIMIT 1
						RETURN d
					AQL,
					[
						'@collection' => static::COLLECTION,
						'address' => $address,
						'time' =>	time()
					],
					errors: $errors
				);
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
	}}
