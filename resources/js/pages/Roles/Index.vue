<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { index as rolesIndex, edit, show } from '@/routes/roles';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles',
                href: rolesIndex(),
            },
        ],
    },
});

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
    roles: PaginatedRoles;
    filters: Filters;
}>();

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

const search = ref<string | null>(props.filters.q ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchRoles({
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
    <Head title="Roles" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Roles</VCardTitle>
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
                    :items="roles.data"
                    :items-length="roles.total"
                    :items-per-page="roles.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="roles.current_page"
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
                    />
                </template>
                </VDataTableServer>

            </VCardText>
        </VCard>
    </div>
</template>
