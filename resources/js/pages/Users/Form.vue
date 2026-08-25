<script setup lang="ts">
import { Head, Form, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import UserController from '@/actions/App/Http/Controllers/User/UserController';
import { index as usersIndex } from '@/routes/users';
import { index as clientesIndex } from '@/routes/clientes';

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
    cliente: boolean;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: props.cliente ? 'Clientes' : 'Usuarios',
            href: props.cliente ? clientesIndex() : usersIndex(),
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

function setRole(checked: boolean | null, roleId: number) {
    const isChecked = checked ?? false;

    roleIds.value = isChecked
        ? [...roleIds.value, roleId]
        : roleIds.value.filter((id) => id !== roleId);
}

const name = ref(props.user?.name ?? '');
const email = ref(props.user?.email ?? '');
const password = ref('');
const passwordConfirmation = ref('');
const roleIds = ref<number[]>(props.user?.role_ids ?? []);

</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo usuario' : 'Editar usuario'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.action === 'create' ? 'Nuevo usuario' : 'Editar usuario' }}</VCardTitle>
            <VDivider />
            <Form
                v-bind="action === 'edit' && props.user ? UserController.update.form(props.user.id) : UserController.store.form()"
                :transform="(data) => ({ ...data, roles: Array.isArray(data.roles) ? data.roles.map(Number) : [] })"
                reset-on-success
                v-slot="{ errors, processing }"
                :key="user?.id ?? 'new'"
            >
                <VCardText>
                    <VTextField
                        name="name"
                        v-model="name"
                        label="Nombre"
                        variant="outlined"
                        density="compact"
                        :error-messages="errors.name"
                    />
                    <VTextField
                        name="email"
                        v-model="email"
                        label="Email"
                        variant="outlined"
                        density="compact"
                        class="mt-4"
                        :error-messages="errors.email"
                    />
                    <VTextField
                        name="password"
                        v-if="props.action !== 'show'"
                        v-model="password"
                        label="Contraseña"
                        :type="showPassword ? 'text' : 'password'"
                        :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showPassword = !showPassword"
                        variant="outlined"
                        density="compact"
                        class="mt-4"
                        :error-messages="errors.password"
                    />
                    <VTextField
                        name="password_confirmation"
                        v-if="props.action !== 'show'"
                        v-model="passwordConfirmation"
                        label="Confirmar contraseña"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        :append-inner-icon="showPasswordConfirmation ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showPasswordConfirmation = !showPasswordConfirmation"
                        variant="outlined"
                        density="compact"
                        class="mt-4"
                        :error-messages="errors.password_confirmation"
                    />
                    <div class="mt-4">
                        <p class="text-body-2 mb-2">Roles</p>
                        <VCheckbox
                            v-for="role in roles"
                            :key="role.id"
                            :label="role.name"
                            density="compact"
                            hide-details
                            :disabled="props.action === 'show'"
                            :model-value="roleIds.includes(role.id)"
                            @update:model-value="(checked: boolean | null) => setRole(checked, role.id)"
                        />
                        <input
                            v-for="id in roleIds"
                            :key="id"
                            type="hidden"
                            name="roles[]"
                            :value="id"
                        />
                    </div>
                    <VBtn
                        v-if="props.action !== 'show'"
                        class="mt-4"
                        color="primary"
                        :loading="processing"
                        type="submit"
                        :disabled="processing"
                    >
                        Guardar
                    </VBtn>
                </VCardText>
            </Form>
        </VCard>
    </div>
</template>
