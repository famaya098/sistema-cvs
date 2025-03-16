<script setup>
const props = defineProps({
    aspirante: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close']);

// Obtener género en formato legible
const obtenerGenero = (genero) => {
    return genero === 'M' ? 'Masculino' : (genero === 'F' ? 'Femenino' : 'No especificado');
};
</script>

<template>
    <dialog :open="true" class="modal">
        <div class="modal-box w-11/12 max-w-2xl bg-white dark:bg-[#303844] relative shadow-2xl rounded-xl flex flex-col h-[85vh]">
            <!-- Header con título y botón de cierre (sticky) -->
            <div class="sticky top-0 flex justify-between items-center bg-white dark:bg-[#303844] z-10 py-4 px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-[#111e60] dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Perfil de Aspirante
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
                <div v-if="aspirante" class="space-y-6">
                    <!-- Header con la información principal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-[#111e60] dark:bg-[#363d4d] text-white rounded-full h-12 w-12 flex items-center justify-center text-xl font-bold">
                                {{ aspirante.nombre_completo.charAt(0) }}
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-medium text-[#111e60] dark:text-white">
                                    {{ aspirante.nombre_completo }}
                                </h2>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ aspirante.email }}
                                    </div>
                                    <div v-if="aspirante.telefono" class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ aspirante.telefono }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información personal -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            Información Personal
                        </h4>
                        
                        <div class="ml-7">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">DUI</h5>
                                    <p class="text-[#111e60] dark:text-white">{{ aspirante.dui_aspirante || 'No disponible' }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Género</h5>
                                    <p class="text-[#111e60] dark:text-white">{{ obtenerGenero(aspirante.genero) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formación académica -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                            </svg>
                            Formación Académica
                        </h4>
                        
                        <div class="ml-7">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nivel académico</h5>
                                    <p class="text-[#111e60] dark:text-white">{{ aspirante.nivelAcademico?.nombre || 'No especificado' }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado académico</h5>
                                    <p class="text-[#111e60] dark:text-white">{{ aspirante.estadoAcademico?.nombre || 'No especificado' }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Carrera</h5>
                                    <p class="text-[#111e60] dark:text-white">{{ aspirante.carrera || 'No especificado' }}</p>
                                </div>
                                
                                <div v-if="aspirante.especialidad">
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Especialidad</h5>
                                    <p class="text-[#111e60] dark:text-white">{{ aspirante.especialidad }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Experiencia -->
                    <div class="bg-gray-50 dark:bg-[#2c3340]/60 rounded-xl p-5">
                        <h4 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-300 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Experiencia
                        </h4>
                        
                        <div class="ml-7">
                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nivel de experiencia</h5>
                            <p class="text-[#111e60] dark:text-white">{{ aspirante.experiencia?.nombre || 'No especificado' }}</p>
                        </div>
                    </div>
                    
                    <!-- Información adicional - Fecha de creación del perfil -->
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                        <p>
                            Fecha de registro: {{ aspirante.created_at ? new Date(aspirante.created_at).toLocaleDateString() : 'No disponible' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer con botones de acción (sticky) -->
            <div class="sticky bottom-0 left-0 right-0 py-4 px-6 bg-white dark:bg-[#303844] border-t border-gray-200 dark:border-gray-700 flex justify-end z-10">
                <button 
                    @click="emit('close')" 
                    class="btn flex items-center gap-1 bg-[#111e60] text-white hover:bg-[#1d2d86] focus:ring-4 focus:ring-[#111e60]/30 border-none shadow-md dark:bg-[#3b4671] dark:hover:bg-[#4a5590] dark:focus:ring-[#3b4671]/40 transition-all duration-200 rounded-md font-medium px-5 py-2"
                >
                    <span>Cerrar</span>
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

/* Personalización del modal */
.modal {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
}

/* Efecto de transición para elementos interactivos */
.btn, .input, .select, .checkbox {
    transition: all 0.2s ease;
}

/* Estilo para header y footer sticky */
.sticky {
    position: sticky;
    z-index: 10;
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

/* Estilos para las secciones de información */
.bg-gray-50 {
    transition: background-color 0.3s ease;
}

/* Diseño responsive */
@media (max-width: 768px) {
    .grid-cols-1 {
        grid-template-columns: 1fr;
    }
    
    .md\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
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