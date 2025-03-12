<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    perfil: Object,
    curriculumActual: Object,
    nivelesAcademicos: Array,
    estadosAcademicos: Array,
    nivelesExperiencia: Array,
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // Fallback a una ruta predeterminada si no hay historial
        router.visit(route('welcome'));
    }
};

// nivel académico requiere especialidad?
const requiereEspecialidad = computed(() => {
    if (!infoForm.id_nivel_academico) return false;
    const nivelSeleccionado = props.nivelesAcademicos.find(nivel => nivel.id == infoForm.id_nivel_academico);
    return nivelSeleccionado?.requiere_especialidad || false;
});

// Formulario para información del perfil
const infoForm = useForm({
    nombre_completo: props.perfil?.nombre_completo || '',
    dui_aspirante: props.perfil?.dui_aspirante || '',
    genero: props.perfil?.genero || '',
    telefono: props.perfil?.telefono || '',
    id_nivel_academico: props.perfil?.id_nivel_academico || '',
    carrera: props.perfil?.carrera || '',
    especialidad: props.perfil?.especialidad || '',
    id_estado_academico: props.perfil?.id_estado_academico || '',
    id_experiencia: props.perfil?.id_experiencia || '',
});

// Formulario para cambio de contraseña
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Formulario para actualizar CV
const cvForm = useForm({
    new_cv: null,
});

// Referencias para validaciones en tiempo real
const passwordFocused = ref(false);
const passwordConfirmationFocused = ref(false);
const fileInput = ref(null);

// Validaciones para la contraseña
const passwordValidation = computed(() => {
    const password = passwordForm.password;
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
    return passwordForm.password === passwordForm.password_confirmation && passwordForm.password_confirmation !== '';
});

// Mostrar/ocultar secciones
const showPasswordSection = ref(false);
const showCVSection = ref(false);

// Formatear el DUI para mostrar
const formatDui = (value) => {
    if (!value) return '';
    value = value.replace(/\D/g, '');
    
    // Formato 00000000-0
    if (value.length > 8) {
        return value.substring(0, 8) + '-' + value.substring(8, 9);
    }
    
    return value;
};

// Manejar cambios en el DUI
const handleDuiChange = (e) => {
    infoForm.dui_aspirante = formatDui(e.target.value);
};

// Formatear teléfono automáticamente
const formatTelefono = (value) => {
    if (!value) return '';
    value = value.replace(/\D/g, '');
    
    // Formato 0000-0000
    if (value.length > 4) {
        return value.substring(0, 4) + '-' + value.substring(4, 8);
    }
    
    return value;
};

// Manejar cambios en el teléfono
const handleTelefonoChange = (e) => {
    infoForm.telefono = formatTelefono(e.target.value);
};

// Manejar la subida del CV
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        cvForm.new_cv = file;
    }
};

// Acciones de formulario
const submitInfoForm = () => {
    infoForm.post(route('aspirante.perfil.info'), {
        onSuccess: () => {
            // Mensaje de éxito ya manejado por flash
        },
    });
};

const submitPasswordForm = () => {
    passwordForm.post(route('aspirante.perfil.password'), {
        onSuccess: () => {
            passwordForm.reset();
            showPasswordSection.value = false;
            passwordFocused.value = false;
            passwordConfirmationFocused.value = false;
        },
    });
};

const submitCVForm = () => {
    cvForm.post(route('aspirante.perfil.cv.update'), {
        onSuccess: () => {
            // Restablecer el formulario
            cvForm.reset();
            if (fileInput.value) {
                fileInput.value.value = null;
            }
            showCVSection.value = false;
        },
    });
};

// Descargar CV actual
const downloadCurrentCV = () => {
    window.location.href = route('aspirante.perfil.cv.download');
};

// Observar cambios en nivel académico para mostrar/ocultar especialidad
watch(() => infoForm.id_nivel_academico, (newValue) => {
    if (newValue) {
        const nivelSeleccionado = props.nivelesAcademicos.find(nivel => nivel.id == newValue);
        if (!nivelSeleccionado?.requiere_especialidad) {
            infoForm.especialidad = '';
        }
    }
});
</script>

<template>
    <Head title="Mi Perfil" />
    <GuestLayout>
        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">Mi Perfil</h1>
                    <p class="text-gray-300 text-sm">Gestiona tu información personal y documentos</p>
                </div>
            </div>

            <!-- Mensajes de éxito y error -->
            <div v-if="$page.props.flash && $page.props.flash.error" 
                class="alert alert-error max-w-3xl mx-auto mb-6 shadow-lg" 
                role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $page.props.flash.error }}</span>
            </div>

            <div v-if="$page.props.flash && $page.props.flash.success" 
                class="alert alert-success max-w-3xl mx-auto mb-6 shadow-lg" 
                role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>

            

            <!-- Register Card -->
            <div class="container mx-auto px-4 mb-8">
                
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <!-- Información Personal -->
                        <div class="px-8 py-6">

                            <!-- Icono de retorno-->
                            
                            <button 
                                @click="goBack"
                                class="flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                <span></span>
                            </button>

                            <!-- -->

                            <div class="text-center space-y-2 mb-8">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Información Personal</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Actualiza tu Infromación Personal, Contraseña o Curriculum</p>
                            </div>
                            
                            <form @submit.prevent="submitInfoForm" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nombre Completo -->
                                    <div class="space-y-1">
                                        <label for="nombre_completo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Nombre Completo *
                                        </label>
                                        <input 
                                            id="nombre_completo"
                                            type="text"
                                            v-model="infoForm.nombre_completo"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            required
                                        />
                                        <InputError class="mt-1" :message="infoForm.errors.nombre_completo" />
                                    </div>

                                    <!-- DUI (Editable) -->
                                    <div class="space-y-1">
                                        <label for="dui_aspirante" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            DUI (sin guiones) *
                                        </label>
                                        <input 
                                            id="dui_aspirante"
                                            type="text"
                                            v-model="infoForm.dui_aspirante"
                                            @input="handleDuiChange"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            placeholder="00000000-0"
                                            maxlength="10"
                                            required
                                        />
                                        <InputError class="mt-1" :message="infoForm.errors.dui_aspirante" />
                                    </div>

                                    <!-- Género (Editable) -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Género *
                                        </label>
                                        <div class="mt-2 flex space-x-6">
                                            <div class="flex items-center">
                                                <input
                                                    id="genero-m"
                                                    type="radio"
                                                    v-model="infoForm.genero"
                                                    value="M"
                                                    class="h-4 w-4 text-blue-600 dark:text-blue-400 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-400"
                                                    required
                                                />
                                                <label for="genero-m" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Masculino
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    id="genero-f"
                                                    type="radio"
                                                    v-model="infoForm.genero"
                                                    value="F"
                                                    class="h-4 w-4 text-blue-600 dark:text-blue-400 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-400"
                                                />
                                                <label for="genero-f" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Femenino
                                                </label>
                                            </div>
                                        </div>
                                        <InputError class="mt-1" :message="infoForm.errors.genero" />
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="space-y-1">
                                        <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Teléfono *
                                        </label>
                                        <input 
                                            id="telefono"
                                            type="tel"
                                            v-model="infoForm.telefono"
                                            @input="handleTelefonoChange"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            placeholder="0000-0000"
                                            maxlength="9"
                                            required
                                        />
                                        <InputError class="mt-1" :message="infoForm.errors.telefono" />
                                    </div>

                                    <!-- Nivel Académico -->
                                    <div class="space-y-1">
                                        <label for="id_nivel_academico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Nivel académico *
                                        </label>
                                        <select
                                            id="id_nivel_academico"
                                            v-model="infoForm.id_nivel_academico"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            required
                                        >
                                            <option value="">Seleccione su nivel académico</option>
                                            <option v-for="nivel in nivelesAcademicos" :key="nivel.id" :value="nivel.id">
                                                {{ nivel.nombre }}
                                            </option>
                                        </select>
                                        <InputError class="mt-1" :message="infoForm.errors.id_nivel_academico" />
                                    </div>

                                    <!-- Estado Académico -->
                                    <div class="space-y-1">
                                        <label for="id_estado_academico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Estado académico *
                                        </label>
                                        <select
                                            id="id_estado_academico"
                                            v-model="infoForm.id_estado_academico"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            required
                                        >
                                            <option value="">Seleccione su estado académico</option>
                                            <option v-for="estado in estadosAcademicos" :key="estado.id" :value="estado.id">
                                                {{ estado.nombre }}
                                            </option>
                                        </select>
                                        <InputError class="mt-1" :message="infoForm.errors.id_estado_academico" />
                                    </div>

                                    <!-- Carrera -->
                                    <div class="space-y-1">
                                        <label for="carrera" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Carrera *
                                        </label>
                                        <input
                                            id="carrera"
                                            type="text"
                                            v-model="infoForm.carrera"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            placeholder="Ej: Ingeniería en Sistemas, Licenciatura en Contabilidad"
                                            required
                                        />
                                        <InputError class="mt-1" :message="infoForm.errors.carrera" />
                                    </div>

                                    <!-- Especialidad (condicional) -->
                                    <div v-if="requiereEspecialidad" class="space-y-1">
                                        <label for="especialidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Especialidad *
                                        </label>
                                        <input
                                            id="especialidad"
                                            type="text"
                                            v-model="infoForm.especialidad"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            placeholder="Ej: Maestría en Admón. de Empresas, Doctorado en Finanzas, etc"
                                            :required="requiereEspecialidad"
                                        />
                                        <InputError class="mt-1" :message="infoForm.errors.especialidad" />
                                    </div>

                                    <!-- Nivel de Experiencia -->
                                    <div class="space-y-1">
                                        <label for="id_experiencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Nivel de experiencia *
                                        </label>
                                        <select
                                            id="id_experiencia"
                                            v-model="infoForm.id_experiencia"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                            required
                                        >
                                            <option value="">Seleccione su nivel de experiencia</option>
                                            <option v-for="experiencia in nivelesExperiencia" :key="experiencia.id" :value="experiencia.id">
                                                {{ experiencia.nombre }}
                                            </option>
                                        </select>
                                        <InputError class="mt-1" :message="infoForm.errors.id_experiencia" />
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button 
                                        type="submit" 
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50"
                                        :disabled="infoForm.processing"
                                    >
                                        {{ infoForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Sección de Contraseña -->
                        <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white">Cambiar Contraseña</h3>
                                <button 
                                    @click="showPasswordSection = !showPasswordSection" 
                                    class="btn btn-sm btn-ghost"
                                >
                                    <span v-if="!showPasswordSection">Mostrar</span>
                                    <span v-else>Ocultar</span>
                                </button>
                            </div>

                            <form v-if="showPasswordSection" @submit.prevent="submitPasswordForm" class="space-y-6">
                                <div class="space-y-1">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Contraseña Actual *
                                    </label>
                                    <input 
                                        id="current_password"
                                        type="password"
                                        v-model="passwordForm.current_password"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        required
                                    />
                                    <InputError class="mt-1" :message="passwordForm.errors.current_password" />
                                </div>

                                <div class="space-y-1">
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nueva Contraseña *
                                    </label>
                                    <input 
                                        id="password"
                                        type="password"
                                        v-model="passwordForm.password"
                                        @focus="passwordFocused = true"
                                        @blur="passwordFocused = passwordForm.password.length > 0"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        required
                                    />
                                    <InputError class="mt-1" :message="passwordForm.errors.password" />
                                    
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
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Confirmar Nueva Contraseña *
                                    </label>
                                    <input 
                                        id="password_confirmation"
                                        type="password"
                                        v-model="passwordForm.password_confirmation"
                                        @focus="passwordConfirmationFocused = true"
                                        @blur="passwordConfirmationFocused = passwordForm.password_confirmation.length > 0"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                        required
                                    />
                                    
                                    <!-- Indicador de coincidencia de contraseñas -->
                                    <div v-if="passwordConfirmationFocused && passwordForm.password_confirmation.length > 0" class="mt-2">
                                        <span v-if="passwordMatch" class="text-green-600 dark:text-green-400 text-sm flex items-center">
                                            <span class="mr-1">✓</span> Las contraseñas coinciden
                                        </span>
                                        <span v-else class="text-red-500 dark:text-red-400 text-sm flex items-center">
                                            <span class="mr-1">✗</span> Las contraseñas no coinciden
                                        </span>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button 
                                        type="submit" 
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50"
                                        :disabled="passwordForm.processing || (!passwordValidation.isValid && passwordForm.password.length > 0)"
                                    >
                                        {{ passwordForm.processing ? 'Actualizando...' : 'Actualizar Contraseña' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Sección de Curriculum Vitae -->
                        <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white">Curriculum Vitae</h3>
                                <button 
                                    @click="showCVSection = !showCVSection" 
                                    class="btn btn-sm btn-ghost"
                                >
                                    <span v-if="!showCVSection">Actualizar</span>
                                    <span v-else>Cancelar</span>
                                </button>
                            </div>

                            <!-- Información del CV actual -->
                            <div v-if="curriculumActual" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">CV actual:</span> {{ curriculumActual.nombre_archivo }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Subido el {{ new Date(curriculumActual.created_at).toLocaleDateString() }}
                                        </p>
                                    </div>
                                    <!-- <button 
                                        @click="downloadCurrentCV"
                                        class="btn btn-info"
                                    >
                                        Descargar
                                    </button> -->

                                    <button @click="downloadCurrentCV" class="btn flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-[1.2em]">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A1.125 1.125 0 0 0 4.125 20.25h15.75A1.125 1.125 0 0 0 21 18.75V16.5M7.5 12 12 16.5m0 0 4.5-4.5M12 16.5V3"/>
                                        </svg>
                                        Descargar
                                    </button>

                                    
                                </div>
                            </div>

                            <!-- Formulario para subir nuevo CV -->
                            <form v-if="showCVSection" @submit.prevent="submitCVForm" class="space-y-6">
                                <div class="space-y-1">
                                    <label for="new_cv" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nuevo Curriculum (PDF) *
                                    </label>
                                    <div class="flex flex-col space-y-2">
                                        <input 
                                            type="file"
                                            id="new_cv"
                                            ref="fileInput"
                                            @change="handleFileUpload"
                                            accept=".pdf"
                                            class="file-input file-input-bordered w-full bg-white dark:bg-gray-700 text-gray-800 dark:text-white"
                                            required
                                        />
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Formato permitido: PDF. Tamaño máximo: 5MB.
                                        </p>
                                    </div>
                                    <InputError class="mt-1" :message="cvForm.errors.new_cv" />
                                </div>

                                <div v-if="curriculumActual" class="alert alert-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <h3 class="font-bold">¡Atención!</h3>
                                        <div class="text-sm">Este proceso reemplazará su CV actual. Asegúrese de descargar su CV actual antes de continuar si desea conservarlo.</div>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button
                                        v-if="curriculumActual"
                                        type="button"
                                        @click="downloadCurrentCV"
                                        class="btn btn-outline"
                                    >
                                        Descargar CV Actual
                                    </button>
                                    <button 
                                        type="submit" 
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50"
                                        :disabled="cvForm.processing"
                                    >
                                        {{ cvForm.processing ? 'Subiendo...' : 'Subir Nuevo CV' }}
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