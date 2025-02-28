<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
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
                <h2 class="text-xl font-semibold leading-tight text-[#111e60] dark:text-white">
                    Gestión de Plazas
                </h2>
                <button 
                    class="btn btn-sm bg-[#1e1b4b] text-white hover:bg-[#2d2a5d] dark:bg-[#363d4d] dark:hover:bg-[#2c3340]" 
                    @click="openCreateModal"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Plaza
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-[#303844] shadow-sm sm:rounded-lg overflow-visible">
                    <!-- Card de Filtros -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-[#1e1b4b] dark:text-white">
                                    Filtros de Búsqueda
                                </h3>
                                <button 
                                    @click="toggleFilters"
                                    class="btn btn-sm btn-ghost dark:text-gray-300"
                                >
                                    <i :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas"></i>
                                </button>
                            </div>

                            <div v-show="showFilters" class="space-y-4">
                                <!-- Barra de búsqueda principal -->
                                <div class="flex-1">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </span>
                                        <input 
                                            type="text"
                                            v-model="filterForm.search"
                                            placeholder="Buscar plazas por título..."
                                            class="input input-bordered w-full pl-10 bg-white dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                        />
                                    </div>
                                </div>

                                <!-- Filtros en grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text text-[#1e1b4b] font-medium dark:text-gray-300">Categoría</span>
                                        </label>
                                        <select 
                                            v-model="filterForm.categoria"
                                            class="select select-bordered w-full bg-white dark:bg-gray-700 dark:text-white dark:border-gray-600"
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

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text text-[#1e1b4b] font-medium dark:text-gray-300">Sección</span>
                                        </label>
                                        <select 
                                            v-model="filterForm.seccion"
                                            class="select select-bordered w-full bg-white dark:bg-gray-700 dark:text-white dark:border-gray-600"
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

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text text-[#1e1b4b] font-medium dark:text-gray-300">Estado</span>
                                        </label>
                                        <select 
                                            v-model="filterForm.estado"
                                            class="select select-bordered w-full bg-white dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                        >
                                            <option value="">Todos los estados</option>
                                            <option value="1">Publicado</option>
                                            <option value="0">No Publicado</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex justify-end space-x-2">
                                    <button 
                                        @click="clearFilters"
                                        class="btn btn-sm btn-ghost dark:text-gray-300 dark:hover:bg-gray-700"
                                    >
                                        <i class="fas fa-times mr-2"></i>
                                        Limpiar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla con resultados -->
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="text-[#1e1b4b] font-semibold dark:text-gray-300">Título</th>
                                    <th class="text-[#1e1b4b] font-semibold dark:text-gray-300">Sección</th>
                                    <th class="text-[#1e1b4b] font-semibold dark:text-gray-300">Categoría</th>
                                    <th class="text-[#1e1b4b] font-semibold dark:text-gray-300">Estado</th>
                                    <th class="text-[#1e1b4b] font-semibold dark:text-gray-300">Fecha Creación</th>
                                    <th class="text-[#1e1b4b] font-semibold text-right dark:text-gray-300">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="plaza in plazas.data" :key="plaza.id_plaza" 
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="font-medium text-gray-900 dark:text-white">{{ plaza.titulo }}</td>
                                    <td class="text-gray-700 dark:text-gray-300">{{ plaza.seccion?.nombre }}</td>
                                    <td class="text-gray-700 dark:text-gray-300">{{ plaza.categoria?.nombre }}</td>
                                    <td>
                                        <span 
                                            class="px-3 py-1 rounded-full text-sm font-medium inline-flex items-center"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': plaza.publicado,
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': !plaza.publicado
                                            }"
                                        >
                                            <span class="w-2 h-2 rounded-full mr-2"
                                                :class="{
                                                    'bg-green-500': plaza.publicado,
                                                    'bg-yellow-500': !plaza.publicado
                                                }"
                                            ></span>
                                            {{ plaza.publicado ? 'Publicado' : 'No Publicado' }}
                                        </span>
                                    </td>
                                    <td class="text-gray-700 dark:text-gray-300">{{ new Date(plaza.created_at).toLocaleDateString() }}</td>
                                    <td class="text-right">
                                        <button 
                                            class="btn btn-sm bg-[#1e1b4b] text-white hover:bg-[#2d2a5d] dark:bg-[#363d4d] dark:hover:bg-[#2c3340]"
                                            @click="openEditModal(plaza)"
                                        >
                                            <i class="fas fa-edit mr-2"></i>
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación (si está disponible) -->
                    <div v-if="plazas.links" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        <!-- Aquí iría el componente de paginación -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Crear/Editar -->
        <CreateEdit 
            v-if="showModal"
            :secciones="secciones" 
            :categorias="categorias"
            :plaza="selectedPlaza"
            :isEditMode="isEditMode"
            @close="closeModal"
            @saved="closeModal"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.btn-ghost:hover {
    background-color: #f3f4f6;
    color: #1e1b4b;
}

.dark .btn-ghost:hover {
    background-color: #374151;
    color: #e5e7eb;
}

/* Transiciones suaves */
.transition-colors {
    transition: background-color 0.2s ease;
}
</style>