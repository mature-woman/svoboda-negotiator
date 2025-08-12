<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\enumerations;

/**
 * Entity
 *
 * Types of entities
 *
 * @package svoboda\antivertical\models\enumerations
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum entity: int
{
	case system = 0;
	case account = 1;
	case distribution = 2;
}
