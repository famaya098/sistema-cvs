<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const emit = defineEmits(['close', 'saved']);

const props = defineProps({
    secciones: Array,
    categorias: Array,
    plaza: Object,
    isEditMode: { type: Boolean, default: false },
    nivelesAcademicos: Array,
    estadosAcademicos: Array,
    nivelesExperiencia: Array,
});

const quillOptions = {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['clean']
        ]
    }
};

const form = useForm({
    id_plaza: '',
    titulo: '',
    id_seccion: '',
    id_categoria: '',
    publicado: true,
    estado: true,
    pagina_principal: false,
    fecha_inicio_publicacion: '',
    fecha_fin_publicacion: '',
    contenido_html: '',
    id_nivel_academico_requerido: '',  
    id_estado_academico_requerido: '', 
    id_experiencia_requerido: ''       
});

const loadPlaza = () => {
    if (props.isEditMode && props.plaza) {
        form.id_plaza = props.plaza.id_plaza;
        form.titulo = props.plaza.titulo;
        form.id_seccion = props.plaza.id_seccion;
        form.id_categoria = props.plaza.id_categoria;
        form.publicado = Boolean(props.plaza.publicado);
        form.estado = Boolean(props.plaza.estado);
        form.pagina_principal = Boolean(props.plaza.pagina_principal);
        form.fecha_inicio_publicacion = props.plaza.fecha_inicio_publicacion;
        form.fecha_fin_publicacion = props.plaza.fecha_fin_publicacion;
        form.contenido_html = props.plaza.contenido_html || '';
        
        // nuevos campos
        form.id_nivel_academico_requerido = props.plaza.id_nivel_academico_requerido || '';
        form.id_estado_academico_requerido = props.plaza.id_estado_academico_requerido || '';
        form.id_experiencia_requerido = props.plaza.id_experiencia_requerido || '';
    }
};

onMounted(() => {
    loadPlaza();
});

const submitForm = () => {
    if (props.isEditMode) {
        form.put(route('plazas.update', form.id_plaza), {
            onSuccess: () => {
                emit('saved');
                emit('close');
            },
        });
    } else {
        form.post(route('plazas.store'), {
            onSuccess: () => {
                emit('saved');
                emit('close');
            },
        });
    }
};
</script>

<template>
    <dialog :open="true" class="modal">
        <div class="modal-box w-11/12 max-w-5xl bg-white dark:bg-[#303844] relative shadow-2xl rounded-xl flex flex-col h-[85vh]">
            <!-- Header con título y botón de cierre (sticky) -->
            <div class="sticky top-0 flex justify-between items-center bg-white dark:bg-[#303844] z-10 py-4 px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-[#111e60] dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ isEditMode ? 'Editar Plaza' : 'Crear Nueva Plaza' }}
                </h3>
                <button 
                    @click="emit('close')" 
                    class="btn btn-circle btn-sm btn-ghost text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                    title="Cerrar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Contenido con scroll -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form @submit.prevent="submitForm">
                    <!-- Sección de información principal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5 mb-6">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4">Información Principal</h4>
                        
                        <!-- Título -->
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Título <span class="text-red-500">*</span></span>
                            </label>
                            <input 
                                type="text" 
                                v-model="form.titulo"
                                class="input input-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] transition duration-200" 
                                placeholder="Ingrese el título de la plaza"
                            />
                            <div v-if="form.errors.titulo" class="text-red-500 text-sm mt-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ form.errors.titulo }}
                            </div>
                        </div>

                        <!-- Sección y Categoría -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Sección <span class="text-red-500">*</span></span>
                                </label>
                                <div class="relative">
                                    <select 
                                        v-model="form.id_seccion"
                                        class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] pr-10 transition duration-200"
                                    >
                                        <option value="" disabled selected>Seleccione una sección</option>
                                        <option 
                                            v-for="seccion in secciones" 
                                            :key="seccion.id_seccion" 
                                            :value="seccion.id_seccion"
                                        >
                                            {{ seccion.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.id_seccion" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.id_seccion }}
                                </div>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Categoría <span class="text-red-500">*</span></span>
                                </label>
                                <div class="relative">
                                    <select 
                                        v-model="form.id_categoria"
                                        class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] pr-10 transition duration-200"
                                    >
                                        <option value="" disabled selected>Seleccione una categoría</option>
                                        <option 
                                            v-for="categoria in categorias" 
                                            :key="categoria.id_categoria" 
                                            :value="categoria.id_categoria"
                                        >
                                            {{ categoria.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.id_categoria" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.id_categoria }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de programación -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5 mb-6">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4">Programación y Visibilidad</h4>
                        
                        <!-- Fechas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">
                                        Fecha Inicio
                                        <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="date"
                                        v-model="form.fecha_inicio_publicacion"
                                        class="input input-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] transition duration-200"
                                    />
                                </div>
                                <div v-if="form.errors.fecha_inicio_publicacion" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.fecha_inicio_publicacion }}
                                </div>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">
                                        Fecha Fin
                                        <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="date"
                                        v-model="form.fecha_fin_publicacion"
                                        class="input input-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] transition duration-200"
                                        :min="form.fecha_inicio_publicacion"
                                    />
                                </div>
                                <div v-if="form.errors.fecha_fin_publicacion" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.fecha_fin_publicacion }}
                                </div>
                            </div>
                        </div>

                        <!-- Checkboxes de estado - Mejorados visualmente -->
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-2">
                            <div class="form-control bg-white dark:bg-[#303844] rounded-lg p-3 shadow-sm">
                                <label class="label cursor-pointer justify-start gap-3">
                                    <input 
                                        type="checkbox"
                                        v-model="form.publicado"
                                        class="checkbox checkbox-primary dark:border-gray-500"
                                    />
                                    <div>
                                        <span class="label-text font-medium text-[#111e60] dark:text-gray-200 block">Publicado</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Visible para usuarios</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>


                    <!-- Sección de Requisitos de la plaza -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5 mb-6">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4">Requisitos de la Plaza</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Nivel Académico Requerido -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Nivel académico requerido</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        v-model="form.id_nivel_academico_requerido"
                                        class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] pr-10 transition duration-200"
                                    >
                                        <option value="">Seleccione un nivel académico</option>
                                        <option 
                                            v-for="nivel in nivelesAcademicos" 
                                            :key="nivel.id" 
                                            :value="nivel.id"
                                        >
                                            {{ nivel.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.id_nivel_academico_requerido" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.id_nivel_academico_requerido }}
                                </div>
                            </div>

                            <!-- Estado Académico Requerido -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Estado académico requerido</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        v-model="form.id_estado_academico_requerido"
                                        class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] pr-10 transition duration-200"
                                    >
                                        <option value="">Seleccione un estado académico</option>
                                        <option 
                                            v-for="estado in estadosAcademicos" 
                                            :key="estado.id" 
                                            :value="estado.id"
                                        >
                                            {{ estado.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.id_estado_academico_requerido" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.id_estado_academico_requerido }}
                                </div>
                            </div>

                            <!-- Nivel de Experiencia Requerido -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Experiencia requerida</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        v-model="form.id_experiencia_requerido"
                                        class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-[#111e60]/20 focus:border-[#111e60] pr-10 transition duration-200"
                                    >
                                        <option value="">Seleccione un nivel de experiencia</option>
                                        <option 
                                            v-for="experiencia in nivelesExperiencia" 
                                            :key="experiencia.id" 
                                            :value="experiencia.id"
                                        >
                                            {{ experiencia.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.id_experiencia_requerido" class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ form.errors.id_experiencia_requerido }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Editor Quill -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5 mb-6">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4">Contenido</h4>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium text-[#111e60] dark:text-gray-300">Descripción detallada <span class="text-red-500">*</span></span>
                            </label>
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm overflow-hidden">
                                <QuillEditor
                                    v-model:content="form.contenido_html"
                                    :options="quillOptions"
                                    contentType="html"
                                    class="bg-white dark:bg-gray-700 quill-dark-mode"
                                    style="min-height: 250px;"
                                />
                            </div>
                            <div v-if="form.errors.contenido_html" class="text-red-500 text-sm mt-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ form.errors.contenido_html }}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer con botones de acción (sticky) -->
            <div class="sticky bottom-0 left-0 right-0 py-4 px-6 bg-white dark:bg-[#303844] border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3 z-10">
                <button 
                    type="button" 
                    class="btn flex items-center gap-1 bg-white text-[#111e60] hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 border border-gray-300 hover:border-[#111e60] dark:bg-[#2a303c] dark:text-gray-200 dark:hover:bg-[#212631] dark:border-gray-600 dark:hover:border-gray-500 dark:focus:ring-gray-700 transition-all duration-200 rounded-md font-medium px-4 py-2" 
                    @click="emit('close')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Cancelar</span>
                </button>
                <button 
                    @click="submitForm"
                    class="btn flex items-center gap-1 bg-[#111e60] text-white hover:bg-[#1d2d86] focus:ring-4 focus:ring-[#111e60]/30 border-none shadow-md dark:bg-[#3b4671] dark:hover:bg-[#4a5590] dark:focus:ring-[#3b4671]/40 transition-all duration-200 rounded-md font-medium px-4 py-2"
                    :disabled="form.processing"
                >
                    <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ isEditMode ? 'Actualizar Plaza' : 'Guardar Plaza' }}</span>
                </button>
            </div>
        </div>
    </dialog>
</template>

<style>
/* Variables de color primario - Basadas en el diseño del sistema */
:root {
    --color-primary: #111e60;
    --color-primary-focus: #0b1535;
    --color-primary-dark: #363d4d;
    --color-primary-dark-focus: #2c3340;
    --color-dark-bg: #303844;
    --color-dark-bg-secondary: #2c3340;
}

/* Estilos del modal */
.modal-box {
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

/* Estilo para el área con scroll */
.modal-box > .overflow-y-auto {
    flex: 1;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.modal-box > .overflow-y-auto::-webkit-scrollbar {
    width: 5px;
}

.modal-box > .overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.modal-box > .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 20px;
}

/* Estilos del editor Quill */
.ql-editor {
    min-height: 250px;
    background-color: white;
    transition: all 0.2s ease;
    line-height: 1.6;
}

.dark .ql-editor {
    background-color: #374151; /* gray-700 */
    color: white;
}

.ql-toolbar {
    background-color: white;
    border-color: #e5e5e5 !important;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.dark .ql-toolbar {
    background-color: #4B5563; /* gray-600 */
    border-color: #4B5563 !important;
}

.dark .ql-toolbar button,
.dark .ql-toolbar .ql-picker {
    color: white !important;
}

.dark .ql-toolbar button svg path {
    stroke: white !important;
}

.dark .ql-toolbar button svg polygon {
    stroke: white !important;
}

.dark .ql-toolbar .ql-picker-label {
    color: white !important;
}

.ql-container {
    border-color: #e5e5e5 !important;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.dark .ql-container {
    border-color: #4B5563 !important;
}

/* Estilos para inputs de tipo fecha */
input[type="date"] {
    padding-left: 2.5rem;
}

.dark input[type="date"] {
    color-scheme: dark;
}

/* Personalización del modal */
.modal {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
}

/* Efecto de transición para elementos interactivos */
.btn, .input, .select, .checkbox {
    transition: all 0.2s ease;
}

/* Mejoras a los campos del formulario */
.input, .select {
    border-color: #e2e8f0;
}

.dark .input, .dark .select {
    border-color: #4B5563;
}

.input:focus, .select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(17, 30, 96, 0.1);
}

.dark .input:focus, .dark .select:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Estilo para header y footer sticky */
.sticky {
    position: sticky;
    z-index: 10;
}

/* Estilos específicos del sistema */
.checkbox-primary {
    border-color: #111e60;
}

.dark .checkbox-primary {
    border-color: #6366f1;
}

.dark .ql-toolbar {
    background-color: #363d4d;
    border-color: #363d4d !important;
}

.dark .ql-editor {
    background-color: #303844;
    color: white;
}

.dark .ql-container {
    border-color: #363d4d !important;
}

/* Sombras sutiles para dar profundidad al header y footer sticky */
.modal-box > .sticky {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.modal-box > .sticky.bottom-0 {
    box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.1);
}

/* Asegurar que el header y footer tengan fondo sólido */
.modal-box > .sticky.top-0 {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

.modal-box > .sticky.bottom-0 {
    border-bottom-left-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
}
.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  pointer-events: none;
  filter: saturate(70%);
}

/* Personalización de foco */
.btn:focus {
  outline: none;
}

/* Transición suave para todos los botones */
.btn {
  position: relative;
  overflow: hidden;
}

/* Efecto de onda para botones primarios */
.btn:not(:disabled)::after {
  content: '';
  display: block;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
  background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
  background-repeat: no-repeat;
  background-position: 50%;
  transform: scale(10, 10);
  opacity: 0;
  transition: transform .3s, opacity .5s;
}

.btn:not(:disabled):active::after {
  transform: scale(0, 0);
  opacity: .3;
  transition: 0s;
}
</style>