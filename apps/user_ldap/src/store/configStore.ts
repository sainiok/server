import { loadState } from '@nextcloud/initial-state'
import { generateOcsUrl } from '@nextcloud/router'
import { defineStore } from 'pinia'

import axios from '@nextcloud/axios'
import Vue from 'vue'

type ILDAPConfig = Record<string, string> & {
	ldapUserFilterMode: number
	ldapLoginFilterMode: number
	ldapLoginFilterEmail: number
	ldapLoginFilterUsername: number
	ldapGroupFilterMode: number
	ldapTLS: number
	ldapCacheTTL: number
	lastJpegPhotoLookup: number
	ldapPagingSize: number
	ldapConnectionTimeout: number

	ldapOverrideMainServer: boolean
	ldapConfigurationActive: boolean

	/**
	 * Only for the front end: This enabled the advanced settings
	 */
	ldapExperiencedAdmin: boolean
	hasMemberOfFilterSupport: boolean
	turnOffCertCheck: boolean
	useMemberOfToDetectMembership: boolean
	markRemnantsAsDisabled: boolean
	ldapNestedGroups: boolean
	turnOnPasswordChange: boolean
}

const apiURL = (prefix?: string) => prefix ? generateOcsUrl('apps/user_ldap/api/v1/config/{prefix}', { prefix }) : generateOcsUrl('apps/user_ldap/api/v1/config')

const sanitizeServerValues = ([key, value]) => {
	switch (key) {
	case 'ldapOverrideMainServer':
	case 'ldapConfigurationActive':
	case 'ldapExperiencedAdmin':
	case 'hasMemberOfFilterSupport':
	case 'useMemberOfToDetectMembership':
	case 'markRemnantsAsDisabled':
	case 'turnOffCertCheck':
	case 'ldapNestedGroups':
	case 'turnOnPasswordChange':
		return [key, !!(value === 1 || value === '1' || value === true)]
	case 'ldapUserFilterMode':
	case 'ldapLoginFilterMode':
	case 'ldapLoginFilterEmail':
	case 'ldapLoginFilterUsername':
	case 'ldapGroupFilterMode':
	case 'ldapTLS':
	case 'ldapCacheTTL':
	case 'lastJpegPhotoLookup':
	case 'ldapPagingSize':
	case 'ldapConnectionTimeout':
		return [key, parseInt(value)]
	default:
		return [key, value]
	}
}

const defaultConfig: ILDAPConfig = Object.fromEntries(Object.entries(loadState<ILDAPConfig>('user_ldap', 'serverConfigurationDefaults')).map(sanitizeServerValues))

/**
 * Load configurations from initial state and sanitze the values
 */
function loadConfigurations() {
	const state = loadState<Record<string, string|number|boolean>>('user_ldap', 'serverConfigurations', {})
	for (const configID in state) {
		state[configID] = Object.fromEntries(Object.entries(state[configID]).map(sanitizeServerValues))
	}
	return state as unknown as Record<string, ILDAPConfig>
}

export const useConfigStore = defineStore('config', {
	state() {
		const configurations = loadConfigurations()
		return {
			currentId: Object.keys(configurations)[0] as string,
			configurations,
		}
	},
	getters: {
		current(state) {
			return state.configurations[state.currentId]
		},
	},
	actions: {
		/**
		 * Return configuration with a given ID
		 * @param id ID of the configuration
		 */
		get(id: string) {
			return this.configurations[id]
		},

		async reset(id: string) {
			await this.update(id, defaultConfig)
			Vue.set(this.configurations, id, { ...defaultConfig })
		},

		async delete(id: string) {
			await axios.delete(apiURL(id))
			Vue.delete(this.configurations, id)
		},

		async clone(id: string) {
			const newId = await this.createNew()
			await this.update(newId, this.get(id))
			Vue.set(this.configurations, newId, { ...this.get(id) })
			return newId
		},

		/**
		 * Create a new empty config on the server, save it in the store and return the id of it
		 */
		async createNew() {
			const { data } = await axios.post(apiURL())
			const id = data.ocs.data.configID
			Vue.set(this.configurations, id, { ...defaultConfig })
			return id
		},

		/**
		 * Attention: This only changes the entry on the backend not in the store!
		 * @param id ID of the configuration to update
		 * @param config The (partial) new configuration
		 */
		async update(id: string, config: Partial<ILDAPConfig>) {
			await axios.put(apiURL(id), {
				configData: Object.fromEntries(Object.entries(config).map(([key, value]) => [key, typeof value === 'boolean' ? (value ? 1 : 0) : value])),
			})
		},
	},
})
