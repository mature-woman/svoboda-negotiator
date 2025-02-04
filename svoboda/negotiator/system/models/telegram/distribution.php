<?php

declare(strict_types=1);

namespace svoboda\negotiator\models\telegram;

// Files of the project
use svoboda\negotiator\models\core,
	svoboda\negotiator\models\account,
	svoboda\negotiator\models\distribution as model,
	svoboda\negotiator\models\localization\distribution as distribution_localization,
	svoboda\negotiator\models\enumerations\language;

// Framework for Telegram
use Zanzara\Context as context,
	Zanzara\Telegram\Type\Message as message;

// Baza database
use mirzaev\baza\record;

/**
 * Telegram distribution
 *
 * @package svoboda\negotiator\models\telegram
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class distribution extends core {}
