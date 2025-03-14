<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    aplicaciones: Object,
    plazas: Array,
    estados: Array,
    nivelesAcademicos: Array,
    estadosAcademicos: Array,
    nivelesExperiencia: Array,
    filters: Object,
});

const page = usePage();

// Mostrar/ocultar secciones
const showFilters = ref(true);

// Modal de vista previa de CV
const showCVModal = ref(false);
const currentCVUrl = ref('');
const currentAspirante = ref(null);

// Modal de detalles del aspirante
const showProfileModal = ref(false);
const selectedAspirante = ref(null);

// Filtros
const filterForm = ref({
    search: props.filters?.search || '',
    plaza: props.filters?.plaza || '',
    estado: props.filters?.estado || '',
    estado_admin: props.filters?.estado_admin || '',
    nivel_academico: props.filters?.nivel_academico || '',
    nivel_experiencia: props.filters?.nivel_experiencia || '',
    estado_academico: props.filters?.estado_academico || '',
    sort_field: props.filters?.sort_field || 'fecha_aplicacion',
    sort_direction: props.filters?.sort_direction || 'desc',
});

// Función para aplicar filtros
const applyFilters = debounce(() => {
    router.get(
        route('admin.aplicaciones.index'),
        filterForm.value,
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}, 300);

// Observar cambios en los filtros
watch(filterForm, () => {
    applyFilters();
}, { deep: true });

// Limpiar filtros
const clearFilters = () => {
    filterForm.value = {
        search: '',
        plaza: '',
        estado: '',
        estado_admin: '',
        nivel_academico: '',
        nivel_experiencia: '',
        estado_academico: '',
        sort_field: 'fecha_aplicacion',
        sort_direction: 'desc',
    };
};

// Alternar filtros
const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

// Ordenar tabla
const sortBy = (field) => {
    if (filterForm.value.sort_field === field) {
        filterForm.value.sort_direction = filterForm.value.sort_direction === 'asc' ? 'desc' : 'asc';
    } else {
        filterForm.value.sort_field = field;
        filterForm.value.sort_direction = 'asc';
    }
};

// Actualizar estado administrativo
const updateEstadoAdmin = (aplicacionId, estadoId) => {
    router.patch(route('admin.aplicaciones.updateEstado', aplicacionId), {
        id_estado_admin_aplicacion: estadoId
    }, {
        preserveScroll: true
    });
};

// Abrir modal de vista previa de CV
const openCVPreview = (aplicacion) => {
    if (aplicacion && aplicacion.documento) {
        currentCVUrl.value = route('admin.aplicaciones.download.cv', aplicacion.id_aplicacion);
        currentAspirante.value = aplicacion.aspirante;
        showCVModal.value = true;
    }
};

// Abrir modal de detalles del aspirante
const openProfileDetails = (aspirante) => {
    selectedAspirante.value = aspirante;
    showProfileModal.value = true;
};
</script>

<template>
    <Head title="Gestión de Aplicaciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-[#111e60] dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Gestión de Aplicaciones
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-[#303844] shadow-md rounded-xl overflow-hidden">
                    <!-- Card de Filtros -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-[#111e60] dark:text-white flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filtros de Búsqueda
                                </h3>
                                <button 
                                    @click="toggleFilters"
                                    class="btn btn-sm btn-circle btn-ghost dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path v-if="showFilters" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <div v-show="showFilters" class="space-y-5">
                                <!-- Barra de búsqueda principal -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input 
                                        type="text"
                                        v-model="filterForm.search"
                                        placeholder="Buscar por nombre de aspirante..."
                                        class="input input-bordered w-full pl-10 bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200"
                                    />
                                </div>

                                <!-- Filtros en grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <!-- Plaza -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Plaza</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.plaza"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todas las plazas</option>
                                                <option 
                                                    v-for="plaza in plazas" 
                                                    :key="plaza.id_plaza"
                                                    :value="plaza.id_plaza"
                                                >
                                                    {{ plaza.titulo }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Estado Sistema -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Estado Sistema</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.estado"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los estados</option>
                                                <option 
                                                    v-for="estado in estados" 
                                                    :key="estado.id"
                                                    :value="estado.id"
                                                >
                                                    {{ estado.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Estado Admin -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Estado Admin</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.estado_admin"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los estados</option>
                                                <option value="null">Pendiente de revisión</option>
                                                <option 
                                                    v-for="estado in estados" 
                                                    :key="estado.id"
                                                    :value="estado.id"
                                                >
                                                    {{ estado.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Nivel Académico -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Nivel Académico</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.nivel_academico"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los niveles</option>
                                                <option 
                                                    v-for="nivel in nivelesAcademicos" 
                                                    :key="nivel.id"
                                                    :value="nivel.id"
                                                >
                                                    {{ nivel.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Estado Académico -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Estado Académico</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.estado_academico"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los estados</option>
                                                <option 
                                                    v-for="estado in estadosAcademicos" 
                                                    :key="estado.id"
                                                    :value="estado.id"
                                                >
                                                    {{ estado.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Nivel de Experiencia -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Experiencia</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.nivel_experiencia"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los niveles</option>
                                                <option 
                                                    v-for="nivel in nivelesExperiencia" 
                                                    :key="nivel.id"
                                                    :value="nivel.id"
                                                >
                                                    {{ nivel.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex justify-end">
                                    <button 
                                        @click="clearFilters"
                                        class="btn btn-sm btn-outline text-[#111e60] hover:bg-[#111e60]/10 hover:border-[#111e60] dark:text-gray-300 dark:hover:bg-gray-700 flex items-center gap-1 transition-all duration-200"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Limpiar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla con resultados -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-[#2c3340]">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300" style="width: 3%;">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        <button @click="sortBy('usuario_perfil.nombre_completo')" class="flex items-center">
                                            Aspirante
                                            <svg v-if="filterForm.sort_field === 'usuario_perfil.nombre_completo'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    :d="filterForm.sort_direction === 'asc' 
                                                        ? 'M5 15l7-7 7 7' 
                                                        : 'M19 9l-7 7-7-7'" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        <button @click="sortBy('plazas.titulo')" class="flex items-center">
                                            Plaza
                                            <svg v-if="filterForm.sort_field === 'plazas.titulo'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    :d="filterForm.sort_direction === 'asc' 
                                                        ? 'M5 15l7-7 7 7' 
                                                        : 'M19 9l-7 7-7-7'" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        <button @click="sortBy('fecha_aplicacion')" class="flex items-center">
                                            Fecha Aplicación
                                            <svg v-if="filterForm.sort_field === 'fecha_aplicacion'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    :d="filterForm.sort_direction === 'asc' 
                                                        ? 'M5 15l7-7 7 7' 
                                                        : 'M19 9l-7 7-7-7'" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Estado Sistema
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Estado Admin
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-[#303844] dark:divide-gray-700">
                                <tr v-for="(aplicacion, index) in aplicaciones.data" :key="aplicacion.id_aplicacion" 
                                    class="hover:bg-gray-50 dark:hover:bg-[#2c3340]/40 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ aplicaciones.from + index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ aplicacion.aspirante?.nombre_completo }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ aplicacion.aspirante?.email }}
                                        </div>
                                        <div class="text-xs flex items-center mt-1 space-x-2">
                                            <span class="badge badge-sm badge-ghost">{{ aplicacion.aspirante?.nivelAcademico?.nombre }}</span>
                                            <span class="badge badge-sm badge-ghost">{{ aplicacion.aspirante?.experiencia?.nombre }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ aplicacion.plaza?.titulo }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ new Date(aplicacion.fecha_aplicacion).toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200': aplicacion.id_estado_aplicacion === 3,
                                                'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200': aplicacion.id_estado_aplicacion === 4
                                            }"
                                        >
                                            {{ aplicacion.estado?.nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select 
                                            v-model="aplicacion.id_estado_admin_aplicacion"
                                            @change="updateEstadoAdmin(aplicacion.id_aplicacion, aplicacion.id_estado_admin_aplicacion)"
                                            class="select select-bordered select-sm w-full max-w-xs"
                                            :class="{
                                                'bg-green-50 border-green-500 text-green-800 dark:bg-green-900/30 dark:text-green-200': aplicacion.id_estado_admin_aplicacion === 1,
                                                'bg-red-50 border-red-500 text-red-800 dark:bg-red-900/30 dark:text-red-200': aplicacion.id_estado_admin_aplicacion === 2,
                                                'bg-gray-50 border-gray-300 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300': !aplicacion.id_estado_admin_aplicacion
                                            }"
                                        >
                                            <option value="">-- Seleccionar --</option>
                                            <option 
                                                v-for="estado in estados.filter(e => e.id <= 2)" 
                                                :key="estado.id" 
                                                :value="estado.id"
                                            >
                                                {{ estado.nombre }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            <!-- Ver perfil del aspirante -->
                                            <button 
                                                @click="openProfileDetails(aplicacion.aspirante)"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md text-xs font-medium dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-800/50 transition duration-150"
                                                title="Ver perfil del aspirante"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Perfil
                                            </button>
                                            
                                            <!-- Ver CV -->
                                            <button 
                                                @click="openCVPreview(aplicacion)"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-md text-xs font-medium dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-800/50 transition duration-150"
                                                title="Ver CV"
                                            >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Ver CV
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Mensaje para cuando no hay resultados -->
                                <tr v-if="aplicaciones.data && aplicaciones.data.length === 0">
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="text-lg font-medium">No se encontraron aplicaciones</p>
                                            <p class="mt-1">Intenta cambiar tus filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación mejorada -->
                    <div v-if="aplicaciones.links && aplicaciones.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Información sobre los resultados -->
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Mostrando {{ aplicaciones.from }} a {{ aplicaciones.to }} de {{ aplicaciones.total }} resultados
                            </div>
                            
                            <!-- Enlaces de paginación -->
                            <div class="flex flex-wrap items-center gap-1">
                                <Link
                                    v-for="(link, i) in aplicaciones.links"
                                    :key="i"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-1.5 rounded-md text-sm font-medium transition duration-150"
                                    :class="{
                                        'bg-[#111e60] text-white dark:bg-[#363d4d]': link.active,
                                        'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url,
                                        'text-gray-400 dark:text-gray-600 cursor-not-allowed': !link.url,
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Vista Previa de CV -->
        <div v-if="showCVModal" class="fixed inset-0 overflow-y-auto z-50">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Fondo oscuro -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showCVModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900 dark:opacity-80"></div>
                </div>

                <!-- Contenido del modal -->
                <div class="inline-block align-bottom bg-white dark:bg-[#303844] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                    <!-- Encabezado del modal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340] px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-[#111e60] dark:text-white">
                            CV de {{ currentAspirante?.nombre_completo }}
                        </h3>
                        <button @click="showCVModal = false" class="btn btn-sm btn-circle btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Cuerpo del modal con visor de PDF -->
                    <div class="p-0 bg-gray-100 dark:bg-gray-800 h-[70vh]">
                        <iframe 
                            :src="currentCVUrl" 
                            class="w-full h-full border-0"
                            frameborder="0"
                        ></iframe>
                    </div>
                    
                    <!-- Pie del modal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340] px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <a :href="currentCVUrl" download class="btn btn-sm bg-[#111e60] hover:bg-[#0c1547] text-white border-none mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Descargar
                        </a>
                        <button @click="showCVModal = false" class="btn btn-sm btn-outline">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Detalles del Perfil del Aspirante -->
        <div v-if="showProfileModal" class="fixed inset-0 overflow-y-auto z-50">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Fondo oscuro -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showProfileModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900 dark:opacity-80"></div>
                </div>

                <!-- Contenido del modal -->
                <div class="inline-block align-bottom bg-white dark:bg-[#303844] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <!-- Encabezado del modal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340] px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-[#111e60] dark:text-white">
                            Perfil de Aspirante
                        </h3>
                        <button @click="showProfileModal = false" class="btn btn-sm btn-circle btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Cuerpo del modal con la información del perfil -->
                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        <div v-if="selectedAspirante" class="space-y-6">
                            <!-- Información personal -->
                            <div class="bg-gray-50 dark:bg-[#2c3340]/50 p-4 rounded-lg">
                                <h4 class="font-medium text-[#111e60] dark:text-white text-lg mb-3">Información Personal</h4>
                                
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre completo</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.nombre_completo }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">DUI</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.dui_aspirante }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Correo electrónico</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.email }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.telefono }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Género</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.genero === 'M' ? 'Masculino' : 'Femenino' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Formación académica -->
                            <div class="bg-gray-50 dark:bg-[#2c3340]/50 p-4 rounded-lg">
                                <h4 class="font-medium text-[#111e60] dark:text-white text-lg mb-3">Formación Académica</h4>
                                
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nivel académico</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.nivelAcademico?.nombre }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado académico</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.estadoAcademico?.nombre }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Carrera</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.carrera }}</p>
                                        </div>
                                        
                                        <div v-if="selectedAspirante.especialidad">
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Especialidad</h5>
                                            <p class="text-gray-900 dark:text-white">{{ selectedAspirante.especialidad }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Experiencia -->
                            <div class="bg-gray-50 dark:bg-[#2c3340]/50 p-4 rounded-lg">
                                <h4 class="font-medium text-[#111e60] dark:text-white text-lg mb-3">Experiencia</h4>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nivel de experiencia</h5>
                                    <p class="text-gray-900 dark:text-white">{{ selectedAspirante.experiencia?.nombre }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pie del modal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340] px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button @click="showProfileModal = false" class="btn btn-sm btn-outline">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Efectos hover y focus mejorados */
input:focus, select:focus {
    border-color: #111e60;
    box-shadow: 0 0 0 3px rgba(17, 30, 96, 0.1);
}

.dark input:focus, .dark select:focus {
    border-color: #363d4d;
    box-shadow: 0 0 0 3px rgba(54, 61, 77, 0.2);
}

/* Animaciones para interacción del usuario */
.btn, a {
    transition: all 0.2s ease;
}

/* Animación para el filtrado */
.btn:active, a:active {
    transform: scale(0.97);
}

/* Sombra para botones floating */
.btn-floating {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
.btn:hover, a:hover {
    transform: translateY(-1px);
}

.btn:active, a:active {
    transform: translateY(0);
}
</style>