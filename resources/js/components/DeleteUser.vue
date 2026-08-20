<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import PasswordInput from '@/components/PasswordInput.vue';

const dialog = ref(false);
const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <section class="settings-section">
        <Heading
            variant="small"
            title="Eliminar cuenta"
            description="Elimina tu cuenta y todos sus recursos"
        />

        <VSheet class="delete-warning pa-5">
            <h3 class="text-subtitle-1 font-weight-bold mb-1">Precaución</h3>
            <p class="text-body-2 mb-5">
                Por favor, procede con precaución, esto no se puede deshacer.
            </p>
            <VBtn
                color="error"
                class="starter-danger-btn"
                data-test="delete-user-button"
                @click="dialog = true"
            >
                Eliminar cuenta
            </VBtn>
        </VSheet>
    </section>

    <VDialog v-model="dialog" max-width="520">
        <VCard>
            <Form
                v-bind="ProfileController.destroy.form()"
                reset-on-success
                :options="{ preserveScroll: true }"
                @error="() => passwordInput?.focus()"
                @success="dialog = false"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <VCardTitle>¿Eliminar cuenta?</VCardTitle>
                <VCardText>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                        Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
                    </p>

                    <PasswordInput
                        id="password"
                        ref="passwordInput"
                        name="password"
                        label="Password"
                        placeholder="Contraseña"
                        :error-messages="errors.password"
                    />
                </VCardText>

                <VCardActions>
                    <VSpacer />
                    <VBtn
                        variant="text"
                        @click="
                            () => {
                                clearErrors();
                                reset();
                                dialog = false;
                            }
                        "
                    >
                        Cancel
                    </VBtn>
                    <VBtn
                        type="submit"
                        color="error"
                        :loading="processing"
                        :disabled="processing"
                        data-test="confirm-delete-user-button"
                    >
                        Delete account
                    </VBtn>
                </VCardActions>
            </Form>
        </VCard>
    </VDialog>
</template>
