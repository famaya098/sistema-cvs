<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Refs para validaciones en tiempo real
const passwordFocused = ref(false);
const passwordConfirmationFocused = ref(false);

// Validaciones para la contraseña
const passwordValidation = computed(() => {
    const password = form.password;
    return {
        hasLowercase: /[a-z]/.test(password),
        hasUppercase: /[A-Z]/.test(password),
        hasNumber: /[0-9]/.test(password),
        hasMinLength: password.length >= 8,
        isValid: /[a-z]/.test(password) && /[A-Z]/.test(password) && /[0-9]/.test(password) && password.length >= 8
    };
});

// Validación para la confirmación de contraseña
const passwordMatch = computed(() => {
    return form.password === form.password_confirmation && form.password_confirmation !== '';
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">Oportunidades de Empleo</h1>
                    <p class="text-gray-300 text-sm">Banco Central de Reserva de El Salvador</p>
                </div>
            </div>

            <!-- Reset Password Card -->
            <div class="container mx-auto px-4 mb-8">
                <div class="max-w-md mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <div class="px-8 py-6">
                            
                            <div class="text-center space-y-2 mb-8">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Restablecer Contraseña</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Ingrese su nueva contraseña
                                </p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="space-y-1">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Correo electrónico
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    />
                                    <InputError class="mt-1" :message="form.errors.email" />
                                </div>

                                <div class="space-y-1">
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nueva Contraseña
                                    </label>
                                    <input
                                        id="password"
                                        type="password"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        v-model="form.password"
                                        @focus="passwordFocused = true"
                                        @blur="passwordFocused = form.password.length > 0"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <InputError class="mt-1" :message="form.errors.password" />
                                    
                                    <!-- Requisitos de contraseña -->
                                    <div v-if="passwordFocused" class="mt-2 p-3 bg-gray-100 dark:bg-gray-700 rounded-md text-sm">
                                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">La contraseña debe cumplir:</h4>
                                        <ul class="space-y-1">
                                            <li class="flex items-center" :class="passwordValidation.hasLowercase ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                                <span class="mr-2" v-if="passwordValidation.hasLowercase">✓</span>
                                                <span class="mr-2" v-else>✗</span>
                                                Al menos una letra minúscula
                                            </li>
                                            <li class="flex items-center" :class="passwordValidation.hasUppercase ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                                <span class="mr-2" v-if="passwordValidation.hasUppercase">✓</span>
                                                <span class="mr-2" v-else>✗</span>
                                                Al menos una letra mayúscula
                                            </li>
                                            <li class="flex items-center" :class="passwordValidation.hasNumber ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                                <span class="mr-2" v-if="passwordValidation.hasNumber">✓</span>
                                                <span class="mr-2" v-else>✗</span>
                                                Al menos un número
                                            </li>
                                            <li class="flex items-center" :class="passwordValidation.hasMinLength ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                                <span class="mr-2" v-if="passwordValidation.hasMinLength">✓</span>
                                                <span class="mr-2" v-else>✗</span>
                                                Mínimo 8 caracteres
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label
                                        for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Confirmar Contraseña
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        v-model="form.password_confirmation"
                                        @focus="passwordConfirmationFocused = true"
                                        @blur="passwordConfirmationFocused = form.password_confirmation.length > 0"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.password_confirmation"
                                    />
                                    
                                    <!-- Indicador de coincidencia de contraseñas -->
                                    <div v-if="passwordConfirmationFocused && form.password_confirmation.length > 0" class="mt-2">
                                        <span v-if="passwordMatch" class="text-green-600 dark:text-green-400 text-sm flex items-center">
                                            <span class="mr-1">✓</span> Las contraseñas coinciden
                                        </span>
                                        <span v-else class="text-red-500 dark:text-red-400 text-sm flex items-center">
                                            <span class="mr-1">✗</span> Las contraseñas no coinciden
                                        </span>
                                    </div>
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
                                        :disabled="form.processing || (!passwordValidation.isValid && form.password.length > 0)"
                                    >
                                        Restablecer Contraseña
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