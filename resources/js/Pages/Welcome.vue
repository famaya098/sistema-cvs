<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { Head, Link, router } from '@inertiajs/vue3';

import { onMounted } from 'vue';

import FlashMessages from '@/Components/FlashMessages.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    plazas: Object,
    filters: Object,
    scroll_top: Boolean,
    flash: Object
});

const filterForm = ref({
    search: props.filters?.search || '',
});

// Función para aplicar filtros
const applyFilters = debounce(() => {
    router.get(
        route('welcome'),
        { search: filterForm.value.search },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: () => {
                // Asegurarnos que el input mantiene el foco
                document.getElementById('search-input')?.focus();
            }
        }
    );
}, 500);

// Observar cambios en los filtros
watch(() => filterForm.value.search, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        applyFilters();
    }
});

const clearFilters = () => {
    filterForm.value = {
        search: '',
    };
};

onMounted(() => {
    if (props.scroll_top || props.flash?.success) {
        window.scrollTo(0, 0);
    }
});

</script>

<template>
    <Head title="Bienvenido" />
    <GuestLayout>
        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] min-h-screen" id="top">
            <!-- Reemplazar los mensajes de alerta por el componente -->
            <FlashMessages />
            
            <!-- Header Section -->
            <div class="container mx-auto px-4 pt-8 pb-16">
                <div class="flex flex-col items-center mb-8 relative">
                    <div class="text-center space-y-2 mb-4">
                        <h1 class="text-2xl md:text-3xl font-light tracking-wide text-white">
                            Oportunidades de Empleo
                        </h1>
                        <p class="text-gray-300 text-sm">
                            Banco Central de Reserva de El Salvador
                        </p>
                    </div>
                    
                    <!-- Botones de login/register en la esquina superior derecha -->
                    <nav v-if="canLogin" class="flex gap-3 absolute top-0 right-0 md:right-4">
                        <template v-if="$page.props.auth.user">
                            <!-- Usuario autenticado (aspirante) -->
                            <div class="flex items-center gap-2 px-4 py-2 text-white">
                                <span class="text-sm">{{ $page.props.auth.user.name }}</span>
                                <Link
                                    :href="route('aspirante.perfil')"
                                    class="px-4 py-2 text-sm font-medium text-white border border-white rounded-lg hover:bg-white hover:text-[#363d4d] transition duration-200"
                                >
                                    Mi Perfil
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="px-4 py-2 text-sm font-medium text-white border border-white rounded-lg hover:bg-white hover:text-[#363d4d] transition duration-200"
                                >
                                    Cerrar Sesión
                                </Link>
                            </div>
                        </template>
                        <template v-else-if="$page.props.auth.admin">
                            <!-- Administrador autenticado -->
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-white">Admin: {{ $page.props.auth.admin.name }}</span>
                                <Link
                                    :href="route('plazas.index')"
                                    class="px-4 py-2 text-sm font-medium text-white border border-white rounded-lg hover:bg-white hover:text-[#363d4d] transition duration-200"
                                >
                                    Panel Admin
                                </Link>
                                <Link
                                    :href="route('admin.logout')"
                                    method="post"
                                    as="button"
                                    class="px-4 py-2 text-sm font-medium text-white border border-white rounded-lg hover:bg-white hover:text-[#363d4d] transition duration-200"
                                >
                                    Cerrar Sesión
                                </Link>
                            </div>
                        </template>
                        <template v-else>
                            <!-- Usuario no autenticado -->
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 text-sm font-medium text-white border border-white rounded-lg hover:bg-white hover:text-[#363d4d] transition duration-200"
                            >
                                Iniciar Sesión
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('registro.aspirante')"
                                class="px-4 py-2 text-sm font-medium text-white border border-white rounded-lg hover:bg-white hover:text-[#363d4d] transition duration-200"
                            >
                                Registrarse
                            </Link>
                        </template>
                    </nav>
                </div>

                <!-- Barra de búsqueda -->
                <div class="max-w-lg mx-auto mb-10">
                    <div class="bg-white dark:bg-[#303844] rounded-lg shadow-md p-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input 
                                id="search-input"
                                type="text"
                                v-model="filterForm.search"
                                @input="applyFilters"
                                placeholder="Buscar plazas disponibles..."
                                class="w-full pl-10 pr-4 py-2 bg-transparent border border-gray-200 dark:border-gray-600 rounded-md text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent transition"
                            />
                        </div>
                    </div>
                </div>

                    <!-- Mensaje cuando no hay plazas disponibles -->
                    <div v-if="plazas.data.length === 0" class="flex flex-col items-center justify-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h3 class="text-xl font-medium text-white mb-2">No hay plazas disponibles</h3>
                        <p class="text-gray-300 text-center max-w-md">En este momento no hay oportunidades de empleo disponibles. Te invitamos a revisar más adelante.</p>
                    </div>

                <!-- Grid de Plazas -->
                <div class="max-w-7xl mx-auto">
                    <div v-if="plazas.data.length > 0" 
                        :class="[
                            'grid gap-6', 
                            plazas.data.length <= 3 
                                ? 'grid-cols-1 md:grid-cols-' + plazas.data.length 
                                : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                        ]">
                        <Link
                            v-for="plaza in plazas.data"
                            :key="plaza.id_plaza"
                            :href="route('plazas.show.public', plaza.id_plaza)"
                            class="bg-white dark:bg-[#303844] rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 mx-auto w-full max-w-md"
                        >
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    {{ plaza.titulo }}
                                </h3>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Inicio: {{ new Date(plaza.fecha_inicio_publicacion).toLocaleDateString() }}
                                    </div>

                                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Fin: {{ new Date(plaza.fecha_fin_publicacion).toLocaleDateString() }}
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ plaza.accesos }} visitas
                                    </span>
                                    
                                    <button class="px-4 py-2 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50 dark:focus:ring-offset-gray-800">
                                        Ver Detalless
                                    </button>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Paginación -->
                    <div v-if="plazas.links" class="mt-8 flex justify-center">
                        <nav class="flex items-center gap-2">
                            <template v-for="(link, key) in plazas.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 rounded-lg text-sm font-medium transition',
                                        link.active
                                            ? 'bg-white text-[#363d4d]'
                                            : 'text-gray-300 hover:text-white'
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="px-3 py-1 text-gray-400"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
    </GuestLayout>
</template>

<style scoped>
/* Estilo para las tarjetas en hover */
.hover\:shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>