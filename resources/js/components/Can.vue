<script setup lang="ts">
import { computed } from 'vue'
import {
    usePermissions,
    type AccessValue,
} from '@/composables/usePermissions'

const props = withDefaults(
    defineProps<{
        permission?: AccessValue
        role?: AccessValue
        mode?: 'any' | 'all'
    }>(),
    {
        mode: 'any',
    },
)

const { can, hasRole } = usePermissions()

const allowed = computed(() => {
    if (props.permission !== undefined) {
        return can(props.permission, {
            mode: props.mode,
        })
    }

    if (props.role !== undefined) {
        return hasRole(props.role, {
            mode: props.mode,
        })
    }

    return true
})
</script>

<template>
    <slot v-if="allowed" />
</template>