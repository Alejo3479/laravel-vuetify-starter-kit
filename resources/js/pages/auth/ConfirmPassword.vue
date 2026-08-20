<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PasswordInput from '@/components/PasswordInput.vue';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirmar contraseña',
        description:
            'Esta es un área restringida de la aplicación. Por favor, confirma tu contraseña antes de continuar.',
    },
});
</script>

<template>
    <Head title="Confirmar contraseña" />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="starter-field mb-6">
            <label for="password">Contraseña</label>
            <PasswordInput
                id="password"
                name="password"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="current-password"
                autofocus
                :error-messages="errors.password"
            />
        </div>

        <VBtn
            type="submit"
            color="primary"
            block
            size="large"
            :loading="processing"
            :disabled="processing"
            data-test="confirm-password-button"
        >
            Confirmar contraseña
        </VBtn>
    </Form>
</template>
