<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
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

defineProps<{
    clientes: PaginatedClientes;
    filters: Filters;
}>();

const headers = [{ title: 'Nombre', key: 'name' }];

const onUpdateOptions = ({
    page,
    itemsPerPage,
    sortBy,
}: {
    page: number;
    itemsPerPage: number;
    sortBy: { key: string; order: 'asc' | 'desc' }[];
}) => {
    router.get(
        clientesIndex().url,
        {
            page,
            limit: itemsPerPage,
            sort: sortBy[0]?.key ?? 'name',
            order: sortBy[0]?.order ?? 'asc',
        },
        { preserveState: true, preserveScroll: true, replace: true, only: ['clientes', 'filters'] },
    );
};

</script>

<template>
    <Head title="Clientes" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Clientes</VCardTitle>
            <VDivider />
            <VCardText>
                <VDataTable
                    :headers="headers"
                    :items="clientes.data"
                    :items-length="clientes.total"
                    @update:options="onUpdateOptions"
                />
            </VCardText>
        </VCard>
    </div>
</template>
