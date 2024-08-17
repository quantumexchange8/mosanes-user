<script setup>
import Button from "@/Components/Button.vue";
import {ref, watch} from "vue";
import Dialog from "primevue/dialog";
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import {transactionFormat} from "@/Composables/index.js";
import InputLabel from "@/Components/InputLabel.vue";
import Dropdown from "primevue/dropdown";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputNumber from "primevue/inputnumber";

const props = defineProps({
    master: Object,
    accounts: Array,
    isLoading: Boolean,
})

const visible = ref(false);
const { formatAmount } = transactionFormat();
const selectedAccount = ref();
const loading = ref(false);

watch([() => props.accounts, () => props.isLoading], ([newAccounts, newLoading]) => {
    selectedAccount.value = newAccounts[0];
    loading.value = newLoading;
})

watch(selectedAccount, (newAccount) => {
    selectedAccount.value = newAccount;
    form.investment_amount = Number(newAccount.balance)
})

const form = useForm({
    asset_master_id: '',
    meta_login: '',
    investment_amount: null,
})

const submitForm = () => {
    if (selectedAccount.value) {
        form.meta_login = selectedAccount.value.meta_login;
    }
    form.asset_master_id = props.master.id;

    form.post(route('asset_master.joinPamm'), {
        onSuccess: () => {
            visible.value = false;
        }
    });
}
</script>

<template>
    <div class="flex justify-center items-center gap-5 self-stretch">
        <Button
            variant="primary-flat"
            size="sm"
            type="button"
            class="w-full"
            :disabled="!master"
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
                        <span class="text-sm text-gray-950 font-bold">{{ $t('public.fees_and_conditions' )}}</span>
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
                            v-model="selectedAccount"
                            :options="accounts"
                            optionLabel="meta_login"
                            class="w-full"
                            :loading="loading"
                            scroll-height="236px"
                            :invalid="!!form.errors.meta_login"
                            :placeholder="loading ? $t('public.loading_caption') : (accounts.length > 0 ? $t('public.select') : $t('public.no_available_accounts'))"
                            :disabled="!accounts.length"
                        />
                        <InputError :message="form.errors.meta_login" />
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch w-full">
                        <InputLabel for="investment_amount" :value="$t('public.investment_amount') + ' ($)'" />
                        <InputNumber
                            v-model="form.investment_amount"
                            inputId="investment_amount"
                            prefix="$ "
                            class="w-full"
                            inputClass="py-3 px-4"
                            :min="0"
                            :step="100"
                            :minFractionDigits="2"
                            :placeholder="'$ ' + formatAmount(master.minimum_investment)"
                            fluid
                            readonly
                            :invalid="!!form.errors.investment_amount"
                        />
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
