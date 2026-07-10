<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="$emit('close')">
        <div class="fixed inset-0 bg-black/50" @click="$emit('close')"></div>
        <div class="relative rounded-xl border p-6 w-full max-w-lg"
            :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
            <h2 class="text-lg font-bold mb-4" style="color:var(--color-text-primary);">Share "{{ board.name }}"</h2>

            <!-- Share URL -->
            <div class="mb-6">
                <label class="text-xs font-semibold mb-2 block" style="color:var(--color-text-muted);">SHAREABLE LINK</label>
                <div class="flex gap-2">
                    <input :value="shareUrl" readonly
                        class="flex-1 rounded-lg border px-3 py-2 text-sm"
                        :style="{ background: 'var(--color-card)', borderColor: 'var(--color-border)', color: 'var(--color-text-primary)' }" />
                    <button @click="copyUrl"
                        class="px-3 py-2 text-sm font-semibold rounded-lg transition-all"
                        :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }">
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>
            </div>

            <!-- Add Collaborator -->
            <div class="mb-6" v-if="isOwner">
                <label class="text-xs font-semibold mb-2 block" style="color:var(--color-text-muted);">ADD COLLABORATOR</label>
                <form @submit.prevent="addCollaborator" class="flex gap-2">
                    <input v-model="addForm.email" type="email" placeholder="email@example.com"
                        class="flex-1 rounded-lg border px-3 py-2 text-sm"
                        :style="{ background: 'var(--color-card)', borderColor: 'var(--color-border)', color: 'var(--color-text-primary)' }"
                        required />
                    <button type="submit"
                        class="px-3 py-2 text-sm font-semibold rounded-lg"
                        :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
                        :disabled="addForm.processing">
                        Add
                    </button>
                </form>
            </div>

            <!-- Collaborators List -->
            <div>
                <label class="text-xs font-semibold mb-2 block" style="color:var(--color-text-muted);">
                    COLLABORATORS ({{ board.collaborators.length }})
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <div v-for="collab in board.collaborators" :key="collab.id"
                        class="flex items-center justify-between py-2 px-3 rounded-lg"
                        :style="{ background: 'var(--color-card)' }">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                                :style="{ background: 'var(--color-accent-bg)', color: 'var(--color-accent)' }">
                                {{ collab.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <div class="text-sm font-medium" style="color:var(--color-text-primary);">
                                    {{ collab.name }}
                                    <span v-if="collab.id === board.owner_id" class="text-xs ml-1" style="color:var(--color-text-muted);">(owner)</span>
                                </div>
                                <div class="text-xs" style="color:var(--color-text-muted);">{{ collab.email }}</div>
                            </div>
                        </div>
                        <button v-if="isOwner && collab.id !== board.owner_id"
                            @click="removeCollaborator(collab.id)"
                            class="text-xs px-2 py-1 rounded"
                            :style="{ color: 'var(--color-accent)' }">
                            Remove
                        </button>
                    </div>
                </div>
            </div>

            <button @click="$emit('close')"
                class="absolute top-4 right-4 p-1 rounded-md"
                :style="{ color: 'var(--color-text-muted)' }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage, useForm } from '@inertiajs/vue3';

const props = defineProps({
    board: Object,
});

const emit = defineEmits(['close']);

const page = usePage();
const isOwner = computed(() => page.props.auth.user.id === props.board.owner_id);

const shareUrl = computed(() => {
    const base = window.location.origin;
    return `${base}/shared/${props.board.slug}`;
});

const copied = ref(false);
const copyUrl = async () => {
    await navigator.clipboard.writeText(shareUrl.value);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const addForm = useForm({ email: '' });

const addCollaborator = () => {
    addForm.post(route('boards.collaborators.store', props.board.slug), {
        onSuccess: () => {
            addForm.reset();
            router.reload({ only: ['board'] });
        },
    });
};

const removeCollaborator = (userId) => {
    router.delete(route('boards.collaborators.destroy', [props.board.slug, userId]), {
        onSuccess: () => {
            router.reload({ only: ['board'] });
        },
    });
};
</script>
