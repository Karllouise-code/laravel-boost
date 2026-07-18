<template>
    <div class="p-4 border-b" :style="{ borderColor: 'var(--color-border)' }">
        <div class="flex items-center justify-between">
            <div class="flex items-center flex-1 min-w-0">
                <ColorPicker v-model="localColor" @update:modelValue="updateColor" />
                <input
                    v-if="editing"
                    ref="nameInput"
                    v-model="localName"
                    @blur="updateName"
                    @keydown.enter="$event.target.blur()"
                    class="ml-2 font-semibold bg-transparent border-b-2 outline-none flex-1"
                    :style="{ color: 'var(--color-text-primary)', borderColor: 'var(--color-accent)' }"
                />
                <h3
                    v-else
                    @click="startEditing"
                    class="font-semibold ml-2 cursor-text truncate"
                    style="color: var(--color-text-primary);"
                >
                    {{ column.name }}
                </h3>
                <span
                    class="ml-2 text-xs px-2 py-1 rounded-full"
                    :style="{ background: 'var(--color-accent-bg)', color: 'var(--color-accent)' }"
                >
                    {{ todoCount }}
                </span>
            </div>
            <button
                v-if="canDelete"
                @click="$emit('delete', column)"
                class="ml-2 p-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
                style="color: var(--color-text-muted);"
                title="Delete column"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import ColorPicker from './ColorPicker.vue';

const props = defineProps({
    column: Object,
    todoCount: Number,
    canDelete: Boolean,
});

const emit = defineEmits(['update', 'delete']);

const editing = ref(false);
const localName = ref(props.column.name);
const localColor = ref(props.column.color);
const nameInput = ref(null);

const startEditing = () => {
    editing.value = true;
    nextTick(() => nameInput.value?.focus());
};

const updateName = () => {
    editing.value = false;
    if (localName.value !== props.column.name) {
        emit('update', props.column, { name: localName.value });
    }
};

const updateColor = (color) => {
    emit('update', props.column, { color });
};
</script>
