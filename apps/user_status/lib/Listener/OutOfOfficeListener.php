<?php

declare(strict_types=1);

/*
 * @copyright 2023 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2023 Christoph Wurst <christoph@winzerhof-wurst.at>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\UserStatus\Listener;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\OutOfOfficeChangedEvent;
use OCP\User\Events\OutOfOfficeEndedEvent;
use OCP\User\Events\OutOfOfficeStartedEvent;
use OCP\UserStatus\IManager;
use OCP\UserStatus\IUserStatus;

class OutOfOfficeListener implements IEventListener {

	public function __construct(private IManager $statusManager,
	private ITimeFactory $timeFactory) {
	}

	public function handle(Event $event): void {
		$now = $this->timeFactory->getTime();

		// Revert OOO status if it just ended or was in the past
		if ($event instanceof OutOfOfficeEndedEvent
			|| ($event instanceof OutOfOfficeChangedEvent && $event->getData()->getEndDate() <= $now)) {
			$this->statusManager->revertUserStatus(
				$event->getData()->getUser()->getUID(),
				IUserStatus::MESSAGE_AVAILABILITY,
				IUserStatus::DND,
			);
		}

		// Set OOO status if it just started, or we are in the middle of it
		if ($event instanceof OutOfOfficeStartedEvent
			|| ($event instanceof OutOfOfficeChangedEvent && $event->getData()->getStartDate() <= $now && $event->getData()->getEndDate() >= $now)) {
			// Get rid of the other automations because OOO is more important
			$this->statusManager->revertUserStatus(
				$event->getData()->getUser()->getUID(),
				IUserStatus::MESSAGE_AVAILABILITY,
				IUserStatus::DND,
			);
			$this->statusManager->revertUserStatus(
				$event->getData()->getUser()->getUID(),
				IUserStatus::MESSAGE_CALL,
				IUserStatus::AWAY,
			);

			$this->statusManager->setUserStatus(
				$event->getData()->getUser()->getUID(),
				IUserStatus::MESSAGE_AVAILABILITY,
				IUserStatus::DND,
				true,
				$event->getData()->getShortMessage(),
			);
		}
	}

}
