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

defineProps<{
    roles: PaginatedRoles;
    filters: Filters;
}>();

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
