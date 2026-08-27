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
interface Payload {
    id: number;
    name: string;
    email: string;
    role_ids: number[];
}

const props = defineProps<{
    type: 'usuario' | 'cliente';
    action: 'create' | 'edit' | 'show';
    roles: Role[];
    payload?: Payload;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: props.type === 'usuario' ? 'Usuarios' : 'Clientes',
            href: props.type === 'usuario' ? usersIndex() : clientesIndex(),
        },
        {
            title:
                props.action === 'create'
                    ? 'Nuevo'
                    : props.payload?.name ?? 'Editar',
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

const name = ref(props.payload?.name ?? '');
const email = ref(props.payload?.email ?? '');
const password = ref('');
const passwordConfirmation = ref('');
const roleIds = ref<number[]>(
    props.action === 'create' && props.type === 'usuario'
    ? props.roles.map((role) => role.id)
    : props.payload?.role_ids ?? []);

function goBack() {
    window.history.back();
}

</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo usuario' : 'Editar usuario'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.payload?.name ?? '' }}</VCardTitle>
            <Form
                v-bind="action === 'edit' && props.payload
                    ? (props.type === 'usuario'
                        ? UserController.update.form(props.payload.id)
                        : clientesController.update.form(props.payload.id)
                    )
                    : (props.type === 'usuario'
                        ? UserController.store.form()
                        : clientesController.store.form()
                    )"
                :transform="(data) => ({ ...data, roles: Array.isArray(data.roles) ? data.roles.map(Number) : [] })"
                reset-on-success
                v-slot="{ errors, processing }"
                :key="props.payload?.id ?? 'new'"
            >
                <VCardText>
                    <VTextField
                        name="name"
                        v-model="name"
                        label="Nombre"
                        :readonly="props.action === 'show'"
                        variant="outlined"
                        density="compact"
                        :error-messages="errors.name"
                    />
                    <VTextField
                        name="email"
                        v-model="email"
                        label="Email"
                        :readonly="props.action === 'show'"
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
                    <div class="mt-4" v-if="props.type == 'usuario'">
                        <template v-for="role in roles" :key="role.id">
                            <VCheckbox
                                :label="role.name"
                                density="compact"
                                hide-details
                                :readonly="props.action === 'show'"
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
                    <div class="d-flex flex-column flex-md-row align-md-center justify-end ga-4 my-4">
                        <VBtn
                            :loading="processing"
                            :disabled="processing"
                            @click="goBack"
                            :color="props.action == 'show' ? 'info' : 'error'"
                            :text="props.action == 'show' ? 'Volver' : 'Cancelar'"
                        ></VBtn>
                        <VBtn
                            color="info"
                            :loading="processing"
                            type="submit"
                            :disabled="processing"
                            text="Guardar"
                            v-if="props.action !== 'show'"
                        ></VBtn>
                    </div>
                </VCardText>
            </Form>
        </VCard>
    </div>
</template>
