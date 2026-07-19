<template>
    <div class="flex-shrink-0 flex items-start pt-3 group/add relative">
        <button
            v-if="!adding"
            @click="startAdding"
            class="w-10 h-10 rounded-full flex items-center justify-center opacity-30 hover:opacity-100 transition-all duration-200 hover:scale-110"
            style="color: var(--color-text-muted);"
            title="Add column"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>

        <div
            v-else
            class="w-[280px] rounded-lg border p-3"
            :style="{ background: 'var(--color-card)', borderColor: 'var(--color-border)' }"
        >
            <input
                ref="nameInput"
                v-model="newName"
                placeholder="Column name"
                class="w-full px-3 py-2 rounded-md border text-sm mb-2 outline-none focus:border-current"
                :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)', color: 'var(--color-text-primary)' }"
                @keydown.enter="submit"
                @keydown.escape="cancel"
            />
            <div class="flex items-center gap-2 mb-3">
                <ColorPicker v-model="newColor" />
                <span class="text-[11px]" style="color: var(--color-text-muted);">Pick a color</span>
            </div>
            <div class="flex gap-2">
                <button
                    @click="submit"
                    :disabled="!newName.trim()"
                    class="flex-1 px-3 py-1.5 text-xs font-medium rounded-md transition-all disabled:opacity-40"
                    :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
                >
                    Add
                </button>
                <button
                    @click="cancel"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                    style="color: var(--color-text-muted);"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import ColorPicker from './ColorPicker.vue';

const emit = defineEmits(['add']);

const adding = ref(false);
const newName = ref('');
const newColor = ref('#6366f1');
const nameInput = ref(null);

const startAdding = () => {
    adding.value = true;
    nextTick(() => nameInput.value?.focus());
};

const submit = () => {
    if (newName.value.trim()) {
        emit('add', { name: newName.value.trim(), color: newColor.value });
        cancel();
    }
};

const cancel = () => {
    adding.value = false;
    newName.value = '';
    newColor.value = '#6366f1';
};
</script>
