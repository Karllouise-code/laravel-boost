<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h1 class="text-xl font-bold" style="color:var(--color-text-primary);">MY BOARDS</h1>
                <button @click="showCreateModal = true"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-all duration-200"
                    :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Board
                </button>
            </div>

            <div v-if="boards.length === 0" class="text-center py-16">
                <p style="color:var(--color-text-muted);">No boards yet. Create your first board!</p>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <Link v-for="board in boards" :key="board.id"
                    :href="route('boards.show', board.slug)"
                    class="rounded-xl border p-5 transition-all duration-200 hover:shadow-md"
                    :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                    <h3 class="font-semibold mb-2" style="color:var(--color-text-primary);">{{ board.name }}</h3>
                    <p class="text-xs mb-3" style="color:var(--color-text-muted);">
                        {{ board.todos_count ?? 0 }} todos
                    </p>
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-2">
                            <div v-for="(collab, i) in (board.collaborators || []).slice(0, 3)" :key="collab.id"
                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border-2"
                                :style="{ background: 'var(--color-accent-bg)', color: 'var(--color-accent)', borderColor: 'var(--color-surface)' }">
                                {{ collab.name.charAt(0).toUpperCase() }}
                            </div>
                        </div>
                        <span v-if="(board.collaborators || []).length > 3" class="text-xs" style="color:var(--color-text-muted);">
                            +{{ board.collaborators.length - 3 }}
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Create Board Modal -->
            <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click.self="showCreateModal = false">
                <div class="fixed inset-0 bg-black/50" @click="showCreateModal = false"></div>
                <div class="relative rounded-xl border p-6 w-full max-w-md"
                    :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                    <h2 class="text-lg font-bold mb-4" style="color:var(--color-text-primary);">Create Board</h2>
                    <form @submit.prevent="createBoard">
                        <input v-model="createForm.name" type="text" placeholder="Board name"
                            class="w-full rounded-lg border px-3 py-2 mb-4 text-sm"
                            :style="{ background: 'var(--color-card)', borderColor: 'var(--color-border)', color: 'var(--color-text-primary)' }"
                            required autofocus />
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showCreateModal = false"
                                class="px-4 py-2 text-sm rounded-lg"
                                :style="{ color: 'var(--color-text-muted)' }">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-semibold rounded-lg"
                                :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
                                :disabled="createForm.processing">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    boards: Array,
});

const page = usePage();
watch(() => page.props.flash?.message, (msg) => {
    if (msg) toast.success(msg);
}, { immediate: true });

const showCreateModal = ref(false);
const createForm = useForm({ name: '' });

const createBoard = () => {
    createForm.post(route('boards.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};
</script>
