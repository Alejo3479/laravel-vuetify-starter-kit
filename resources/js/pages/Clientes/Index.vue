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
