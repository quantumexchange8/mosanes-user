<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import {usePage} from "@inertiajs/vue3";
import {transactionFormat} from "@/Composables/index.js";
import {computed, ref} from "vue";
import {
    DepositIcon,
    WithdrawalIcon,
    NetBalanceIcon,
    NetAssetIcon,
} from '@/Components/Icons/solid';
import {trans} from "laravel-vue-i18n";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const user = usePage().props.auth.user;
const { formatAmount } = transactionFormat();
const group_total_deposit = ref(999);
const group_total_withdrawal = ref(999);
const total_group_net_balance = ref(999);
const total_group_net_asset = ref(999);

// data overview
const dataOverviews = computed(() => [
    {
        icon: DepositIcon,
        total: group_total_deposit.value,
        label: trans('public.group_total_deposit'),
        borderColor: 'border-green',
    },
    {
        icon: WithdrawalIcon,
        total: group_total_withdrawal.value,
        label: trans('public.group_total_withdrawal'),
        borderColor: 'border-pink',
    },
    {
        icon: NetBalanceIcon,
        total: total_group_net_balance.value,
        label: trans('public.total_group_net_balance'),
        borderColor: 'border-[#FEDC32]',
    },
    {
        icon: NetAssetIcon,
        total: total_group_net_asset.value,
        label: trans('public.total_group_net_asset'),
        borderColor: 'border-indigo',
    },
]);
</script>

<template>
    <AuthenticatedLayout :title="$t('public.dashboard')">
        <div class="flex flex-col xl:flex-row items-center gap-5 self-stretch w-full">
            <div class="flex flex-col gap-5 items-center self-stretch w-full">
                <!-- greeting card -->
                <div class="bg-white rounded-2xl h-30 md:h-40 shadow-toast relative overflow-hidden p-8 w-full">
                    <div class="flex flex-col gap-2 items-start justify-center w-full max-w-[180px] xl:max-w-[280px]">
                        <span class="text-lg text-gray-950 font-bold">{{ $t('public.welcome_back', {'name': user.name}) }}</span>
                        <span class="text-xs text-gray-700">{{ $t('public.greeting_caption') }}</span>
                    </div>

                    <div class="absolute right-0 top-1">
                        <img src="/img/greeting_banner.svg" alt="banner">
                    </div>
                </div>

                <!-- overview data -->
                <div class="grid grid-cols-2 gap-5 w-full">
                    <div
                        class="flex flex-col justify-center items-center gap-3 px-6 pt-6 pb-4 rounded-2xl border-b-8 w-full shadow-toast"
                        :class="item.borderColor"
                        v-for="(item, index) in dataOverviews"
                        :key="index"
                    >
                        <component :is="item.icon" class="w-12 h-12 grow-0 shrink-0 rounded-full" />
                        <div class="flex flex-col items-center gap-1 w-full">
                            <span class="text-gray-500 text-xs md:text-sm">{{ item.label }}</span>
                            <div class="text-gray-950 text-lg md:text-xl font-semibold">
                                {{ formatAmount(item.total) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-1 gap-5 items-center self-stretch w-full">
                <!-- rebate earn -->
                <div class="bg-white rounded-2xl p-4 md:px-10 md:py-8 flex flex-col gap-2 md:gap-7 items-center self-stretch shadow-toast w-full">
                    <span class="text-left w-full text-sm font-medium text-gray-500">{{ $t('public.total_rebate_earned') }}</span>
                    <div class="text-left w-full text-xxl font-semibold text-gray-950">
                        $ {{ formatAmount(0) }}
                    </div>
                    <div class="flex flex-col gap-3 items-center self-stretch pt-1 md:pt-0">
                        <Button
                            type="button"
                            variant="primary-flat"
                            class="w-full"
                        >
                            {{ $t('public.apply_rebate') }}
                        </Button>
                        <span class="text-sm text-gray-500">{{ $t('public.last_applied_on') }}</span>
                    </div>
                </div>

                <!-- rebate wallet -->
                <div class="flex flex-col items-center self-stretch w-full rounded-3xl shadow-toast relative overflow-hidden">
                    <div class="absolute right-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="345" height="254" viewBox="0 0 345 254" fill="none">
                            <path opacity="0.2" d="M607.874 363.397C390.559 303.674 -34.5139 165.082 3.70844 88.4965C51.4864 -7.23541 306.019 95.1543 317.71 101.043M317.71 101.043C329.401 106.932 544.041 201.381 628.094 209.587L632.482 292.784C455.107 238.075 100.928 112.502 103.212 47.8793C104.487 11.8008 123.985 -8.13416 173.887 -5.95469M317.71 101.043C180.498 45.2856 164.656 6.47896 173.887 -5.95469M173.887 -5.95469C213.368 -4.23034 256.971 4.62692 287.138 12.9569L173.887 -5.95469Z" stroke="#FCFCFD" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="bg-logo py-5 px-10 flex flex-col justify-between items-center self-stretch">
                        <div class="flex items-center justify-end w-full">
                            <ApplicationLogo class="w-8 h-8 fill-white" />
                        </div>
                        <div class="flex flex-col gap-3 items-start self-stretch">
                            <span class="text-sm text-gray-200 font-medium">{{ $t('public.available_rebate_balance') }}</span>
                            <span class="text-xxl text-white font-semibold">$ {{ formatAmount(0) }}</span>
                        </div>
                    </div>
                    <div class="bg-white py-7 px-10 flex gap-5 items-center self-stretch">
                        <Button
                            type="button"
                            variant="gray-outlined"
                            class="w-full"
                        >
                            {{ $t('public.transfer') }}
                        </Button>
                        <Button
                            type="button"
                            variant="gray-outlined"
                            class="w-full"
                        >
                            {{ $t('public.withdrawal') }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
