<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <div class="container mx-auto px-4 py-8 max-w-2xl">
            <!-- Header -->
            <div class="mb-8">
                <Link :href="route('todos.index')" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-4 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Todos
                </Link>
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-800">Todo Details</h1>
                    <div class="flex items-center space-x-2">
                        <Link :href="route('todos.edit', todo.id)" class="px-4 py-2 text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </Link>
                        <button @click="deleteTodo" class="px-4 py-2 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Todo Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <!-- Status Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        :class="todo.completed 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-yellow-100 text-yellow-800'">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path v-if="todo.completed" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ todo.completed ? 'Completed' : 'Pending' }}
                    </span>
                </div>

                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-800 mb-4" :class="{ 'line-through text-gray-500': todo.completed }">
                    {{ todo.title }}
                </h2>

                <!-- Description -->
                <div v-if="todo.description" class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                    <p class="text-gray-600 bg-gray-50 p-4 rounded-lg" :class="{ 'line-through': todo.completed }">
                        {{ todo.description }}
                    </p>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Priority -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                            Priority
                        </h3>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium"
                            :class="priorityColor(todo.priority)">
                            {{ priorityText(todo.priority) }}
                        </span>
                    </div>

                    <!-- Due Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Due Date
                        </h3>
                        <p class="text-gray-800" :class="{ 'text-red-600': isOverdue(todo.due_date) && !todo.completed }">
                            {{ todo.due_date ? formatDate(todo.due_date) : 'No due date' }}
                            <span v-if="isOverdue(todo.due_date) && !todo.completed" class="text-red-600 text-sm ml-1">(Overdue)</span>
                        </p>
                    </div>

                    <!-- Created Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Created
                        </h3>
                        <p class="text-gray-800">{{ formatDate(todo.created_at) }}</p>
                    </div>

                    <!-- Last Updated -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Last Updated
                        </h3>
                        <p class="text-gray-800">{{ formatDate(todo.updated_at) }}</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Quick Actions</h3>
                    <div class="flex items-center space-x-4">
                        <button
                            @click="toggleComplete"
                            class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors"
                            :class="todo.completed 
                                ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' 
                                : 'bg-green-100 text-green-700 hover:bg-green-200'"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="todo.completed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ todo.completed ? 'Mark as Pending' : 'Mark as Completed' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    todo: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        weekday: 'long',
        month: 'long', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const isOverdue = (dueDate) => {
    return dueDate && new Date(dueDate) < new Date();
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
        1: 'Low Priority',
        2: 'Normal Priority',
        3: 'Medium Priority',
        4: 'High Priority',
        5: 'Urgent Priority',
    };
    return texts[priority] || 'Low Priority';
};

const toggleComplete = () => {
    router.patch(route('todos.update', props.todo.id), {
        completed: !props.todo.completed
    });
};

const deleteTodo = () => {
    if (confirm('Are you sure you want to delete this todo?')) {
        router.delete(route('todos.destroy', props.todo.id));
    }
};
</script>