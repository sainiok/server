import { generateFilePath } from '@nextcloud/router'
import axios from '@nextcloud/axios'

const wizardURL = generateFilePath('user_ldap', 'ajax', 'wizard.php')

export const detectPort = (configPrefix: string) => {
	const data = new FormData()
	data.append('action', 'guessPortAndTLS')
	data.append('ldap_serverconfig_chooser', configPrefix)
	return axios.post(wizardURL, data).then(({ data }) => {
		if (data.status === 'error') {
			throw new Error()
		}
	})
}
