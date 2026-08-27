<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { index as rolesIndex, edit, show, destroy, create as rolesCreate } from '@/routes/roles';
import type { PaginatedPayload, Filters, FetchDataParams, TableOptions } from '@/types/paginacion';

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Roles',
            href: rolesIndex(),
        },
    ],
});

interface ItemRow {
    id: number;
    name: string;
}

// interface PaginatedPayload {
//     data: ItemRow[];
//     current_page: number;
//     per_page: number;
//     total: number;
// }

// interface Filters {
//     q: string | null;
//     sort: string | null;
//     order: 'asc' | 'desc' | null;
//     limit: number | null;
// }

const props = defineProps<{
    payload?: PaginatedPayload<ItemRow>;
    filters?: Filters;
}>();

const headers = [
    { title: 'Nombre', key: 'name' },
    { title: 'Acciones', key: 'actions', sortable: false, align: 'center' as const, width: '180px' },
];

const fetchData = (params: FetchDataParams) => {
    router.get(rolesIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['payload', 'filters'],
    });
};

const onUpdateOptions = ({
    page,
    itemsPerPage,
    sortBy,
}: TableOptions) => {
    fetchData({
        page,
        limit: itemsPerPage,
        sort: sortBy[0]?.key ?? 'name',
        order: sortBy[0]?.order ?? 'asc',
        q: search.value ?? '',
    });
};

const search = ref<string | null>(props.filters?.q ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

const confirmDialog = ref(false);
const roleToDelete = ref<ItemRow | null>(null);

const askDelete = (role: ItemRow) => {
    roleToDelete.value = role;
    confirmDialog.value = true;
};

const confirmDelete = () => {
    if (!roleToDelete.value) {
        return;
    }

    router.delete(destroy(roleToDelete.value.id).url, {
        preserveScroll: true,
        onFinish: () => {
            confirmDialog.value = false;
            roleToDelete.value = null;
        },
    });
};

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchData({
            page: 1,
            limit: props.filters?.limit ?? 10,
            sort: props.filters?.sort ?? 'name',
            order: props.filters?.order ?? 'asc',
            q: value ?? '',
        });
    }, 400);
});

</script>
<template>
    <Head title="Roles" />

    <div class="app-page">
        <VCard>
            <VCardText>
                <div class="d-flex flex-column flex-md-row align-md-center ga-4 mb-4">
                    <VTextField
                        v-model="search"
                        label="Buscar por nombre"
                        prepend-inner-icon="mdi-magnify"
                        placeholder="Administrador"
                        density="compact"
                        variant="outlined"
                        clearable
                        hide-details
                        class="order-2 order-md-1 grow"
                    />
                    <VBtn
                        class="order-1 order-md-2 w-100 w-md-auto"
                        color="primary"
                        prepend-icon="mdi-plus"
                        :href="rolesCreate().url"
                    >
                        Nuevo
                    </VBtn>
                </div>
                <VDataTableServer
                    density="compact"
                    :headers="headers"
                    :items="payload?.data ?? []"
                    :items-length="payload?.total ?? 0"
                    :items-per-page="payload?.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="payload?.current_page"
                    item-value="id"
                    @update:options="onUpdateOptions"
                >
                    <template #headers="{ columns }">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column.key ?? column.title"
                                class="font-weight-bold"
                                :class="{ 'text-center': column.key === 'actions' }"
                            >
                                {{ column.title }}
                            </th>
                        </tr>
                    </template>
                    <template v-slot:[`item.actions`]="{ item }">
                        <VBtn
                            icon="mdi-eye-outline"
                            variant="text"
                            size="small"
                            @click="router.visit(show(item.id).url)"
                        >
                            <VIcon icon="mdi-eye-outline" />
                            <VTooltip activator="parent" location="top">Ver</VTooltip>
                        </VBtn>
                        <VBtn
                            icon="mdi-pencil-outline"
                            variant="text"
                            size="small"
                            @click="router.visit(edit(item.id).url)"
                        >
                            <VIcon icon="mdi-pencil-outline" />
                            <VTooltip activator="parent" location="top">Editar</VTooltip>
                        </VBtn>
                        <VBtn
                            icon="mdi-trash-can-outline"
                            variant="text"
                            size="small"
                            color="error"
                            @click="askDelete(item)"
                            >
                            <VIcon icon="mdi-trash-can-outline" />
                            <VTooltip activator="parent" location="top">Eliminar</VTooltip>
                        </VBtn>
                    </template>
                </VDataTableServer>
                <VDialog v-model="confirmDialog" max-width="420">
                    <VCard>
                        <VCardTitle>Eliminar rol</VCardTitle>
                        <VCardText>
                            ¿Seguro que querés eliminar el rol
                            <strong>{{ roleToDelete?.name }}</strong>? Esta acción no
                            se puede deshacer.
                        </VCardText>
                        <VCardActions>
                            <VSpacer />
                            <VBtn variant="text" @click="confirmDialog = false">Cancelar</VBtn>
                            <VBtn color="error" variant="flat" @click="confirmDelete">Eliminar</VBtn>
                        </VCardActions>
                    </VCard>
                </VDialog>
            </VCardText>
        </VCard>
    </div>
</template>
