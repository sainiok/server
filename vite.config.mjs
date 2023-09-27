/**
 * @copyright Copyright (c) 2021 John Molakvoæ <skjnldsv@protonmail.com>
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
import { createAppConfig } from '@nextcloud/vite-config'
import { defineConfig } from 'vite'
import modules from './vite.modules'

const entries = {}
Object.entries(modules).forEach(([appName, script]) => {
	Object.entries(script).forEach(([scriptName, scriptPath]) => {
		entries[`${appName}-${scriptName}`] = scriptPath
	})
})

console.debug(entries)

const serverConfig = defineConfig({
	build: {
		outDir: 'dist',
		rollupOptions: {
			output: {
				entryFileNames: '[name].js',
				chunkFileNames: '[name].js',
				// Group all node_modules into a single chunk
				manualChunks(id) {
					if (id.includes('vue-material-design-icons')) {
						return 'vue-material-design-icons'
					}
					if (id.includes('node_modules')) {
						return 'core-common'
					}
				},
			},
		},
	},
})

export default createAppConfig({
	...entries,
}, {
	inlineCSS: true,
	emptyOutputDirectory: true,
	config: serverConfig,
})
