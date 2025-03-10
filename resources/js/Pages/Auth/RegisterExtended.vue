<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted, computed } from 'vue';

const props = defineProps({
    nivelesAcademicos: Array,
    estadosAcademicos: Array,
    nivelesExperiencia: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    dui_aspirante: '',
    genero: '',
    telefono: '',
    id_nivel_academico: '',
    carrera: '',
    especialidad: '',
    id_estado_academico: '',
    id_experiencia: '',
    cv: null,
    'g-recaptcha-response': null,
});

// Refs para validaciones en tiempo real
const requiereEspecialidad = ref(false);
const previewCv = ref(null);
const captchaLoaded = ref(false);
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

// Función para formatear el DUI automáticamente
const formatDui = (value) => {
    if (!value) return '';
    value = value.replace(/\D/g, '');
    
    // Formato 00000000-0
    if (value.length > 8) {
        return value.substring(0, 8) + '-' + value.substring(8, 9);
    }
    
    return value;
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

// Verificar si el nivel académico requiere especialidad
watch(() => form.id_nivel_academico, (newValue) => {
    if (newValue) {
        const nivelSeleccionado = props.nivelesAcademicos.find(nivel => nivel.id == newValue);
        requiereEspecialidad.value = nivelSeleccionado?.requiere_especialidad || false;
        
        if (!requiereEspecialidad.value) {
            form.especialidad = '';
        }
    } else {
        requiereEspecialidad.value = false;
    }
});

// Aplicar formato automático al teléfono
watch(() => form.telefono, (newValue) => {
    form.telefono = formatTelefono(newValue);
});

// Manejar la subida del CV
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Actualizar el formulario con el archivo
        form.cv = file;
        
        // Crear vista previa del nombre del archivo
        previewCv.value = file.name;
    }
};

// Función para inicializar reCAPTCHA
const initRecaptcha = () => {
    if (!window.grecaptcha) {
        setTimeout(initRecaptcha, 100);
        return;
    }
    
    captchaLoaded.value = true;
    window.onRecaptchaVerify = (response) => {
        form['g-recaptcha-response'] = response;
    };
    
    window.onRecaptchaExpired = () => {
        form['g-recaptcha-response'] = null;
    };
    
    window.grecaptcha.render('recaptcha-container', {
        sitekey: '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI', // Esta es una clave de prueba
        callback: 'onRecaptchaVerify',
        'expired-callback': 'onRecaptchaExpired'
    });
};

// Cargar el script de reCAPTCHA cuando el componente se monte
onMounted(() => {
    if (!document.getElementById('recaptcha-script')) {
        const script = document.createElement('script');
        script.id = 'recaptcha-script';
        script.src = 'https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
        
        // Definir la función de carga en el contexto global
        window.onRecaptchaLoad = initRecaptcha;
    } else {
        initRecaptcha();
    }
});

const submit = () => {
    form.post(route('registro.aspirante.store'), {
        preserveScroll: true,
        onError: (errors) => {
            if (errors['g-recaptcha-response']) {
                // Si hay un error con el captcha, reiniciarlo
                window.grecaptcha?.reset();
            }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registro de Aspirante" />
        

        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">Registro de Aspirante</h1>
                    <p class="text-gray-300 text-sm">Banco Central de Reserva de El Salvador</p>
                </div>
            </div>

            <!-- Register Card -->
            <div class="container mx-auto px-4 mb-8">
                
                <div class="max-w-4xl mx-auto">
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
                            

                            <!-- -->
                            
                            <div class="text-center space-y-2 mb-8">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Crear Cuenta</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Complete el formulario para registrarse como aspirante</p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Datos de la cuenta -->
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Información de la cuenta</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-1">
                                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Nombre completo *
                                            </label>
                                            <input
                                                id="name"
                                                type="text"
                                                v-model="form.name"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                required
                                                autofocus
                                                autocomplete="name"
                                            />
                                            <InputError class="mt-1" :message="form.errors.name" />
                                        </div>

                                        <div class="space-y-1">
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Correo electrónico *
                                            </label>
                                            <input
                                                id="email"
                                                type="email"
                                                v-model="form.email"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                placeholder="correo@ejemplo.com"
                                                required
                                                autocomplete="username"
                                            />
                                            <InputError class="mt-1" :message="form.errors.email" />
                                        </div>

                                        <div class="space-y-1">
                                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Contraseña *
                                            </label>
                                            <input
                                                id="password"
                                                type="password"
                                                v-model="form.password"
                                                @focus="passwordFocused = true"
                                                @blur="passwordFocused = form.password.length > 0"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
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
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Confirmar contraseña *
                                            </label>
                                            <input
                                                id="password_confirmation"
                                                type="password"
                                                v-model="form.password_confirmation"
                                                @focus="passwordConfirmationFocused = true"
                                                @blur="passwordConfirmationFocused = form.password_confirmation.length > 0"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                required
                                                autocomplete="new-password"
                                            />
                                            <InputError class="mt-1" :message="form.errors.password_confirmation" />
                                            
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
                                    </div>
                                </div>

                                <!-- Datos personales -->
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Información personal</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-1">
                                            <label for="dui_aspirante" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                DUI (sin guiones) *
                                            </label>
                                            <input
                                                id="dui_aspirante"
                                                type="text"
                                                v-model="form.dui_aspirante"
                                                @input="form.dui_aspirante = formatDui(form.dui_aspirante)"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                placeholder="00000000-0"
                                                maxlength="10"
                                                required
                                            />
                                            <InputError class="mt-1" :message="form.errors.dui_aspirante" />
                                        </div>

                                        <div class="space-y-1">
                                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Teléfono *
                                            </label>
                                            <input
                                                id="telefono"
                                                type="tel"
                                                v-model="form.telefono"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                placeholder="0000-0000"
                                                maxlength="9"
                                                required
                                            />
                                            <InputError class="mt-1" :message="form.errors.telefono" />
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Género *
                                            </label>
                                            <div class="mt-2 flex space-x-6">
                                                <div class="flex items-center">
                                                    <input
                                                        id="genero-m"
                                                        type="radio"
                                                        v-model="form.genero"
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
                                                        v-model="form.genero"
                                                        value="F"
                                                        class="h-4 w-4 text-blue-600 dark:text-blue-400 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-400"
                                                    />
                                                    <label for="genero-f" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Femenino
                                                    </label>
                                                </div>
                                            </div>
                                            <InputError class="mt-1" :message="form.errors.genero" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Formación académica -->
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Formación académica</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-1">
                                            <label for="id_nivel_academico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Nivel académico *
                                            </label>
                                            <select
                                                id="id_nivel_academico"
                                                v-model="form.id_nivel_academico"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                required
                                            >
                                                <option value="">Seleccione su nivel académico</option>
                                                <option v-for="nivel in nivelesAcademicos" :key="nivel.id" :value="nivel.id">
                                                    {{ nivel.nombre }}
                                                </option>
                                            </select>
                                            <InputError class="mt-1" :message="form.errors.id_nivel_academico" />
                                        </div>

                                        <div class="space-y-1">
                                            <label for="id_estado_academico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Estado académico *
                                            </label>
                                            <select
                                                id="id_estado_academico"
                                                v-model="form.id_estado_academico"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                required
                                            >
                                                <option value="">Seleccione su estado académico</option>
                                                <option v-for="estado in estadosAcademicos" :key="estado.id" :value="estado.id">
                                                    {{ estado.nombre }}
                                                </option>
                                            </select>
                                            <InputError class="mt-1" :message="form.errors.id_estado_academico" />
                                        </div>

                                        <div class="space-y-1">
                                            <label for="carrera" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Carrera *
                                            </label>
                                            <input
                                                id="carrera"
                                                type="text"
                                                v-model="form.carrera"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                placeholder="Ej: Ingeniería en Sistemas, Licenciatura en Contabilidad"
                                                required
                                            />
                                            <InputError class="mt-1" :message="form.errors.carrera" />
                                        </div>

                                        <div class="space-y-1" v-if="requiereEspecialidad">
                                            <label for="especialidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Especialidad *
                                            </label>
                                            <input
                                                id="especialidad"
                                                type="text"
                                                v-model="form.especialidad"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                placeholder="Ej: Maestría en Admón. de Empresas, Doctorado en Finanzas, etc"
                                                :required="requiereEspecialidad"
                                            />
                                            <InputError class="mt-1" :message="form.errors.especialidad" />
                                        </div>

                                        <div class="space-y-1">
                                            <label for="id_experiencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Nivel de experiencia *
                                            </label>
                                            <select
                                                id="id_experiencia"
                                                v-model="form.id_experiencia"
                                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:text-white transition"
                                                required
                                            >
                                                <option value="">Seleccione su nivel de experiencia</option>
                                                <option v-for="experiencia in nivelesExperiencia" :key="experiencia.id" :value="experiencia.id">
                                                    {{ experiencia.nombre }}
                                                </option>
                                            </select>
                                            <InputError class="mt-1" :message="form.errors.id_experiencia" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Curriculum Vitae -->
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Curriculum Vitae</h3>
                                    
                                    <div class="space-y-2">
                                        <label for="cv" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Suba su CV en formato PDF *
                                        </label>
                                        <div class="flex items-center space-x-3">
                                            <label 
                                                class="flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                                            >
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Seleccionar archivo</span>
                                                <input 
                                                    type="file" 
                                                    id="cv" 
                                                    class="hidden" 
                                                    accept=".pdf" 
                                                    @change="handleFileUpload"
                                                    required
                                                />
                                            </label>
                                            <span 
                                                v-if="previewCv" 
                                                class="text-sm text-gray-600 dark:text-gray-400 truncate max-w-xs"
                                            >
                                                {{ previewCv }}
                                            </span>
                                            <span 
                                                v-else 
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Ningún archivo seleccionado
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Formato permitido: PDF. Tamaño máximo: 5MB.
                                        </p>
                                        <InputError class="mt-1" :message="form.errors.cv" />
                                    </div>
                                </div>

                                <!-- Captcha -->
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Verificación de seguridad</h3>
                                    
                                    <div class="space-y-2">
                                        <!-- reCAPTCHA container -->
                                        <div 
                                            id="recaptcha-container" 
                                            class="flex justify-center"
                                            data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"
                                        ></div>
                                        <InputError class="mt-1" :message="form.errors['g-recaptcha-response']" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <Link
                                        :href="route('login')"
                                        class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition"
                                    >
                                        ¿Ya tiene una cuenta?
                                    </Link>

                                    <button
                                        type="submit"
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50 dark:focus:ring-offset-gray-800"
                                        :disabled="form.processing || (!passwordValidation.isValid && form.password.length > 0)"
                                    >
                                        {{ form.processing ? 'Registrando...' : 'Registrarse' }}
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