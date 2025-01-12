<?php

declare(strict_types=1);

namespace svoboda\negotiatior\controllers;

// Files of the project
use svoboda\negotiatior\views\templater,
	svoboda\negotiatior\models\core as models,
	svoboda\negotiatior\models\session,
	svoboda\negotiatior\models\enumerations\language;

// Framework for PHP
use mirzaev\minimal\core as minimal,
	mirzaev\minimal\controller,
	mirzaev\minimal\http\response,
	mirzaev\minimal\http\enumerations\status;

/**
 * Controllers core
 *
 * @package svoboda\negotiatior\controllers
 *
 * @param session $session Instance of the session
 * @param language $language Language
 * @param response $response Response
 * @param array $errors Registry of errors
 *
 * @method void __construct(minimal $minimal, bool $initialize) Constructor
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author svoboda <mail@domain.zone>
 */
class core extends controller
{
	/**
	 * Session
	 * 
	 * @var session|null $session Instance of the session
	 */
	protected readonly session $session;

	/**
	 * Language
	 *
	 * @var language $language Language
	 */
	protected language $language = language::en;

	/**
	 * Response
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 *
	 * @var response $response Response
	 */
	protected response $response {
		// Read
		get => $this->response ??= $this->request->response();
	}

	/**
	 * Errors
	 *
	 * @var array $errors Registry of errors
	 */
	protected array $errors = [
		'session' => [],
		'account' => []
	];

	/**
	 * Constructor
	 *
	 * @param minimal $core Instance of the MINIMAL
	 * @param bool $initialize Initialize a controller?
	 *
	 * @return void
	 */
	public function __construct(minimal $core, bool $initialize = true)
	{
		// Blocking requests from CloudFlare (better to write this blocking into nginx config file)
		if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'nginx-ssl early hints') return status::bruh->label;

		// For the extends system
		parent::__construct(core: $core);

		if ($initialize) {
			// Requestet initializing

			// Initializing core of the models
			new models();

			// Initializing of the date until which the session will be active
			$expires = strtotime('+1 week');

			// Initializing of default value of hash of the session
			$_COOKIE["session"] ??= null;

			// Initializing of session
			$this->session = new session($_COOKIE["session"], $expires, $this->errors['session']);

			// Handle a problems with initializing a session
			if (!empty($this->errors['session'])) die;
			else if ($_COOKIE["session"] !== $this->session->hash) {
				// Hash of the session is changed (implies that the session has expired and recreated)

				// Write a new hash of the session to cookies
				setcookie(
					'session',
					$this->session->hash,
					[
						'expires' => $expires,
						'path' => '/',
						'secure' => true,
						'httponly' => true,
						'samesite' => 'strict'
					]
				);
			}

			// Initializing of preprocessor of views
			$this->view = new templater($this->session);
		}
	}
}
