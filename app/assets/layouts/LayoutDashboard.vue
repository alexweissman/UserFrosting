<script setup lang="ts">
import NavBar from '../components/NavBar.vue'
import FooterContent from '../components/FooterContent.vue'
import { SidebarMenuItems } from '@userfrosting/sprinkle-admin/components'
</script>

<template>
    <NavBar />
    <UFSideBar>
        <UFSideBarLabel label="NAVIGATION" />
        <SidebarMenuItems />
        <UFSideBarItem :to="{ name: 'test' }" faIcon="person-digging" label="Test" />
    </UFSideBar>
    <UFMainContent>
        <RouterView />
    </UFMainContent>
    <FooterContent />
</template>
