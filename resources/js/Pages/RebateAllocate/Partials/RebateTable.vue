<script setup>
import {computed, onMounted, ref, watch, watchEffect} from "vue";
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';
import Button from '@/Components/Button.vue';
import {usePage} from '@inertiajs/vue3';
import OverlayPanel from 'primevue/overlaypanel';
import DataTable from "primevue/datatable";
import Column from "primevue/column";
// import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import {FilterMatchMode} from "primevue/api";
import Loader from "@/Components/Loader.vue";
import Dropdown from "primevue/dropdown";
import {
    IconSearch,
    IconCircleXFilled,
    IconAdjustments,
} from '@tabler/icons-vue';
import { wTrans } from "laravel-vue-i18n";
import AgentDropdown from '@/Pages/RebateAllocate/Partials/AgentDropdown.vue';

const dropdownOptions = [
    {
        name: wTrans('public.standard_account'),
        value: 'created_at'
    },
    {
        name: wTrans('public.premium_account'),
        value: 'popular'
    },
]

const columns = [
    {name: wTrans('public.forex')},
    {name: wTrans('public.stocks')},
    {name: wTrans('public.indices')},
    {name: wTrans('public.commodities')},
    {name: wTrans('public.cryptocurrency')},
    {name: ''},
]

const accountType = ref(dropdownOptions[0].value);
const loading = ref(false);
const dt = ref();
const agents = ref();
const rebates = ref();
const agent_id = ref();

const getResults = async () => {
    loading.value = true;

    try {
        const response = await axios.get('/rebate_allocate/getAgents');
        agents.value = response.data.agents;
        // rebates.value = response.data.rebates;
        console.log(agents.value)
        // console.log(rebates.value)
    } catch (error) {
        console.error('Error get agents:', error);
    } finally {
        loading.value = false;
    }
};

getResults();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
    }
});

const changeAgent = async (newAgent) => {
    console.log(newAgent);
    loading.value = true;

    try {
        const response = await axios.get('/rebate_allocate/getAgents');
        agents.value = response.data.agents;
        rebates.value = response.data.rebates;
    } catch (error) {
        console.error('Error get agents:', error);
    } finally {
        loading.value = false;
    }
}

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    upline_id: { value: null, matchMode: FilterMatchMode.EQUALS },
    level: { value: null, matchMode: FilterMatchMode.EQUALS },
    role: { value: null, matchMode: FilterMatchMode.EQUALS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const clearFilter = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
        upline_id: { value: null, matchMode: FilterMatchMode.EQUALS },
        level: { value: null, matchMode: FilterMatchMode.EQUALS },
        role: { value: null, matchMode: FilterMatchMode.EQUALS },
        status: { value: null, matchMode: FilterMatchMode.EQUALS },
    };

    upline_id.value = null;
    level.value = null;
};

const clearFilterGlobal = () => {
    filters.value['global'].value = null;
}

</script>

<template>
    <div class="p-6 flex flex-col items-center justify-center self-stretch gap-6 border border-gray-200 bg-white shadow-table rounded-2xl">
        <DataTable
            v-model:filters="filters"
            :value="agents"
            tableStyle="min-width: 50rem"
            :globalFilterFields="['name']"
            ref="dt"
            :loading="loading"
            table-style="min-width:fit-content"
        >
            <template #header>
                <div class="flex flex-col md:flex-row gap-3 items-center self-stretch md:justify-between">
                    <div class="relative w-full md:w-60">
                        <div class="absolute top-2/4 -mt-[9px] left-4 text-gray-400">
                            <IconSearch size="20" stroke-width="1.25" />
                        </div>
                        <InputText v-model="filters['global'].value" :placeholder="$t('public.keyword_search')" class="font-normal pl-12 w-full md:w-60" />
                        <div
                            v-if="filters['global'].value !== null"
                            class="absolute top-2/4 -mt-2 right-4 text-gray-300 hover:text-gray-400 select-none cursor-pointer"
                            @click="clearFilterGlobal"
                        >
                            <IconCircleXFilled size="16" />
                        </div>
                    </div>
                    <Dropdown
                        v-model="accountType"
                        :options="dropdownOptions"
                        optionLabel="name"
                        optionValue="value"
                        class="w-52 font-normal"
                    />
                </div>
            </template>
            <template #empty> {{ $t('public.no_user_header') }} </template>
            <template #loading>
                <div class="flex flex-col gap-2 items-center justify-center">
                    <Loader />
                    <span class="text-sm text-gray-700">{{ $t('public.loading_users_caption') }}</span>
                </div>
            </template>
            <Column field="level">
                <template #header>
                    <span>{{ $t('public.level') }}</span>
                </template>
                <template #body="slotProps">
                    {{ slotProps.data[0][0].level }}
                </template>
            </Column>
            <Column field="Agent">
                <template #header>
                    <span>{{ $t('public.agent') }}</span>
                </template>
                <template #body="slotProps">
                    <!-- {{ slotProps.data }} -->
                    <AgentDropdown :agents="slotProps.data[0]" @update:modelValue="changeAgent($event)" />
                </template>
            </Column>
            <Column 
                v-for="(column, index) of columns"
                :key="index"
                field="field"
            >
                <template #header>
                    <span>{{ column.name }}</span>
                </template>
                <template #body="slotProps">
                    {{ slotProps.data }}
                </template>
            </Column>
            <!-- <Column field="forex">
                <template #header>
                    <span>{{ $t('public.forex') }}</span>
                </template>
                <template #body="slotProps">
                    {{ rebates }}
                </template>
            </Column> -->
        </DataTable>
    </div>
</template>