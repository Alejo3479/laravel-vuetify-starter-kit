<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { index as clientesIndex } from '@/routes/clientes';

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

interface ClienteRow {
    id: number;
    name: string;
    email: string;
}

interface PaginatedClientes {
    data: ClienteRow[];
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
    clientes: PaginatedClientes;
    filters: Filters;
}>();

const headers = [{ title: 'Nombre', key: 'name' }];

const fetchClientes = (params: {
    page: number;
    limit: number;
    sort: string;
    order: 'asc' | 'desc';
    q: string;
}) => {
    router.get(clientesIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['clientes', 'filters'],
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
        fetchClientes({
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
        fetchClientes({
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
    <Head title="Clientes" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Clientes</VCardTitle>
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
                    :items="clientes.data"
                    :items-length="clientes.total"
                    :items-per-page="clientes.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="clientes.current_page"
                    item-value="id"
                    @update:options="onUpdateOptions"
                />
            </VCardText>
        </VCard>
    </div>
</template>
