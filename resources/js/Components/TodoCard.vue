<template>
    <div class="rounded-xl p-6 shadow-sm border transition-all duration-200"
        :style="{
            background: 'var(--color-surface)',
            borderColor: 'var(--color-border)',
            color: 'var(--color-text-primary)',
        }"
    >
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
                <div class="flex-shrink-0 mt-1">
                    <button 
                        @click="$emit('toggle-complete', todo)"
                        class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200"
                        :style="{
                            borderColor: todo.completed ? 'var(--color-accent)' : 'var(--color-border)',
                            background: todo.completed ? 'var(--color-accent)' : 'transparent',
                            color: todo.completed ? 'var(--color-accent-text)' : 'transparent',
                        }"
                    >
                        <svg v-if="todo.completed" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-2">
                        <h3 class="text-lg font-semibold"
                            :style="{
                                color: 'var(--color-text-primary)',
                                textDecoration: todo.completed ? 'line-through' : 'none',
                                opacity: todo.completed ? 0.6 : 1,
                            }"
                        >
                            {{ todo.title }}
                        </h3>
                        <span v-if="todo.priority > 1" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                            :style="priorityStyle(todo.priority)">
                            {{ priorityText(todo.priority) }}
                        </span>
                    </div>
                    
                    <p v-if="todo.description" class="mb-3"
                        :style="{
                            color: 'var(--color-text-secondary)',
                            textDecoration: todo.completed ? 'line-through' : 'none',
                        }"
                    >
                        {{ todo.description }}
                    </p>
                    
                    <div class="flex items-center space-x-4 text-sm"
                        :style="{ color: 'var(--color-text-muted)' }"
                    >
                        <div v-if="todo.due_date" class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span :style="isOverdue(todo.due_date) && !todo.completed ? { color: 'var(--color-accent)' } : {}">
                                Due: {{ formatDate(todo.due_date) }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Created: {{ formatDate(todo.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-2 ml-4">
                <Link :href="route('todos.edit', todo.id)" class="p-2 rounded-lg transition-all"
                    :style="{ color: 'var(--color-text-muted)' }"
                    @mouseenter="e => e.target.style.color = 'var(--color-accent)'"
                    @mouseleave="e => e.target.style.color = 'var(--color-text-muted)'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </Link>
                <button @click="deleteTodo" class="p-2 rounded-lg transition-all"
                    :style="{ color: 'var(--color-text-muted)' }"
                    @mouseenter="e => e.target.style.color = 'var(--color-accent)'"
                    @mouseleave="e => e.target.style.color = 'var(--color-text-muted)'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <ConfirmDialog ref="confirmDialog" />
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    todo: Object,
});

const emit = defineEmits(['toggle-complete']);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
};

const isOverdue = (dueDate) => {
    return dueDate && new Date(dueDate) < new Date();
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

const confirmDialog = ref(null);

const deleteTodo = async () => {
    const confirmed = await confirmDialog.value?.open({
        title: 'Delete Todo',
        message: `Are you sure you want to delete "${props.todo.title}"?`,
        confirmText: 'Delete',
    });
    if (confirmed) {
        router.delete(route('todos.destroy', props.todo.id));
    }
};
</script>
