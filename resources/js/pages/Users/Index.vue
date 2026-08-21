<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
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
