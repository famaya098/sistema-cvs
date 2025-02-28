<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    plaza: Object,
});
</script>

<template>
    <GuestLayout>
        <Head :title="plaza.titulo" />

        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-light tracking-wide">{{ plaza.titulo }}</h1>
                    <p class="text-gray-300 text-sm">
                        {{ plaza.seccion.nombre }} - {{ plaza.categoria.nombre }}
                    </p>
                </div>
            </div>

            <!-- Contenido de la Plaza -->
            <div class="container mx-auto px-4 mb-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <!-- Información adicional -->
                            <div class="flex justify-between items-center mb-6 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Disponible hasta: {{ new Date(plaza.fecha_fin_publicacion).toLocaleDateString() }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-eye mr-2"></i>
                                        {{ plaza.accesos }} visitas
                                    </span>
                                </div>
                                <Link
                                    :href="route('welcome')"
                                    class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Volver al listado
                                </Link>
                            </div>

                            <!-- Contenido HTML de la plaza -->
                            <div 
                                v-html="plaza.contenido_html" 
                                class="prose max-w-none dark:prose-invert prose-headings:text-[#111e60] dark:prose-headings:text-gray-200"
                            ></div>

                            <!-- Botón de aplicar (solo visible para usuarios autenticados) -->
                            <div v-if="$page.props.auth.user" class="mt-8 flex justify-center">
                                <Link
                                    :href="route('plazas.apply', plaza.id_plaza)"
                                    class="px-6 py-3 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d]"
                                >
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Aplicar a esta plaza
                                </Link>
                            </div>
                            <div v-else class="mt-8 text-center">
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    Para aplicar a esta plaza, necesitas iniciar sesión
                                </p>
                                <div class="flex justify-center space-x-4">
                                    <Link
                                        :href="route('login')"
                                        class="px-6 py-3 text-sm font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition"
                                    >
                                        Iniciar Sesión
                                    </Link>
                                    <Link
                                        :href="route('register')"
                                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 rounded-lg transition"
                                    >
                                        Registrarse
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
/* Estilos para el contenido del editor Quill */
.ql-editor {
    padding: 0;
}

/* Preservar estilos de alineación */
.ql-align-center {
    text-align: center;
}

.ql-align-right {
    text-align: right;
}

.ql-align-justify {
    text-align: justify;
}

/* Estilos de encabezados */
h1, h2, h3, h4, h5, h6 {
    margin-top: 1em;
    margin-bottom: 0.5em;
}

h1 {
    font-size: 2em;
    font-weight: bold;
}

h3 {
    font-size: 1.5em;
    font-weight: bold;
}

/* Estilos de listas */
ul, ol {
    padding-left: 2em;
    margin: 1em 0;
}

li {
    margin: 0.5em 0;
}

/* Espaciado */
p {
    margin: 1em 0;
}

/* Imágenes */
img {
    display: inline-block;
    vertical-align: middle;
    max-width: 100%;
    height: auto;
}

/* Links */
a {
    color: #2563eb;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Estilos para modo oscuro */
.dark {
    color-scheme: dark;
}

.dark a {
    color: #60a5fa;
}

/* Estilos adicionales */
strong {
    font-weight: bold;
}

.dark strong {
    color: inherit;
}
</style>