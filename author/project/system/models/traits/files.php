<?php

declare(strict_types=1);

namespace svoboda\negotiatior\models\traits;

// Built-in libraries
use exception;

/**
 * Files
 *
 * Trait with files handlers
 *
 * @method static void delete(string $directory, array &$errors)
 *
 * @package svoboda\negotiatior\models\traits
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
trait files
{
	/**
	 * Delete
	 *
	 * Delete files recursively
	 * 
	 * @param string $directory Directory
	 * @param array &$errors Registry of errors
	 *
	 * @return void
	 */
	private static function delete(string $directory, array &$errors = []): void
	{
		try {
			if (file_exists($directory)) {
				// Directory exists

				// Deleting descendant files and directories (enter to the recursion)
				foreach (scandir($directory) as $file) {
					if ($file === '.' || $file === '..') continue;
					else if (is_dir("$directory/$file")) static::delete("$directory/$file", $errors);
					else unlink("$directory/$file");
				}

				// Deleting the directory
				rmdir($directory);

				// Exit (success)
				return;
			} else throw new exception('Directory does not exist');
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
		return;
	}
}
