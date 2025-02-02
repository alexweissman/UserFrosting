<script setup lang="ts">
import { onMounted } from 'vue'
import { usePageMeta } from '@userfrosting/sprinkle-core/composables'
import { useTranslator } from '@userfrosting/sprinkle-core/stores'
const { $t } = useTranslator()
const page = usePageMeta()

// Hide the title on this page
onMounted(() => {
    page.hideTitle = true
})
</script>
<template>
    <h1>{{ $t('ABOUT') }}</h1>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
        labore et dolore magna aliqua. Sit amet luctus venenatis lectus magna. In hac habitasse
        platea dictumst quisque sagittis purus. Ac felis donec et odio pellentesque diam volutpat
        commodo sed. Rhoncus dolor purus non enim praesent. Aliquet bibendum enim facilisis gravida
        neque convallis a cras. Sed vulputate odio ut enim blandit volutpat maecenas. A diam
        sollicitudin tempor id eu nisl nunc mi. Sed nisi lacus sed viverra tellus in. Vestibulum
        morbi blandit cursus risus at ultrices mi tempus. Suspendisse faucibus interdum posuere
        lorem ipsum dolor sit amet. Nec feugiat nisl pretium fusce id velit ut tortor pretium.
        Scelerisque varius morbi enim nunc. Sagittis vitae et leo duis ut diam quam nulla porttitor.
        Amet risus nullam eget felis eget nunc lobortis mattis. Amet mattis vulputate enim nulla
        aliquet porttitor lacus luctus. Vitae proin sagittis nisl rhoncus mattis rhoncus urna neque.
        Placerat orci nulla pellentesque dignissim enim.
    </p>
</template>
