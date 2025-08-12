<?php

declare(strict_types=1);

namespace svoboda\antivertical\models\enumerations;

/**
 * Language
 *
 * Types of languages by ISO 639-1 standart
 *
 * @package svoboda\antivertical\models\enumerations
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum language
{
	case en;
	case ru;

	/**
   * Label
   *
	 * Initialize label of the language
	 *
	 * @param language|null $language Language into which to translate
	 *
	 * @return string Translated label of the language
	 */
	public function label(?language $language = language::en): string
	{
		// Exit (success)
		return match ($this) {
			language::en =>	match ($language) {
				language::en => 'English',
				language::ru => 'Английский'
			},
			language::ru => match ($language) {
				language::en => 'Russian',
				language::ru => 'Русский'
			}
		};
	}

	/**
   * Flag
   *
	 * Initialize the flag emoji of the language
	 *
	 * @return string The flag emoji of the language
	 */
	public function flag(): string
	{
		// Exit (success)
		return match ($this) {
			language::en => '🇺🇸',
			language::ru => '🇷🇺'		
		};
	}
}
