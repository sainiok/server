import { createPinia, PiniaVuePlugin } from 'pinia'

import Vue from 'vue'
import AdminSettings from './views/AdminSettings.vue'

Vue.use(PiniaVuePlugin)

const pinia = createPinia()

export default new Vue({
	name: 'UserLDAPAdminSettings',
	el: '#content-vue-ldap-settings',
	render: (h) => h(AdminSettings),
	// @ts-expect-error Vue 2 typings are quite bad remove with Vue 3
	pinia,
})
