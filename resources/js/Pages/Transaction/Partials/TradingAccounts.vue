<script setup>
import OverlayPanel from 'primevue/overlaypanel';
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import {FilterMatchMode} from "primevue/api";
import Loader from "@/Components/Loader.vue";
import {
    IconSearch,
    IconCircleXFilled,
    IconAdjustments,
    IconCloudDownload,
} from '@tabler/icons-vue';
import {transactionFormat} from "@/Composables/index.js";
import {computed, onMounted, ref, watch, watchEffect} from "vue";
import { trans, wTrans } from 'laravel-vue-i18n';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import InputText from 'primevue/inputtext';
import { usePage } from '@inertiajs/vue3';
import StatusBadge from "@/Components/StatusBadge.vue";
import RadioButton from 'primevue/radiobutton';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import TradingAccountDetails from '@/Pages/Transaction/Partials/TradingAccountDetails.vue';

const { formatDateTime, formatAmount } = transactionFormat();

const loading = ref(false);
const dt = ref();
const paginator_caption = wTrans('public.paginator_caption');
const transactions = ref();

const getResults = async () => {
    loading.value = true;

    try {
        const response = await axios.get('/transaction/getTransactions');
        transactions.value = response.data.transactions;
    } catch (error) {
        console.error('Error get transactions:', error);
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

const getFilterData = async () => {
    try {
        const uplineResponse = await axios.get('/');
        uplines.value = uplineResponse.data.uplines;
        maxLevel.value = uplineResponse.data.maxLevel;
    } catch (error) {
        console.error('Error filter data:', error);
    } finally {
        loading.value = false;
    }
};

getFilterData();

const exportCSV = () => {
    dt.value.exportCSV();
};

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    upline_id: { value: null, matchMode: FilterMatchMode.EQUALS },
    level: { value: null, matchMode: FilterMatchMode.EQUALS },
    role: { value: null, matchMode: FilterMatchMode.EQUALS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
});

// overlay panel
const op = ref();
const uplines = ref()
const maxLevel = ref(0)
const levels = ref([])
const upline_id = ref(null)
const level = ref(null)
const filterCount = ref(0);

const toggle = (event) => {
    op.value.toggle(event);
}

watch([upline_id, level], ([newUplineId, newLevel]) => {
    if (upline_id.value !== null) {
        filters.value['upline_id'].value = newUplineId.value
    }

    if (level.value !== null) {
        filters.value['level'].value = newLevel.value
    }
})

watch(filters, () => {
    // Count active filters
    filterCount.value = Object.values(filters.value).filter(filter => filter.value !== null).length;
}, { deep: true });

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

// dialog
const visible = ref(false);
const selectedRow = ref();
const rowClicked = (data) => {
    console.log(data);
    selectedRow.value = data;
    visible.value = true;
}
</script>

<template>
        <div class="p-6 flex flex-col items-center justify-center self-stretch gap-6 border border-gray-200 bg-white shadow-table rounded-2xl">
        <DataTable
            v-model:filters="filters"
            :value="transactions"
            paginator
            removableSort
            :rows="10"
            :rowsPerPageOptions="[10, 20, 50, 100]"
            tableStyle="min-width: 50rem"
            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
            :currentPageReportTemplate="paginator_caption"
            :globalFilterFields="['name']"
            ref="dt"
            :loading="loading"
            table-style="min-width:fit-content"
            selectionMode="single"
            @row-click="rowClicked($event.data)"
        >
            <template #header>
                <div class="flex flex-col md:flex-row gap-3 items-center self-stretch">
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
                    <div class="grid grid-cols-2 w-full gap-3">
                        <Button
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
                        </Button>
                        <div class="w-full flex justify-end">
                            <Button
                                variant="primary-outlined"
                                @click="exportCSV($event)"
                                class="w-full md:w-auto"
                            >
                                {{ $t('public.export') }}
                                <IconCloudDownload size="20" />
                            </Button>
                        </div>
                    </div>
                </div>
            </template>
            <template #empty> {{ $t('public.no_transaction_header') }} </template>
            <template #loading>
                <div class="flex flex-col gap-2 items-center justify-center">
                    <Loader />
                    <span class="text-sm text-gray-700">{{ $t('public.loading_transactions_caption') }}</span>
                </div>
            </template>
            <Column
                field="created_at"
                sortable
                :header="$t('public.date')"
            >
                <template #body="slotProps">
                    {{ formatDateTime(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column
                field="transaction_number"
                sortable
                class="w-auto"
                :header="$t('public.id')"
            >
                <template #body="slotProps">
                    {{ slotProps.data.transaction_number }}
                </template>
            </Column>
            <Column
                field="description"
                class="w-auto"
                :header="$t('public.description')"
            >
                <template #body="slotProps">
                    {{ $t(`public.${slotProps.data.transaction_type}`) }}
                </template>
            </Column>
            <Column
                field="account"
                class="w-auto"
                :header="$t('public.account')"
            >
                <template #body="slotProps">
                    {{ slotProps.data.transaction_type === 'deposit' ? slotProps.data.to_meta_login : slotProps.data.from_meta_login }}
                </template>
            </Column>
            <Column
                field="amount"
                sortable
                class="w-auto"
                :header="`${$t('public.amount')} ($)`"
            >
                <template #body="slotProps">
                    {{ formatAmount(slotProps.data.transaction_type === 'deposit' ? slotProps.data.transaction_amount : slotProps.data.amount) }}
                </template>
            </Column>
            <Column
                field="status"
                :header="$t('public.status')"
            >
                <template #body="slotProps">
                    <div class="flex py-3 items-center flex-1">
                        <StatusBadge :value="slotProps.data.status">
                            {{ $t(`public.${slotProps.data.status}`) }}
                        </StatusBadge>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>

    <OverlayPanel ref="op">
        <div class="flex flex-col gap-8 w-60 py-5 px-4">
            <!-- Filter Role-->
            <div class="flex flex-col gap-2 items-center self-stretch">
                <div class="flex self-stretch text-xs text-gray-950 font-semibold">
                    {{ $t('public.filter_role_header') }}
                </div>
                <div class="flex flex-col gap-1 self-stretch">
                    <div class="flex items-center gap-2 text-sm text-gray-950">
                        <RadioButton v-model="filters['role'].value" inputId="role_member" value="member" />
                        <label for="role_member">{{ $t('public.member') }}</label>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-950">
                        <RadioButton v-model="filters['role'].value" inputId="role_agent" value="agent" />
                        <label for="role_agent">{{ $t('public.agent') }}</label>
                    </div>
                </div>
            </div>

            <!-- Filter Level-->
            <div class="flex flex-col gap-2 items-center self-stretch">
                <div class="flex self-stretch text-xs text-gray-950 font-semibold">
                    {{ $t('public.filter_level_header') }}
                </div>
                <!-- <Dropdown
                    v-model="level"
                    :options="levels"
                    filter
                    :filterFields="['name']"
                    optionLabel="name"
                    :placeholder="$t('public.select_level_placeholder')"
                    class="w-full"
                    scroll-height="236px"
                >
                    <template #value="slotProps">
                        <div v-if="slotProps.value" class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full overflow-hidden grow-0 shrink-0"></div>
                                <div>{{ slotProps.value.name }}</div>
                            </div>
                        </div>
                        <span v-else class="text-gray-400">{{ slotProps.placeholder }}</span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full overflow-hidden grow-0 shrink-0"></div>
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </Dropdown> -->
            </div>

            <!-- Filter Upline-->
            <div class="flex flex-col gap-2 items-center self-stretch">
                <div class="flex self-stretch text-xs text-gray-950 font-semibold">
                    {{ $t('public.filter_upline_header') }}
                </div>
                <!-- <Dropdown
                    v-model="upline_id"
                    :options="uplines"
                    filter
                    :filterFields="['name']"
                    optionLabel="name"
                    :placeholder="$t('public.select_upline_placeholder')"
                    class="w-full"
                    scroll-height="236px"
                >
                    <template #value="slotProps">
                        <div v-if="slotProps.value" class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full overflow-hidden">
                                    <template v-if="slotProps.value.profile_photo">
                                        <img :src="slotProps.value.profile_photo" alt="profile_picture" />
                                    </template>
                                    <template v-else>
                                        <DefaultProfilePhoto />
                                    </template>
                                </div>
                                <div>{{ slotProps.value.name }}</div>
                            </div>
                        </div>
                        <span v-else class="text-gray-400">{{ slotProps.placeholder }}</span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full overflow-hidden">
                                <template v-if="slotProps.option.profile_photo">
                                    <img :src="slotProps.option.profile_photo" alt="profile_picture" />
                                </template>
                                <template v-else>
                                    <DefaultProfilePhoto />
                                </template>
                            </div>
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </Dropdown> -->
            </div>

            <div class="flex w-full">
                <Button
                    type="button"
                    variant="primary-outlined"
                    class="flex justify-center w-full"
                    @click="clearFilter()"
                >
                    {{ $t('public.clear_all') }}
                </Button>
            </div>
        </div>
    </OverlayPanel>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.details')"
        class="md:dialog-sm"
    >
        <TradingAccountDetails :details="selectedRow" />
    </Dialog>
</template>