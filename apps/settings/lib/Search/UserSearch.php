<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2023 Stephan Orbaugh <stephan.orbaugh@nextcloud.com>
 *
 * @author Stephan Orbaugh <stephan.orbaugh@nextcloud.com>
 *
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
namespace OCA\Settings\Search;

use OCP\Accounts\IAccountManager;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\Search\IProvider;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use OCP\Search\SearchResultEntry;

class UserSearch implements IProvider {
	public function __construct(
		private IL10N $l,
		private IUserManager $userManager,
		private IGroupManager $groupManager,
		private IAccountManager $accountManager,
		private IURLGenerator $urlGenerator,
	) {
	}

	public function getId(): string {
		return 'users';
	}

	public function getName(): string {
		return $this->l->t('Users');
	}

	public function getOrder(string $route, array $routeParameters): int {
		return 300;
	}

	public function search(IUser $currentUser, ISearchQuery $query): SearchResult {
		if (!$this->groupManager->isAdmin($currentUser->getUID())) {
			return SearchResult::complete($this->getName(), []);
		}

		$users = $this->userManager->search($query->getTerm(), $query->getLimit(), 0);

		$results = [];
		foreach ($users as $user) {
			$targetUserObject = $this->userManager->get($user->getUID());
			if ($targetUserObject === null) {
				continue;
			}

			$results[] = new SearchResultEntry(
				'/avatar/'.$user->getUID().'/64',
				$targetUserObject->getDisplayName(),
				$user->getUID(),
				$this->urlGenerator->linkToRouteAbsolute('settings.Users.usersList'),
			);
		}

		return SearchResult::complete($this->getName(), $results);
	}
}
