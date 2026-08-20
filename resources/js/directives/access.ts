import type { Directive, DirectiveBinding } from 'vue'
import { usePermissions, type AccessValue } from '@/composables/usePermissions'

type AccessType = 'permission' | 'role'
type AccessMode = 'any' | 'all'

interface AccessElement extends HTMLElement {
    __accessOriginalDisplay?: string
    __accessHidden?: boolean
}

function normalizeBinding(
    binding: DirectiveBinding<AccessValue>,
): {
    value: AccessValue
    mode: AccessMode
} {
    let mode: AccessMode = 'any'

    if (binding.modifiers.all) {
        mode = 'all'
    }

    if (binding.modifiers.any) {
        mode = 'any'
    }

    return {
        value: binding.value,
        mode,
    }
}

function checkAccess(
    type: AccessType,
    value: AccessValue,
    mode: AccessMode,
): boolean {
    const { can, hasRole } = usePermissions()

    return type === 'permission'
        ? can(value, { mode })
        : hasRole(value, { mode })
}

function hide(el: AccessElement): void {
    if (el.__accessHidden) {
        return
    }

    el.__accessOriginalDisplay = el.style.display
    el.__accessHidden = true
    el.style.display = 'none'
}

function show(el: AccessElement): void {
    if (!el.__accessHidden) {
        return
    }

    el.style.display = el.__accessOriginalDisplay ?? ''
    delete el.__accessOriginalDisplay
    delete el.__accessHidden
}

function update(
    el: AccessElement,
    binding: DirectiveBinding<AccessValue>,
    type: AccessType,
): void {
    const { value, mode } = normalizeBinding(binding)

    if (checkAccess(type, value, mode)) {
        show(el)
    } else {
        hide(el)
    }
}

export function createAccessDirective(
    type: AccessType,
): Directive<HTMLElement, AccessValue> {
    return {
        mounted(el, binding) {
            update(el, binding, type)
        },

        updated(el, binding) {
            update(el, binding, type)
        },
    }
}