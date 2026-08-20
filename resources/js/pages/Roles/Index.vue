<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { index as rolesIndex } from '@/routes/roles';



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


</script>

<template>
    <Head title="Roles" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Roles</VCardTitle>
            <VDivider />
            <VCardText>
                <VDataTable

                />
            </VCardText>
        </VCard>
    </div>
</template>
