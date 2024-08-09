<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import {ref, h} from "vue";
import Network from '@/Pages/Structure/Partials/Network.vue';
import Listing from '@/Pages/Structure/Partials/Listing.vue';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    tab: Number,
})

const user = usePage().props.auth.user;

const tabs = ref([
        {
            title: wTrans('public.network'),
            component: h(Network)
        },
        {
            title: wTrans('public.listing'),
            component: h(Listing),
        }
]);

const activeIndex = ref(props.tab);
</script>

<template>
    <AuthenticatedLayout :title="$t('public.structure')">
        <TabView
            v-if="user.role === 'agent'"
            v-model:activeIndex="activeIndex"
            class="flex flex-col gap-5 self-stretch"
        >
            <TabPanel v-for="(tab, index) in tabs" :key="index" :header="tab.title">
                <component :is="tab.component" />
            </TabPanel>
        </TabView>

        <Network
            v-else
        />
    </AuthenticatedLayout>
</template>
