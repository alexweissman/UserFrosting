<script setup>
import { useAuthStore } from '@userfrosting/sprinkle-account/stores'
import { useRouter } from 'vue-router'
const router = useRouter()

// Logout API variables
const auth = useAuthStore()
</script>

<template>
    <UFNavBar title="Userfrosting" :to="{ name: 'home' }">
        <UFNavBarItem :to="{ name: 'about' }" label="About" />
        <UFNavBarItem
            :to="{ name: 'account.register' }"
            label="Register"
            v-if="!auth.isAuthenticated" />
        <UFNavBarLogin
            v-if="!auth.isAuthenticated"
            @goto-registration="router.push({ name: 'account.register' })" />
        <UFNavBarUserCard
            v-if="auth.isAuthenticated"
            :username="auth.user.full_name"
            :avatar="auth.user.avatar"
            :meta="auth.user.user_name">
            <UFNavBarUserCardButton label="Admin Panel" :to="{ name: 'admin.dashboard' }" />
            <UFNavBarUserCardButton label="My Account" :to="{ name: 'account.settings' }" />
            <UFNavBarUserCardButton label="Logout" @click="auth.logout()" />
        </UFNavBarUserCard>
    </UFNavBar>
</template>
