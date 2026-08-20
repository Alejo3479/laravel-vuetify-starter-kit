import type { Directive } from 'vue'
import { usePermissions } from '@/composables/usePermissions'

type PermissionValue = string | string[]

const permission: Directive<HTMLElement, PermissionValue> = {
    mounted(el, binding) {
        const { can } = usePermissions()

        if (!can(binding.value)) {
            hide(el)
        }
    },

    updated(el, binding) {
        const { can } = usePermissions()

        if (can(binding.value)) {
            show(el)
        } else {
            hide(el)
        }
    },
}

function hide(el: HTMLElement) {
    el.dataset.permissionHidden = 'true'
    el.style.display = 'none'
}

function show(el: HTMLElement) {
    delete el.dataset.permissionHidden
    el.style.removeProperty('display')
}

export default permission