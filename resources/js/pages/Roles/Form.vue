<script setup lang="ts">
import { Head, Form, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import RoleController from '@/actions/App/Http/Controllers/Role/RoleController';
import { index as rolesIndex } from '@/routes/roles';
import type { BaseFormProps } from '@/types/paginacion';

interface Payload {
    id: number;
    name: string;
    permission_ids: number[];
}

interface Permission {
    id: number;
    name: string;
    label: string;
}

interface PermissionGroup {
    id: number;
    name: string;
    permissions: Permission[];
}

const props = defineProps<BaseFormProps<Payload> & {
    permissionGroups: PermissionGroup[];
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Roles',
            href: rolesIndex(),
        },
        {
            title: props.action === 'create' ? 'Nuevo' : (props.action === 'edit' ? 'Editar' : 'Ver'),
        },
    ],
});
function setPermission(event: Event, permissionId: number) {
    const checked = (event.target as HTMLInputElement).checked;
    permissionIds.value = checked ? [...permissionIds.value, permissionId] : permissionIds.value.filter((id) => id !== permissionId);
}

const selectedGroup = ref(props.permissionGroups[0]?.id ?? null);

const name = ref(props.payload?.name ?? '');
const permissionIds = ref<number[]>(props.payload?.permission_ids ?? []);

function goBack() {
    window.history.back();
}
</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo rol' : (props.action === 'edit' ? `Editar rol` : `Ver rol`)" />
    <div class="app-page">
        <VCard>
            <Form
                v-bind="action === 'edit' && props.payload ? RoleController.update.form(props.payload.id) : RoleController.store.form()"
                :options="{ preserveScroll: true }"
                :transform="(data) => ({ ...data, permissions: Array.isArray(data.permissions) ? data.permissions.map(Number) : [] })"
                reset-on-success
                v-slot="{ errors, processing }"
                :key="props.payload?.id ?? 'new'"
            >
                <VCardText>
                    <VTextField
                        name="name"
                        :error-messages="errors.name"
                        label="Nombre"
                        variant="outlined"
                        density="compact"
                        v-model="name"
                        :readonly="props.action === 'show'"
                    />
                    <div class="mt-4">
                        <p class="text-body-2 mb-2">Permisos</p>

                        <VTabs v-model="selectedGroup">
                            <VTab
                                v-for="group in permissionGroups"
                                :key="group.id"
                                :value="group.id"
                            >
                                {{ group.name }}
                            </VTab>
                        </VTabs>

                        <VWindow v-model="selectedGroup" class="mt-4">
                            <VWindowItem
                                v-for="group in permissionGroups"
                                :key="group.id"
                                :value="group.id"
                            >
                                <VCheckbox
                                    v-for="permission in group.permissions"
                                    :key="permission.id"
                                    :label="permission.label"
                                    density="compact"
                                    hide-details
                                    :model-value="permissionIds.includes(permission.id)"
                                    @change="(e: Event) => setPermission(e, permission.id)"
                                    :readonly="props.action === 'show'"
                                />
                            </VWindowItem>
                        </VWindow>
                        <input
                            v-for="id in permissionIds"
                            :key="id"
                            type="hidden"
                            name="permissions[]"
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
                            type="submit"
                            color="info"
                            :loading="processing"
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
