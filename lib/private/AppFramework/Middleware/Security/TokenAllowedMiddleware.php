<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2023 Joas Schilling <coding@schilljs.com>
 * @copyright Copyright (c) 2017 Lukas Reschke <lukas@statuscode.ch>
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OC\AppFramework\Middleware\Security;

use OC\AppFramework\Utility\ControllerMethodReflector;
use OC\Authentication\Token\IProvider as IAuthTokenProvider;
use OCP\App\AppPathNotFoundException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\BruteForceProtection;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TooManyRequestsResponse;
use OCP\AppFramework\Middleware;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use OCP\ISession;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Security\Bruteforce\IThrottler;
use OCP\Security\Bruteforce\MaxDelayReached;
use Psr\Log\LoggerInterface;
use ReflectionMethod;

/**
 * Class BruteForceMiddleware performs the bruteforce protection for controllers
 * that are annotated with @BruteForceProtection(action=$action) whereas $action
 * is the action that should be logged within the database.
 *
 * @package OC\AppFramework\Middleware\Security
 */
class TokenAllowedMiddleware extends Middleware {

	/** @var IAuthTokenProvider */
	private $tokenProvider;


	/** @var string */
	private $appName;


	/** @var ISession */
	private $session;


	public function __construct(
		protected ControllerMethodReflector $reflector,
		protected IRequest $request,
		protected LoggerInterface $logger,
		string $appName,
		ISession $session,
		IAuthTokenProvider $tokenProvider,
	) {
		$this->appName = $appName;
		$this->session = $session;
		$this->tokenProvider = $tokenProvider;
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeController($controller, $methodName) {
		parent::beforeController($controller, $methodName);

	}

	/**
	 * {@inheritDoc}
	 */
	public function afterController($controller, $methodName, Response $response) {


		$this->logger->error('Response for ' . $this->appName . ' got token denied.');


		$sessionId = $this->session->getId();
		$sessionToken = $this->tokenProvider->getToken($sessionId);

		$this->logger->error('Response for ' . $sessionToken->getName() . ' got token denied.');

		if($this->appName == "files") {
			$response = new Response(Http::STATUS_FORBIDDEN);
			$response->setHeaders(['X-RequestDeniedReason' => 'Apptoken missing permissions!']);
			//return $response;
		}

		return parent::afterController($controller, $methodName, $response);
	}

	/**
	 * @param Controller $controller
	 * @param string $methodName
	 * @param \Exception $exception
	 * @throws \Exception
	 * @return Response
	 */
	public function afterException($controller, $methodName, \Exception $exception): Response {
		if ($exception instanceof MaxDelayReached) {
			if ($controller instanceof OCSController) {
				throw new OCSException($exception->getMessage(), Http::STATUS_TOO_MANY_REQUESTS);
			}

			return new TooManyRequestsResponse();
		}

		throw $exception;
	}
}
