<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <!-- Mensaje de error -->
    <div v-if="page.props.flash && page.props.flash.error" 
        class="alert alert-error max-w-lg mx-auto mt-4 shadow-lg" 
        role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ page.props.flash.error }}</span>
    </div>

    <!-- Mensaje de éxito -->
    <div v-if="page.props.flash && page.props.flash.success" 
        class="alert alert-success max-w-lg mx-auto mt-4 shadow-lg" 
        role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ page.props.flash.success }}</span>
    </div>

    <!-- Mensaje informativo -->
    <div v-if="page.props.flash && page.props.flash.message" 
        class="alert alert-info max-w-lg mx-auto mt-4 shadow-lg" 
        role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ page.props.flash.message }}</span>
    </div>
</template>