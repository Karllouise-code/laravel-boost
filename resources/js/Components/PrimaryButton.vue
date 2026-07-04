<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-95 disabled:opacity-25 cursor-pointer"
        style="background: var(--color-accent); color: var(--color-accent-text);"
        @mouseenter="(e) => e.target.style.background = 'var(--color-accent-hover)'"
        @mouseleave="(e) => e.target.style.background = 'var(--color-accent)'"
    >
        <slot />
    </button>
</template>
