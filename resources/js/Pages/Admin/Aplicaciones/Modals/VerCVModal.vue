<script setup>
const props = defineProps({
    url: {
        type: String,
        required: true
    },
    aspirante: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close']);

const downloadCurrentCV = () => {
    // Abrir en una nueva pestaña para descarga
    window.open(props.url, '_blank');
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
                    Currículum de {{ aspirante?.nombre_completo }}
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
            
            <!-- Cuerpo del modal con visor de PDF (área con scroll) -->
            <div class="flex-1 overflow-hidden bg-gray-100 dark:bg-gray-800">
                <iframe 
                    :src="url" 
                    class="w-full h-full border-0"
                    frameborder="0"
                ></iframe>
            </div>
            
            <!-- Información del aspirante -->
            <!-- <div class="bg-white dark:bg-[#2c3340] px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex flex-col">
                        <div class="font-medium text-[#111e60] dark:text-white">{{ aspirante?.nombre_completo }}</div>
                        <div>{{ aspirante?.email }}</div>
                    </div>
                    <div class="mt-2 md:mt-0 flex flex-col md:flex-row md:items-center gap-3">
                        <div v-if="aspirante?.telefono" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ aspirante?.telefono }}
                        </div>
                        <div v-if="aspirante?.dui_aspirante" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            DUI: {{ aspirante?.dui_aspirante }}
                        </div>
                    </div>
                </div>
            </div> -->
            
            <!-- Footer con botones de acción (sticky) -->
            <div class="sticky bottom-0 left-0 right-0 py-4 px-6 bg-white dark:bg-[#303844] border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3 z-10">
                <a 
                    :href="url" 
                    download 
                    class="btn flex items-center gap-1 bg-[#111e60] text-white hover:bg-[#1d2d86] focus:ring-4 focus:ring-[#111e60]/30 border-none shadow-md dark:bg-[#3b4671] dark:hover:bg-[#4a5590] dark:focus:ring-[#3b4671]/40 transition-all duration-200 rounded-md font-medium px-4 py-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Descargar</span>
                </a>
                <button 
                    @click="emit('close')" 
                    class="btn flex items-center gap-1 bg-white text-[#111e60] hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 border border-gray-300 hover:border-[#111e60] dark:bg-[#2a303c] dark:text-gray-200 dark:hover:bg-[#212631] dark:border-gray-600 dark:hover:border-gray-500 dark:focus:ring-gray-700 transition-all duration-200 rounded-md font-medium px-4 py-2"
                >
                    Cerrar
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

/* Estilos específicos para botones */
.btn {
    position: relative;
    overflow: hidden;
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    pointer-events: none;
    filter: saturate(70%);
}

.btn:focus {
    outline: none;
}

/* Efecto de onda para botones */
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

/* Transición suave para el modal */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-box {
    animation: fadeIn 0.2s ease-in-out;
}

/* Diseño responsive */
@media (max-width: 768px) {
    .flex-col {
        flex-direction: column;
    }
}
</style>