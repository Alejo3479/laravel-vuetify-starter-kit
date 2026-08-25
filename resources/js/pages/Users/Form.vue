<script setup lang="ts">
import { Head, useForm, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    index as usersIndex,
    store as usersStore,
    update as usersUpdate,
} from '@/routes/users';

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
    roles: Role[];
    user?: UserData;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Usuarios',
            href: usersIndex(),
        },
        {
            title:
                props.action === 'create'
                    ? 'Nuevo'
                    : props.user?.name ?? 'Editar',
        },
    ],
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    password_confirmation: '',
    roles: props.user?.role_ids ?? [],
});

const submit = () => {
    if (props.action === 'create') {
        form.post(usersStore().url);
    } else {
        form.put(usersUpdate(props.user!.id).url);
    }
};
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
                    :error-messages="form.errors.name"
                />
                <VTextField
                    v-model="form.email"
                    label="Email"
                    variant="outlined"
                    density="compact"
                    class="mt-4"
                    :error-messages="form.errors.email"
                />
                <VTextField
                    v-if="props.action !== 'show'"
                    v-model="form.password"
                    label="Contraseña"
                    :type="showPassword ? 'text' : 'password'"
                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showPassword = !showPassword"
                    variant="outlined"
                    density="compact"
                    class="mt-4"
                    :error-messages="form.errors.password"
                />
                <VTextField
                    v-if="props.action !== 'show'"
                    v-model="form.password_confirmation"
                    label="Confirmar contraseña"
                    :type="showPasswordConfirmation ? 'text' : 'password'"
                    :append-inner-icon="showPasswordConfirmation ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showPasswordConfirmation = !showPasswordConfirmation"
                    variant="outlined"
                    density="compact"
                    class="mt-4"
                    :error-messages="form.errors.password_confirmation"
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
                        :disabled="props.action === 'show'"
                    />
                </div>
                <VBtn
                    v-if="props.action !== 'show'"
                    class="mt-4"
                    color="primary"
                    :loading="form.processing"
                    @click="submit"
                >
                    Guardar
                </VBtn>
            </VCardText>
        </VCard>
    </div>
</template>
