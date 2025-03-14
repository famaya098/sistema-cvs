<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import CreateEdit from './CreateEdit.vue';
import debounce from 'lodash/debounce';

const showModal = ref(false);
const isEditMode = ref(false);
const selectedPlaza = ref(null);
const showFilters = ref(true);

const props = defineProps({
    plazas: Object,
    secciones: Array,
    categorias: Array,
    filters: Object,
    nivelesAcademicos: Array,  
    estadosAcademicos: Array,
    nivelesExperiencia: Array
});

// Filtros
const filterForm = ref({
    search: props.filters?.search || '',
    categoria: props.filters?.categoria || '',
    seccion: props.filters?.seccion || '',
    estado: props.filters?.estado || '',
});

const openCreateModal = () => {
    isEditMode.value = false;
    selectedPlaza.value = null;
    showModal.value = true;
};

const openEditModal = (plaza) => {
    isEditMode.value = true;
    selectedPlaza.value = plaza;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    router.reload({ only: ['plazas'] });
};

// Función para aplicar filtros
const applyFilters = debounce(() => {
    router.get(
        route('plazas.index'),
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
        categoria: '',
        seccion: '',
        estado: '',
    };
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};
</script>

<template>
    <Head title="Gestión de Plazas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-[#111e60] dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Gestión de Plazas
                </h2>
                <button 
                    class="btn bg-[#111e60] text-white hover:bg-[#0b1535] dark:bg-[#363d4d] dark:hover:bg-[#2c3340] flex items-center gap-2 px-4 transition duration-200" 
                    @click="openCreateModal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Plaza
                </button>
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
                                        placeholder="Buscar plazas por título..."
                                        class="input input-bordered w-full pl-10 bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200"
                                    />
                                </div>

                                <!-- Filtros en grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Categoría</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.categoria"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todas las categorías</option>
                                                <option 
                                                    v-for="categoria in categorias" 
                                                    :key="categoria.id_categoria"
                                                    :value="categoria.id_categoria"
                                                >
                                                    {{ categoria.nombre }}
                                                </option>
                                            </select>
                                            
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Sección</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.seccion"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todas las secciones</option>
                                                <option 
                                                    v-for="seccion in secciones" 
                                                    :key="seccion.id_seccion"
                                                    :value="seccion.id_seccion"
                                                >
                                                    {{ seccion.nombre }}
                                                </option>
                                            </select>
                                            
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Estado</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.estado"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los estados</option>
                                                <option value="1">Publicado</option>
                                                <option value="0">No Publicado</option>
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
                                        Título
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Sección / Categoría
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Fechas
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-[#303844] dark:divide-gray-700">
                                <tr v-for="(plaza, index) in plazas.data" :key="plaza.id_plaza" 
                                    class="hover:bg-gray-50 dark:hover:bg-[#2c3340]/40 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ plazas.from + index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ plaza.titulo }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ plaza.alias_autor || "Sin asignar" }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ plaza.accesos }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ plaza.seccion?.nombre }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ plaza.categoria?.nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200': plaza.publicado,
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200': !plaza.publicado
                                            }"
                                        >
                                            <svg v-if="plaza.publicado" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ plaza.publicado ? 'Publicado' : 'No Publicado' }}
                                        </span>
                                        <!-- <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            <span 
                                                class="px-2 py-0.5 rounded-full"
                                                :class="{
                                                    'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': plaza.estado,
                                                    'bg-gray-50 text-gray-500 dark:bg-gray-700/50 dark:text-gray-400': !plaza.estado
                                                }"
                                            >
                                                {{ plaza.estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div> -->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col space-y-2">
                                            <div class="text-xs flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="font-medium text-gray-600 dark:text-gray-300">Creación:</span> 
                                                <span class="ml-1">{{ new Date(plaza.created_at).toLocaleDateString() }}</span>
                                            </div>
                                            <div class="text-xs flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="font-medium text-gray-600 dark:text-gray-300">Publicación:</span> 
                                                <span class="ml-1">{{ new Date(plaza.fecha_inicio_publicacion).toLocaleDateString() }}</span>
                                            </div>
                                            <div class="text-xs flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="font-medium text-gray-600 dark:text-gray-300">Expiración:</span> 
                                                <span class="ml-1">{{ new Date(plaza.fecha_fin_publicacion).toLocaleDateString() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a 
                                                :href="route('plazas.show.public', plaza.id_plaza)" 
                                                target="_blank"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md text-xs font-medium dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-800/50 transition duration-150"
                                                title="Ver plaza publicada"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver
                                            </a>
                                            <button 
                                                @click="openEditModal(plaza)"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-[#111e60]/10 text-[#111e60] hover:bg-[#111e60]/20 rounded-md text-xs font-medium dark:bg-[#363d4d]/50 dark:text-blue-200 dark:hover:bg-[#363d4d]/70 transition duration-150"
                                                title="Editar plaza"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Mensaje para cuando no hay resultados -->
                                <tr v-if="plazas.data && plazas.data.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-lg font-medium">No se encontraron plazas</p>
                                            <p class="mt-1">Intenta cambiar tus filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación mejorada -->
                    <div v-if="plazas.links && plazas.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Información sobre los resultados -->
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Mostrando {{ plazas.from }} a {{ plazas.to }} de {{ plazas.total }} resultados
                            </div>
                            
                            <!-- Enlaces de paginación -->
                            <div class="flex flex-wrap items-center gap-1">
                                <Link
                                    v-for="(link, i) in plazas.links"
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

                    <!-- Estado de carga o vacío mejorado -->
                    <div v-if="!plazas.data || plazas.data.length === 0 && !plazas.links" class="flex flex-col items-center justify-center py-16">
                        <div class="animate-pulse flex flex-col items-center justify-center">
                            <div class="rounded-full bg-gray-200 dark:bg-gray-700 h-16 w-16 mb-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg>
                            </div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-48 mb-3"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-64"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Crear/Editar -->
            <CreateEdit 
        v-if="showModal"
        :secciones="secciones" 
        :categorias="categorias"
        :niveles-academicos="nivelesAcademicos"
        :estados-academicos="estadosAcademicos"
        :niveles-experiencia="nivelesExperiencia"
        :plaza="selectedPlaza"
        :isEditMode="isEditMode"
        @close="closeModal"
        @saved="closeModal"
    />
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