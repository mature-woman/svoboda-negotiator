<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\enumerations\membership;

/**
 * Status
 *
 * Membership status
 *
 * @package svoboda\antivertical\models\enumerations\membership
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum status: int
{
	case unknown = 0;
	case planned = 1;
	case joined = 2;

	/**
   * Emoji
   *
	 * Initialize emoji of the status
	 *
	 * @return string Emoji of the status
	 */
	public function emoji(): string
	{
		// Exit (success)
		return match ($this) {
			status::unknown =>	'👽',
			status::planned =>	'📅',
			status::joined =>	'🧳'
		};
	}
}
