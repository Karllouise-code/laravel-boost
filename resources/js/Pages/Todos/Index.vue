<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900 transition-colors duration-300">
        <div class="container mx-auto px-4 py-8">
            <!-- Header with Theme Toggle -->
            <div class="mb-8">
                <div class="flex justify-between items-start mb-6">
                    <div class="text-center flex-1">
                        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">📋 Todo Board</h1>
                        <p class="text-gray-600 dark:text-gray-300">Drag and drop to organize your tasks</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link :href="route('todos.create')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-purple-700 dark:hover:from-blue-700 dark:hover:to-purple-800 transform hover:scale-105 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Todo
                        </Link>
                        <a href="/download-sqlite" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-teal-600 dark:from-green-600 dark:to-teal-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-600 hover:to-teal-700 dark:hover:from-green-700 dark:hover:to-teal-800 transform hover:scale-105 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download SQLite
                        </a>
                        <ThemeToggle />
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="$page.props.flash?.message" class="mb-6">
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg shadow-sm">
                    {{ $page.props.flash.message }}
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ todos.length }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ todoCount }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">To Do</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ inProgressCount }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">In Progress</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ doneCount }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Done</div>
                    </div>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- To Do Column -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                To Do
                                <span class="ml-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded-full">{{ todoTasks.length }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 min-h-[200px]">
                        <draggable
                            v-model="todoTasks"
                            group="todos"
                            @change="onDragChange"
                            item-key="id"
                            class="space-y-3 min-h-[150px]"
                            :animation="200"
                        >
                            <template #item="{ element }">
                                <KanbanCard 
                                    :todo="element" 
                                    @delete="deleteTodo"
                                />
                            </template>
                        </draggable>
                        <div v-if="todoTasks.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm">Drop todos here</p>
                        </div>
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                                In Progress
                                <span class="ml-2 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs px-2 py-1 rounded-full">{{ inProgressTasks.length }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 min-h-[200px]">
                        <draggable
                            v-model="inProgressTasks"
                            group="todos"
                            @change="onDragChange"
                            item-key="id"
                            class="space-y-3 min-h-[150px]"
                            :animation="200"
                        >
                            <template #item="{ element }">
                                <KanbanCard 
                                    :todo="element" 
                                    @delete="deleteTodo"
                                />
                            </template>
                        </draggable>
                        <div v-if="inProgressTasks.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">Drop todos here</p>
                        </div>
                    </div>
                </div>

                <!-- Done Column -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                Done
                                <span class="ml-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-1 rounded-full">{{ doneTasks.length }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 min-h-[200px]">
                        <draggable
                            v-model="doneTasks"
                            group="todos"
                            @change="onDragChange"
                            item-key="id"
                            class="space-y-3 min-h-[150px]"
                            :animation="200"
                        >
                            <template #item="{ element }">
                                <KanbanCard 
                                    :todo="element" 
                                    @delete="deleteTodo"
                                />
                            </template>
                        </draggable>
                        <div v-if="doneTasks.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm">Drop todos here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import KanbanCard from '@/Components/KanbanCard.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const props = defineProps({
    todos: Array,
});

// Reactive arrays for each column
const todoTasks = ref(props.todos.filter(todo => todo.status === 'todo'));
const inProgressTasks = ref(props.todos.filter(todo => todo.status === 'in_progress'));
const doneTasks = ref(props.todos.filter(todo => todo.status === 'done'));

// Computed stats
const todoCount = computed(() => todoTasks.value.length);
const inProgressCount = computed(() => inProgressTasks.value.length);
const doneCount = computed(() => doneTasks.value.length);

// Handle drag and drop changes
const onDragChange = (evt) => {
    if (evt.added) {
        const todo = evt.added.element;
        const newStatus = getStatusFromColumn(todo);
        
        // Update the todo's status
        updateTodoStatus(todo.id, newStatus);
    }
};

const getStatusFromColumn = (todo) => {
    if (todoTasks.value.includes(todo)) return 'todo';
    if (inProgressTasks.value.includes(todo)) return 'in_progress';
    if (doneTasks.value.includes(todo)) return 'done';
    return 'todo';
};

const updateTodoStatus = (todoId, newStatus) => {
    // Update the todo's completed status based on the column
    const completed = newStatus === 'done';
    
    router.patch(route('todos.update', todoId), {
        status: newStatus,
        completed: completed
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            console.log(`Todo ${todoId} moved to ${newStatus}`);
        }
    });
};

const deleteTodo = (todo) => {
    if (confirm('Are you sure you want to delete this todo?')) {
        router.delete(route('todos.destroy', todo.id), {
            onSuccess: () => {
                // Remove from local arrays
                todoTasks.value = todoTasks.value.filter(t => t.id !== todo.id);
                inProgressTasks.value = inProgressTasks.value.filter(t => t.id !== todo.id);
                doneTasks.value = doneTasks.value.filter(t => t.id !== todo.id);
            }
        });
    }
};
</script>