<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8 max-w-2xl">
            <div class="mb-8">
                <Link :href="route('boards.show', board.slug)" class="inline-flex items-center mb-4 transition-colors" style="color:var(--color-text-muted);">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to {{ board.name }}
                </Link>
                <h1 class="text-3xl font-bold" style="color:var(--color-text-primary);">Create New Todo</h1>
                <p class="mt-2" style="color:var(--color-text-secondary);">Add a new task to your list</p>
            </div>

            <div class="rounded-xl border p-6" :style="{background:'var(--color-surface)', borderColor:'var(--color-border)'}">
                <form @submit.prevent="submit">
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium mb-2" style="color:var(--color-text-secondary);">
                            Title <span style="color:var(--color-accent);">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-3 border rounded-lg transition-colors"
                            :style="{
                                background: 'var(--color-card)',
                                borderColor: form.errors.title ? 'var(--color-accent)' : 'var(--color-border)',
                                color: 'var(--color-text-primary)',
                            }"
                            placeholder="Enter todo title..."
                            required
                        />
                        <p v-if="form.errors.title" class="mt-2 text-sm" style="color:var(--color-accent);">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium mb-2" style="color:var(--color-text-secondary);">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-3 border rounded-lg transition-colors"
                            :style="{
                                background: 'var(--color-card)',
                                borderColor: form.errors.description ? 'var(--color-accent)' : 'var(--color-border)',
                                color: 'var(--color-text-primary)',
                            }"
                            placeholder="Enter a description (optional)..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-2 text-sm" style="color:var(--color-accent);">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="column_id" class="block text-sm font-medium mb-2" style="color:var(--color-text-secondary);">
                                Column <span style="color:var(--color-accent);">*</span>
                            </label>
                            <select
                                id="column_id"
                                v-model="form.column_id"
                                class="w-full px-4 py-3 border rounded-lg transition-colors"
                                :style="{
                                    background: 'var(--color-card)',
                                    borderColor: form.errors.column_id ? 'var(--color-accent)' : 'var(--color-border)',
                                    color: 'var(--color-text-primary)',
                                }"
                            >
                                <option v-for="col in board.columns" :key="col.id" :value="col.id">
                                    {{ col.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.column_id" class="mt-2 text-sm" style="color:var(--color-accent);">
                                {{ form.errors.column_id }}
                            </p>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium mb-2" style="color:var(--color-text-secondary);">
                                Priority
                            </label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="w-full px-4 py-3 border rounded-lg transition-colors"
                                :style="{
                                    background: 'var(--color-card)',
                                    borderColor: form.errors.priority ? 'var(--color-accent)' : 'var(--color-border)',
                                    color: 'var(--color-text-primary)',
                                }"
                            >
                                <option value="1">Low Priority</option>
                                <option value="2">Normal Priority</option>
                                <option value="3">Medium Priority</option>
                                <option value="4">High Priority</option>
                                <option value="5">Urgent Priority</option>
                            </select>
                            <p v-if="form.errors.priority" class="mt-2 text-sm" style="color:var(--color-accent);">
                                {{ form.errors.priority }}
                            </p>
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium mb-2" style="color:var(--color-text-secondary);">
                                Due Date
                            </label>
                            <input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                class="w-full px-4 py-3 border rounded-lg transition-colors"
                                :style="{
                                    background: 'var(--color-card)',
                                    borderColor: form.errors.due_date ? 'var(--color-accent)' : 'var(--color-border)',
                                    color: 'var(--color-text-primary)',
                                }"
                                :min="today"
                            />
                            <p v-if="form.errors.due_date" class="mt-2 text-sm" style="color:var(--color-accent);">
                                {{ form.errors.due_date }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t" :style="{borderColor:'var(--color-border)'}">
                        <Link
                            :href="route('boards.show', board.slug)"
                            class="px-6 py-3 border rounded-lg transition-colors"
                            :style="{
                                color: 'var(--color-text-secondary)',
                                background: 'var(--color-card)',
                                borderColor: 'var(--color-border)',
                            }"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{
                                background: 'var(--color-accent)',
                                color: 'var(--color-accent-text)',
                            }"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                            <span v-else>Create Todo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    board: Object,
});

const form = useForm({
    title: '',
    description: '',
    priority: 2,
    due_date: '',
    completed: false,
    column_id: props.board.columns[0]?.id || null,
});

const today = computed(() => {
    return new Date().toISOString().split('T')[0];
});

const submit = () => {
    form.post(route('todos.store', props.board.slug));
};
</script>