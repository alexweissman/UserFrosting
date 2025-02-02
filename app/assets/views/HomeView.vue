<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@userfrosting/sprinkle-account/stores'
import { useConfigStore, useTranslator } from '@userfrosting/sprinkle-core/stores'

const auth = useAuthStore()
const config = useConfigStore()
const { $t } = useTranslator()

const helloMsg = computed(() => {
    return $t('WELCOME_TO', {
        title: config.get('site.title'),
        user: auth.user?.full_name ?? '&GUEST'
    })
})
</script>

<template>
    <article class="uk-article">
        <h1 class="uk-article-title">{{ helloMsg }}</h1>
        <p class="uk-article-meta">
            Written by <a href="#">Angela Davis</a>, Professor on 29 September 2018.
        </p>
        <p class="uk-text-lead">
            This is a demo of UserFrosting using a custom UiKit based template built using Vue 3.0
        </p>
        <p>All the content below is just for example and I've not added any functionality.</p>

        <div class="uk-grid-small uk-child-width-auto" uk-grid>
            <div>
                <a class="uk-button uk-button-text" href="#">Read more</a>
            </div>
            <div>
                <a class="uk-button uk-button-text" href="#">5 Comments</a>
            </div>
        </div>
    </article>

    <div style="padding-top: 25px; padding-bottom: 25px"></div>

    <table class="uk-table uk-table-divider">
        <thead>
            <tr>
                <th>Table Heading</th>
                <th>Table Heading</th>
                <th>Table Heading</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Table Data</td>
                <td>Table Data</td>
                <td>Table Data</td>
            </tr>
            <tr>
                <td>Table Data</td>
                <td>Table Data</td>
                <td>Table Data</td>
            </tr>
            <tr>
                <td>Table Data</td>
                <td>Table Data</td>
                <td>Table Data</td>
            </tr>
        </tbody>
    </table>

    <hr class="uk-divider-icon" style="padding-top: 25px; padding-bottom: 25px" />

    <div class="uk-child-width-1-2@s uk-grid-match" uk-grid>
        <div>
            <div class="uk-card uk-card-hover uk-card-body">
                <h3 class="uk-card-title">Hover</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-hover uk-card-body">
                <h3 class="uk-card-title">Hover</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-hover uk-card-body">
                <h3 class="uk-card-title">Hover</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-hover uk-card-body">
                <h3 class="uk-card-title">Hover</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            </div>
        </div>
    </div>

    <div style="padding-top: 50px"></div>
</template>
