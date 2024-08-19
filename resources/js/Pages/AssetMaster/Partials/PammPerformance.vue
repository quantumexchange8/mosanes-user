<script setup>
import PammPerformanceChart from "@/Pages/AssetMaster/Partials/PammPerformanceChart.vue";
import Button from "@/Components/Button.vue"
import Calendar from "primevue/calendar";
import {ref, watch} from "vue";
import dayjs from "dayjs";
import {IconChevronDown} from "@tabler/icons-vue"

const props = defineProps({
    masterDetail: Object
})

const isCalendarVisible = ref(false);
const month = ref(dayjs().format('MM/YYYY'));
const selectedMonth = ref(dayjs().format('MM/YYYY'));

const toggleCalendar = () => {
    isCalendarVisible.value = !isCalendarVisible.value;
};

watch(month, (newMonth) => {
    selectedMonth.value = dayjs(newMonth).format('MM/YYYY');
    isCalendarVisible.value = !isCalendarVisible.value;
})
</script>

<template>
    <div class="p-4 md:p-8 flex flex-col items-center self-stretch gap-5 rounded-2xl bg-white shadow-toast w-full">
        <div class="flex flex-col items-start self-stretch">
            <div
                class="flex justify-between items-start self-stretch w-full relative"
            >
                <span class="text-sm text-gray-950 font-bold">{{ $t('public.monthly_pnl_performance') }}</span>
                <Button
                    type="button"
                    size="sm"
                    variant="gray-text"
                    @click="toggleCalendar"
                    class="text-gray-950 font-medium"
                >
                    {{ selectedMonth }}
                    <IconChevronDown size="16" color="#667085" stroke-width="1.25" />
                </Button>
                <div
                    v-if="isCalendarVisible"
                    class="absolute right-0">
                    <Calendar
                        v-model="month"
                        selectionMode="single"
                        :manualInput="false"
                        view="month"
                        dateFormat="mm/yy"
                        inline
                        class="w-52"
                    />
                </div>

            </div>
            <span class="text-xxl text-green font-semibold">{{ masterDetail ? masterDetail.monthly_gain : 0 }}%</span>
        </div>

        <!-- chart -->
        <PammPerformanceChart
            :selectedMonth="selectedMonth"
            :masterDetail="masterDetail"
        />
    </div>
</template>
