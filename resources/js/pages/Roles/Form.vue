<script setup lang="ts">
import { Head, Form, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import RoleController from '@/actions/App/Http/Controllers/Role/RoleController';
import { index as rolesIndex } from '@/routes/roles';

interface RoleData {
    id: number;
    name: string;
    permission_ids: number[];
}

interface Permission {
    id: number;
    name: string;
    permission_group_id: number;
}

interface PermissionGroup {
    id: number;
    name: string;
    permissions: Permission[];
}

const props = defineProps<{
    action: 'create' | 'edit' | 'show';
    permissionGroups: PermissionGroup[];
    role?: RoleData;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Roles',
            href: rolesIndex(),
        },
        {
            title:
                props.action === 'create'
                    ? 'Nuevo'
                    : props.role?.name ?? 'Editar',
        },
    ],
});
function setPermission(event: Event, permissionId: number) {
    const checked = (event.target as HTMLInputElement).checked;
    permissionIds.value = checked ? [...permissionIds.value, permissionId] : permissionIds.value.filter((id) => id !== permissionId);
}

const selectedGroup = ref(props.permissionGroups[0]?.id ?? null);

const name = ref(props.role?.name ?? '');
const permissionIds = ref<number[]>(props.role?.permission_ids ?? []);

</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo rol' : 'Editar rol'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.action === 'create' ? 'Nuevo rol' : 'Editar rol' }}</VCardTitle>
            <VDivider />
            <Form
                v-bind="action === 'edit' && props.role ? RoleController.update.form(props.role.id) : RoleController.store.form()"
                :options="{ preserveScroll: true }"
                :transform="(data) => ({ ...data, permissions: Array.isArray(data.permissions) ? data.permissions.map(Number) : [] })"
                reset-on-success
                v-slot="{ errors, processing }"
                :key="role?.id ?? 'new'"
            >
                <VCardText>
                    <VTextField
                        name="name"
                        :error-messages="errors.name"
                        label="Nombre"
                        variant="outlined"
                        density="compact"
                        v-model="name"
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
                                    :label="permission.name"
                                    density="compact"
                                    hide-details
                                    :model-value="permissionIds.includes(permission.id)"
                                    @change="(e: Event) => setPermission(e, permission.id)"
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
                    <VBtn
                        type="submit"
                        class="mt-4"
                        color="primary"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Guardar
                    </VBtn>
                </VCardText>
            </Form>
        </VCard>
    </div>
</template>
