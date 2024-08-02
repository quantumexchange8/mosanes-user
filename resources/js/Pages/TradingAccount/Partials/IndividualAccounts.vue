<script setup>
import { ref, onMounted } from "vue";
import StatusBadge from '@/Components/StatusBadge.vue';
import Action from "@/Pages/TradingAccount/Partials/Action.vue";
import ActionButton from "@/Pages/TradingAccount/Partials/ActionButton.vue";
import Empty from '@/Components/Empty.vue';

const accounts = ref([]);
const accountType = ref('individual');

const props = defineProps({
    leverages: Array,
    transferOptions: Array,
    walletOptions: Array,
});

// Fetch live accounts from the backend
const fetchLiveAccounts = async () => {
  try {
    const response = await axios.get(`/account/getLiveAccount?accountType=${accountType.value}`);
    accounts.value = response.data;
  } catch (error) {
    console.error('Error fetching live accounts:', error);
  }
};

// Fetch live accounts when the component is mounted
onMounted(fetchLiveAccounts);

</script>

<template>
  <Empty 
    v-if="!accounts.length" 
    :title="$t('public.empty_live_acccount_title')" 
    :message="$t('public.empty_live_acccount_message')" 
  />

  <div v-else class="w-full grid grid-cols-1 gap-5 md:grid-cols-2">
    <div v-for="account in accounts" :key="account.id" class="min-w-[300px] flex flex-col justify-center items-center py-4 pl-6 pr-3 gap-5 flex-grow md:pr-6 rounded-2xl border-l-8 border-success-500 bg-white shadow-toast">
      <div class="flex items-center gap-5 self-stretch">
        <div class="flex items-center content-center gap-3 md:gap-4 flex-grow">
          <span class="text-gray-950 font-semibold md:text-lg">#{{ account.meta_login }}</span>
          <StatusBadge value="success">
            {{ account.account_type }}
          </StatusBadge>
        </div>
        <Action :account="account" type="individual" :leverages="leverages" :walletOptions="walletOptions" />
      </div>
      <div class="grid grid-cols-2 gap-2 self-stretch">
        <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
          <span class="w-16 text-gray-500 text-xs">{{ $t('public.balance') }}:</span>
          <span class="text-gray-950 text-xs font-medium">$&nbsp;{{ account.balance }}</span>
        </div>
        <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
          <span class="w-16 text-gray-500 text-xs">{{ $t('public.equity') }}:</span>
          <span class="text-gray-950 text-xs font-medium">$&nbsp;{{ account.equity }}</span>
        </div>
        <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
          <span class="w-16 text-gray-500 text-xs">{{ $t('public.credit') }}:</span>
          <span class="text-gray-950 text-xs font-medium">$&nbsp;{{ account.credit }}</span>
        </div>
        <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
          <span class="w-16 text-gray-500 text-xs">{{ $t('public.leverage') }}:</span>
          <span class="text-gray-950 text-xs font-medium">1:{{ account.leverage }}</span>
        </div>
      </div>
      <div class="flex justify-end items-center gap-3 self-stretch">
        <ActionButton :account="account" :transferOptions="transferOptions" />
      </div>
    </div>
  </div>
</template>