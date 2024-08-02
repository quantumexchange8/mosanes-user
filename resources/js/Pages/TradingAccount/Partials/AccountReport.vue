<script setup>
import { ref, watch } from 'vue';
import Calendar from 'primevue/calendar';
import Dropdown from "primevue/dropdown";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { transactionFormat } from '@/Composables/index.js';
import Empty from '@/Components/Empty.vue';
import Loader from "@/Components/Loader.vue";
import { IconX } from '@tabler/icons-vue';
import Dialog from 'primevue/dialog';
import StatusBadge from '@/Components/StatusBadge.vue';
import { trans, wTrans } from "laravel-vue-i18n";

const { formatDate, formatDateTime, formatAmount } = transactionFormat();

const props = defineProps({
    account: Object,
});

const transactions = ref(null);
const selectedDate = ref([]);
const selectedOption = ref(null);
const loading = ref(false);
const visible = ref(false);
const data = ref({}); 

const transferOptions = [
  { name: wTrans('public.all'), value: 'all' },
  { name: wTrans('public.deposit'), value: 'deposit' },
  { name: wTrans('public.withdrawal'), value: 'withdrawal' },
  { name: wTrans('public.transfer'), value: 'transfer' }
];

const getAccountReport = async (selectedDate = [], selectedOption = null) => {
    if (loading.value) return;
    loading.value = true;

    try {
        let url = `/account/getAccountReport?meta_login=${props.account.meta_login}`;

        if (selectedDate.length) {
            const [startDate, endDate] = selectedDate;
            url += `&startDate=${startDate}&endDate=${endDate}`;
        }

        if (selectedOption) {
            url += `&type=${selectedOption}`;
        }

        const response = await axios.get(url);
        transactions.value = response.data;
    } catch (error) {
        console.error('Error fetching account report:', error);
    } finally {
        loading.value = false;
    }
};

getAccountReport();

watch(selectedDate, (newDateRange) => {
    if (Array.isArray(newDateRange)) {
        const [startDate, endDate] = newDateRange;

        if (startDate && endDate) {
            getAccountReport([startDate, endDate], selectedOption.value);
        } else if (startDate || endDate) {
            getAccountReport([startDate || endDate, endDate || startDate], selectedOption.value);
        } else {
            getAccountReport([], selectedOption.value);
        }
    } else {
        console.warn('Invalid date range format:', newDateRange);
    }
});

watch(selectedOption, (newOption) => {
    getAccountReport(selectedDate.value, newOption);
});

const clearDate = () => {
    selectedDate.value = [];
};

const openDialog = (rowData) => {
    visible.value = true;
    data.value = rowData;
};

function copyToClipboard(text) {
    const textToCopy = text;

    const textArea = document.createElement('textarea');
    document.body.appendChild(textArea);

    textArea.value = textToCopy;
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            console.log('Copied to clipboard:', textToCopy);
        } else {
            console.error('Unable to copy to clipboard.');
        }
    } catch (err) {
        console.error('Copy to clipboard failed:', err);
    }

    document.body.removeChild(textArea);
}

</script>

<template>
    <DataTable
        :value="transactions"
        paginator
        removableSort
        selectionMode="single"
        :rows="10"
        :rowsPerPageOptions="[10, 20, 50, 100]"
        tableStyle="md:min-width: 50rem"
        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
        @row-click="(event) => openDialog(event.data)"
        :loading="loading"
    >
        <template #header>
            <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-3">
                <div class="relative w-full">
                    <Calendar
                        v-model="selectedDate"
                        selectionMode="range"
                        :manualInput="false"
                        dateFormat="dd/mm/yy"
                        showIcon 
                        iconDisplay="input"
                        :placeholder="$t('public.date_placeholder')"
                        class="w-full"
                    />
                    <div
                        v-if="selectedDate && selectedDate.length > 0"
                        class="absolute top-2/4 -mt-2.5 right-4 text-gray-400 select-none cursor-pointer bg-white"
                        @click="clearDate"
                    >
                        <IconX size="20" />
                    </div>
                </div>
                <Dropdown
                    v-model="selectedOption"
                    :options="transferOptions"
                    optionLabel="name"
                    optionValue="value"
                    :placeholder="$t('public.transaction_type_option_placeholder')"
                    class="w-full"
                    scroll-height="236px"
                />
            </div>
        </template>
        <template #empty><Empty :message="$t('public.no_record_message')"/></template>
        <template #loading>
            <div class="flex flex-col gap-2 items-center justify-center">
                <Loader />
                <span class="text-sm text-gray-700">{{ $t('public.loading_caption') }}</span>
            </div>
        </template>
        <Column 
            field="created_at" 
            sortable 
            :header="$t('public.date')" 
            class="hidden md:table-cell"
        >
            <template #body="slotProps">
                {{ formatDateTime(slotProps.data.created_at) }}
            </template>
        </Column>
        <Column 
            :header="$t('public.description')" 
            class="hidden md:table-cell"
        >
            <template #body="slotProps">
                {{ slotProps.data.transaction_type }}
            </template>
        </Column>
        <Column 
            field="transaction_amount" 
            sortable 
            :header="$t('public.amount') + ` ($)`" 
            class="hidden md:table-cell"
        >
            <template #body="slotProps">
                {{ formatAmount(slotProps.data.transaction_amount) }}
            </template>
        </Column>
        <Column class="md:hidden">
            <template #body="slotProps">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col items-start gap-1 flex-grow">
                        <span class="overflow-hidden text-gray-950 text-ellipsis text-sm font-semibold">
                            {{ slotProps.data.transaction_type }}
                        </span>
                        <span class="text-gray-500 text-xs">
                            {{ formatDateTime(slotProps.data.created_at) }}
                        </span>
                    </div>
                    <div class="overflow-hidden text-right text-ellipsis font-semibold">
                        {{ formatAmount(slotProps.data.transaction_amount) }}
                    </div>
                </div>
            </template>
        </Column>
    </DataTable>

    <Dialog v-model:visible="visible" modal :header="$t('public.transfer_details')" class="dialog-xs md:dialog-md">
        <div 
            class="flex flex-col items-center gap-3 self-stretch" 
            :class="{
                'pb-4 border-b border-gray-200': ['deposit', 'withdrawal', 'balance_in', 'balance_out', 'credit_in', 'credit_out', 'rebate_in', 'rebate_out'].includes(data.transaction_type)
            }"
        >
            <div class="flex items-center gap-1 self-stretch">
                <span class="w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.transaction_id') }}</span>
                <span class="flex-grow text-gray-950 text-sm font-medium">{{ data.transaction_number }}</span>
            </div>
            <div class="flex items-center gap-1 self-stretch">
                <span class="w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.transaction_date') }}</span>
                <span class="flex-grow text-gray-950 text-sm font-medium">{{ data.created_at }}</span>
            </div>
            <div class="flex items-center gap-1 self-stretch">
                <span class="w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.account') }}</span>
                <span class="flex-grow text-gray-950 text-sm font-medium">{{  }}</span>
            </div>
            <div class="flex items-center gap-1 self-stretch">
                <span class="w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.description') }}</span>
                <span class="flex-grow text-gray-950 text-sm font-medium">{{ data.transaction_type }}</span>
            </div>
            <div class="flex items-center gap-1 self-stretch">
                <span class="w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.amount') }}</span>
                <span class="flex-grow text-gray-950 text-sm font-medium">{{ data.transaction_amount }}</span>
            </div>
            <div class="flex items-center gap-1 self-stretch">
                <span class="w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.status') }}</span>
                <StatusBadge :value="data.status">{{ $t('public.' + data.status) }}</StatusBadge>
            </div>
        </div>
        <div v-if="['deposit', 'withdrawal'].includes(data.transaction_type)" class="flex flex-col items-center py-4 gap-3 self-stretch border-b border-gray-200">
            <div v-if="data.transaction_type == 'deposit'" class="h-[42px] flex flex-col justify-center items-start gap-1 self-stretch md:h-auto md:flex-row md:justify-normal md:items-center">
                <span class="self-stretch w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.sent_address') }}</span>
                <div class="flex justify-center items-center self-stretch" @click="copyToClipboard('TAzY2emMte5Zs4vJu2La8KmXwkzoE78qgs')">
                    <span class="flex-grow overflow-hidden text-gray-950 text-ellipsis text-sm font-medium">{{ 'TAzY2emMte5Zs4vJu2La8KmXwkzoE78qgs' }}</span>
                </div>
            </div>
            <div v-if="data.transaction_type == 'withdrawal'" class="h-[42px] flex flex-col justify-center items-start gap-1 self-stretch md:h-auto md:flex-row md:justify-normal md:items-center">
                <span class="self-stretch w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.wallet_name') }}</span>
                <div class="flex justify-center items-center self-stretch" @click="copyToClipboard('My Wallet')">
                    <span class="flex-grow overflow-hidden text-gray-950 text-ellipsis text-sm font-medium">{{ 'My Wallet' }}</span>
                </div>
            </div>
            <div v-if="data.transaction_type == 'withdrawal'" class="h-[42px] flex flex-col justify-center items-start gap-1 self-stretch md:h-auto md:flex-row md:justify-normal md:items-center">
                <span class="self-stretch w-[120px] text-gray-500 text-xs font-medium">{{ $t('public.receiving_address') }}</span>
                <div class="flex justify-center items-center self-stretch" @click="copyToClipboard('TAzY2emMte5Zs4vJu2La8KmXwkzoE78qgs')">
                    <span class="flex-grow overflow-hidden text-gray-950 text-ellipsis text-sm font-medium">{{ 'TAzY2emMte5Zs4vJu2La8KmXwkzoE78qgs' }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col items-center py-4 gap-3 self-stretch">
            <div class="flex flex-col items-start gap-1 self-stretch md:flex-row">
                <span class="h-5 flex flex-col justify-center self-stretch text-gray-500 text-xs font-medium md:w-[120px]">{{ $t('public.remarks') }}</span>
                <span class="self-stretch text-gray-950 text-sm font-medium md:flex-grow">{{ data.remarks }}</span>
            </div>
        </div>
    </Dialog>
</template>
