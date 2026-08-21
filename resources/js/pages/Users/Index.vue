<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { index as usersIndex } from '@/routes/users';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: usersIndex(),
            },
        ],
    },
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

const headers = [{ title: 'Nombre', key: 'name' }];

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
        only: ['users' ,'email', 'filters'],
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
            <VCardTitle>Listado de Usuarios</VCardTitle>
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

                />
            </VCardText>
        </VCard>
    </div>
</template>
