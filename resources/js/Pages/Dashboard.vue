<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight"
                style="color: var(--color-text-primary);"
            >
                Dashboard
            </h2>
        </template>

        <div class="container mx-auto px-4 py-8">
            <div
                class="overflow-hidden rounded-xl border"
                :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }"
            >
                <div class="p-6">
                    <p class="text-base font-semibold" style="color: var(--color-text-secondary);">
                        You're logged in!
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
