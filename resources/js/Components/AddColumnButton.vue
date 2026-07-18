<template>
    <div v-if="!adding" class="min-w-[280px] flex-shrink-0">
        <button
            @click="adding = true"
            class="w-full h-full min-h-[200px] rounded-xl border-2 border-dashed flex items-center justify-center transition-all hover:border-solid"
            :style="{ borderColor: 'var(--color-border)', color: 'var(--color-text-muted)' }"
        >
            <div class="text-center">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm font-medium">Add Column</span>
            </div>
        </button>
    </div>

    <div v-else class="min-w-[280px] flex-shrink-0 rounded-xl border" :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
        <div class="p-4">
            <input
                ref="nameInput"
                v-model="newName"
                placeholder="Column name"
                class="w-full px-3 py-2 rounded-lg border mb-3"
                :style="{ background: 'var(--color-card)', borderColor: 'var(--color-border)', color: 'var(--color-text-primary)' }"
                @keydown.enter="submit"
                @keydown.escape="cancel"
            />
            <div class="flex items-center gap-2 mb-3">
                <ColorPicker v-model="newColor" />
                <span class="text-sm" style="color: var(--color-text-muted);">Pick a color</span>
            </div>
            <div class="flex gap-2">
                <button
                    @click="submit"
                    :disabled="!newName.trim()"
                    class="flex-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-all disabled:opacity-50"
                    :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
                >
                    Add
                </button>
                <button
                    @click="cancel"
                    class="px-3 py-1.5 text-sm font-semibold rounded-lg transition-all"
                    :style="{ background: 'var(--color-surface)', color: 'var(--color-text-muted)' }"
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
