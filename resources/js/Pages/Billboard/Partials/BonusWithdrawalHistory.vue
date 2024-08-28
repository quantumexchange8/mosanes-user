<script setup>
import dayjs from "dayjs";
import Button from "@/Components/Button.vue";
import {IconCloudDownload, IconX} from "@tabler/icons-vue";
import ColumnGroup from "primevue/columngroup";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Loader from "@/Components/Loader.vue";
import Calendar from "primevue/calendar";
import Empty from "@/Components/Empty.vue";
import Row from "primevue/row";
import {ref, watch} from "vue";
import {transactionFormat} from "@/Composables/index.js";
import StatusBadge from "@/Components/StatusBadge.vue";

const props = defineProps({
    bonusWallet: Object
})

const dt = ref(null);
const loading = ref(false);
const bonusWithdrawalHistories = ref([]);
const totalApprovedAmount = ref();
const {formatAmount} = transactionFormat();

// Reactive variable for selected date range
const selectedDate = ref([]);

// Get current date
const today = new Date();
const maxDate = ref(today);

const getStatementData = async (filterDate = null) => {
    loading.value = true;

    try {
        let url = `/billboard/getBonusWithdrawalHistories`;

        if (filterDate) {
            const [startDate, endDate] = filterDate;
            url += `&startDate=${dayjs(startDate).format('YYYY-MM-DD')}&endDate=${dayjs(endDate).format('YYYY-MM-DD')}`;
        }

        const response = await axios.get(url);
        bonusWithdrawalHistories.value = response.data.bonusWithdrawalHistories;
        totalApprovedAmount.value = response.data.totalApprovedAmount;
    } catch (error) {
        console.error('Error changing locale:', error);
    } finally {
        loading.value = false;
    }
};

getStatementData();

watch(selectedDate, (newDateRange) => {
    if (Array.isArray(newDateRange)) {
        const [startDate, endDate] = newDateRange;

        if (startDate && endDate) {
            getStatementData([startDate, endDate]);
        } else if (startDate || endDate) {
            getStatementData([startDate || endDate, endDate || startDate]);
        } else {
            getStatementData();
        }
    } else {
        console.warn('Invalid date range format:', newDateRange);
    }
})

const clearDate = () => {
    selectedDate.value = [];
};
</script>

<template>
    <div class="flex flex-col items-center gap-4 flex-grow self-stretch">
        <DataTable
            :value="bonusWithdrawalHistories"
            removableSort
            scrollable
            scrollHeight="400px"
            tableStyle="md:min-width: 50rem"
            ref="dt"
            :loading="loading"
        >
            <template #header>
                <div class="flex flex-col md:flex-row gap-3 items-center self-stretch md:pb-6">
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="relative w-full md:w-[272px]">
                            <Calendar
                                v-model="selectedDate"
                                selectionMode="range"
                                :manualInput="false"
                                :maxDate="maxDate"
                                dateFormat="dd/mm/yy"
                                showIcon
                                iconDisplay="input"
                                placeholder="yyyy/mm/dd - yyyy/mm/dd"
                                class="w-full font-normal"
                            />
                            <div
                                v-if="selectedDate && selectedDate.length > 0"
                                class="absolute top-2/4 -mt-2.5 right-4 text-gray-400 select-none cursor-pointer bg-white"
                                @click="clearDate"
                            >
                                <IconX size="20" />
                            </div>
                        </div>
                        <div class="w-full flex justify-end">
                            <Button
                                variant="primary-outlined"
                                @click="exportCSV($event)"
                                class="w-full md:w-auto"
                            >
                                {{ $t('public.export') }}
                                <IconCloudDownload size="20" color="#2970FF" stroke-width="1.25" />
                            </Button>
                        </div>
                    </div>
                    <div class="flex justify-end self-stretch md:hidden">
                        <span class="text-gray-500 text-right text-sm font-medium">{{ $t('public.total_approved') }}:</span>
                        <span class="text-gray-950 text-sm font-semibold ml-2">$ {{ formatAmount(totalApprovedAmount ? totalApprovedAmount : 0) }}</span>
                    </div>
                </div>
            </template>
            <template #empty><Empty :message="$t('public.no_record_message')"/></template>
            <template #loading>
                <div class="flex flex-col gap-2 items-center justify-center">
                    <Loader />
                </div>
            </template>

            <Column
                field="created_at"
                sortable
                :header="$t('public.date')"
                class="hidden md:table-cell md:py-3"
            >
                <template #body="slotProps">
                    {{ dayjs(slotProps.data.created_at).format('YYYY/MM/DD') }}
                </template>
            </Column>
            <Column
                field="transaction_number"
                sortable
                :header="$t('public.id')"
                class="hidden md:table-cell"
            >
                <template #body="slotProps">
                    {{ slotProps.data.transaction_number }}
                </template>
            </Column>
            <Column
                field="amount"
                sortable
                :header="$t('public.amount') + ' ($)'"
                class="hidden md:table-cell">
                <template #body="slotProps"
                >
                    {{ formatAmount(slotProps.data.amount) }}
                </template>
            </Column>
            <Column
                field="status"
                :header="$t('public.status')"
                class="hidden md:table-cell"
            >
                <template #body="slotProps">
                    <StatusBadge :value="slotProps.data.status" class="w-fit">
                        {{ $t(`public.${slotProps.data.status}`) }}
                    </StatusBadge>
                </template>
            </Column>
            <Column class="md:hidden px-0">
                <template #body="slotProps">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-start gap-1">
                                <div class="flex gap-1 items-center self-stretch">
                                    <div class="text-sm text-gray-950 font-semibold">
                                        {{ slotProps.data.transaction_number }}
                                    </div>
                                    <StatusBadge :value="slotProps.data.status" class="w-fit">
                                        <span class="text-xxs">{{ $t(`public.${slotProps.data.status}`) }}</span>
                                    </StatusBadge>
                                </div>

                                <div class="flex items-center gap-2 text-gray-500 text-xs">
                                    <span>{{ dayjs(slotProps.data.created_at).format('YYYY/MM/DD H:m:ss') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full text-base text-right max-w-[90px] truncate font-semibold">
                            $ {{ formatAmount(slotProps.data.amount) }}
                        </div>
                    </div>
                </template>
            </Column>
            <ColumnGroup type="footer">
                <Row>
                    <Column class="hidden md:table-cell" :footer="$t('public.total_approved') + ':'" :colspan="2" footerStyle="text-align:right" />
                    <Column class="hidden md:table-cell" :colspan="2" :footer="formatAmount(totalApprovedAmount ? totalApprovedAmount : 0)" />
                </Row>
            </ColumnGroup>
        </DataTable>
    </div>
</template>
