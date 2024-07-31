<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import StatusBadge from '@/Components/StatusBadge.vue';
import { DepositIcon, WithdrawalIcon, RebateIcon, MemberIcon, AgentIcon } from '@/Components/Icons/solid';
import { computed } from 'vue';
import {transactionFormat} from "@/Composables/index.js";
import Empty from "@/Components/Empty.vue";

const props = defineProps({
    user: Object,
    tradingAccounts: Object,
})

const { formatAmount } = transactionFormat();

// data overview
const dataOverviews = computed(() => [
    {
        icon: DepositIcon,
        total: 0,
        label: "trans('public.total_deposit')"+" ($)",
        type: 'member',
    },
    {
        icon: WithdrawalIcon,
        total: 0,
        label: "trans('public.total_withdrawal')"+" ($)",
        type: 'member',
    },
    {
        icon: RebateIcon,
        total: 0,
        label: "trans('public.rebate_earned')"+" ($)",
        type: 'agent',
    },
    {
        icon: MemberIcon,
        total: 0,
        label: "trans('public.referred_member')",
        type: 'member',
    },
    {
        icon: AgentIcon,
        total: 0,
        label: "trans('public.referred_agent')",
        type: 'agent',
    },
]);

const filteredDataOverviews = computed(() => {
    if (props.user.role === 'member') {
        return dataOverviews.value.filter((item) =>
            item.type === 'member'
        );
    }

    return dataOverviews.value;
});

</script>

<template>
    <AuthenticatedLayout title="$t('public.structure')">
        <div class="flex flex-col items-center gap-5 self-stretch">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2 self-stretch">
                <Button
                    external
                    type="button"
                    variant="primary-text"
                    size="sm"
                    :href="route('structure')"
                >
                    {{ "$t('public.structure_listing')" }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <span class="flex px-4 py-2 text-gray-400 items-center justify-center text-sm font-medium">{{ user.name }} - {{ "$t('public.view_downline_info')" }}</span>
            </div>

            <!-- Profile -->
            <div class="p-4 flex flex-col justify-center items-center gap-6 self-stretch rounded-2xl bg-white shadow-toast md:py-6 md:px-8 md:flex-row md:gap-9">
                <div class="pb-6 flex items-center gap-4 self-stretch border-b border-solid border-gray-200 md:pb-0 md:flex-col md:border-b-0 md:border-r ">
                    <div class="w-14 h-14 rounded-full overflow-hidden md:w-[60px] md:h-[60px]">
                        <DefaultProfilePhoto />
                    </div>

                    <div class="flex flex-col items-start gap-1 flex-1 md:items-center md:self-stretch">
                        <div class="w-52 truncate text-gray-950 text-lg font-semibold md:text-center">
                            {{ user.name }}
                        </div>
                        <div class="text-gray-700">
                            {{ user.id_number }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-3 self-stretch md:gap-5 md:flex-1">
                    <div class="grid grid-cols-2 grid-flow-row gap-y-3 gap-x-2 items-center self-stretch md:grid-rows-2 md:grid-flow-col md:gap-y-2 md:gap-x-5">
                        <div class="text-gray-500 text-xs">
                            {{ "$t('public.email_address')" }}
                        </div>
                        <div class="w-48 truncate flex-1 text-gray-950 text-sm font-medium">
                            {{ user.email }}
                        </div>
                        <div class="text-gray-500 text-xs">
                            {{ "$t('public.phone_number')" }}
                        </div>
                        <div class="w-48 truncate flex-1 text-gray-950 text-sm font-medium">
                            {{ user.phone_number }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 grid-flow-row gap-y-3 gap-x-2 items-center self-stretch md:grid-rows-2 md:grid-flow-col md:gap-y-2 md:gap-x-5">
                        <div class="text-gray-500 text-xs">
                            {{ "$t('public.role')" }}
                        </div>
                        <div class="flex items-start">
                            <StatusBadge :value="user.role">
                                {{ "$t(`public.${user.role}`)" }}
                            </StatusBadge>
                        </div>
                        <div class="text-gray-500 text-xs">
                            {{ "$t('public.upline')" }}
                        </div>
                        <div class="flex items-center gap-2 flex-1">
                            <div class="w-[26px] h-[26px] rounded-full overflow-hidden">
                                <DefaultProfilePhoto />
                            </div>
                            <div class="w-48 truncate flex-1 text-gray-950 text-sm font-medium">
                                {{ user.upline_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Overview -->
            <div class="p-4 flex justify-center items-center gap-5 self-stretch flex-wrap rounded-2xl bg-white shadow-dropdown md:p-8 md:gap-3">
                <div
                    v-for="(item, index) in filteredDataOverviews"
                    :key="index"
                    class="flex flex-col justify-center items-center gap-3 flex-1"
                >
                    <component :is="item.icon" class="grow-0 shrink-0" />
                    <div class="flex flex-col items-center gap-1 self-stretch">
                        <span class="text-gray-500 text-xs">{{ item.label }}</span>
                        <div class="text-gray-950 text-lg font-semibold">
                            {{ formatAmount(item.total) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trading Accounts -->
            <div class="pt-3 flex flex-col items-start gap-5 self-stretch">
                <div class="self-stretch text-gray-950 font-bold">
                    {{ "$t('public.all_trading_accounts')" }}
                </div>

                <template v-if="tradingAccounts === null">
                    <Empty message="$t('public.trading_account_empty_caption')"/>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>