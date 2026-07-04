<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight"
                style="color: var(--color-text-primary);"
            >
                Profile
            </h2>
        </template>

        <div class="container mx-auto px-4 py-8 space-y-6">
            <div
                class="rounded-xl border p-4 sm:p-8"
                :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }"
            >
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div
                class="rounded-xl border p-4 sm:p-8"
                :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }"
            >
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div
                class="rounded-xl border p-4 sm:p-8"
                :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }"
            >
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
