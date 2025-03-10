<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Admin Login" />
        

        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">Oportunidades de Empleo</h1>
                    <p class="text-gray-300 text-sm">Banco Central de Reserva de El Salvador</p>
                </div>
            </div>

            <!-- Login Card -->
            <div class="container mx-auto px-4 mb-8">
                
                <div class="max-w-md mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <div class="px-8 py-6">
                            
                            <!-- Icono de retorno-->
                            <Link 
                                :href="route('welcome')" 
                                class="flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                <span></span>
                            </Link>

                            <div class="text-center space-y-2 mb-8">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Acceso Administrativo</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ingrese sus credenciales de administrador</p>
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
                                        placeholder="admin@ejemplo.com"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-1" :message="form.errors.email" />
                                </div>

                                <div class="space-y-1">
                                    <div class="flex justify-between">
                                        <label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Contraseña
                                        </label>
                                        <Link
                                            v-if="canResetPassword"
                                            :href="route('password.request')"
                                            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition"
                                        >
                                            ¿Olvidó su contraseña?
                                        </Link>
                                    </div>
                                    <input
                                        id="password"
                                        type="password"
                                        v-model="form.password"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        required
                                    />
                                    <InputError class="mt-1" :message="form.errors.password" />
                                </div>

                                <div class="flex items-center">
                                    <Checkbox 
                                        name="remember" 
                                        v-model:checked="form.remember"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                    />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full px-4 py-3 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50 dark:focus:ring-offset-gray-800"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Ingresando...' : 'Ingresar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>