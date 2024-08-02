<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IconSearch, IconCircleXFilled, IconUserDollar, IconPremiumRights, IconAdjustments } from '@tabler/icons-vue';
import { ref, watch } from 'vue';
import {FilterMatchMode} from "primevue/api";
import InputText from 'primevue/inputtext';
import DefaultProfilePhoto from '@/Components/DefaultProfilePhoto.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import {transactionFormat} from "@/Composables/index.js";
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Dropdown from 'primevue/dropdown';
import { wTrans } from 'laravel-vue-i18n';

const { formatAmount } = transactionFormat();

const sortingDropdownOptions = [
    'Latest', 'Popular', 'Largest Fund', 'Most Investor', 'My Favourites', 'My Joining'
    //wTrans('public.lastest'), wTrans('public.popular'), wTrans('public.largest_fund'), wTrans('public.most_investor'), wTrans('public.my_favourites'), wTrans('public.my_joining')
]
const masters = ref();
const sorting = ref(sortingDropdownOptions[0])

const getResults = async () => {
    try {
        const response = await axios.get('/asset_master/getMasters');
        masters.value = response.data.masters;
        console.log(masters.value);
    } catch (error) {
        console.error('Error get masters:', error);
    }
};

getResults();

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

</script>

<template>
    <AuthenticatedLayout :title="$t('public.asset_master')">
        <div class="flex flex-col items-center gap-5 self-stretch">
            <div class="flex flex-col items-stretch rounded-2xl shadow-toast w-full">
                <img src="/img/banner-pamm.svg"  alt="">
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
                    <div class="w-full flex items-start gap-4">
                        <div class="w-14 h-14 rounded-full overflow-hidden md:w-[60px] md:h-[60px]">
                            <div v-if="master.profile_photo">
                                <img :src="master.profile_photo" alt="Profile Photo" />
                            </div>
                            <div v-else>
                                <DefaultProfilePhoto />
                            </div>
                        </div>
                        <div class="flex flex-col items-start">
                            <div class="self-stretch truncate w-64 text-gray-950 font-bold">
                                {{ master.asset_name }}
                            </div>
                            <div class="self-stretch truncate w-24 text-gray-500 text-sm">
                                {{ master.trader_name }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-stretch">
                        <StatusBadge value="info">
                            $ {{ formatAmount(master.minimum_invesment) }}
                        </StatusBadge>
                        <StatusBadge value="gray">
                            <div v-if="master.minimum_invesment_period != 0">
                                {{ master.minimum_invesment_period }} {{ $t('public.months') }}
                            </div>
                            <div v-else>
                                {{ $t('public.lock_free') }}
                            </div>
                        </StatusBadge>
                        <StatusBadge value="gray">
                            {{ master.performance_fee||master.performance_fee == 0 ? formatAmount(master.performance_fee, 0)+'%' : $t('public.zero') }} {{ $t('public.fee') }}
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
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.monthly_gain') }}
                            </div>
                        </div>
                        <div class="w-full flex flex-col items-center">
                            <div class="self-stretch text-center font-semibold">
                                <div 
                                    v-if="master.latest_profit != 0"
                                    :class="(master.latest_profit < 0) ? 'text-error-500' : 'text-success-500'"
                                >
                                    {{ master.latest_profit }}
                                </div>
                                <div
                                    v-else
                                    class="text-gray-950"
                                >
                                    -
                                </div>
                            </div>
                            <div class="self-stretch text-gray-500 text-center text-xs">
                                {{ $t('public.lastest') }}
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

                    <div class="flex justify-center items-center gap-5 self-stretch">
                        <Button
                            variant="primary-flat"
                            size="sm"
                            type="button"
                            class="w-full"
                        >
                            {{ $t('public.join_pamm') }}
                        </Button>

                        <!-- <div class="flex items-center gap-3">
                            heart icon
                            <div class="text-gray-950 text-sm font-medium">{{ 'like number' }}</div>
                        </div> -->
                    </div>
                </div>
            </div>

            <div v-else>
                loading
            </div>
        </div>
    </AuthenticatedLayout>
</template>