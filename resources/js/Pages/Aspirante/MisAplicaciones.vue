<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';

const props = defineProps({
    aplicaciones: Array
});

const page = usePage();

// Estado para filtros y búsqueda
const searchQuery = ref('');
const filteredAplicaciones = computed(() => {
    if (!searchQuery.value) return props.aplicaciones;
    
    const query = searchQuery.value.toLowerCase();
    return props.aplicaciones.filter(aplicacion => 
        aplicacion.plaza.titulo.toLowerCase().includes(query)
    );
});

// Para volver atrás en el historial
const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // Fallback a la ruta de bienvenida
        window.location.href = route('welcome');
    }
};

// Formatear fecha
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Obtener clase CSS para los estados
const getEstadoClass = (estadoId) => {
    switch(estadoId) {
        case 1: // Aprobado
            return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
        case 2: // Rechazado
            return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400';
        case 3: // Cumple
            return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
        case 4: // No cumple
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200';
        case 5: // Pendiente
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Mis Aplicaciones" />
    <GuestLayout>
        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">Mis Aplicaciones</h1>
                    <p class="text-gray-300 text-sm">Historial de tus postulaciones a oportunidades de empleo</p>
                </div>
            </div>

            <!-- Mensajes de éxito y error -->
            <FlashMessages />

            <!-- Contenido Principal -->
            <div class="container mx-auto px-4 mb-8">
                <div class="max-w-5xl mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <!-- Header con botón de regreso y buscador -->
                        <div class="bg-gray-50 dark:bg-[#2c3340] p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-3">
                            <button 
                                @click="goBack"
                                class="flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                <span>Regresar</span>
                            </button>

                            <div class="relative w-full md:w-auto">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input 
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Buscar por título de plaza..."
                                    class="pl-10 pr-4 py-2 w-full md:w-64 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition"
                                />
                            </div>
                        </div>

                        <!-- Tabla de aplicaciones o mensaje de no data -->
                        <div v-if="aplicaciones && aplicaciones.length > 0" class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-[#2c3340]">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                                Plaza
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                                Fecha de Aplicación
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                                Estado
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-[#303844] dark:divide-gray-700">
                                        <tr v-for="aplicacion in filteredAplicaciones" :key="aplicacion.id_aplicacion" 
                                            class="hover:bg-gray-50 dark:hover:bg-[#2c3340]/40 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ aplicacion.plaza.titulo }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ aplicacion.plaza.seccion?.nombre }} - {{ aplicacion.plaza.categoria?.nombre }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ formatDate(aplicacion.fecha_aplicacion) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col space-y-2">
                                                    <!-- Estado Sistema -->
                                                    <span 
                                                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                        :class="getEstadoClass(aplicacion.id_estado_aplicacion)"
                                                    >
                                                        <span class="mr-1">Sistema:</span> {{ aplicacion.estado?.nombre }}
                                                    </span>
                                                    
                                                    <!-- Estado Admin -->
                                                    <span 
                                                        v-if="aplicacion.id_estado_admin_aplicacion"
                                                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                        :class="getEstadoClass(aplicacion.id_estado_admin_aplicacion)"
                                                    >
                                                        <span class="mr-1">Admin:</span> {{ aplicacion.estadoAdmin?.nombre }}
                                                    </span>
                                                    <span 
                                                        v-else
                                                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                                    >
                                                        <span class="mr-1">Admin:</span> Pendiente
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <Link 
                                                    :href="route('plazas.show.public', aplicacion.id_plaza)" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-[#111e60]/10 text-[#111e60] hover:bg-[#111e60]/20 rounded-md text-xs font-medium dark:bg-[#363d4d]/50 dark:text-blue-200 dark:hover:bg-[#363d4d]/70 transition duration-150"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver Plaza
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Cuando no hay aplicaciones -->
                        <div v-else class="p-6">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300 mb-2">No tienes aplicaciones</h3>
                                <p class="text-gray-500 dark:text-gray-400 max-w-md mb-6">Aún no has aplicado a ninguna plaza de trabajo. Explora las oportunidades disponibles y aplica a las que te interesen.</p>
                                
                                <Link 
                                    :href="route('welcome')" 
                                    class="px-6 py-3 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] inline-flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Explorar Plazas Disponibles
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Efectos hover y focus mejorados */
input:focus {
    border-color: #111e60;
    box-shadow: 0 0 0 3px rgba(17, 30, 96, 0.1);
}

.dark input:focus {
    border-color: #363d4d;
    box-shadow: 0 0 0 3px rgba(54, 61, 77, 0.2);
}

/* Animaciones para interacción del usuario */
a, button {
    transition: all 0.2s ease;
}

/* Animación para el filtrado */
button:active, a:active {
    transform: scale(0.97);
}

/* Transiciones suaves para cambios de estado en la tabla */
tr, td, th {
    transition: background-color 0.2s ease, transform 0.1s ease;
}

/* Efectos hover para filas de tabla */
tr:hover {
    background-color: rgba(243, 244, 246, 0.5);
}

.dark tr:hover {
    background-color: rgba(44, 51, 64, 0.4);
}

/* Efecto de escala para elementos clickeables */
button:hover, a:hover {
    transform: translateY(-1px);
}

button:active, a:active {
    transform: translateY(0);
}
</style>