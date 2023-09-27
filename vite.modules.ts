/**
 * @copyright Copyright (c) 2023 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
export default {
	comments: {
		comments: 'apps/comments/src/comments.js',
		'comments-app': 'apps/comments/src/comments-app.js',
		'comments-tab': 'apps/comments/src/comments-tab.js',
		init: 'apps/comments/src/init.ts',
	},
	core: {
		files_client: 'core/src/files/client.js',
		files_fileinfo: 'core/src/files/fileinfo.js',
		install: 'core/src/install.js',
		login: 'core/src/login.js',
		main: 'core/src/main.js',
		maintenance: 'core/src/maintenance.js',
		profile: 'core/src/profile.js',
		recommendedapps: 'core/src/recommendedapps.js',
		'unified-search': 'core/src/unified-search.js',
		'unsupported-browser': 'core/src/unsupported-browser.js',
		'unsupported-browser-redirect': 'core/src/unsupported-browser-redirect.js',
	},
	dashboard: {
		main: 'apps/dashboard/src/main.js',
	},
	dav: {
		'settings-admin-caldav': 'apps/dav/src/settings.js',
		'settings-personal-availability': 'apps/dav/src/settings-personal-availability.js',
	},
	files: {
		sidebar: 'apps/files/src/sidebar.js',
		main: 'apps/files/src/main.ts',
		init: 'apps/files/src/init.ts',
		'personal-settings': 'apps/files/src/main-personal-settings.js',
		'reference-files': 'apps/files/src/reference-files.js',
	},
	files_external: {
		init: 'apps/files_external/src/init.ts',
	},
	files_reminders: {
		main: 'apps/files_reminders/src/main.ts',
	},
	files_sharing: {
		additionalScripts: 'apps/files_sharing/src/additionalScripts.js',
		collaboration: 'apps/files_sharing/src/collaborationresourceshandler.js',
		files_sharing_tab: 'apps/files_sharing/src/files_sharing_tab.js',
		init: 'apps/files_sharing/src/init.ts',
		main: 'apps/files_sharing/src/main.ts',
		'personal-settings': 'apps/files_sharing/src/personal-settings.js',
	},
	files_trashbin: {
		main: 'apps/files_trashbin/src/main.ts',
	},
	files_versions: {
		files_versions: 'apps/files_versions/src/files_versions_tab.js',
	},
	oauth2: {
		oauth2: 'apps/oauth2/src/main.js',
	},
	federatedfilesharing: {
		'vue-settings-admin': 'apps/federatedfilesharing/src/main-admin.js',
		'vue-settings-personal': 'apps/federatedfilesharing/src/main-personal.js',
	},
	settings: {
		apps: 'apps/settings/src/apps.js',
		'legacy-admin': 'apps/settings/src/admin.js',
		'vue-settings-admin-basic-settings': 'apps/settings/src/main-admin-basic-settings.js',
		'vue-settings-admin-ai': 'apps/settings/src/main-admin-ai.js',
		'vue-settings-admin-delegation': 'apps/settings/src/main-admin-delegation.js',
		'vue-settings-admin-security': 'apps/settings/src/main-admin-security.js',
		'vue-settings-apps-users-management': 'apps/settings/src/main-apps-users-management.js',
		'vue-settings-nextcloud-pdf': 'apps/settings/src/main-nextcloud-pdf.js',
		'vue-settings-personal-info': 'apps/settings/src/main-personal-info.js',
		'vue-settings-personal-password': 'apps/settings/src/main-personal-password.js',
		'vue-settings-personal-security': 'apps/settings/src/main-personal-security.js',
		'vue-settings-personal-webauthn': 'apps/settings/src/main-personal-webauth.js',
	},
	sharebymail: {
		'vue-settings-admin-sharebymail': 'apps/sharebymail/src/main-admin.js',
	},
	systemtags: {
		init: 'apps/systemtags/src/init.ts',
	},
	theming: {
		'personal-theming': 'apps/theming/src/personal-settings.js',
		'admin-theming': 'apps/theming/src/admin-settings.js',
	},
	twofactor_backupcodes: {
		settings: 'apps/twofactor_backupcodes/src/settings.js',
	},
	updatenotification: {
		updatenotification: 'apps/updatenotification/src/init.js',
	},
	user_status: {
		menu: 'apps/user_status/src/menu.js',
	},
	weather_status: {
		'weather-status': 'apps/weather_status/src/weather-status.js',
	},
	workflowengine: {
		workflowengine: 'apps/workflowengine/src/workflowengine.js',
	},
} as Record<string, Record<string, string>>
