<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm font-semibold leading-5 transition duration-150 ease-in-out focus:outline-none"
        :style="{ color: 'var(--color-text-primary)' }"
        @mouseenter="(e) => e.target.style.background = 'var(--color-accent-bg)'"
        @mouseleave="(e) => e.target.style.background = 'transparent'"
    >
        <slot />
    </Link>
</template>
