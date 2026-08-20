<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
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
        rolesIndex().url,
        {
            page,
            limit: itemsPerPage,
            sort: sortBy[0]?.key ?? 'name',
            order: sortBy[0]?.order ?? 'asc',
        },
        { preserveState: true, preserveScroll: true, replace: true, only: ['roles', 'filters'] },
    );
};

</script>
<template>
    <Head title="Roles" />

    <div class="app-page">
        <VCard>
            <VCardTitle>Listado de Roles</VCardTitle>
            <VDivider />
            <VCardText>
                <VDataTable
                    :headers="headers"
                    :items="roles.data"
                    :items-length="roles.total"
                    :items-per-page="roles.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="roles.current_page"
                    item-value="id"
                    @update:options="onUpdateOptions"
                />
            </VCardText>
        </VCard>
    </div>
</template>
