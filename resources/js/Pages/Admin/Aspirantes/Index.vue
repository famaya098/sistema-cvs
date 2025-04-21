<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import PerfilAspiranteModal from '../Aplicaciones/Modals/PerfilAspiranteModal.vue';
import VerCVModal from '../Aplicaciones/Modals/VerCVModal.vue';

const props = defineProps({
    aspirantes: Object,
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
    nivel_academico: props.filters?.nivel_academico || '',
    nivel_experiencia: props.filters?.nivel_experiencia || '',
    estado_academico: props.filters?.estado_academico || '',
    sort_field: props.filters?.sort_field || 'created_at',
    sort_direction: props.filters?.sort_direction || 'desc',
});

// Función para aplicar filtros
const applyFilters = debounce(() => {
    router.get(
        route('admin.aspirantes.index'),
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
        nivel_academico: '',
        nivel_experiencia: '',
        estado_academico: '',
        sort_field: 'created_at',
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

// Abrir modal de vista previa de CV
const openCVPreview = (aspirante) => {
    currentCVUrl.value = route('admin.aspirantes.cv', aspirante.id_aspirante);
    currentAspirante.value = aspirante;
    showCVModal.value = true;
};

// Abrir modal de detalles del aspirante
const openProfileDetails = (aspirante) => {
    selectedAspirante.value = aspirante;
    showProfileModal.value = true;
};

// Cerrar modales
const closeModals = () => {
    showCVModal.value = false;
    showProfileModal.value = false;
};
</script>

<template>
    <Head title="Gestión de Aspirantes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-[#111e60] dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Gestión de Aspirantes
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
                                        placeholder="Buscar por nombre, correo o DUI..."
                                        class="input input-bordered w-full pl-10 bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200"
                                    />
                                </div>

                                <!-- Filtros en grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
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

                                    <!-- Experiencia -->
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Nivel de Experiencia</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                v-model="filterForm.nivel_experiencia"
                                                class="select select-bordered w-full bg-white dark:bg-[#2c3340] dark:text-white border-gray-300 dark:border-gray-600 focus:border-[#111e60] focus:ring-2 focus:ring-[#111e60]/20 dark:focus:border-[#363d4d] dark:focus:ring-[#363d4d]/20 transition duration-200 pr-10"
                                            >
                                                <option value="">Todos los niveles</option>
                                                <option 
                                                    v-for="experiencia in nivelesExperiencia" 
                                                    :key="experiencia.id"
                                                    :value="experiencia.id"
                                                >
                                                    {{ experiencia.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex justify-end">
                                    <button 
                                        @click="clearFilters"
                                        class="btn btn-sm flex items-center gap-1 bg-transparent text-[#111e60] hover:bg-[#111e60]/10 border border-transparent hover:border-[#111e60]/30 dark:text-gray-300 dark:hover:bg-gray-700/30 dark:hover:border-gray-600 focus:ring-2 focus:ring-[#111e60]/20 dark:focus:ring-gray-600/20 transition-all duration-200 rounded px-3 py-1 text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Limpiar Filtros</span>
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
                                        <button @click="sortBy('nombre_completo')" class="flex items-center">
                                            Aspirante
                                            <svg v-if="filterForm.sort_field === 'nombre_completo'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    :d="filterForm.sort_direction === 'asc' 
                                                        ? 'M5 15l7-7 7 7' 
                                                        : 'M19 9l-7 7-7-7'" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Formación Académica
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        <button @click="sortBy('created_at')" class="flex items-center">
                                            Fecha Registro
                                            <svg v-if="filterForm.sort_field === 'created_at'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    :d="filterForm.sort_direction === 'asc' 
                                                        ? 'M5 15l7-7 7 7' 
                                                        : 'M19 9l-7 7-7-7'" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-[#111e60] uppercase tracking-wider dark:text-gray-300">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-[#303844] dark:divide-gray-700">
                                <tr v-for="(aspirante, index) in aspirantes.data" :key="aspirante.id_aspirante" 
                                    class="hover:bg-gray-50 dark:hover:bg-[#2c3340]/40 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ aspirantes.from + index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ aspirante.nombre_completo }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ aspirante.email }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">DUI: {{ aspirante.dui_aspirante }}</div>
                                    </td>
                                    <!-- Formación Académica -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-2 text-xs">
                                            <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 px-2 py-1 inline-flex items-center">
                                                <span class="text-blue-700 dark:text-blue-300 whitespace-nowrap">
                                                    {{ aspirante.nivel_academico?.nombre || 'Sin nivel académico' }}
                                                </span>
                                            </div>
                                            <div class="rounded-md bg-purple-50 dark:bg-purple-900/20 px-2 py-1 inline-flex items-center">
                                                <span class="text-purple-700 dark:text-purple-300 whitespace-nowrap">
                                                    {{ aspirante.estado_academico?.nombre || 'Sin estado académico' }}
                                                </span>
                                            </div>
                                            <div class="rounded-md bg-teal-50 dark:bg-teal-900/20 px-2 py-1 inline-flex items-center">
                                                <span class="text-teal-700 dark:text-teal-300 whitespace-nowrap">
                                                    {{ aspirante.experiencia?.nombre || 'Sin experiencia' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ new Date(aspirante.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button 
                                                @click="openProfileDetails(aspirante)"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md text-xs font-medium dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-800/50 transition duration-150"
                                                title="Ver perfil del aspirante"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Perfil
                                            </button>
                                            
                                            <button 
                                                @click="openCVPreview(aspirante)"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-md text-xs font-medium dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-800/50 transition duration-150"
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
                                <tr v-if="aspirantes.data && aspirantes.data.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-lg font-medium">No se encontraron aspirantes</p>
                                            <p class="mt-1">Intenta cambiar tus filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación mejorada -->
                    <div v-if="aspirantes.links && aspirantes.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Información sobre los resultados -->
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Mostrando {{ aspirantes.from }} a {{ aspirantes.to }} de {{ aspirantes.total }} resultados
                            </div>
                            
                            <!-- Enlaces de paginación -->
                            <div class="flex flex-wrap items-center gap-1">
                                <Link
                                    v-for="(link, i) in aspirantes.links"
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

        <!-- Componentes de modales -->
        <PerfilAspiranteModal 
            v-if="showProfileModal"
            :aspirante="selectedAspirante"
            @close="closeModals"
        />

        <VerCVModal 
            v-if="showCVModal"
            :url="currentCVUrl"
            :aspirante="currentAspirante"
            @close="closeModals"
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

/* Fix para select en estado admin */
.select-sm {
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
    height: auto;
    min-height: 2rem;
}
</style>