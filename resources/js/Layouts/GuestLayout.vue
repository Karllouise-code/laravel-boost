<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0 transition-colors duration-300"
        style="background: var(--color-bg);"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="mx-auto" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg transition-colors duration-300"
            style="background: var(--color-surface); border: 1px solid var(--color-border);"
        >
            <slot />
        </div>
    </div>
</template>
