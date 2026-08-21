<script setup lang="ts">
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
    <div class="app-page">
        <VCard>
            <VCardTitle>Edición/Nuevo</VCardTitle>
            <VDivider />
            <VCardText>
                <VTextField
                    label="Nombre"
                    variant="outlined"
                    density="compact"
                />
                <div class="mt-4">
                    <p class="text-body-2 mb-2">Permisos</p>
                    <VCheckbox
                    />
                </div>
                <VBtn class="mt-4" color="primary">Guardar</VBtn>
            </VCardText>
        </VCard>
    </div>
</template>
