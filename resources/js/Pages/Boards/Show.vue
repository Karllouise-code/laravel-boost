<template>
    <AuthenticatedLayout>
        <div class="px-6 py-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-lg font-semibold" style="color:var(--color-text-primary);">{{ board.name }}</h1>
                <div class="flex items-center gap-3">
                    <div v-if="onlineUsers.length > 0" class="flex items-center -space-x-1.5">
                        <div v-for="user in onlineUsers" :key="user.id"
                            class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold border-2 border-white relative"
                            :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
                            :title="user.name">
                            {{ user.name.charAt(0).toUpperCase() }}
                            <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-green-500 rounded-full border border-white"></span>
                        </div>
                    </div>
                    <button @click="showShareModal = true"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all duration-200"
                        :style="{ background: 'var(--color-surface)', color: 'var(--color-text-secondary)', border: '1px solid var(--color-border)' }">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                        Share
                    </button>
                    <Link :href="route('todos.create', board.slug)"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all duration-200"
                        :style="{ background: 'var(--color-surface)', color: 'var(--color-text-secondary)', border: '1px solid var(--color-border)' }">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add
                    </Link>
                    <a :href="route('todos.export', board.slug)"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all duration-200"
                        :style="{ background: 'var(--color-surface)', color: 'var(--color-text-secondary)', border: '1px solid var(--color-border)' }">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        CSV
                    </a>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="overflow-x-auto pb-4">
                <div class="flex gap-0 min-h-[calc(100vh-120px)]">
                    <draggable
                        :model-value="board.columns"
                        @end="onColumnDragEnd"
                        group="columns"
                        item-key="id"
                        handle=".column-drag-handle"
                        class="flex gap-0"
                        :animation="200"
                        ghost-class="drag-ghost"
                        drag-class="dragging"
                        :force-fallback="true"
                        fallback-class="drag-fallback"
                    >
                        <template #item="{ element: column }">
                            <div
                                class="w-[300px] flex-shrink-0 flex flex-col group"
                                :style="{ borderRight: '1px solid var(--color-border)' }"
                            >
                                <ColumnHeader
                                    :column="column"
                                    :todo-count="getColumnTodos(column.id).length"
                                    :can-delete="board.columns.length > 1"
                                    @update="updateColumn"
                                    @delete="deleteColumn"
                                />
                                <div class="px-5 py-4 space-y-3 min-h-[200px]">
                                    <draggable
                                        :model-value="getColumnTodos(column.id)"
                                        @update:model-value="(val) => updateColumnTodos(column.id, val)"
                                        group="todos"
                                        @change="(evt) => onDragChange(evt, column.id)"
                                        item-key="id"
                                        class="space-y-3 min-h-[150px]"
                                        :animation="200"
                                        ghost-class="drag-ghost"
                                        drag-class="dragging"
                                        :force-fallback="true"
                                        fallback-class="drag-fallback"
                                    >
                                        <template #item="{ element }">
                                            <KanbanCard :todo="element" :board-slug="board.slug" @delete="deleteTodo" />
                                        </template>
                                    </draggable>
                                    <div v-if="getColumnTodos(column.id).length === 0" class="text-center py-8" style="color: var(--color-text-dim);">
                                        <p class="text-sm">Drop todos here</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </draggable>

                    <AddColumnButton v-if="board.columns.length < 10" @add="addColumn" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <ShareModal v-if="showShareModal" :board="board" @close="showShareModal = false" />
    <ConfirmDialog ref="confirmDialog" />
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import KanbanCard from '@/Components/KanbanCard.vue';
import ColumnHeader from '@/Components/ColumnHeader.vue';
import AddColumnButton from '@/Components/AddColumnButton.vue';
import ShareModal from '@/Components/ShareModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    board: Object,
    todos: Array,
});

const page = usePage();
watch(() => page.props.flash?.message, (msg) => {
    if (msg) toast.success(msg);
}, { immediate: true });

const todosByColumn = ref({});
const initTodosByColumn = () => {
    const grouped = {};
    props.board.columns.forEach(col => {
        grouped[col.id] = props.todos.filter(t => t.column_id === col.id);
    });
    todosByColumn.value = grouped;
};
initTodosByColumn();

const getColumnTodos = (columnId) => {
    return todosByColumn.value[columnId] || [];
};

const updateColumnTodos = (columnId, val) => {
    todosByColumn.value[columnId] = val;
};

const onlineUsers = ref([]);

let echoChannel = null;
let echoPresence = null;

onMounted(() => {
    if (window.Echo) {
        echoChannel = window.Echo.private(`board.${props.board.slug}`)
            .listen('.TodoCreated', (e) => {
                const allTodos = Object.values(todosByColumn.value).flat();
                if (!allTodos.find(t => t.id === e.id)) {
                    const colId = e.column_id || props.board.columns[0]?.id;
                    if (colId && todosByColumn.value[colId]) {
                        todosByColumn.value[colId].push(e);
                    }
                    toast.info(`New todo added: ${e.title}`);
                }
            })
            .listen('.TodoUpdated', (e) => {
                for (const colId in todosByColumn.value) {
                    const idx = todosByColumn.value[colId].findIndex(t => t.id === e.id);
                    if (idx !== -1) {
                        if (e.column_id && e.column_id != colId) {
                            todosByColumn.value[colId].splice(idx, 1);
                            if (todosByColumn.value[e.column_id]) {
                                todosByColumn.value[e.column_id].push(e);
                            }
                        } else {
                            todosByColumn.value[colId][idx] = e;
                        }
                        break;
                    }
                }
            })
            .listen('.TodoDeleted', (e) => {
                for (const colId in todosByColumn.value) {
                    todosByColumn.value[colId] = todosByColumn.value[colId].filter(t => t.id !== e.id);
                }
                toast.info('A todo was deleted');
            })
            .listen('.TodoReordered', (e) => {
                for (const colId in todosByColumn.value) {
                    const idx = todosByColumn.value[colId].findIndex(t => t.id === e.id);
                    if (idx !== -1) {
                        todosByColumn.value[colId].splice(idx, 1);
                        break;
                    }
                }
                const targetColId = e.column_id;
                if (targetColId && todosByColumn.value[targetColId]) {
                    todosByColumn.value[targetColId].push({ ...e, column_id: targetColId });
                }
            })
            .listen('.ColumnCreated', (e) => {
                if (!props.board.columns.find(c => c.id === e.id)) {
                    props.board.columns.push(e);
                    todosByColumn.value[e.id] = [];
                    toast.info(`New column added: ${e.name}`);
                }
            })
            .listen('.ColumnUpdated', (e) => {
                const idx = props.board.columns.findIndex(c => c.id === e.id);
                if (idx !== -1) {
                    props.board.columns[idx] = { ...props.board.columns[idx], ...e };
                }
            })
            .listen('.ColumnDeleted', (e) => {
                props.board.columns = props.board.columns.filter(c => c.id !== e.id);
                delete todosByColumn.value[e.id];
                toast.info('A column was deleted');
            })
            .listen('.ColumnsReordered', (e) => {
                e.columns.forEach(({ id, position }) => {
                    const col = props.board.columns.find(c => c.id === id);
                    if (col) col.position = position;
                });
                props.board.columns.sort((a, b) => a.position - b.position);
            });

        echoPresence = window.Echo.join(`presence-board.${props.board.slug}`)
            .here((users) => {
                onlineUsers.value = users;
            })
            .joining((user) => {
                if (!onlineUsers.value.find(u => u.id === user.id)) {
                    onlineUsers.value.push(user);
                    toast.info(`${user.name} joined the board`);
                }
            })
            .leaving((user) => {
                onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
                toast.info(`${user.name} left the board`);
            });
    }
});

onUnmounted(() => {
    if (echoChannel) window.Echo.leave(`board.${props.board.slug}`);
    if (echoPresence) window.Echo.leave(`presence-board.${props.board.slug}`);
});

const showShareModal = ref(false);

const onDragChange = (evt, columnId) => {
    if (evt.added) {
        const todo = evt.added.element;
        const todos = todosByColumn.value[columnId];
        const columnIndex = todos.indexOf(todo);
        const priority = todos.length - columnIndex;

        updateTodoStatus(todo.id, columnId, priority);
    }
};

const updateTodoStatus = (todoId, columnId, priority) => {
    router.patch(route('todos.reorder', props.board.slug), {
        todo_id: todoId,
        column_id: columnId,
        priority: priority,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const onColumnDragEnd = () => {
    const columns = board.columns.map((c, i) => ({ id: c.id, position: i }));
    router.patch(route('columns.reorder', props.board.slug), {
        columns,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const addColumn = async (data) => {
    router.post(route('columns.store', props.board.slug), data, {
        preserveState: true,
        preserveScroll: true,
    });
};

const updateColumn = async (column, data) => {
    router.patch(route('columns.update', [props.board.slug, column.id]), data, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteColumn = async (column) => {
    if (!confirm(`Delete "${column.name}"? Todos will be moved to another column.`)) return;
    router.delete(route('columns.destroy', [props.board.slug, column.id]), {
        preserveState: true,
        preserveScroll: true,
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
        router.delete(route('todos.destroy', [props.board.slug, todo.id]), {
            onSuccess: () => {
                for (const colId in todosByColumn.value) {
                    todosByColumn.value[colId] = todosByColumn.value[colId].filter(t => t.id !== todo.id);
                }
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
