<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\enumerations\relation;

/**
 * Type
 *
 * Relation type
 *
 * @package svoboda\antivertical\models\enumerations\relation
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum status: int
{
	case unknown = 0;
	case recognition = 1;
	case unrecognition = 2;
}
