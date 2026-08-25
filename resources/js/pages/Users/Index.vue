<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { index as usersIndex, edit, show, destroy, create as usersCreate } from '@/routes/users';

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Usuarios',
            href: usersIndex(),
        },
    ],
});

interface UserRow {
    id: number;
    name: string;
    email: string;
}

interface PaginatedUsers {
    data: UserRow[];
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
    users: PaginatedUsers;
    filters: Filters;
}>();

const headers = [
    { title: 'Nombre', key: 'name' },
    { title: 'Acciones', key: 'actions', sortable: false, align: 'end' as const }
];

const fetchUsers = (params: {
    page: number;
    limit: number;
    sort: string;
    order: 'asc' | 'desc';
    q: string;
}) => {
    router.get(usersIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['users' , 'filters'],
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
    fetchUsers({
            page,
            limit: itemsPerPage,
            sort: sortBy[0]?.key ?? 'name',
            order: sortBy[0]?.order ?? 'asc',
            q: search.value ?? '',
    });
};


const search = ref<string | null>(props.filters.q ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

const confirmDialog = ref(false);
const userToDelete = ref<UserRow | null>(null);

const askDelete = (user: UserRow) => {
    userToDelete.value = user;
    confirmDialog.value = true;
};

const confirmDelete = () => {
    if (!userToDelete.value) {
        return;
    }

    router.delete(destroy(userToDelete.value.id).url, {
        preserveScroll: true,
        onFinish: () => {
            confirmDialog.value = false;
            userToDelete.value = null;
        },
    });
};

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchUsers({
            page: 1,
            limit: props.filters.limit ?? 10,
            sort: props.filters.sort ?? 'name',
            order: props.filters.order ?? 'asc',
            q: value ?? '',
        });
    }, 400);
});


</script>

<template>
    <Head title="Usuarios" />

    <div class="app-page">
        <VCard>
            <div class="d-flex align-stretch" style="padding: 0;">
                <VCardTitle class="align-self-center">Listado de Usuarios</VCardTitle>
                <VBtn
                    class="ms-auto rounded-0"
                    color="primary"
                    prepend-icon="mdi-plus"
                    :href="usersCreate().url"
                    style="border-radius: 0;"
                >
                    Nuevo
                </VBtn>
            </div>

            <VDivider />
            <VCardText>
                 <VTextField
                    v-model="search"
                    label="Buscar por nombre o correo"
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
                    :items="users.data"
                    :items-length="users.total"
                    :items-per-page="users.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="users.current_page"
                    item-value="id"
                    @update:options="onUpdateOptions"
                >
                <template v-slot:[`item.actions`]="{ item }">
                    <VBtn
                        icon="mdi-eye-outline"
                        variant="text"
                        size="small"
                        @click="router.visit(show(item.id).url)"
                    >
                        <VIcon icon="mdi-eye-outline" />
                        <VTooltip activator="parent" location="top">Ver</VTooltip>
                    </VBtn>
                    <VBtn
                        icon="mdi-pencil-outline"
                        variant="text"
                        size="small"
                        @click="router.visit(edit(item.id).url)"
                    >
                        <VIcon icon="mdi-pencil-outline" />
                        <VTooltip activator="parent" location="top">Editar</VTooltip>
                    </VBtn>
                    <VBtn
                        icon="mdi-trash-can-outline"
                        variant="text"
                        size="small"
                        color="error"
                        @click="askDelete(item)"
                    >
                        <VIcon icon="mdi-trash-can-outline" />
                        <VTooltip activator="parent" location="top">Eliminar</VTooltip>
                    </VBtn>
                </template>
                </VDataTableServer>
                <VDialog v-model="confirmDialog" max-width="420">
                        <VCard>
                            <VCardTitle>Eliminar usuario</VCardTitle>
                            <VCardText>
                                ¿Seguro que querés eliminar el usuario
                                <strong>{{ userToDelete?.name }}</strong>? Esta acción no
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
    </div>
</template>
