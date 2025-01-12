<?php

declare(strict_types=1);

namespace svoboda\negotiator\models;

// Framework for Telegram
use Zanzara\Zanzara,
	Zanzara\Context as context,
	Zanzara\Telegram\Type\Input\InputFile,
	Zanzara\Telegram\Type\File\Document as telegram_document,
	Zanzara\Middleware\MiddlewareNode as Node,
	Zanzara\Telegram\Type\User as user;


// Built-in libraries
use exception;

/**
 * Telegram
 *
 * @package svoboda\negotiator\models
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
final class telegram extends core
{
	/**
	 * Society
	 *
	 * Responce for the command: "/society"
	 *
	 * @param context $ctx
	 *
	 * @return void
	 */
	public static function society(context $ctx): void
	{
		$ctx->sendPhoto(
			new InputFile(STORAGE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'mushroom.jpg'),
			[
				'caption' => 'why so shroomious',
				'disable_notification' => true
			]
		);
	}
}
