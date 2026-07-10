<template>
    <div
        class="rounded-lg p-4 border transition-colors duration-200 cursor-grab active:cursor-grabbing group select-none"
        :style="{
            background: 'var(--color-card)',
            borderColor: isOverdue && !todo.completed ? 'var(--color-accent)' : 'var(--color-border)',
            borderLeftWidth: isOverdue && !todo.completed ? '4px' : '1px',
            opacity: todo.completed ? 0.6 : 1,
        }"
    >
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-2 flex-1">
                <span
                    v-if="todo.priority > 1"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                    :style="priorityStyle(todo.priority)"
                >
                    {{ priorityText(todo.priority) }}
                </span>
                <span
                    v-if="todo.completed"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                    style="background:var(--color-accent-bg);color:var(--color-accent);"
                >
                    ✓ Done
                </span>
            </div>

            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                    @click.stop="toggleComplete"
                    class="p-1.5 rounded-md transition-all"
                    style="color:var(--color-text-muted);"
                    :title="todo.completed ? 'Mark as incomplete' : 'Mark as complete'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
                <Link
                    :href="route('todos.edit', [props.boardSlug, todo.id])"
                    class="p-1.5 rounded-md transition-all"
                    style="color:var(--color-text-muted);"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </Link>
                <button
                    @click.stop="$emit('delete', todo)"
                    class="p-1.5 rounded-md transition-all"
                    style="color:var(--color-text-muted);"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <h3
            class="font-semibold mb-2 leading-tight"
            :style="{
                color: 'var(--color-text-primary)',
                textDecoration: todo.completed ? 'line-through' : 'none',
            }"
        >
            {{ todo.title }}
        </h3>

        <p
            v-if="todo.description"
            class="text-sm mb-3 line-clamp-2"
            :style="{
                color: 'var(--color-text-secondary)',
                textDecoration: todo.completed ? 'line-through' : 'none',
            }"
        >
            {{ todo.description }}
        </p>

        <div class="flex items-center justify-between text-xs" style="color:var(--color-text-muted);">
            <div v-if="todo.due_date" class="flex items-center space-x-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span :style="{ color: isOverdue && !todo.completed ? 'var(--color-accent)' : 'inherit', fontWeight: isOverdue && !todo.completed ? 500 : 400 }">
                    {{ formatDate(todo.due_date) }}
                </span>
            </div>

            <div class="flex items-center space-x-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ formatDate(todo.created_at) }}</span>
            </div>
        </div>

        <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity" style="color:var(--color-text-dim);">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
            </svg>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    todo: Object,
    boardSlug: String,
});

const emit = defineEmits(['delete']);

const isOverdue = computed(() => {
    return props.todo.due_date && new Date(props.todo.due_date) < new Date();
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric'
    });
};

const priorityStyle = (priority) => {
    const styles = {
        2: { background: 'color-mix(in srgb, var(--color-accent) 12%, transparent)', color: 'var(--color-accent)' },
        3: { background: 'color-mix(in srgb, var(--color-accent) 18%, transparent)', color: 'var(--color-accent)' },
        4: { background: 'color-mix(in srgb, var(--color-accent) 25%, transparent)', color: 'var(--color-accent)' },
        5: { background: 'color-mix(in srgb, var(--color-accent) 35%, transparent)', color: 'var(--color-accent)' },
    };
    return styles[priority] || styles[2];
};

const priorityText = (priority) => {
    const texts = {
        1: 'Low',
        2: 'Normal', 
        3: 'Medium',
        4: 'High',
        5: 'Urgent',
    };
    return texts[priority] || 'Low';
};

const toggleComplete = () => {
    router.patch(route('todos.update', [props.boardSlug, props.todo.id]), {
        completed: !props.todo.completed
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>