<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
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
        usersIndex().url,
        {
            page,
            limit: itemsPerPage,
            sort: sortBy[0]?.key ?? 'name',
            order: sortBy[0]?.order ?? 'asc',
        },
        { preserveState: true, preserveScroll: true, replace: true, only: ['users', 'filters'] },
    );
};


</script>

<template>
    <Head title="Usuarios" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Usuarios</VCardTitle>
            <VDivider />
            <VCardText>
                <VDataTable
                :headers="headers"
                    :items="users.data"
                    :items-length="users.total"
                    @update:options="onUpdateOptions"

                />
            </VCardText>
        </VCard>
    </div>
</template>
