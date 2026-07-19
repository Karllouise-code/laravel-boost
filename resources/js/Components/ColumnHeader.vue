<template>
    <div>
        <div class="px-5 pt-3 pb-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1 min-w-0">
                    <div class="column-drag-handle cursor-grab active:cursor-grabbing mr-1.5 p-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity" style="color: var(--color-text-muted);">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="9" cy="5" r="1.5"/><circle cx="15" cy="5" r="1.5"/>
                            <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                            <circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="19" r="1.5"/>
                        </svg>
                    </div>
                    <input
                        v-if="editing"
                        ref="nameInput"
                        v-model="localName"
                        @blur="updateName"
                        @keydown.enter="$event.target.blur()"
                        class="font-medium text-sm bg-transparent border-b border-transparent outline-none flex-1 focus:border-current"
                        :style="{ color: 'var(--color-text-primary)' }"
                    />
                    <h3
                        v-else
                        @click="startEditing"
                        class="font-medium text-sm cursor-text truncate"
                        style="color: var(--color-text-primary);"
                    >
                        {{ column.name }}
                    </h3>
                    <span
                        class="ml-1.5 text-[10px] tabular-nums opacity-50"
                        style="color: var(--color-text-muted);"
                    >
                        {{ todoCount }}
                    </span>
                </div>
                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                        @click="$emit('update', column, { color: nextColor })"
                        class="p-0.5 rounded transition-colors"
                        style="color: var(--color-text-muted);"
                        title="Cycle color"
                    >
                        <div class="w-3.5 h-3.5 rounded-full" :style="{ background: column.color }"></div>
                    </button>
                    <button
                        v-if="canDelete"
                        @click="$emit('delete', column)"
                        class="p-0.5 rounded transition-colors"
                        style="color: var(--color-text-muted);"
                        title="Delete column"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Accent line -->
        <div class="h-[3px] rounded-full mx-5" :style="{ background: column.color }"></div>
    </div>
</template>

<script setup>
import { ref, nextTick, computed } from 'vue';

const props = defineProps({
    column: Object,
    todoCount: Number,
    canDelete: Boolean,
});

const emit = defineEmits(['update', 'delete']);

const editing = ref(false);
const localName = ref(props.column.name);
const nameInput = ref(null);

const COLORS = ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'];
const nextColor = computed(() => {
    const idx = COLORS.indexOf(props.column.color);
    return COLORS[(idx + 1) % COLORS.length];
});

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
</script>
