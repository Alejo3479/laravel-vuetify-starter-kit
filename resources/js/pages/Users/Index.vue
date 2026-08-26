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
    { title: 'Correo', key: 'email' },
    { title: 'Acciones', key: 'actions', align: 'center' as const },
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
            <VCardText>
                <div class="d-flex flex-column flex-md-row align-md-center ga-4 mb-4">
                    <VTextField
                        v-model="search"
                        label="Buscar por nombre o correo"
                        prepend-inner-icon="mdi-magnify"
                        placeholder="Administrador"
                        density="compact"
                        variant="outlined"
                        clearable
                        hide-details
                        class="order-2 order-md-1 grow"
                    />
                    <VBtn
                        class="order-1 order-md-2 w-100 w-md-auto"
                        color="primary"
                        prepend-icon="mdi-plus"
                        :href="usersCreate().url"
                    >
                        Nuevo
                    </VBtn>
                </div>
                <VDataTableServer
                    density="compact"
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
                <template #headers="{ columns }">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key ?? column.title"
                            class="font-weight-bold"
                            :class="{ 'text-center': column.key === 'actions' }"
                        >
                            {{ column.title }}
                        </th>
                    </tr>
                </template>
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
