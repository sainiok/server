<?php
/**
 * @copyright Copyright (c) 2016 Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Ferdinand Thiessen <opensource@fthiessen.de>
 *
 * @license AGPL-3.0-or-later
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
namespace OCA\User_LDAP\Settings;

use OCA\User_LDAP\Configuration;
use OCA\User_LDAP\Helper;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IDelegatedSettings;
use OCP\Util;

class Admin implements IDelegatedSettings {
	/**
	 * @param IL10N $l
	 */
	public function __construct(
		private string $appName,
		private Helper $helper,
		private IInitialState $initialState,
		private IURLGenerator $url,
	) {
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		$prefixes = $this->helper->getServerConfigurationPrefixes();
		if (count($prefixes) === 0) {
			$newPrefix = $this->helper->getNextServerConfigurationPrefix();
			$config = new Configuration($newPrefix, false);
			$config->setConfiguration($config->getDefaults());
			$config->saveConfiguration();
			$configurations[$newPrefix] = $config->getConfiguration();
		} else {
			$configurations = array_flip($prefixes);
			$configurations = array_map(fn($idx) => (new Configuration($prefixes[$idx]))->getConfiguration(), $configurations);
		}

		$mapping = Configuration::getConfigTranslationArray();
		foreach(Configuration::getDefaults() as $key => $value) {
			$defaults[$mapping[$key]] = $value;
		}

		$this->initialState->provideInitialState('documentationURL', $this->url->linkToDocs('admin-ldap'));
		$this->initialState->provideInitialState('serverConfigurations', $configurations);
		$this->initialState->provideInitialState('serverConfigurationDefaults', $defaults);

		Util::addScript($this->appName, 'admin-settings');
		return new TemplateResponse($this->appName, 'settings-admin');
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'ldap';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 5;
	}

	public function getName(): ?string {
		return null; // Only one setting in this section
	}

	public function getAuthorizedAppConfig(): array {
		return []; // Custom controller
	}
}
