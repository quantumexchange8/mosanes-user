<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import {ref, h} from "vue";
import Network from '@/Pages/Structure/Partials/Network.vue';
import Listing from '@/Pages/Structure/Partials/Listing.vue';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

const tabs = ref([
        {
            title: ('public.network'),
            component: h(Network)
        },
        {
            title: ('public.listing'),
            component: h(Listing),
        }
]);

</script>

<template>
    <Head title="Structure" />

    <AuthenticatedLayout title="$t('public.structure')">
        <TabView class="flex flex-col gap-5 self-stretch">
            <TabPanel v-for="(tab, index) in tabs" :key="index" :header="tab.title">
                <component :is="tab.component" />
            </TabPanel>
        </TabView>
    </AuthenticatedLayout>
</template>