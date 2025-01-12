<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models\enumerations;

/**
 * Session
 *
 * Types of session verification
 *
 * @package svoboda\negotiatior\models\enumerations
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
enum session
{
    case hash_only;
    case hash_else_address;
}
