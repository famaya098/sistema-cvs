<script setup>
import { ref } from 'vue'
const isDark = ref()
isDark.value = localStorage.getItem('darkMode') == 'true'
const sourceimg = ref('')
 
    if (isDark.value) {
        sourceimg.value = 'https://www.bcr.gob.sv/wp-content/uploads/2022/11/logo-institucional.png';
    } else {
        sourceimg.value = 'https://www.bcr.gob.sv/wp-content/uploads/2023/07/logo_dark.png';
    }
</script>

<template>
    <img :src="sourceimg" />
</template>