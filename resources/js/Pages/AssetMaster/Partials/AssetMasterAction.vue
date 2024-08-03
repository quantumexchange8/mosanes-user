<script setup>
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import Dialog from "primevue/dialog";
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import {transactionFormat} from "@/Composables/index.js";
import InputLabel from "@/Components/InputLabel.vue";
import Dropdown from "primevue/dropdown";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";

const props = defineProps({
    master: Object
})

const visible = ref(false);
const { formatAmount } = transactionFormat();
const accounts = ref();

const getAvailableAccounts = async () => {
    try {
        const response = await axios.get('/asset_master/getAvailableAccounts');
        accounts.value = response.data.accounts;
    } catch (error) {
        console.error('Error get masters:', error);
    }
};

getAvailableAccounts();

const form = useForm({
    meta_login: '',
    investment_amount: '',
})

const submitForm = () => {

}
</script>

<template>
    <div class="flex justify-center items-center gap-5 self-stretch">
        <Button
            variant="primary-flat"
            size="sm"
            type="button"
            class="w-full"
            @click="visible = true"
        >
            {{ $t('public.join_pamm') }}
        </Button>

        <!-- <div class="flex items-center gap-3">
            heart icon
            <div class="text-gray-950 text-sm font-medium">{{ 'like number' }}</div>
        </div> -->
    </div>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.join_pamm')"
        class="dialog-xs md:dialog-md"
    >
        <form @submit.prevent="submitForm">
            <div class="flex flex-col items-center self-stretch gap-5 md:gap-8">
                <div class="py-5 px-6 flex flex-col items-center gap-4 bg-gray-50 divide-y self-stretch">
                    <div class="w-full flex items-center gap-4">
                        <div class="w-[42px] h-[42px] shrink-0 grow-0 rounded-full overflow-hidden">
                            <div v-if="master.profile_photo">
                                <img :src="master.profile_photo" alt="Profile Photo" />
                            </div>
                            <div v-else>
                                <DefaultProfilePhoto />
                            </div>
                        </div>
                        <div class="flex flex-col items-start self-stretch">
                            <div class="self-stretch truncate w-[190px] md:w-64 text-gray-950 font-bold">
                                {{ master.asset_name }}
                            </div>
                            <div class="self-stretch truncate w-24 text-gray-500 text-sm">
                                {{ master.trader_name }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-3 w-full pt-4 self-stretch">
                        <span class="text-sm text-gray-950 font-bold">{{ $t('public.fee_and_conditions' )}}</span>
                        <div class="flex flex-col gap-1 items-center self-stretch">
                            <div class="flex py-1 gap-3 items-center self-stretch">
                                <span class="w-full text-gray-500 font-medium text-xs">{{ $t('public.minimum_investment') }}</span>
                                <span class="w-full text-gray-950 font-medium text-sm">$ {{ formatAmount(master.minimum_investment) }}</span>
                            </div>
                            <div class="flex py-1 gap-3 items-center self-stretch">
                                <span class="w-full text-gray-500 font-medium text-xs">{{ $t('public.minimum_investment_period') }}</span>
                                <span class="w-full text-gray-950 font-medium text-sm">{{ master.minimum_investment_period }} {{ $t('public.months') }}</span>
                            </div>
                            <div class="flex py-1 gap-3 items-center self-stretch">
                                <span class="w-full text-gray-500 font-medium text-xs">{{ $t('public.performance_fee') }}</span>
                                <span class="w-full text-gray-950 font-medium text-sm lowercase">{{ master.performance_fee > 0 ? master.performance_fee + '%' : $t('public.zero_fee')}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-3 md:gap-5 items-center self-stretch">
                    <div class="flex flex-col items-start gap-1 self-stretch w-full">
                        <InputLabel for="meta_login" :value="$t('public.managed_account')" />
                        <Dropdown
                            v-model="form.meta_login"
                            :options="accounts"
                            optionLabel="meta_login"
                            class="w-full"
                            scroll-height="236px"
                            :invalid="!!form.errors.meta_login"
                        />
                        <InputError :message="form.errors.meta_login" />
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch w-full">
                        <InputLabel for="investment_amount" :value="$t('public.investment_amount') + ' ($)'" />
                        <IconField iconPosition="left" class="w-full">
                            <div class="text-gray-950 text-sm">$</div>
                            <InputText
                                id="investment_amount"
                                type="number"
                                class="block w-full"
                                v-model="form.investment_amount"
                                :placeholder="formatAmount(1000)"
                            />
                        </IconField>
                        <InputError :message="form.errors.investment_amount" />
                    </div>
                </div>

                <!-- risk warning -->
                <div class="flex flex-col items-start gap-3 self-stretch">
                    <span class="text-gray-950 text-sm font-bold">{{ $t('public.warning') }}</span>
                </div>
            </div>

            <div class="flex gap-3 md:gap-4 md:justify-end pt-5 md:pt-7 self-stretch">
                <Button
                    variant="primary-flat"
                    size="base"
                    class="w-full md:w-auto"
                    @click="submitForm"
                    :disabled="form.processing"
                >
                    {{ $t('public.join_now') }}
                </Button>
            </div>
        </form>
    </Dialog>
</template>
