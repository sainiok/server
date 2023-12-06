<template>
	<SubSection :name="t('user_ldap', 'Connection settings')">
		<form class="ldap-server">
			<div class="ldap-server__entry-wrapper">
				<NcTextField :aria-describedby="serverPortHintId"
					:label="t('user_ldap', 'Host')"
					:value.sync="currentConfig.ldapHost"
					placeholder="ldaps://example.com" />
				<NcTextField class="ldap-server__port"
					:label="t('user_ldap', 'Port')"
					:value.sync="currentConfig.ldapPort"
					placeholder="636"
					type="number"
					min="1"
					max="65535" />
				<NcButton class="ldap-server__port-detect"
					:disabled="isDetectingPort"
					type="tertiary"
					@click="onDetectPort">
					<template v-if="isDetectingPort" #icon>
						<NcLoadingIcon />
					</template>
					{{ isDetectingPort ? t('user_ldap', 'Detecting port…') : t('user_ldap', 'Detect port') }}
				</NcButton>
			</div>
			<p :id="serverPortHintId" class="ldap-server__hint">
				{{ t('user_ldap', 'The protocol might be omitted if not using SSL.') }}
			</p>

			<NcTextField aria-describedby="ldap-server__user-dn-hint ldap-server__user-password-hint"
				:label="t('user_ldap', 'User DN')"
				:value.sync="currentConfig.ldapAgentName"
				placeholder="uid=agent,dc=example,dc=com" />
			<p id="ldap-server__user-dn-hint" class="ldap-server__hint">
				{{ t('user_ldap', 'The DN of the client user with which the bind shall be done.') }}
			</p>

			<NcPasswordField aria-describedby="ldap-server__user-password-hint"
				:label="t('user_ldap', 'Password')"
				:value.sync="currentConfig.ldapAgentPassword" />
			<p id="ldap-server__user-password-hint" class="ldap-server__hint">
				{{ t('user_ldap', 'For anonymous access, leave DN and Password empty.') }}
			</p>

			<div class="ldap-server__entry-wrapper">
				<NcTextArea aria-describedby="ldap-server__basedn-hint"
					:label="t('user_ldap', 'Base DN')"
					:placeholder="'dc=example,dc=com\ndc=other.example,dc=com'"
					:value.sync="currentConfig.ldapBase" />
				<div class="ldap-server__basedn-actions">
					<NcButton type="tertiary">
						{{ t('user_ldap', 'Detect base DN') }}
					</NcButton>
					<NcButton type="tertiary">
						{{ t('user_ldap', 'Test base DN') }}
					</NcButton>
				</div>
			</div>
			<p id="ldap-server__basedn-hint" class="ldap-server__hint">
				{{ t('user_ldap', 'One base DN per line, you can also specify separate base DN for users and groups in the advanced settings below') }}
			</p>

			<NcCheckboxRadioSwitch type="switch"
				aria-controls="user_ldap-connection-advanced"
				:checked.sync="currentConfig.ldapExperiencedAdmin">
				{{ t('user_ldap', 'Show advanced settings') }}
			</NcCheckboxRadioSwitch>
			<fieldset v-show="currentConfig.ldapExperiencedAdmin" id="user_ldap-connection-advanced" class="advanced-settings">
				<NcCheckboxRadioSwitch :checked.sync="currentConfig.ldapConfigurationActive">
					{{ t('user_ldap', 'Configuration active. When unchecked, this configuration will be skipped.') }}
				</NcCheckboxRadioSwitch>

				<div class="ldap-server__entry-wrapper">
					<NcTextField aria-describedby="user_ldap-connection-hint--backup-host"
						:label="t('user_ldap', 'Backup (replica) host')"
						:value.sync="currentConfig.ldapBackupHost"
						placeholder="ldaps://example.com" />
					<NcTextField class="ldap-server__port"
						:label="t('user_ldap', 'Backup (replica) port')"
						:value.sync="currentConfig.ldapBackupPort"
						aria-describedby="user_ldap-connection-hint--backup-host"
						placeholder="636"
						type="number"
						min="1"
						max="65535" />
				</div>
				<p id="user_ldap-connection-hint--backup-host" class="ldap-server__hint">
					{{ t('user_ldap', 'Optional backup host. It must be a replica of the main LDAP/AD server.') }}
				</p>

				<NcCheckboxRadioSwitch :checked.sync="currentConfig.ldapOverrideMainServer">
					{{ t('user_ldap', 'Disable main server and only connect to replica.') }}
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch :checked.sync="currentConfig.turnOffCertCheck" aria-describedby="user_ldap-connection-hint--disable-cert">
					{{ t('user_ldap', 'Turn off SSL certificate validation.') }}
				</NcCheckboxRadioSwitch>
				<p id="user_ldap-connection-hint--disable-cert" class="ldap-server__hint">
					{{ t('user_ldap', 'Not recommended, use it for testing only! If connection only works with this option, import the LDAP server\'s SSL certificate in your server.') }}
				</p>

				<NcTextField :label="t('user_ldap', 'Cache Time-To-Live in seconds')"
					:value.sync="currentConfig.ldapCacheTTL"
					aria-describedby="user_ldap-connection-hint--cache-ttl"
					type="number" />
				<p id="user_ldap-connection-hint--cache-ttl" class="ldap-server__hint">
					{{ t('user_ldap', 'A change to the cache TTL empties the cache.') }}
				</p>
			</fieldset>
		</form>
	</SubSection>
</template>
<script setup lang="ts">
import { translate as t } from '@nextcloud/l10n'
import { NcButton, NcCheckboxRadioSwitch, NcLoadingIcon, NcPasswordField, NcTextArea, NcTextField } from '@nextcloud/vue'
import { computed, ref } from 'vue'
import { showError } from '@nextcloud/dialogs'

import SubSection from '../components/SubSection.vue'
import { useConfigStore } from '../store/configStore'

const configStore = useConfigStore()
/**
 * Current selected server configuration
 */
const currentConfig = computed(() => configStore.current)

const isDetectingPort = ref(false)
const onDetectPort = async () => {
	try {
		isDetectingPort.value = true
		// await detectPort(selectedConfiguration.value.id)
	} catch (e) {
		showError(t('user_ldap', 'Could not detect LDAP port'))
		console.error(e)
	} finally {
		isDetectingPort.value = false
	}
}

const serverPortHintId = `ldap-server-port-hint-${Math.random().toString(36).slice(7)}`
</script>

<style scoped lang="scss">
.advanced-settings {
	margin-inline-start: 44px;
	display: flex;
	flex-direction: column;
	gap: 8px;

	> :deep(:not(.checkbox-radio-switch)) {
		margin-inline-start: 16px; // align all other elements with the checkboxes
	}
}

.ldap-server {
	display: flex;
	flex-direction: column;
	gap: 8px;
	margin-block-start: 16px;

	&__hint {
		color: var(--color-text-maxcontrast);
	}

	// Only for the hint that is a child of ldap server
	#{&}__hint {
		margin-block-start: -4px;
		padding-inline-start: 14px; // align with input field
	}

	&__port {
		flex: 0 1 15em;

		&-detect {
			flex: 0 0 fit-content;
		}
	}

	&__basedn-actions {
		display: flex;
		align-items: end;
		flex-direction: column;
		gap: 8px
	}

	&__entry-wrapper {
		display: flex;
		gap: 16px;
		justify-content: start;
		align-items: center;
	}
}
</style>
