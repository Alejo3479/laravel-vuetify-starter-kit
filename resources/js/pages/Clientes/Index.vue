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
</script>

<template>
    <Head title="Roles" />

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
