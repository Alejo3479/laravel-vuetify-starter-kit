<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { index as usersIndex } from '@/routes/users';

interface Role {
    id: number;
    name: string;
}

interface UserData {
    id: number;
    name: string;
    email: string;
    role_ids: number[];
}

const props = defineProps<{
    action: 'create' | 'edit' | 'show';
    cliente?: boolean;
    roles: Role[];
    user?: UserData;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: usersIndex(),
            },
        ],
    },
});

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    roles: props.user?.role_ids ?? [],
});
</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo usuario' : 'Editar usuario'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.action === 'create' ? 'Nuevo usuario' : 'Editar usuario' }}</VCardTitle>
            <VDivider />
            <VCardText>
                <VTextField
                    v-model="form.name"
                    label="Nombre"
                    variant="outlined"
                    density="compact"
                />
                <VTextField
                    v-model="form.email"
                    label="Email"
                    variant="outlined"
                    density="compact"
                    class="mt-4"
                />
                <div class="mt-4">
                    <p class="text-body-2 mb-2">Roles</p>
                    <VCheckbox
                        v-for="role in roles"
                        :key="role.id"
                        v-model="form.roles"
                        :label="role.name"
                        :value="role.id"
                        density="compact"
                        hide-details
                    />
                </div>
                <VBtn class="mt-4" color="primary">Guardar</VBtn>
            </VCardText>
        </VCard>
    </div>
</template>
