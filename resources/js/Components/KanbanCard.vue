<template>
    <div 
        class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200 cursor-grab active:cursor-grabbing group"
        :class="{
            'border-l-4 border-l-red-500': isOverdue && !todo.completed,
            'opacity-60': todo.completed
        }"
    >
        <!-- Header -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-2 flex-1">
                <!-- Priority Badge -->
                <span 
                    v-if="todo.priority > 1" 
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                    :class="priorityColor(todo.priority)"
                >
                    {{ priorityText(todo.priority) }}
                </span>
                <!-- Completion Badge -->
                <span 
                    v-if="todo.completed"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300"
                >
                    ✓ Done
                </span>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button 
                    @click.stop="toggleComplete"
                    class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-green-500 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/50 transition-all"
                    :title="todo.completed ? 'Mark as incomplete' : 'Mark as complete'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
                <Link 
                    :href="route('todos.edit', todo.id)" 
                    class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/50 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </Link>
                <button 
                    @click.stop="$emit('delete', todo)"
                    class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Title -->
        <h3 
            class="font-semibold text-gray-800 dark:text-white mb-2 leading-tight"
            :class="{ 'line-through text-gray-500 dark:text-gray-400': todo.completed }"
        >
            {{ todo.title }}
        </h3>

        <!-- Description -->
        <p 
            v-if="todo.description" 
            class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2"
            :class="{ 'line-through': todo.completed }"
        >
            {{ todo.description }}
        </p>

        <!-- Footer -->
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <!-- Due Date -->
            <div v-if="todo.due_date" class="flex items-center space-x-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span :class="{ 'text-red-500 dark:text-red-400 font-medium': isOverdue && !todo.completed }">
                    {{ formatDate(todo.due_date) }}
                </span>
            </div>
            
            <!-- Created Date -->
            <div class="flex items-center space-x-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ formatDate(todo.created_at) }}</span>
            </div>
        </div>

        <!-- Drag Handle (visible on hover) -->
        <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
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

const priorityColor = (priority) => {
    const colors = {
        1: 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        2: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        3: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300',
        4: 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300',
        5: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
    };
    return colors[priority] || colors[1];
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
    router.patch(route('todos.update', props.todo.id), {
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