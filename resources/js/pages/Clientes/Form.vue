<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import ClienteController from '@/actions/App/Http/Controllers/Cliente/ClienteController';
import { index as clientesIndex } from '@/routes/clientes';


interface Role {
    id: number;
    name: string;
}

interface ClienteData {
    id: number;
    name: string;
    email: string;
    role_ids: number[];
}

const props = defineProps<{
    action: 'create' | 'edit' | 'show';
    cliente?: ClienteData;
    roles: Role[];
    user?: ClienteData;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: clientesIndex(),
            },
        ],
    },
});

const name = ref(props.cliente?.name ?? '');
const email = ref(props.cliente?.email ?? '');
const roleIds = ref<number[]>(props.action === 'create' ? props.roles.map((r) => r.id) : props.cliente?.role_ids ?? []);
</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo cliente' : 'Editar cliente'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.action === 'create' ? 'Nuevo cliente' : 'Editar cliente' }}</VCardTitle>
            <VDivider />
            <Form
                 v-bind="action === 'edit' && props.user ? ClienteController.update.form(props.user.id) : ClienteController.store.form()"
                :transform="(data) => ({ ...data, roles: Array.isArray(data.roles) ? data.roles.map(Number) : [] })"
                reset-on-success
                v-slot="{ }"
                :key="user?.id ?? 'new'"
            >
                <VCardText>
                    <VTextField
                        v-model="name"
                        label="Nombre"
                        variant="outlined"
                        density="compact"
                    />
                    <VTextField
                        v-model="email"
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
                            v-model="roleIds"
                            :label="role.name"
                            :value="role.id"
                            density="compact"
                            hide-details
                            :disabled="action === 'create'"
                        />
                    </div>
                    <VBtn class="mt-4" color="primary">Guardar</VBtn>
                </VCardText>
            </Form>
        </VCard>
    </div>
</template>
