<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Ajustes de apariencia',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Ajustes de apariencia" />

    <section class="settings-section">
        <Heading
            variant="small"
            title="Ajustes de apariencia"
            description="Actualice los ajustes de apariencia de su cuenta"
        />
        <AppearanceTabs />
    </section>
</template>
