<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">✨ My Todos</h1>
                <p class="text-gray-600">Stay organized and productive</p>
            </div>

            <!-- Success Message -->
            <div v-if="$page.props.flash?.message" class="mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                    {{ $page.props.flash.message }}
                </div>
            </div>

            <!-- Add Todo Button -->
            <div class="mb-6">
                <Link :href="route('todos.create')" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Todo
                </Link>
            </div>

            <!-- Todo Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ todos.length }}</p>
                            <p class="text-gray-600 text-sm">Total Todos</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ completedCount }}</p>
                            <p class="text-gray-600 text-sm">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ pendingCount }}</p>
                            <p class="text-gray-600 text-sm">Pending</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="mb-6">
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg w-fit">
                    <button @click="currentFilter = 'all'" :class="currentFilter === 'all' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800'" class="px-4 py-2 rounded-md font-medium transition-all">
                        All
                    </button>
                    <button @click="currentFilter = 'pending'" :class="currentFilter === 'pending' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800'" class="px-4 py-2 rounded-md font-medium transition-all">
                        Pending
                    </button>
                    <button @click="currentFilter = 'completed'" :class="currentFilter === 'completed' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800'" class="px-4 py-2 rounded-md font-medium transition-all">
                        Completed
                    </button>
                </div>
            </div>

            <!-- Todo List -->
            <div class="space-y-4">
                <div v-if="filteredTodos.length === 0" class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">No todos found</h3>
                    <p class="text-gray-500">{{ currentFilter === 'all' ? 'Create your first todo to get started!' : `No ${currentFilter} todos yet.` }}</p>
                </div>

                <TodoCard 
                    v-for="todo in filteredTodos" 
                    :key="todo.id" 
                    :todo="todo" 
                    @toggle-complete="toggleComplete"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import TodoCard from '@/Components/TodoCard.vue';

const props = defineProps({
    todos: Array,
});

const currentFilter = ref('all');

const completedCount = computed(() => 
    props.todos.filter(todo => todo.completed).length
);

const pendingCount = computed(() => 
    props.todos.filter(todo => !todo.completed).length
);

const filteredTodos = computed(() => {
    if (currentFilter.value === 'completed') {
        return props.todos.filter(todo => todo.completed);
    } else if (currentFilter.value === 'pending') {
        return props.todos.filter(todo => !todo.completed);
    }
    return props.todos;
});

const toggleComplete = (todo) => {
    router.patch(route('todos.update', todo.id), {
        completed: !todo.completed
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>