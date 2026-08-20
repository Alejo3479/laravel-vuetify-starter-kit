import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export type AccessValue = string | string[]

export type AccessOptions = {
    mode?: 'any' | 'all'
}

export function usePermissions() {
    const page = usePage()

    const permissions = computed<string[]>(
        () => page.props.auth?.permissions ?? [],
    )

    const roles = computed<string[]>(
        () => page.props.auth?.roles ?? [],
    )

    const matches = (
        available: string[],
        required: AccessValue,
        mode: 'any' | 'all' = 'any',
    ): boolean => {
        const values = Array.isArray(required)
            ? required
            : [required]

        if (values.length === 0) {
            return true
        }

        const check = (value: string): boolean => {
            if (value.endsWith('*')) {
                const prefix = value.slice(0, -1)

                return available.some(
                    item => item === value || item.startsWith(prefix),
                )
            }

            return available.includes(value)
        }

        return mode === 'all'
            ? values.every(check)
            : values.some(check)
    }

    const can = (
        value: AccessValue,
        options: AccessOptions = {},
    ): boolean => {
        return matches(
            permissions.value,
            value,
            options.mode ?? 'any',
        )
    }

    const hasRole = (
        value: AccessValue,
        options: AccessOptions = {},
    ): boolean => {
        return matches(
            roles.value,
            value,
            options.mode ?? 'any',
        )
    }

    return {
        permissions,
        roles,
        can,
        hasRole,
    }
}