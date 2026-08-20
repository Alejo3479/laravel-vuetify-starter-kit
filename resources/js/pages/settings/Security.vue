<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { onUnmounted, ref } from 'vue';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { edit } from '@/routes/security';
import { disable, enable } from '@/routes/two-factor';

type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    canManageTwoFactor: false,
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Security settings',
                href: edit(),
            },
        ],
    },
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => clearTwoFactorAuthData());
</script>

<template>
    <Head title="Security settings" />

    <section class="settings-section">
        <Heading
            variant="small"
            title="Cambiar contraseña"
            description="Asegúrese de que su cuenta esté usando una contraseña segura"
        />

        <Form
            v-bind="SecurityController.update.form()"
            :options="{ preserveScroll: true }"
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            v-slot="{ errors, processing }"
        >
            <div class="starter-field mb-4">
                <label for="current_password">Contraseña actual</label>
                <PasswordInput
                    id="current_password"
                    name="current_password"
                    density="compact"
                    variant="outlined"
                    hide-details="auto"
                    autocomplete="current-password"
                    :error-messages="errors.current_password"
                />
            </div>

            <div class="starter-field mb-4">
                <label for="password">Nueva contraseña</label>
                <PasswordInput
                    id="password"
                    name="password"
                    density="compact"
                    variant="outlined"
                    hide-details="auto"
                    autocomplete="new-password"
                    :error-messages="errors.password"
                />
            </div>

            <div class="starter-field mb-6">
                <label for="password_confirmation">Confirmar contraseña</label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    density="compact"
                    variant="outlined"
                    hide-details="auto"
                    autocomplete="new-password"
                    :error-messages="errors.password_confirmation"
                />
            </div>

            <VBtn
                color="primary"
                type="submit"
                :loading="processing"
                :disabled="processing"
                data-test="update-password-button"
            >
                Guardar contraseña
            </VBtn>
        </Form>
    </section>

    <section v-if="canManageTwoFactor" class="settings-section">
        <Heading
            variant="small"
            title="Autenticación de dos factores"
            description="Gestione sus configuraciones de autenticación de doble factor"
        />

        <template v-if="!twoFactorEnabled">
            <p class="text-body-2 text-medium-emphasis mb-4">
                Cuando habilite la autenticación de dos factores, se le pedirá un pin seguro durante el inicio de sesión. Este pin puede ser recuperado desde una aplicación compatible con TOTP en su teléfono.
            </p>

            <VBtn
                v-if="hasSetupData"
                prepend-icon="mdi-shield-check-outline"
                color="primary"
                @click="showSetupModal = true"
            >
                Proceder
            </VBtn>
            <Form
                v-else
                v-bind="enable.form()"
                @success="showSetupModal = true"
                #default="{ processing }"
            >
                <VBtn
                    type="submit"
                    color="primary"
                    :loading="processing"
                    :disabled="processing"
                >
                    Habilitar 2FA
                </VBtn>
            </Form>
        </template>

        <template v-else>
            <p class="text-body-2 text-medium-emphasis mb-4">
                Se le pedirá un pin seguro durante el inicio de sesión, puede recuperar el pin desde una aplicación compatible con TOTP en su teléfono.
            </p>

            <Form v-bind="disable.form()" #default="{ processing }">
                <VBtn
                    type="submit"
                    color="error"
                    class="mb-6 starter-danger-btn"
                    :loading="processing"
                    :disabled="processing"
                >
                    Desactivar 2FA
                </VBtn>
            </Form>

            <TwoFactorRecoveryCodes />
        </template>

        <TwoFactorSetupModal
            v-model:isOpen="showSetupModal"
            :requires-confirmation="requiresConfirmation"
            :two-factor-enabled="twoFactorEnabled"
        />
    </section>
</template>
