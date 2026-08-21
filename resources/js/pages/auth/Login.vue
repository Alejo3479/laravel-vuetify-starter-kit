<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Ingresar',
        description: null,
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Ingresar" />

    <VAlert v-if="status" type="success" variant="tonal" class="mb-4">
        {{ status }}
    </VAlert>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
    >
        <div class="starter-field mb-4">
            <label for="email">Email</label>
            <VTextField
                id="email"
                type="email"
                name="email"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autofocus
                autocomplete="email"
                :error-messages="errors.email"
            />
        </div>

        <div class="d-flex align-center justify-space-between">
            <label for="password" class="auth-field-label">Contraseña</label>
            <TextLink v-if="canResetPassword" :href="request()" :tabindex="5">
                ¿Olvidaste tu contraseña?
            </TextLink>
        </div>

        <div class="starter-field mb-2">
            <PasswordInput
                id="password"
                name="password"
                density="compact"
                variant="outlined"
                hide-details="auto"
                required
                autocomplete="current-password"
                :error-messages="errors.password"
            />
        </div>

        <VCheckbox
            name="remember"
            label="Recordar sesión"
            value="on"
            density="compact"
            hide-details
        />

        <VBtn
            type="submit"
            class="mt-4" color="primary"
            block
            size="large"
            :loading="processing"
            :disabled="processing"
            data-test="login-button"
        >
            Ingresar
        </VBtn>

        <div v-if="canRegister" class="text-center text-body-2 mt-6">
            ¿No tienes una cuenta aún?
            <TextLink :href="register()" :tabindex="5">Registrar</TextLink>
        </div>
    </Form>
</template>
