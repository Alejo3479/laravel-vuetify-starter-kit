import type { Directive } from 'vue'
import { usePermissions } from '@/composables/usePermissions'

type RoleValue = string | string[]

const role: Directive<HTMLElement, RoleValue> = {
    mounted(el, binding) {
        const { hasRole } = usePermissions()

        if (!hasRole(binding.value)) {
            hide(el)
        }
    },

    updated(el, binding) {
        const { hasRole } = usePermissions()

        if (hasRole(binding.value)) {
            show(el)
        } else {
            hide(el)
        }
    },
}

function hide(el: HTMLElement) {
    el.dataset.roleHidden = 'true'
    el.style.display = 'none'
}

function show(el: HTMLElement) {
    delete el.dataset.roleHidden
    el.style.removeProperty('display')
}

export default role