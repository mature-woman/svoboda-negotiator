<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models\interfaces;

// Framework for ArangoDB
use mirzaev\arangodb\enumerations\collection\type;

/**
 * Collection
 *
 * Interface for implementing a collection from ArangoDB
 *
 * @package svoboda\negotiatior\models\interfaces
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
interface collection
{
	/**
	 * Name of the collection in ArangoDB
	 */
	public const string COLLECTION = 'THIS_COLLECTION_SHOULD_NOT_EXIST';

	/**
	 * Type of the collection in ArangoDB
	 */
	public const type TYPE = type::document;
}
