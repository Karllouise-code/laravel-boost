<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

router.visit(route('boards.index'));
</script>

<template>
    <AuthenticatedLayout>
        <div></div>
    </AuthenticatedLayout>
</template>
