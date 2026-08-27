<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { index as usersIndex, edit as userEdit, show as userShow, destroy as userDestroy, create as userCreate } from '@/routes/users';
import { index as clientsIndex, edit as clienteEdit, show as clienteShow, destroy as clienteDestroy, create as clientesCreate } from '@/routes/clientes';

interface ItemRow {
    id: number;
    name: string;
    email: string;
}

interface PaginatedPayload {
    data: ItemRow[];
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

const props = defineProps<{
    type: 'usuario' | 'cliente';
    payload: PaginatedPayload;
    filters: Filters;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: props.type === 'usuario' ? 'Usuarios' : 'Clientes',
            href: props.type === 'usuario' ? usersIndex() : clientsIndex(),
        },
    ],
});

const headers = [
    { title: 'Nombre', key: 'name' },
    { title: 'Correo', key: 'email' },
    { title: 'Acciones', key: 'actions', sortable: false, align: 'center' as const, width: '180px' },
];

const fetchData = (params: {
    page: number;
    limit: number;
    sort: string;
    order: 'asc' | 'desc';
    q: string;
}) => {
    router.get(props.type === 'usuario' ? usersIndex().url : clientsIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['payload' , 'filters'],
    });
};

const onUpdateOptions = ({
    page,
    itemsPerPage,
    sortBy,
}: {
    page: number;
    itemsPerPage: number;
    sortBy: { key: string; order: 'asc' | 'desc' }[];
}) => {
    fetchData({
        page,
        limit: itemsPerPage,
        sort: sortBy[0]?.key ?? 'name',
        order: sortBy[0]?.order ?? 'asc',
        q: search.value ?? '',
    });
};

const search = ref<string | null>(props.filters.q ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

const confirmDialog = ref(false);
const itemToDelete = ref<ItemRow | null>(null);

const askDelete = (user: ItemRow) => {
    itemToDelete.value = user;
    confirmDialog.value = true;
};

const confirmDelete = () => {
    if (!itemToDelete.value) {
        return;
    }

    router.delete(props.type === 'usuario' ? userDestroy(itemToDelete.value.id).url : clienteDestroy(itemToDelete.value.id).url, {
        preserveScroll: true,
        onFinish: () => {
            confirmDialog.value = false;
            itemToDelete.value = null;
        },
    });
};

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchData({
            page: 1,
            limit: props.filters.limit ?? 10,
            sort: props.filters.sort ?? 'name',
            order: props.filters.order ?? 'asc',
            q: value ?? '',
        });
    }, 400);
});

</script>

<template>
    <Head title="Usuarios" />

    <div class="app-page">
        <VCard>
            <VCardText>
                <div class="d-flex flex-column flex-md-row align-md-center ga-4 mb-4">
                    <VTextField
                        v-model="search"
                        label="Buscar por nombre o correo"
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
                        prepend-icon="mdi-plus"
                        :href="props.type === 'usuario' ? userCreate().url : clientesCreate().url"
                    >
                        Nuevo
                    </VBtn>
                </div>
                <VDataTableServer
                    density="compact"
                    :headers="headers"
                    :items="payload.data"
                    :items-length="payload.total"
                    :items-per-page="payload.per_page"
                    :items-per-page-options="[
                        { value: 10, title: '10' },
                        { value: 25, title: '25' },
                        { value: 50, title: '50' },
                    ]"
                    :page="payload.current_page"
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
                            @click="router.visit(props.type === 'usuario' ? userShow(item.id).url : clienteShow(item.id).url)"
                        >
                            <VIcon icon="mdi-eye-outline" />
                            <VTooltip activator="parent" location="top">Ver</VTooltip>
                        </VBtn>
                        <VBtn
                            icon="mdi-pencil-outline"
                            variant="text"
                            size="small"
                            @click="router.visit(props.type === 'usuario' ? userEdit(item.id).url : clienteEdit(item.id).url)"
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
                        <VCardTitle>Eliminar usuario</VCardTitle>
                        <VCardText>
                            ¿Seguro que querés eliminar el usuario
                            <strong>{{ itemToDelete?.name }}</strong>? Esta acción no
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
