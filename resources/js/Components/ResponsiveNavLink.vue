<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-semibold focus:outline-none transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-semibold focus:outline-none transition duration-150 ease-in-out',
);
</script>

<template>
    <Link
        :href="href"
        :class="classes"
        :style="{
            color: active ? 'var(--color-accent)' : 'var(--color-text-secondary)',
            borderColor: active ? 'var(--color-accent)' : 'transparent',
            background: active ? 'var(--color-accent-bg)' : 'transparent',
        }"
        @mouseenter="(e) => { if (!active) { e.target.style.color = 'var(--color-accent)'; e.target.style.background = 'var(--color-accent-bg)'; } }"
        @mouseleave="(e) => { if (!active) { e.target.style.color = 'var(--color-text-secondary)'; e.target.style.background = 'transparent'; } }"
    >
        <slot />
    </Link>
</template>
