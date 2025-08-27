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
                <h1 class="text-3xl font-bold text-gray-800">Edit Todo</h1>
                <p class="text-gray-600 mt-2">Update your task details</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form @submit.prevent="submit">
                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.title }"
                            placeholder="Enter todo title..."
                            required
                        />
                        <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.description }"
                            placeholder="Enter a description (optional)..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Priority, Due Date, and Completed Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Priority
                            </label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.priority }"
                            >
                                <option value="1">Low Priority</option>
                                <option value="2">Normal Priority</option>
                                <option value="3">Medium Priority</option>
                                <option value="4">High Priority</option>
                                <option value="5">Urgent Priority</option>
                            </select>
                            <p v-if="form.errors.priority" class="mt-2 text-sm text-red-600">
                                {{ form.errors.priority }}
                            </p>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Due Date
                            </label>
                            <input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.due_date }"
                            />
                            <p v-if="form.errors.due_date" class="mt-2 text-sm text-red-600">
                                {{ form.errors.due_date }}
                            </p>
                        </div>

                        <!-- Completed Status -->
                        <div>
                            <label for="completed" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <div class="pt-3">
                                <label class="inline-flex items-center">
                                    <input
                                        id="completed"
                                        v-model="form.completed"
                                        type="checkbox"
                                        class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Mark as completed</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                        <Link 
                            :href="route('todos.index')" 
                            class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                            <span v-else>Update Todo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    todo: Object,
});

const form = useForm({
    title: props.todo.title,
    description: props.todo.description,
    priority: props.todo.priority,
    due_date: props.todo.due_date,
    completed: props.todo.completed,
});

const submit = () => {
    form.patch(route('todos.update', props.todo.id));
};
</script>