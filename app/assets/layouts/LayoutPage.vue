<script setup>
import NavBar from '../components/NavBar.vue'
import FooterContent from '../components/FooterContent.vue'
</script>

<template>
    <NavBar />
    <UFMainContent>
        <RouterView />
    </UFMainContent>
    <FooterContent />
</template>
