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
    publicado: false,
    estado: true,
    pagina_principal: false,
    fecha_inicio_publicacion: '',
    fecha_fin_publicacion: '',
    contenido_html: ''
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
        <div class="modal-box w-11/12 max-w-5xl bg-white dark:bg-[#303844]">
            <h3 class="text-lg font-bold mb-4 text-[#111e60] dark:text-white">{{ isEditMode ? 'Editar Plaza' : 'Crear Nueva Plaza' }}</h3>
            
            <form @submit.prevent="submitForm">
                <!-- Título -->
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text text-[#111e60] dark:text-gray-300">Título</span>
                    </label>
                    <input 
                        type="text" 
                        v-model="form.titulo"
                        class="input input-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700" 
                        placeholder="Ingrese el título de la plaza"
                        style="border-color: #e5e5e5;"
                    />
                    <div v-if="form.errors.titulo" class="text-error text-sm mt-1">
                        {{ form.errors.titulo }}
                    </div>
                </div>

                <!-- Sección y Categoría -->
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-[#111e60] dark:text-gray-300">Sección</span>
                        </label>
                        <select 
                            v-model="form.id_seccion"
                            class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700"
                            style="border-color: #e5e5e5;"
                        >
                            <option value="">Seleccione una sección</option>
                            <option 
                                v-for="seccion in secciones" 
                                :key="seccion.id_seccion" 
                                :value="seccion.id_seccion"
                            >
                                {{ seccion.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.id_seccion" class="text-error text-sm mt-1">
                            {{ form.errors.id_seccion }}
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-[#111e60] dark:text-gray-300">Categoría</span>
                        </label>
                        <select 
                            v-model="form.id_categoria"
                            class="select select-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700"
                            style="border-color: #e5e5e5;"
                        >
                            <option value="">Seleccione una categoría</option>
                            <option 
                                v-for="categoria in categorias" 
                                :key="categoria.id_categoria" 
                                :value="categoria.id_categoria"
                            >
                                {{ categoria.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.id_categoria" class="text-error text-sm mt-1">
                            {{ form.errors.id_categoria }}
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-[#111e60] dark:text-gray-300">Fecha Inicio</span>
                        </label>
                        <input 
                            type="date"
                            v-model="form.fecha_inicio_publicacion"
                            class="input input-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700"
                            style="border-color: #e5e5e5;"
                        />
                        <div v-if="form.errors.fecha_inicio_publicacion" class="text-error text-sm mt-1">
                            {{ form.errors.fecha_inicio_publicacion }}
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-[#111e60] dark:text-gray-300">Fecha Fin</span>
                        </label>
                        <input 
                            type="date"
                            v-model="form.fecha_fin_publicacion"
                            class="input input-bordered w-full text-[#111e60] dark:text-white bg-white dark:bg-gray-700"
                            style="border-color: #e5e5e5;"
                            :min="form.fecha_inicio_publicacion"
                        />
                        <div v-if="form.errors.fecha_fin_publicacion" class="text-error text-sm mt-1">
                            {{ form.errors.fecha_fin_publicacion }}
                        </div>
                    </div>
                </div>

                <!-- Checkboxes de estado -->
                <div class="form-control mt-4">
                    <label class="label cursor-pointer">
                        <span class="label-text text-[#111e60] dark:text-gray-300">Publicado</span>
                        <input 
                            type="checkbox"
                            v-model="form.publicado"
                            class="checkbox dark:border-gray-500 dark:bg-gray-700"
                            style="border-color: #111e60;"
                        />
                    </label>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text text-[#111e60] dark:text-gray-300">Mostrar en página principal</span>
                        <input 
                            type="checkbox"
                            v-model="form.pagina_principal"
                            class="checkbox dark:border-gray-500 dark:bg-gray-700"
                            style="border-color: #111e60;"
                        />
                    </label>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text text-[#111e60] dark:text-gray-300">Estado activo</span>
                        <input 
                            type="checkbox"
                            v-model="form.estado"
                            class="checkbox dark:border-gray-500 dark:bg-gray-700"
                            style="border-color: #111e60;"
                        />
                    </label>
                </div>

                <!-- Editor Quill -->
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text text-[#111e60] dark:text-gray-300">Contenido</span>
                    </label>
                    <div class="bg-white dark:bg-gray-700 rounded-lg">
                        <QuillEditor
                            v-model:content="form.contenido_html"
                            :options="quillOptions"
                            contentType="html"
                            class="bg-white dark:bg-gray-700 quill-dark-mode"
                            style="min-height: 200px;"
                        />
                    </div>
                    <div v-if="form.errors.contenido_html" class="text-error text-sm mt-1">
                        {{ form.errors.contenido_html }}
                    </div>
                </div>

                <div class="modal-action">
                    <button 
                        type="button" 
                        class="btn bg-[#f5f6f6] text-[#111e60] hover:bg-[#e2e2e2] dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600" 
                        @click="emit('close')"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="btn bg-[#111e60] text-white hover:bg-[#0b1535] dark:bg-[#363d4d] dark:hover:bg-[#2c3340]" 
                        :disabled="form.processing"
                    >
                        {{ isEditMode ? 'Actualizar Plaza' : 'Guardar Plaza' }}
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</template>

<style>
.ql-editor {
    min-height: 200px;
    background-color: white;
}

.dark .ql-editor {
    background-color: #374151; /* gray-700 */
    color: white;
}

.ql-toolbar {
    background-color: white;
    border-color: #e5e5e5 !important;
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
}

.dark .ql-container {
    border-color: #4B5563 !important;
}

/* Estilos para selects en modo oscuro */
.dark select option {
    background-color: #374151; /* gray-700 */
    color: white;
}

/* Estilos para inputs de tipo fecha en modo oscuro */
.dark input[type="date"] {
    color-scheme: dark;
}
</style>