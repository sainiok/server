<template>
	<div class="settings-sub-section">
		<h3 class="settings-sub-section__header">
			{{ name }}
		</h3>
		<slot />
	</div>
</template>

<script setup lang="ts">
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
	name: string
}>()
</script>

<style scoped>
.settings-sub-section {
	padding-block: 12px;
}
.settings-sub-section__header {
	display: block;
	font-weight: bold;
}
</style>
