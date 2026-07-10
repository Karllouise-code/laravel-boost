<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-16 max-w-md text-center">
            <div class="rounded-xl border p-8"
                :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }">
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                    :style="{ background: 'var(--color-accent-bg)' }">
                    <svg class="w-8 h-8" :style="{ color: 'var(--color-accent)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                </div>
                <h1 class="text-xl font-bold mb-2" style="color:var(--color-text-primary);">{{ board.name }}</h1>
                <p class="text-sm mb-6" style="color:var(--color-text-muted);">
                    Shared by {{ board.owner.name }}
                </p>
                <form @submit.prevent="joinBoard">
                    <button type="submit"
                        class="w-full px-4 py-2.5 text-sm font-semibold rounded-lg transition-all"
                        :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
                        :disabled="joining">
                        {{ joining ? 'Joining...' : 'Join Board' }}
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    board: Object,
});

const joining = ref(false);

const joinBoard = () => {
    joining.value = true;
    router.post(route('shared.join', props.board.slug));
};
</script>
