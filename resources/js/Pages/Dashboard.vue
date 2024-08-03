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
                <div class="bg-white rounded-2xl h-30 md:h-40 shadow-toast p-8 w-full">
                    <div class="flex flex-col gap-2 items-start justify-center">
                        <span class="text-lg text-gray-950 font-bold w-full xl:max-w-[280px]">{{ $t('public.welcome_back', {'name': user.name}) }}</span>
                        <span class="text-xs text-gray-700 w-full xl:max-w-[280px]">{{ $t('public.greeting_caption') }}</span>
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
                <div class="bg-white rounded-2xl px-10 py-8 flex flex-col justify-between items-center self-stretch shadow-toast w-full">
                    <span class="text-sm font-medium text-gray-500">{{ $t('public.total_rebate_earned') }}</span>
                    <div class="text-xxl font-semibold text-gray-950">
                        $ {{ formatAmount(0) }}
                    </div>
                    <div class="flex flex-col gap-3 items-center self-stretch">
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
                <div class="flex flex-col items-center self-stretch w-full rounded-3xl shadow-toast relative overflow-hidden flex-[1_0%_0%]">
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
