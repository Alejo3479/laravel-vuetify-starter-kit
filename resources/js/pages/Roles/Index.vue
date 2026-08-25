<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { index as rolesIndex, edit, show, destroy, create as rolesCreate } from '@/routes/roles';

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Roles',
            href: rolesIndex(),
        },
    ],
});
interface RoleData {
    id: number;
    name: string;
    permission_ids: number[];
}

interface Permission {
    id: number;
    name: string;
}

interface PermissionGroup {
    id: number;
    permissions: Permission[];
}

interface RoleRow {
    id: number;
    name: string;
}

interface PaginatedRoles {
    data: RoleRow[];
    current_page: number;
    per_page: number;
    total: number;
}

interface Filters {
    q: string | null;
    sort: string | null;
    order: 'asc' | 'desc' | null;
    limit: number | null;
}

const props = defineProps<{
    roles?: PaginatedRoles;
    filters?: Filters;
    action?: 'show' | 'index' | 'create' | 'edit';
    role?: RoleData;
    permissionGroups?: PermissionGroup[];
}>();

const permissionNames = computed(() =>
    (props.permissionGroups ?? [])
        .flatMap((group) => group.permissions)
        .filter((permission) => props.role?.permission_ids.includes(permission.id))
        .map((permission) => permission.name)
);

const headers = [
    { title: 'Nombre', key: 'name' },
    { title: 'Acciones', key: 'actions', sortable: false, align: 'end' as const },
];

const fetchRoles = (params: {
    page: number;
    limit: number;
    sort: string;
    order: 'asc' | 'desc';
    q: string;
}) => {
    router.get(rolesIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['roles', 'filters'],
    });
};

const onUpdateOptions = ({
    page,
    itemsPerPage,
    sortBy,
}: {
    page: number;
    itemsPerPage: number;
    sortBy: { key: string; order: 'asc' | 'desc' }[];
}) => {
        fetchRoles({
            page,
            limit: itemsPerPage,
            sort: sortBy[0]?.key ?? 'name',
            order: sortBy[0]?.order ?? 'asc',
            q: search.value ?? '',
        });
};

const search = ref<string | null>(props.filters?.q ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

const confirmDialog = ref(false);
const roleToDelete = ref<RoleRow | null>(null);

const askDelete = (role: RoleRow) => {
    roleToDelete.value = role;
    confirmDialog.value = true;
};

const confirmDelete = () => {
    if (!roleToDelete.value) {
        return;
    }

    router.delete(destroy(roleToDelete.value.id).url, {
        preserveScroll: true,
        onFinish: () => {
            confirmDialog.value = false;
            roleToDelete.value = null;
        },
    });
};

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchRoles({
            page: 1,
            limit: props.filters?.limit ?? 10,
            sort: props.filters?.sort ?? 'name',
            order: props.filters?.order ?? 'asc',
            q: value ?? '',
        });
    }, 400);
});

</script>
<template>
    <Head title="Roles" />

    <div class="app-page">
        <VCard v-if="props.action !== 'show'">
            <div class="d-flex align-stretch" style="padding: 0;">
                <VCardTitle class="align-self-center">Listado de Roles</VCardTitle>
                <VBtn
                    class="ms-auto rounded-0"
                    color="primary"
                    prepend-icon="mdi-plus"
                    :href="rolesCreate().url"
                    style="border-radius: 0;"
                >
                    Nuevo
                </VBtn>
            </div>
            <VDivider />
            <VCardText>

                <VTextField
                    v-model="search"
                    label="Buscar por nombre"
                    prepend-inner-icon="mdi-magnify"
                    placeholder="Administrador"
                    density="compact"
                    variant="outlined"
                    clearable
                    hide-details
                    class="mb-4"
                />

                <VDataTableServer
                    :headers="headers"
                    :items="roles?.data ?? []"
                    :items-length="roles?.total ?? 0"
                    :items-per-page="roles?.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="roles?.current_page"
                    item-value="id"
                    @update:options="onUpdateOptions"
                    >
                    <template v-slot:[`item.actions`]="{ item }">
                        <VBtn
                        icon="mdi-eye-outline"
                        variant="text"
                        size="small"
                        @click="router.visit(show(item.id).url)"
                        />
                        <VBtn
                        icon="mdi-pencil-outline"
                        variant="text"
                        size="small"
                        @click="router.visit(edit(item.id).url)"
                        />
                        <VBtn
                            icon="mdi-trash-can-outline"
                            variant="text"
                            size="small"
                            color="error"
                            @click="askDelete(item)"
                            />
                        </template>
                    </VDataTableServer>
                    <VDialog v-model="confirmDialog" max-width="420">
                        <VCard>
                            <VCardTitle>Eliminar rol</VCardTitle>
                            <VCardText>
                                ¿Seguro que querés eliminar el rol
                                <strong>{{ roleToDelete?.name }}</strong>? Esta acción no
                                se puede deshacer.
                            </VCardText>
                            <VCardActions>
                                <VSpacer />
                                <VBtn variant="text" @click="confirmDialog = false">Cancelar</VBtn>
                                <VBtn color="error" variant="flat" @click="confirmDelete">Eliminar</VBtn>
                            </VCardActions>
                        </VCard>
                    </VDialog>
            </VCardText>
        </VCard>
        <VCard v-else-if="props.role">
            <VCardTitle>{{ props.role.name }}</VCardTitle>
            <VDivider />
            <VCardText>
                <p class="text-body-2 mb-2">Permisos:</p>
                <ul>
                    <li v-for="name in permissionNames" :key="name">{{ name }}</li>
                </ul>
            </VCardText>
        </VCard>
    </div>
</template>
