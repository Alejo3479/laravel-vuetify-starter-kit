<script setup lang="ts">
import { Head, Form, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import clientesController from '@/actions/App/Http/Controllers/Cliente/ClienteController';
import UserController from '@/actions/App/Http/Controllers/User/UserController';
import { index as clientesIndex } from '@/routes/clientes';
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
const roleIds = ref<number[]>(
    props.action === 'create' && props.cliente
    ? props.roles.map((role) => role.id)
    : props.user?.role_ids ?? []);

function goBack() {
    window.history.back();
}

</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo usuario' : 'Editar usuario'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.user?.name ?? '' }}</VCardTitle>
            <Form
                v-bind="action === 'edit' && props.user
                    ? (props.cliente
                        ? clientesController.update.form(props.user.id)
                        : UserController.update.form(props.user.id)
                    )
                    : (props.cliente
                        ? clientesController.store.form()
                        : UserController.store.form()
                    )"
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
                        :disabled="props.action === 'show'"
                        variant="outlined"
                        density="compact"
                        :error-messages="errors.name"
                    />
                    <VTextField
                        name="email"
                        v-model="email"
                        label="Email"
                        :disabled="props.action === 'show'"
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
                        <template v-for="role in roles" :key="role.id">
                            <VCheckbox
                                v-if="!(role.name === 'Cliente' && (props.action === 'create' || props.cliente))"
                                :label="role.name"
                                density="compact"
                                hide-details
                                :disabled="props.action === 'show' || (props.cliente && props.action === 'create')"
                                :model-value="roleIds.includes(role.id)"
                                @update:model-value="(checked: boolean | null) => setRole(checked, role.id)"
                            />
                        </template>
                        <input
                            v-for="id in roleIds"
                            :key="id"
                            type="hidden"
                            name="roles[]"
                            :value="id"
                        />
                    </div>
                    <div class="d-flex flex-column flex-md-row align-md-center justify-end ga-4 mb-4">
                        <VBtn
                            v-if="props.action !== 'show'"
                            class="mt-4"
                            color="info"
                            :loading="processing"
                            type="submit"
                            :disabled="processing"
                        >
                            Guardar
                        </VBtn>
                        <VBtn
                            v-if="props.action !== 'show'"
                            class="mt-4"
                            color="error"
                            type="button"
                            @click="goBack"
                        >
                            Cancelar
                        </VBtn>
                    </div>
                </VCardText>
            </Form>
        </VCard>
    </div>
</template>
