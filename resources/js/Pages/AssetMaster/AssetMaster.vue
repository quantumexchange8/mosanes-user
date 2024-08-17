<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IconSearch, IconCircleXFilled, IconUserDollar, IconPremiumRights, IconAdjustments } from '@tabler/icons-vue';
import { ref, watch, watchEffect } from 'vue';
import {FilterMatchMode} from "primevue/api";
import InputText from 'primevue/inputtext';
import DefaultProfilePhoto from '@/Components/DefaultProfilePhoto.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import {transactionFormat} from "@/Composables/index.js";
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Dropdown from 'primevue/dropdown';
import { wTrans } from 'laravel-vue-i18n';
import AssetMasterAction from "@/Pages/AssetMaster/Partials/AssetMasterAction.vue";
import { usePage } from '@inertiajs/vue3';

const { formatAmount } = transactionFormat();

const sortingDropdownOptions = [
    {
        name: wTrans('public.latest'),
        value: 'created_at'
    },
    {
        name: wTrans('public.popular'),
        value: 'popular'
    },
    {
        name: wTrans('public.largest_fund'),
        value: 'total_fund'
    },
    {
        name: wTrans('public.most_investor'),
        value: 'total_investors'
    },
    {
        name: wTrans('public.my_favourites'),
        value: 'favourites'
    },
    {
        name: wTrans('public.my_joining'),
        value: 'joining'
    },
]

const masters = ref();
const sorting = ref(sortingDropdownOptions[0].value)

const getResults = async () => {
    try {
        const response = await axios.get('/asset_master/getMasters');
        masters.value = response.data.masters;
    } catch (error) {
        console.error('Error get masters:', error);
    }
};

getResults();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
        getAvailableAccounts();
    }
});

const getFilterData = async (filter) => {
    try {
        const response = await axios.get(`/asset_master/getFilterMasters/${filter}`);
        masters.value = response.data.masters;
    } catch (error) {
        console.error('Error get filter:', error);
    }
}

watch(sorting, (newValue) => {
    getFilterData(newValue)
})

const viewPammInfo = (index) => {
    window.open(route('asset_master.showPammInfo', masters.value[index].id), '_self')
}

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    upline_id: { value: null, matchMode: FilterMatchMode.EQUALS },
    level: { value: null, matchMode: FilterMatchMode.EQUALS },
    role: { value: null, matchMode: FilterMatchMode.EQUALS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const clearFilterGlobal = () => {
    filters.value['global'].value = null;
}

const accounts = ref([]);
const isLoading = ref(false);

const getAvailableAccounts = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/asset_master/getAvailableAccounts');
        accounts.value = response.data.accounts;
    } catch (error) {
        console.error('Error get masters:', error);
    } finally {
        isLoading.value = false;
    }
};

getAvailableAccounts();
</script>

<template>
    <AuthenticatedLayout :title="$t('public.asset_master')">
        <div class="flex flex-col items-center gap-5 self-stretch">
            <div class="flex flex-col items-stretch rounded-2xl shadow-toast w-full">
                <img src="/img/banner-pamm.png"  alt="">
            </div>

            <div class="flex justify-between items-center self-stretch">
                <div class="flex items-center gap-3">
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
                    <!-- <Button
                        variant="gray-outlined"
                        @click="toggle"
                        size="sm"
                        class="flex gap-3 items-center justify-center py-3 w-full md:w-[130px]"
                    >
                        <IconAdjustments size="20" color="#0C111D" stroke-width="1.25" />
                        <div class="text-sm text-gray-950 font-medium">
                            {{ $t('public.filter') }}
                        </div>
                        <Badge class="w-5 h-5 text-xs text-white" variant="numberbadge">
                            {{ filterCount }}
                        </Badge>
                    </Button> -->
                </div>

                <Dropdown
                    v-model="sorting"
                    :options="sortingDropdownOptions"
                    optionLabel="name"
                    option-value="value"
                    class="w-40"
                    scrollHeight="236px"
                />
            </div>

            <div
                v-if="masters"
                class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-5 self-stretch"
            >
                <div
                    v-for="(master, index) in masters"
                    :key="index"
                    class="w-full p-6 flex flex-col items-center gap-4 rounded-2xl bg-white shadow-toast"
                >
                    <div
                        @click="viewPammInfo(index)"
                        class="w-full flex items-center gap-4 hover:cursor-pointer"
                    >
                        <div class="w-[42px] h-[42px] shrink-0 grow-0 rounded-full overflow-hidden">
                            <div v-if="master.profile_photo">
                                <img :src="master.profile_photo" alt="Profile Photo" />
                            </div>
                            <div v-else>
                                <DefaultProfilePhoto />
                            </div>
                        </div>
                        <div class="flex flex-col items-start">
                            <div class="self-stretch truncate w-44 md:w-full md:max-w-[240px] xl:max-w-full 2xl:max-w-[200px] 3xl:max-w-[240px] text-gray-950 font-bold">
                                {{ master.asset_name }}
                            </div>
                            <div class="self-stretch truncate w-36 text-gray-500 text-sm">
                                {{ master.trader_name }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-stretch">
                        <StatusBadge value="info">
                            $ {{ formatAmount(master.minimum_investment) }}
                        </StatusBadge>
                        <StatusBadge value="gray">
                            <div v-if="master.minimum_investment_period !== 0">
                                {{ master.minimum_investment_period }} {{ $t('public.months') }}
                            </div>
                            <div v-else>
                                {{ $t('public.lock_free') }}
                            </div>
                        </StatusBadge>
                        <StatusBadge value="gray">
                            {{ master.performance_fee > 0 ? formatAmount(master.performance_fee, 0) + '% ' + $t('public.fee') : $t('public.zero_fee') }}
                        </StatusBadge>
                    </div>

                    <div class="py-2 flex justify-center items-center gap-2 self-stretch border-y border-solid border-gray-200">
                        <div class="w-full flex flex-col items-center">
                            <div class="self-stretch text-gray-950 text-center font-semibold">
                                {{ master.total_gain }}%
                            </div>
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.total_gain') }}
                            </div>
                        </div>
                        <div class="w-full flex flex-col items-center">
                            <div class="self-stretch text-gray-950 text-center font-semibold">
                                {{ master.monthly_gain }}%
                            </div>
                            <div class="w-16 sm:w-auto mx-auto truncate self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.monthly_gain') }}
                            </div>
                        </div>
                        <div class="w-full flex flex-col items-center">
                            <div class="self-stretch text-center font-semibold">
                                <div
                                    v-if="master.latest_profit !== 0"
                                    :class="(master.latest_profit < 0) ? 'text-error-500' : 'text-success-500'"
                                >
                                    {{ master.latest_profit }}%
                                </div>
                                <div
                                    v-else
                                    class="text-gray-950"
                                >
                                    -
                                </div>
                            </div>
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.latest') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center gap-1 self-stretch">
                        <div class="py-1 flex items-center gap-3 self-stretch">
                            <IconUserDollar size="20" stroke-width="1.25" />
                            <div class="w-full text-gray-950 text-sm font-medium">
                                {{ master.total_investors }} {{ $t('public.investors') }}
                            </div>
                        </div>
                        <div class="py-1 flex items-center gap-3 self-stretch">
                            <IconPremiumRights size="20" stroke-width="1.25" />
                            <div class="w-full text-gray-950 text-sm font-medium">
                                {{ $t('public.total_fund_size_caption') }}
                                <span class="text-primary-500">$ {{ formatAmount(master.total_fund) }}</span>
                            </div>
                        </div>
                    </div>

                    <AssetMasterAction
                        :master="master"
                        :accounts="accounts"
                        :isLoading="isLoading"
                    />
                </div>
            </div>

            <div
                v-else
                class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-5 self-stretch"
            >
                <div class="w-full p-6 flex flex-col items-center gap-4 rounded-2xl bg-white shadow-toast">
                    <div class="w-full flex items-center gap-4 hover:cursor-pointer">
                        <div class="w-[42px] h-[42px] shrink-0 grow-0 rounded-full overflow-hidden animate-pulse">
                            <DefaultProfilePhoto />
                        </div>
                        <div class="flex flex-col items-start animate-pulse">
                            <div class="h-2 bg-gray-200 rounded-full w-40 my-2"></div>
                            <div class="h-2 bg-gray-200 rounded-full w-36 my-1.5"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-stretch">
                        <div class="h-2 bg-info-200 rounded-full w-12 my-2"></div>
                        <div class="h-2 bg-gray-200 rounded-full w-12 my-2"></div>
                        <div class="h-2 bg-gray-200 rounded-full w-12 my-2"></div>
                    </div>

                    <div class="py-2 flex justify-center items-center gap-2 self-stretch border-y border-solid border-gray-200">
                        <div class="w-full flex flex-col items-center">
                            <div class="h-2 bg-gray-200 rounded-full w-16 my-2"></div>
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.total_gain') }}
                            </div>
                        </div>
                        <div class="w-full flex flex-col items-center">
                            <div class="h-2 bg-gray-200 rounded-full w-16 my-2"></div>
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.monthly_gain') }}
                            </div>
                        </div>
                        <div class="w-full flex flex-col items-center">
                            <div class="h-2 bg-gray-200 rounded-full w-16 my-2"></div>
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.lastest') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center gap-1 self-stretch">
                        <div class="py-1 flex items-center gap-3 self-stretch">
                            <IconUserDollar size="20" stroke-width="1.25" />
                            <div class="h-2 bg-gray-200 rounded-full w-40 my-1.5"></div>
                        </div>
                        <div class="py-1 flex items-center gap-3 self-stretch">
                            <IconPremiumRights size="20" stroke-width="1.25" />
                            <div class="h-2 bg-gray-200 rounded-full w-40 my-1.5"></div>
                        </div>
                    </div>

                    <AssetMasterAction
                        :master="null"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
