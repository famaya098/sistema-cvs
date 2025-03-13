<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';  
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref, onMounted } from 'vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import axios from 'axios';

const props = defineProps({
    plaza: Object,
});

const page = usePage();

// Estado para la aplicación
const aplicado = ref(false);
const aplicacionInfo = ref(null);
const aplicacionCargando = ref(false);

// Verificar si el usuario ya aplicó a esta plaza
const verificarAplicacion = async () => {
    if (!props.plaza || !props.plaza.id_plaza) return;
    
    // Solo verificar si el usuario está autenticado
    if (!page.props.auth.user) return;
    
    aplicacionCargando.value = true;
    
    try {
        const response = await axios.get(route('aplicaciones.verificar', props.plaza.id_plaza));
        console.log("Respuesta verificación:", response.data);
        aplicado.value = response.data.aplicado;
        aplicacionInfo.value = response.data.aplicacion;
    } catch (error) {
        console.error("Error al verificar aplicación:", error);
    } finally {
        aplicacionCargando.value = false;
    }
};

// Aplicar a la plaza
const aplicarAPlaza = () => {
    if (aplicacionCargando.value) return;
    
    window.location.href = route('aplicaciones.aplicar', props.plaza.id_plaza);
};

onMounted(() => {
    // Verificar aplicación cuando se carga el componente
    verificarAplicacion();
});



// Estado para el botón de compartir
const shareOptionsVisible = ref(false);
const copied = ref(false);

// Manejar compartir
const toggleShareOptions = () => {
    shareOptionsVisible.value = !shareOptionsVisible.value;
};

const copyToClipboard = () => {
    navigator.clipboard.writeText(window.location.href).then(() => {
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    });
};


const shareOnSocial = (platform) => {
    let shareUrl = '';
    const currentUrl = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(props.plaza.titulo);
    
    if (platform === 'facebook') {
        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
    } else if (platform === 'twitter') {
        shareUrl = `https://twitter.com/intent/tweet?url=${currentUrl}&text=${title}`;
    } else if (platform === 'linkedin') {
        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${currentUrl}`;
    } else if (platform === 'whatsapp') {
        shareUrl = `https://api.whatsapp.com/send?text=${title}%20${currentUrl}`;
    }
    
    window.open(shareUrl, '_blank');
};
</script>

<template>
    <GuestLayout>
        <Head :title="plaza.titulo" />
        <FlashMessages />

        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">{{ plaza.titulo }}</h1>
                    <p class="text-gray-300 text-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        {{ plaza.seccion.nombre }} - {{ plaza.categoria.nombre }}
                    </p>
                </div>
            </div>

            <!-- Contenido de la Plaza -->
            <div class="container mx-auto px-4 mb-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <!-- Barra de información y acciones -->
                        <div class="bg-gray-50 dark:bg-[#2c3340] p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-3">
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-300 flex-wrap justify-center md:justify-start">
                                <!-- Icono de retorno-->

                            <Link
                                    :href="route('welcome')"
                                    class=""
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                            </Link>

                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#111e60] dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Disponible hasta: {{ new Date(plaza.fecha_fin_publicacion).toLocaleDateString() }}
                                </span>
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#111e60] dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ plaza.accesos }} visitas
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <button 
                                        @click="toggleShareOptions" 
                                        class="px-3 py-1.5 text-xs font-medium rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                        Compartir
                                    </button>
                                    <div v-if="shareOptionsVisible" class="absolute right-0 mt-2 bg-white dark:bg-[#303844] shadow-lg rounded-lg overflow-hidden z-10 border dark:border-gray-700 w-48">
                                        <button 
                                            @click="copyToClipboard" 
                                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                            {{ copied ? 'Copiado!' : 'Copiar enlace' }}
                                        </button>
                                        <button 
                                            @click="shareOnSocial('whatsapp')" 
                                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center"
                                        >
                                            <svg class="h-4 w-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.498 14.382c-.301-.15-1.767-.867-2.04-.966-.273-.101-.473-.15-.673.15-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.77-1.66-2.07-.174-.3-.019-.465.13-.615.136-.135.301-.345.451-.523.146-.181.194-.301.297-.496.1-.21.049-.375-.025-.524-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51-.172-.015-.371-.015-.571-.015-.2 0-.523.074-.797.359-.273.3-1.045 1.02-1.045 2.475s1.07 2.865 1.219 3.075c.149.195 2.105 3.195 5.1 4.485.714.3 1.27.48 1.704.629.714.227 1.365.195 1.88.121.574-.091 1.767-.721 2.016-1.426.255-.705.255-1.29.18-1.425-.074-.135-.27-.21-.57-.345m-5.446 7.443h-.016c-1.77 0-3.524-.48-5.055-1.38l-.36-.214-3.75.975 1.005-3.645-.239-.375a9.869 9.869 0 01-1.516-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                            </svg>
                                            WhatsApp
                                        </button>
                                        <button 
                                            @click="shareOnSocial('facebook')" 
                                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center"
                                        >
                                            <svg class="h-4 w-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z" />
                                            </svg>
                                            Facebook
                                        </button>
                                        <button @click="shareOnSocial('twitter')" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="h-4 w-4 mr-2 text-black dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                        X
                                        </button>
                                        <!-- <button 
                                            @click="shareOnSocial('linkedin')" 
                                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center"
                                        >
                                            <svg class="h-4 w-4 mr-2 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19.7 3H4.3A1.3 1.3 0 003 4.3v15.4A1.3 1.3 0 004.3 21h15.4a1.3 1.3 0 001.3-1.3V4.3A1.3 1.3 0 0019.7 3zM8.339 18.338H5.667v-8.59h2.672v8.59zM7.003 8.574a1.548 1.548 0 11-.002-3.096 1.548 1.548 0 01.002 3.096zm11.335 9.764h-2.669V14.16c0-.996-.018-2.277-1.388-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248h-2.667v-8.59h2.56v1.174h.037c.355-.675 1.227-1.387 2.524-1.387 2.704 0 3.203 1.778 3.203 4.092v4.71z" />
                                            </svg>
                                            LinkedIn
                                        </button> -->
                                        
                                    </div>
                                </div>
                                

                            </div>
                        </div>

                        <div class="p-8">
                            <!-- Contenido HTML de la plaza con estilos mejorados -->
                            <div 
                                v-html="plaza.contenido_html" 
                                class="content-html prose max-w-none dark:prose-invert prose-headings:text-[#111e60] dark:prose-headings:text-gray-200 prose-img:inline-block prose-img:align-middle"
                            ></div>

                            <!-- Botón de aplicar (solo visible para usuarios autenticados) -->
                            <div v-if="page.props.auth.user" class="mt-10">
                                <div class="bg-gray-50 dark:bg-[#2c3340]/50 p-6 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                                    <div v-if="aplicacionCargando" class="flex justify-center">
                                        <svg class="animate-spin h-8 w-8 text-[#363d4d]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <div v-else-if="aplicado">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">¡Ya haz aplicado a esta plaza!</h3>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4">Su aplicación ha sido registrada correctamente.</p>
                                        <div class="inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Aplicación enviada
                                        </div>
                                    </div>
                                    <div v-else>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">¿Te interesa esta oportunidad de empleo?</h3>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4">Completa tu aplicación ahora</p>
                                        <Link
                                            :href="route('aplicaciones.aplicar', plaza.id_plaza)"
                                            method="post"
                                            as="button"
                                            class="px-6 py-3 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#0b1535] dark:bg-[#363d4d] dark:hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] inline-flex items-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Aplicar a esta plaza
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="mt-10">
                                <div class="bg-gray-50 dark:bg-[#2c3340]/50 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white text-center mb-3">¿Te interesa esta oportunidad de empleo?</h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-center mb-4">
                                        Para aplicar a esta plaza, necesitas iniciar sesión o crear una cuenta
                                    </p>
                                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                                        <Link
                                            :href="route('login')"
                                            class="px-6 py-3 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#0b1535] dark:bg-[#363d4d] dark:hover:bg-[#2c3340] rounded-lg transition flex items-center justify-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Iniciar Sesión
                                        </Link>
                                        <Link
                                            :href="route('registro.aspirante')"
                                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 rounded-lg transition flex items-center justify-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                            Registrarse
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección de Preguntas Frecuentes y Contacto -->
                            <!-- <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#111e60] dark:text-gray-200 mb-4 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Preguntas Frecuentes
                                        </h3>
                                        <div class="space-y-3 text-sm">
                                            <div class="bg-white dark:bg-[#303844] rounded-lg p-3 shadow-sm">
                                                <p class="font-medium text-[#111e60] dark:text-gray-200">¿Cómo es el proceso de selección?</p>
                                                <p class="text-gray-600 dark:text-gray-400 mt-1">El proceso incluye evaluación de CV, entrevistas y pruebas técnicas según el puesto.</p>
                                            </div>
                                            <div class="bg-white dark:bg-[#303844] rounded-lg p-3 shadow-sm">
                                                <p class="font-medium text-[#111e60] dark:text-gray-200">¿Cuánto tiempo dura el proceso?</p>
                                                <p class="text-gray-600 dark:text-gray-400 mt-1">El proceso de selección puede durar entre 3 y 4 semanas dependiendo de la posición.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#111e60] dark:text-gray-200 mb-4 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            Contacto
                                        </h3>
                                        <div class="space-y-3 text-sm">
                                            <p class="text-gray-600 dark:text-gray-400">
                                                Si tiene dudas sobre esta plaza o el proceso de aplicación, puede contactarnos:
                                            </p>
                                            <div class="flex items-start space-x-2 text-gray-600 dark:text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#111e60] dark:text-blue-300 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium">Correo electrónico:</p>
                                                    <a href="mailto:rrhh@bcr.gob.sv" class="text-blue-600 dark:text-blue-400 hover:underline">rrhh@bcr.gob.sv</a>
                                                </div>
                                            </div>
                                            <div class="flex items-start space-x-2 text-gray-600 dark:text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#111e60] dark:text-blue-300 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <div>
                                                    <p class="font-medium">Teléfono:</p>
                                                    <a href="tel:+50322814444" class="text-blue-600 dark:text-blue-400 hover:underline">+503 2281-4444</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
/* Estilos para el contenido del editor Quill */
.content-html {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1.05rem;
    line-height: 1.7;
    color: #374151;
}

.dark .content-html {
    color: #e5e7eb;
}

/* Preservar estilos de alineación */
.ql-align-center {
    text-align: center;
}

.ql-align-right {
    text-align: right;
}

.ql-align-justify {
    text-align: justify;
}

/* Estilos de encabezados */
.content-html h1 {
    font-size: 2em;
    font-weight: bold;
    margin-top: 1.5em;
    margin-bottom: 0.8em;
    color: #111e60;
}

.dark .content-html h2 {
    color: #f3f4f6;
}

.content-html h3, .content-html h4, .content-html h5, .content-html h6 {
    margin-top: 1.2em;
    margin-bottom: 0.6em;
    font-weight: 600;
    color: #111e60;
    line-height: 1.3;
}

.dark .content-html h3, .dark .content-html h4, .dark .content-html h5, .dark .content-html h6 {
    color: #f3f4f6;
}

/* Estilos de listas */
.content-html ul, .content-html ol {
    padding-left: 2em;
    margin: 1em 0;
}

.content-html li {
    margin: 0.5em 0;
    position: relative;
}

.content-html ul li::marker {
    color: #111e60;
}

.dark .content-html ul li::marker {
    color: #93c5fd;
}

/* Espaciado y párrafos */
.content-html p {
    margin: 1em 0;
}

/* Imágenes */
.content-html img {
    display: inline-block;
    vertical-align: middle;
    max-width: 100%;
    height: auto;
    border-radius: 0.25rem;
}

/* Links */
.content-html a {
    color: #2563eb;
    text-decoration: none;
    transition: all 0.2s;
}

.content-html a:hover {
    text-decoration: underline;
    color: #1d4ed8;
}

.dark .content-html a {
    color: #60a5fa;
}

.dark .content-html a:hover {
    color: #93c5fd;
}

/* Estilos para texto con énfasis */
.content-html strong {
    font-weight: 700;
    color: #111827;
}

.dark .content-html strong {
    color: #f9fafb;
}

/* Estilos para el texto de color personalizado */
.content-html strong[style*="color: rgb(0, 0, 128)"] {
    color: #111e60 !important;
    font-weight: 700;
}

.dark .content-html strong[style*="color: rgb(0, 0, 128)"] {
    color: #93c5fd !important;
}

/* Estilos para bloques de contenido */
.content-html blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
    color: #6b7280;
    font-style: italic;
    margin: 1.5em 0;
}

.dark .content-html blockquote {
    border-left-color: #4b5563;
    color: #9ca3af;
}

/* Estilos responsivos para dispositivos móviles */
@media (max-width: 640px) {
    .content-html {
        font-size: 1rem;
    }
    
    .content-html h1 {
        font-size: 1.75em;
    }
    
    .content-html h2 {
        font-size: 1.5em;
    }
    
    .content-html h3 {
        font-size: 1.25em;
    }
    
    .content-html ul, .content-html ol {
        padding-left: 1.5em;
    }
}

/* Animaciones para elementos interactivos */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

.animate-pulse-custom {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
    line-height: 1.2;
}

.dark .content-html h1 {
    color: #f3f4f6;
}

.content-html h2 {
    font-size: 1.8em;
    font-weight: bold;
    margin-top: 1.5em;
    margin-bottom: 0.8em;
    color: #111e60;