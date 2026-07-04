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
        ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 focus:outline-none transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-semibold leading-5 focus:outline-none transition duration-150 ease-in-out',
);
</script>

<template>
    <Link
        :href="href"
        :class="classes"
        :style="{
            color: active ? 'var(--color-accent)' : 'var(--color-text-muted)',
            borderColor: active ? 'var(--color-accent)' : 'transparent',
        }"
        @mouseenter="(e) => { if (!active) { e.target.style.color = 'var(--color-accent)'; e.target.style.borderColor = 'var(--color-accent)'; } }"
        @mouseleave="(e) => { if (!active) { e.target.style.color = 'var(--color-text-muted)'; e.target.style.borderColor = 'transparent'; } }"
    >
        <slot />
    </Link>
</template>
