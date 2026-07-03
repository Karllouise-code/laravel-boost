<template>
    <Teleport to="body">
        <Transition name="dialog">
            <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center"
                style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px)">
                <div class="rounded-xl border p-6 w-full max-w-sm mx-4 shadow-2xl"
                    :style="{background:'var(--color-surface)', borderColor:'var(--color-border)'}">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                            style="background:color-mix(in srgb, var(--color-accent) 15%, transparent)">
                            <svg class="w-5 h-5" style="color:var(--color-accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold" style="color:var(--color-text-primary)">{{ title }}</h3>
                    </div>
                    <p class="text-sm mb-6" style="color:var(--color-text-secondary)">{{ message }}</p>
                    <div class="flex items-center justify-end space-x-3">
                        <button @click="cancel"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                            :style="{color:'var(--color-text-secondary)', background:'var(--color-card)'}">
                            {{ cancelText }}
                        </button>
                        <button @click="confirm"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all"
                            :style="{background:'var(--color-accent)', color:'var(--color-accent-text)'}">
                            {{ confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue';

const visible = ref(false);
const title = ref('');
const message = ref('');
const confirmText = ref('Delete');
const cancelText = ref('Cancel');
let resolveFn = null;

const open = (opts = {}) => {
    title.value = opts.title || 'Confirm';
    message.value = opts.message || 'Are you sure?';
    confirmText.value = opts.confirmText || 'Delete';
    cancelText.value = opts.cancelText || 'Cancel';
    visible.value = true;
    return new Promise((resolve) => {
        resolveFn = resolve;
    });
};

const confirm = () => {
    visible.value = false;
    resolveFn?.(true);
};

const cancel = () => {
    visible.value = false;
    resolveFn?.(false);
};

defineExpose({ open });
</script>

<style scoped>
.dialog-enter-active, .dialog-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.dialog-enter-from, .dialog-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
