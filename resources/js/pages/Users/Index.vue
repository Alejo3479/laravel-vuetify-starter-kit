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

defineProps<{
    users: PaginatedUsers;
    filters: Filters;
}>();

</script>

<template>
    <Head title="Usuarios" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Usuarios</VCardTitle>
            <VDivider />
            <VCardText>
                <VDataTable

                />
            </VCardText>
        </VCard>
    </div>
</template>
