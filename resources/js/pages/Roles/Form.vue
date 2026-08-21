<script setup lang="ts">
import { Head, useForm, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import storeForm, { index as rolesIndex, update as updateForm } from '@/routes/roles';

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
    action: 'create' | 'edit';
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

const form = useForm({
    name: props.role?.name ?? '',
    permissions: props.role?.permission_ids ?? [],
});
const selectedGroup = ref(props.permissionGroups[0]?.id ?? null);

const submit = () => {
    if (props.action === 'create') {
        form.post(storeForm.store().url);
    } else {
        form.submit(updateForm.put(props.role!.id));
    }
};
</script>

<template>
    <Head :title="props.action === 'create' ? 'Nuevo rol' : 'Editar rol'" />
    <div class="app-page">
        <VCard>
            <VCardTitle>{{ props.action === 'create' ? 'Nuevo rol' : 'Editar rol' }}</VCardTitle>
            <VDivider />
            <VCardText>
                <VTextField
                    v-model="form.name"
                    label="Nombre"
                    variant="outlined"
                    density="compact"
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
                                v-model="form.permissions"
                                :label="permission.name"
                                :value="permission.id"
                                density="compact"
                                hide-details
                            />
                        </VWindowItem>
                    </VWindow>
                </div>
                <VBtn
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
