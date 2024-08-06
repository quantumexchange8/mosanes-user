<script setup>
import {ref, watchEffect, onMounted, h, computed} from 'vue';
import { usePage, useForm } from "@inertiajs/vue3";
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from "@/Components/Button.vue";
import {
    IconDots,
    IconCreditCardPay,
    IconScale,
    IconHistory,
    IconDatabaseMinus,
    IconTrash
} from '@tabler/icons-vue';
import OverlayPanel from "primevue/overlaypanel";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import Dropdown from "primevue/dropdown";
import toast from '@/Composables/toast';
import AccountReport from '@/Pages/TradingAccount/Partials/AccountReport.vue';
import { useConfirm } from 'primevue/useconfirm';
import { trans, wTrans } from "laravel-vue-i18n";
import TieredMenu from "primevue/tieredmenu";
import AccountWithdrawal from "@/Pages/TradingAccount/Partials/AccountWithdrawal.vue";
import ChangeLeverage from "@/Pages/TradingAccount/Partials/ChangeLeverage.vue";

const props = defineProps({
    account: Object,
    type: String,
    leverages: Array,
    walletOptions: Array,
});
const op = ref(null);
const showWithdrawalDialog = ref(false);
const showLeverageDialog = ref(false);
const showAccountReportDialog = ref(false);
const showTnCDialog = ref(false);
const leverages = ref(props.leverages);
const walletOptions = ref(props.walletOptions);

const paymentAccounts = usePage().props.auth.payment_account;

const toggle = (event) => {
    menu.value.toggle(event);
};

const openDialog = (dialogRef, formRef = null) => {
    if (formRef) formRef.reset();
    if (dialogRef === 'withdrawal') {
        // Check if paymentAccounts is empty
        if (paymentAccounts.length === 0) {
            requireAccountConfirmation('crypto');
        } else {
            showWithdrawalDialog.value = true;
        }
    } else if (dialogRef === 'leverage') {
        if (props.account.leverage !== 0) {
            showLeverageDialog.value = true;
        } else {
            toast.add({
                title: trans('public.toast_leverage_change_warning'),
                type: 'warning',
            });
        }
    } else if (dialogRef === 'accountReport') {
        showAccountReportDialog.value = true;
    } else if (dialogRef === 'tnc') {
        showTnCDialog.value = true;
    }
};

const closeDialog = (dialogName, formRef = null) => {
    if (formRef) formRef.reset();
    if (dialogName === 'withdrawal') {
        showWithdrawalDialog.value = false;
    } else if (dialogName === 'leverage') {
        showLeverageDialog.value = false;
    } else if (dialogName === 'accountReport') {
        showAccountReportDialog.value = false;
    } else if (dialogName === 'tnc') {
        showTnCDialog.value = false;
    }
};

const withdrawalForm = useForm({
    account_id: props.account.id,
    amount: '',
    receiving_wallet: '',
});

const leverageForm = useForm({
    account_id: props.account.id,
    leverage: '',
});

const form = useForm({
    account_id: props.account.id,
    type: '',
});


const submitWithdrawal = () => {
    withdrawalForm.receiving_wallet = paymentAccounts[0].account_no
    withdrawalForm.post(route('account.withdrawal_from_account'), {
        onSuccess: () => {
            // Close the dialog and show the withdrawal request dialog
            closeDialog('withdrawal', withdrawalForm);
        },
        onError: (errors) => {
            console.error('Error processing withdrawal:', errors);
        }
    });
};

const submitChangeLeverage = () => {
    leverageForm.post(route('account.change_leverage'), {
        onSuccess: () => {
            closeDialog('leverage', leverageForm)
        },
        onError: (errors) => {
            console.error('Error processing leverage change:', errors);
        }
    });
};

const confirm = useConfirm();
const requireAccountConfirmation = (accountType) => {
    const messages = {
        live: {
            group: 'headless-error',
            header: trans('public.delete_account'),
            text: trans('public.delete_account_text'),
            dynamicText: props.account.meta_login,
            suffix: '? ' + trans('public.confirmation_text_suffix'),
            actionType: 'delete',
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.delete_confirm'),
            action: () => {
                form.delete(route('account.delete_account'));
            }
        },
        demo: {
            group: 'headless-error',
            header: trans('public.delete_demo_account'),
            text: trans('public.delete_demo_account_text') + '?',
            suffix: trans('public.confirmation_text_suffix'),
            actionType: 'delete',
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.delete_confirm'),
            action: () => {
                form.type = 'demo';
                form.delete(route('account.delete_account'));
            }
        },
        revoke: {
            group: 'headless-error',
            header: trans('public.revoke_pamm'),
            text: trans('public.revoke_account_text'),
            suffix: '? ' + trans('public.confirmation_text_suffix'),
            actionType: 'revoke',
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.revoke_confirm'),
            action: () => {
                form.post(route('account.revoke_account'));
            }
        },
        crypto: {
            group: 'headless-primary',
            header: trans('public.crypto_wallet_required'),
            text: trans('public.crypto_wallet_required_text'),
            actionType: 'crypto',
            cancelButton: trans('public.later'),
            acceptButton: trans('public.add_Wallet'),
            action: () => {
                window.location.href = route('profile.edit');
            }
        }
    };

    const { group, header, text, dynamicText, suffix, actionType, cancelButton, acceptButton, action } = messages[accountType];

    confirm.require({
        group,
        header,
        actionType,
        message: {
            text,
            dynamicText,
            suffix
        },
        cancelButton,
        acceptButton,
        accept: action
    });
};

const toggleFullAmount = () => {
    if (withdrawalForm.amount) {
        withdrawalForm.amount = '';
    } else {
        withdrawalForm.amount = props.account.balance;
    }
};


// new
const menu = ref();
const visible = ref(false);
const dialogType = ref('');

const items = ref([
    {
        label: trans('public.withdrawal'),
        icon: h(IconCreditCardPay),
        command: () => {
            visible.value = true;
            dialogType.value = 'withdrawal';
        },
    },
    {
        label: trans('public.change_leverage'),
        icon: h(IconScale),
        command: () => {
            if (props.account.account_type_leverage === 0) {
                visible.value = true;
                dialogType.value = 'change_leverage';
            } else {
                toast.add({
                    title: trans('public.toast_leverage_change_warning'),
                    type: 'warning',
                });
            }
        },
        account_type: 'Standard Account'
    },
    {
        label: trans('public.revoke_pamm'),
        icon: h(IconDatabaseMinus),
        command: () => {
            visible.value = true;
            dialogType.value = 'revoke_pamm';
        },
        account_type: 'Premium Account'
    },
    {
        label: trans('public.account_report'),
        icon: h(IconHistory),
        command: () => {
            visible.value = true;
            dialogType.value = 'account_report';
        },
    },
    {
        separator: true,
    },
    {
        label: trans('public.delete_account'),
        icon: h(IconTrash),
        command: () => {
            visible.value = true;
            dialogType.value = 'delete_account';
        },
        action: 'delete_account'
    },
]);

const filteredItems = computed(() => {
    return items.value.filter(item => {
        if (item.account_type) {
            return item.account_type === props.account.account_type;
        }
        return true;
    });
});
</script>

<template>
    <Button
        v-if="props.type !== 'demo'"
        variant="gray-text"
        size="sm"
        type="button"
        iconOnly
        pill
        @click="toggle"
        aria-haspopup="true"
        aria-controls="overlay_tmenu"
    >
        <IconDots size="16" stroke-width="1.25" color="#667085" />
    </Button>

    <Button
        v-if="props.type === 'demo'"
        variant="gray-text"
        size="sm"
        type="button"
        iconOnly
        pill
        @click="requireAccountConfirmation('demo')"
    >
        <IconTrash size="16" stroke-width="1.25" color="#667085" />
    </Button>

    <!-- Menu -->
    <TieredMenu ref="menu" id="overlay_tmenu" :model="filteredItems" popup>
        <template #item="{ item, props }">
            <div
                class="flex items-center gap-3 self-stretch"
                v-bind="props.action"
            >
                <component :is="item.icon" size="20" stroke-width="1.25" :color="item.action === 'delete_account' ? '#F04438' : '#667085'" />
                <span class="font-medium" :class="{'text-error-500': item.action === 'delete_account'}">{{ item.label }}</span>
            </div>
        </template>
    </TieredMenu>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t(`public.${dialogType}`)"
        class="dialog-xs"
        :class="(dialogType === 'account_report') ? 'md:dialog-md' : 'md:dialog-sm'"
    >
        <template v-if="dialogType === 'withdrawal'">
            <AccountWithdrawal
                :account="account"
                @update:visible="visible = false"
            />
        </template>

        <template v-if="dialogType === 'change_leverage'">
            <ChangeLeverage
                :account="account"
                @update:visible="visible = false"
            />
        </template>

        <template v-if="dialogType === 'account_report'">
            <AccountReport
                :account="props.account"
                @update:visible="visible = false"
            />
        </template>
    </Dialog>

    <OverlayPanel ref="op">
        <div class="w-60 py-2 flex flex-col items-center">
            <div class="p-3 flex items-center gap-3 self-stretch hover:bg-gray-100 hover:cursor-pointer" @click="openDialog('withdrawal', withdrawalForm)">
                <IconCreditCardPay size="20" stroke-width="1.25" color="#667085" />
                <div class="text-gray-950 text-sm font-medium">
                    {{ $t('public.withdrawal') }}
                </div>
            </div>
            <div v-if="props.type == 'individual'" class="p-3 flex items-center gap-3 self-stretch hover:bg-gray-100 hover:cursor-pointer" @click="openDialog('leverage', leverageForm)">
                <IconScale size="20" stroke-width="1.25" color="#667085" />
                <div class="text-gray-950 text-sm font-medium">
                    {{ $t('public.change_leverage') }}
                </div>
            </div>
            <div v-if="props.type == 'manage'" class="p-3 flex items-center gap-3 self-stretch hover:bg-gray-100 hover:cursor-pointer" @click="requireAccountConfirmation('revoke')">
                <IconDatabaseMinus size="20" stroke-width="1.25" color="#667085" />
                <div class="text-gray-950 text-sm font-medium">
                    {{ $t('public.revoke_pamm') }}
                </div>
            </div>
            <div class="p-3 flex items-center gap-3 self-stretch hover:bg-gray-100 hover:cursor-pointer" @click="openDialog('accountReport')">
                <IconHistory size="20" stroke-width="1.25" color="#667085" />
                <div class="text-gray-950 text-sm font-medium">
                    {{ $t('public.account_report') }}
                </div>
            </div>
            <div class="h-1 self-stretch bg-gray-200"></div>
            <div class="p-3 flex items-center gap-3 self-stretch hover:bg-gray-100 hover:cursor-pointer" @click="requireAccountConfirmation('live')">
                <IconTrash size="20" stroke-width="1.25" color="#F04438" />
                <div class="text-error-500 text-sm font-medium">
                    {{ $t('public.delete_account') }}
                </div>
            </div>
        </div>
    </OverlayPanel>

    <!-- Withdrawal Dialog -->
    <Dialog v-model:visible="showWithdrawalDialog" :header="$t('public.withdrawal')" modal class="dialog-xs sm:dialog-sm">
        <form @submit.prevent="submitWithdrawal">
            <div class="flex flex-col items-center gap-8 self-stretch sm:gap-10">
                <div class="flex flex-col items-center gap-5 self-stretch">
                    <div class="flex flex-col justify-center items-center py-4 px-8 gap-2 self-stretch bg-gray-200">
                        <span class="w-full text-gray-500 text-center text-xs font-medium">#{{ props.account.meta_login}} - {{ $t('public.current_account_balance') }}</span>
                        <span class="w-full text-gray-950 text-center text-xl font-semibold">$ {{ props.account.balance}}</span>
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch">
                        <InputLabel for="amount" :value="$t('public.amount')" />
                        <div class="relative w-full">
                            <IconField iconPosition="left" class="w-full">
                                    <div class="text-gray-950 text-sm">$</div>
                                    <InputText
                                        id="amount"
                                        type="number"
                                        class="block w-full"
                                        v-model="withdrawalForm.amount"
                                        :placeholder="$t('public.amount_placeholder')"
                                        :invalid="!!withdrawalForm.errors.amount"
                                    />
                            </IconField>
                            <span
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer font-semibold"
                                :class="{
                                    'text-primary-500': !withdrawalForm.amount,
                                    'text-error-500': withdrawalForm.amount,
                                }"
                                @click="toggleFullAmount"
                            >
                                {{ withdrawalForm.amount ? $t('public.clear') : $t('public.full_amount') }}
                            </span>
                        </div>
                        <span class="self-stretch text-gray-500 text-xs">{{ $t('public.minimum_amount') }}: ${{ $t('public.minimum_amount_placeholder') }}</span>
                        <InputError :message="withdrawalForm.errors.amount" />
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch">
                        <InputLabel for="receiving_wallet" :value="$t('public.receiving_wallet')" />
                        <Dropdown
                            v-model="withdrawalForm.receiving_wallet"
                            :options="walletOptions"
                            optionLabel="name"
                            optionValue="value"
                            :placeholder="$t('public.receiving_wallet_placeholder')"
                            class="w-full"
                            scroll-height="236px"
                            :invalid="!!withdrawalForm.errors.receiving_wallet"
                        />
                        <span class="self-stretch text-gray-500 text-xs">{{ withdrawalForm.receiving_wallet }}</span>
                        <InputError :message="withdrawalForm.errors.receiving_wallet" />
                    </div>
                </div>
                <div class="self-stretch">
                    <span class="text-gray-500 text-xs">{{ $t('public.agreement_text') }} <span class="text-primary-500 text-xs font-medium" @click="openDialog('tnc')">{{ $t('public.tnc') }}</span>.</span>
                </div>
            </div>
            <div class="flex justify-end items-center pt-5 gap-4 self-stretch sm:pt-7">
                <Button type="button" variant="gray-tonal" class="w-full sm:w-[120px]" @click.prevent="closeDialog('withdrawal', withdrawalForm)">{{ $t('public.cancel') }}</Button>
                <Button variant="primary-flat" class="w-full sm:w-[120px]" @click.prevent="submitWithdrawal">{{ $t('public.confirm') }}</Button>
            </div>
        </form>
    </Dialog>

    <!-- Change Leverage Dialog -->
    <Dialog v-model:visible="showLeverageDialog" :header="$t('public.change_leverage')" modal class="dialog-xs sm:dialog-sm">
        <form @submit.prevent="submitChangeLeverage">
            <div class="flex flex-col items-center gap-8 self-stretch sm:gap-10">
                <div class="flex flex-col items-center gap-5 self-stretch">
                    <div class="flex flex-col justify-center items-center py-4 px-8 gap-2 self-stretch bg-gray-200">
                        <span class="w-full text-gray-500 text-center text-xs font-medium">#{{ props.account.meta_login}} - {{ $t('public.current_account_balance') }}</span>
                        <span class="w-full text-gray-950 text-center text-xl font-semibold">$ {{ props.account.balance}}</span>
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch">
                        <InputLabel for="leverage" :value="$t('public.leverages')" />
                        <Dropdown
                            v-model="leverageForm.leverage"
                            :options="leverages"
                            optionLabel="name"
                            optionValue="value"
                            :placeholder="$t('public.leverages_placeholder')"
                            class="w-full"
                            scroll-height="236px"
                            :invalid="leverageForm.errors.leverage"
                        />
                        <InputError :message="leverageForm.errors.leverage" />
                    </div>
                </div>
                <div class="self-stretch">
                    <span class="text-gray-500 text-xs">
                        {{ $t('public.agreement_text') }}
                        <span class="text-primary-500 text-xs font-medium">
                            {{ $t('public.trading_account_agreement') }}
                        </span>
                        .
                    </span>
                </div>
            </div>
            <div class="flex justify-end items-center pt-5 gap-4 self-stretch sm:pt-7">
                <Button type="button" variant="gray-tonal" class="w-full sm:w-[120px]" @click.prevent="closeDialog('leverage', leverageForm)">{{ $t('public.cancel') }}</Button>
                <Button variant="primary-flat" class="w-full sm:w-[120px]" @click.prevent="submitChangeLeverage">{{ $t('public.confirm') }}</Button>
            </div>
        </form>
    </Dialog>

    <!-- Account Report Dialog -->
    <Dialog v-model:visible="showAccountReportDialog" :header="`${$t('public.account_report')} #${props.account.meta_login}`" modal class="dialog-xs sm:dialog-md">
        <div class="flex flex-col items-center gap-3 self-stretch sm:gap-5">
            <div class="flex flex-col items-center self-stretch">
                <AccountReport :account="props.account" />
            </div>
        </div>
    </Dialog>

    <Dialog v-model:visible="showTnCDialog" modal :header="$t('public.tnc')" class="dialog-xs md:dialog-lg">
        <div class="flex flex-col items-center pb-4 gap-8 self-stretch md:pb-7 md:gap-10">
            <span class="self-stretch text-gray-950 text-sm"></span>
            <div class="flex flex-col items-center gap-5 self-stretch">

            </div>
        </div>
    </Dialog>

</template>
