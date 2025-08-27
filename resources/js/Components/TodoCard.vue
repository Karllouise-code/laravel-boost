<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md dark:hover:shadow-lg transition-all duration-200">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
                <!-- Complete Checkbox -->
                <div class="flex-shrink-0 mt-1">
                    <button 
                        @click="$emit('toggle-complete', todo)"
                        class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200"
                        :class="todo.completed 
                            ? 'bg-green-500 border-green-500 text-white' 
                            : 'border-gray-300 dark:border-gray-600 hover:border-green-400 dark:hover:border-green-500'"
                    >
                        <svg v-if="todo.completed" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Todo Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-2">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white" :class="{ 'line-through text-gray-500 dark:text-gray-400': todo.completed }">
                            {{ todo.title }}
                        </h3>
                        <span v-if="todo.priority > 1" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                            :class="priorityColor(todo.priority)">
                            {{ priorityText(todo.priority) }}
                        </span>
                    </div>
                    
                    <p v-if="todo.description" class="text-gray-600 dark:text-gray-300 mb-3" :class="{ 'line-through': todo.completed }">
                        {{ todo.description }}
                    </p>
                    
                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <div v-if="todo.due_date" class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span :class="{ 'text-red-500': isOverdue(todo.due_date) && !todo.completed }">
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

            <!-- Actions -->
            <div class="flex items-center space-x-2 ml-4">
                <Link :href="route('todos.edit', todo.id)" class="p-2 text-gray-400 dark:text-gray-500 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </Link>
                <button @click="deleteTodo" class="p-2 text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

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
    return new Date(dueDate) < new Date();
};

const priorityColor = (priority) => {
    const colors = {
        1: 'bg-gray-100 text-gray-700',
        2: 'bg-blue-100 text-blue-700',
        3: 'bg-yellow-100 text-yellow-700',
        4: 'bg-orange-100 text-orange-700',
        5: 'bg-red-100 text-red-700',
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

const deleteTodo = () => {
    if (confirm('Are you sure you want to delete this todo?')) {
        router.delete(route('todos.destroy', props.todo.id));
    }
};
</script>