<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // Fallback a una ruta predeterminada si no hay historial
        router.visit(route('welcome'));
    }
};

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Contraseña" />

        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">Oportunidades de Empleo</h1>
                    <p class="text-gray-300 text-sm">Banco Central de Reserva de El Salvador</p>
                </div>
            </div>

            <!-- Recovery Card -->
            <div class="container mx-auto px-4 mb-8">
                <div class="max-w-md mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <div class="px-8 py-6">
                            

                            
                            <div class="text-center space-y-2 mb-8">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Recuperar Contraseña</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer la contraseña
                                </p>
                            </div>

                            <div v-if="status" class="mb-6 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <p class="text-sm text-green-600 dark:text-green-400 text-center">{{ status }}</p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="space-y-1">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Correo electrónico
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        v-model="form.email"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        placeholder="correo@ejemplo.com"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-1" :message="form.errors.email" />
                                </div>

                                <div class="flex items-center justify-between">
                                    <a
                                        :href="route('login')"
                                        class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition"
                                    >
                                        Volver al login
                                    </a>

                                    <button
                                        type="submit"
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50 dark:focus:ring-offset-gray-800"
                                        :disabled="form.processing"
                                    >
                                        {{ form.processing ? 'Enviando...' : 'Enviar enlace' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>