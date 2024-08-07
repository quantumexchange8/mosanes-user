<script setup>
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import Dialog from "primevue/dialog";
import AccountWithdrawal from "@/Pages/TradingAccount/Partials/AccountWithdrawal.vue";
import WalletWithdrawal from "@/Pages/Dashboard/Partials/WalletWithdrawal.vue";

const props = defineProps({
    rebateWallet: Object
})

const visible = ref(false);
const dialogType = ref('');

const openDialog = (type) => {
    dialogType.value = type;
    visible.value = true;
}
</script>

<template>
    <div class="bg-white py-7 px-10 flex gap-5 items-center self-stretch z-20">
        <Button
            type="button"
            variant="gray-outlined"
            class="w-full"
            @click="openDialog('transfer')"
            :disabled="!rebateWallet"
        >
            {{ $t('public.transfer') }}
        </Button>
        <Button
            type="button"
            variant="gray-outlined"
            class="w-full"
            @click="openDialog('withdrawal')"
            :disabled="!rebateWallet"
        >
            {{ $t('public.withdrawal') }}
        </Button>
    </div>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t(`public.${dialogType}`)"
        class="dialog-xs md:dialog-sm"
    >
        <template v-if="dialogType === 'withdrawal'">
            <WalletWithdrawal
                :rebateWallet="rebateWallet"
                @update:visible="visible = false"
            />
        </template>
    </Dialog>
</template>
