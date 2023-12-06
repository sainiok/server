<template>
	<NcSettingsSection :name="t('user_ldap', 'LDAP/AD settings')"
		:description="t('user_ldap', 'Select a LDAP server to edit or add a new one.')"
		:doc-url="ldapDocumentation || undefined"
		:limit-width="true">
		<div :class="$style.wrapper">
			<label :class="$style['select-label']" :for="serverSelectId">{{ t('user_ldap', 'LDAP server configuration') }}</label>
			<NcSelect v-model="selectedConfiguration"
				:class="$style.select"
				:clearable="false"
				:input-id="serverSelectId"
				:options="options" />
			<NcButton type="tertiary-no-background" @click="onReloadStatus">
				<template #icon>
					<NcIconSvgWrapper :class="statusIconClass" :path="statusIconPath" />
				</template>
				{{ statusText }}
			</NcButton>
		</div>

		<div :class="$style.actions">
			<NcButton v-if="options.length > 1" type="error" @click="onDelete">
				{{ t('user_ldap', 'Delete configuration') }}
			</NcButton>
			<NcButton type="tertiary" @click="onReset">
				{{ t('user_ldap', 'Reset to defaults') }}
			</NcButton>
			<NcButton type="tertiary" @click="onCloneConfig">
				{{ t('user_ldap', 'Clone configuration') }}
			</NcButton>
			<NcButton type="primary" @click="onCreateNew">
				{{ t('user_ldap', 'New configuration') }}
			</NcButton>
		</div>

		<ConnectionSettings />
		<DirectorySettings />
	</NcSettingsSection>
</template>
<script setup lang="ts">
import { mdiCheck, mdiAlertCircle } from '@mdi/js'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { loadState } from '@nextcloud/initial-state'
import { translate as t } from '@nextcloud/l10n'
import { NcButton, NcSelect, NcSettingsSection, NcIconSvgWrapper } from '@nextcloud/vue'
import { computed, ref, watch, useCssModule } from 'vue'
import { useConfigStore } from '../store/configStore'

import debounce from 'debounce'
import ConnectionSettings from './ConnectionSettings.vue'
import DirectorySettings from './DirectorySettings.vue'

const styles = useCssModule()

const configStore = useConfigStore()

/**
 * Configuration status
 */
const configStatus = ref(false)
const statusIconPath = computed(() => configStatus.value ? mdiCheck : mdiAlertCircle)
const statusIconClass = computed(() => configStatus.value ? styles['icon-success'] : styles['icon-error'])
const statusText = computed(() => configStatus.value ? t('user_ldap', 'Configuration ok') : t('user_ldap', 'Configuration incomplete'))
const onReloadStatus = () => { configStatus.value = !configStatus.value }

/**
 * URL to the documentation for the LDAP settings
 */
const ldapDocumentation = loadState<string>('user_ldap', 'documentationURL', '')

/**
 * ID for the select input element
 */
const serverSelectId = `ldap-server-input-${Math.random().toString(36).slice(7)}`

/**
 * Server configurations that can be choosen
 */
const options = computed(() => Object.keys(configStore.configurations)
	.map((prefix, index) => ({
		id: prefix,
		label: configStore.configurations[prefix].ldapHost
			? t('user_ldap', 'Configuration {index} ({server})', { index: index + 1, server: configStore.configurations[prefix].ldapHost })
			: t('user_ldap', 'Configuration {index}', { index: index + 1 }),
	})),
)

/**
 * Currently selected server configuration option
 */
const selectedConfiguration = ref(options.value[0])

watch([selectedConfiguration], () => {
	configStore.currentId = selectedConfiguration.value.id
})

const debouncedSave = debounce((id: string, config) => configStore.update(id, config), 800)

/**
 * Save changes of the configuration to the backend
 */
configStore.$subscribe(() => {
	console.debug('Modified configuration, calling debouned save')
	debouncedSave(configStore.currentId, configStore.current)
})

/**
 * Reset LDAP configuration to defaults
 */
const onReset = async () => {
	try {
		await configStore.reset(selectedConfiguration.value.id)
		// reset the current option as the host name is removed (just changes the option name)
		selectedConfiguration.value = options.value.find(({ id }) => id === configStore.currentId)!
		showSuccess(t('user_ldap', 'Resetted LDAP configuration to default values'))
	} catch (e) {
		showError(t('user_ldap', 'Could not reset LDAP configuration'))
	}
}

/**
 * Clone current configuration and select it
 */
const onCloneConfig = async () => {
	try {
		const newId = await configStore.clone(selectedConfiguration.value.id)
		selectedConfiguration.value = options.value.find(({ id }) => id === newId)!
		showSuccess(t('user_ldap', 'Cloned LDAP configuration'))
	} catch (e) {
		showError(t('user_ldap', 'Could not clone LDAP configuration'))
	}
}

/**
 * Create a new configuration
 */
const onCreateNew = async () => {
	try {
		await configStore.createNew()
		selectedConfiguration.value = options.value.at(-1)!
		showSuccess(t('user_ldap', 'Created new LDAP configuration'))
	} catch (e) {
		showError(t('user_ldap', 'Could not create new LDAP configuration'))
	}
}

/**
 * Delete current configuration
 */
const onDelete = async () => {
	try {
		await configStore.delete(selectedConfiguration.value.id)
		selectedConfiguration.value = options.value[0]
		showSuccess(t('user_ldap', 'LDAP configuration deleted'))
	} catch (e) {
		showError(t('user_ldap', 'Could not delete LDAP configuration'))
	}
}
</script>

<style module>
.wrapper {
	/* Make selector sticky to the top */
	position: sticky;
	top: 0px;
	background-color: var(--color-main-background);
	z-index: 999; /* Ensure it is always on top */
	padding-block: 22px;

	display: flex;
	flex-wrap: wrap;
	gap: 16px;
	justify-content: start;
	align-items: center;
}

.icon-error {
	color: var(--color-error);
}

.icon-success {
	color: var(--color-success);
}

.select-label {
	display: block;
	flex: 0 0 fit-content;
}
.select {
	flex: 1 1 0;
}

.actions {
	display: flex;
	gap: 16px;
	justify-content: end;
	flex-wrap: wrap;
}
</style>
