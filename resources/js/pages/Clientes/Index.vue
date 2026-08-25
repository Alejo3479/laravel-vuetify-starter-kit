<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
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


</script>

<template>
    <Head title="Clientes" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Clientes</VCardTitle>
            <VDivider />
            <VCardText>
                <VDataTable

                />
            </VCardText>
        </VCard>
    </div>
</template>
