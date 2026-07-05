<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h1 class="text-xl font-bold" style="color:var(--color-text-primary);">KANBAN BOARD</h1>
                <div class="flex items-center justify-between sm:justify-end sm:gap-2">
                    <Link :href="route('todos.create')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-all duration-200"
                        :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="hidden sm:inline">Add</span>
                        <span class="sm:hidden">+</span>
                    </Link>
                    <div class="flex items-center gap-2">
                        <a :href="route('todos.export')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-all duration-200"
                            :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)', opacity: 0.8 }">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span class="hidden sm:inline">CSV</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
                <div v-for="stat in stats" :key="stat.label"
                    class="rounded-lg px-3 py-3 sm:px-4 sm:py-4 border"
                    :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                    <div class="text-center">
                        <div class="text-xl sm:text-2xl font-bold" style="color:var(--color-text-primary);">{{ stat.count }}</div>
                        <div class="text-xs sm:text-sm font-semibold whitespace-nowrap" style="color:var(--color-text-muted);">{{ stat.label }}</div>
                    </div>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- To Do Column -->
                <div class="rounded-xl border" :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                    <div class="p-4 border-b" :style="{ borderColor: 'var(--color-border)' }">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold flex items-center" style="color:var(--color-text-primary);">
                                <div class="w-3 h-3 rounded-full mr-2" style="background:var(--color-dot-todo);"></div>
                                To Do
                                <span class="ml-2 text-xs px-2 py-1 rounded-full"
                                    :style="{ background: 'var(--color-accent-bg)', color: 'var(--color-accent)' }">{{ todoTasks.length }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 min-h-[200px]">
                        <draggable v-model="todoTasks" group="todos" @change="onDragChange" item-key="id"
                            class="space-y-3 min-h-[150px]" :animation="200"
                            ghost-class="drag-ghost" drag-class="dragging"
                            :force-fallback="true" fallback-class="drag-fallback">
                            <template #item="{ element }">
                                <KanbanCard :todo="element" @delete="deleteTodo" />
                            </template>
                        </draggable>
                        <div v-if="todoTasks.length === 0" class="text-center py-8" style="color:var(--color-text-dim);">
                            <p class="text-sm">Drop todos here</p>
                        </div>
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="rounded-xl border" :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                    <div class="p-4 border-b" :style="{ borderColor: 'var(--color-border)' }">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold flex items-center" style="color:var(--color-text-primary);">
                                <div class="w-3 h-3 rounded-full mr-2" style="background:var(--color-dot-progress);"></div>
                                In Progress
                                <span class="ml-2 text-xs px-2 py-1 rounded-full"
                                    :style="{ background: 'var(--color-accent-bg)', color: 'var(--color-accent)' }">{{ inProgressTasks.length }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 min-h-[200px]">
                        <draggable v-model="inProgressTasks" group="todos" @change="onDragChange" item-key="id"
                            class="space-y-3 min-h-[150px]" :animation="200"
                            ghost-class="drag-ghost" drag-class="dragging"
                            :force-fallback="true" fallback-class="drag-fallback">
                            <template #item="{ element }">
                                <KanbanCard :todo="element" @delete="deleteTodo" />
                            </template>
                        </draggable>
                        <div v-if="inProgressTasks.length === 0" class="text-center py-8" style="color:var(--color-text-dim);">
                            <p class="text-sm">Drop todos here</p>
                        </div>
                    </div>
                </div>

                <!-- Done Column -->
                <div class="rounded-xl border" :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                    <div class="p-4 border-b" :style="{ borderColor: 'var(--color-border)' }">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold flex items-center" style="color:var(--color-text-primary);">
                                <div class="w-3 h-3 rounded-full mr-2" style="background:var(--color-dot-done);"></div>
                                Done
                                <span class="ml-2 text-xs px-2 py-1 rounded-full"
                                    :style="{ background: 'var(--color-accent-bg)', color: 'var(--color-accent)' }">{{ doneTasks.length }}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 min-h-[200px]">
                        <draggable v-model="doneTasks" group="todos" @change="onDragChange" item-key="id"
                            class="space-y-3 min-h-[150px]" :animation="200"
                            ghost-class="drag-ghost" drag-class="dragging"
                            :force-fallback="true" fallback-class="drag-fallback">
                            <template #item="{ element }">
                                <KanbanCard :todo="element" @delete="deleteTodo" />
                            </template>
                        </draggable>
                        <div v-if="doneTasks.length === 0" class="text-center py-8" style="color:var(--color-text-dim);">
                            <p class="text-sm">Drop todos here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <ConfirmDialog ref="confirmDialog" />
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import KanbanCard from '@/Components/KanbanCard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    todos: Array,
});

const page = usePage();
watch(() => page.props.flash?.message, (msg) => {
    if (msg) toast.success(msg);
}, { immediate: true });

// Reactive arrays for each column
const todoTasks = ref(props.todos.filter(todo => todo.status === 'todo'));
const inProgressTasks = ref(props.todos.filter(todo => todo.status === 'in_progress'));
const doneTasks = ref(props.todos.filter(todo => todo.status === 'done'));

// Computed stats
const todoCount = computed(() => todoTasks.value.length);
const inProgressCount = computed(() => inProgressTasks.value.length);
const doneCount = computed(() => doneTasks.value.length);

const stats = computed(() => [
    { label: 'Total', count: props.todos.length },
    { label: 'To Do', count: todoCount.value },
    { label: 'In Progress', count: inProgressCount.value },
    { label: 'Done', count: doneCount.value },
]);

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

const confirmDialog = ref(null);

const deleteTodo = async (todo) => {
    const confirmed = await confirmDialog.value?.open({
        title: 'Delete Todo',
        message: `Are you sure you want to delete "${todo.title}"?`,
        confirmText: 'Delete',
    });
    if (confirmed) {
        router.delete(route('todos.destroy', todo.id), {
            onSuccess: () => {
                todoTasks.value = todoTasks.value.filter(t => t.id !== todo.id);
                inProgressTasks.value = inProgressTasks.value.filter(t => t.id !== todo.id);
                doneTasks.value = doneTasks.value.filter(t => t.id !== todo.id);
            }
        });
    }
};
</script>

<style>
[draggable],
.drag-ghost,
.dragging,
.drag-fallback {
    -webkit-user-select: none;
    user-select: none;
}

.drag-ghost {
    outline: 2px dashed var(--color-accent, #6366f1);
    outline-offset: -2px;
    border-radius: 0.5rem;
    background: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
    -webkit-user-select: none;
    user-select: none;
}

.dragging {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.25);
    -webkit-user-select: none;
    user-select: none;
}

.drag-fallback {
    transform: scale(1.02);
    box-shadow: 0 15px 40px -8px rgba(0, 0, 0, 0.3);
    border-radius: 0.5rem;
    pointer-events: none !important;
    -webkit-user-select: none;
    user-select: none;
}
</style>