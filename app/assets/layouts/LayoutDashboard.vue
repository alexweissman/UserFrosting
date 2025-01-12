<script setup lang="ts">
import NavBar from '../components/NavBar.vue'
import FooterContent from '../components/FooterContent.vue'
import { SidebarMenuItems } from '@userfrosting/sprinkle-admin/components'
</script>

<template>
    <NavBar />
    <UFSideBar>
        <UFSideBarLabel label="Admin Panel" />
        <SidebarMenuItems />
    </UFSideBar>
    <UFMainContent>
        <RouterView />
    </UFMainContent>
    <FooterContent />
</template>
