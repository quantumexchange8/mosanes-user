<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/Button.vue";
import { ref, h, watch, onMounted } from "vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import IndividualAccounts from '@/Pages/TradingAccount/Partials/IndividualAccounts.vue';
import ManagedAccounts from '@/Pages/TradingAccount/Partials/ManagedAccounts.vue';
import DemoAccounts from '@/Pages/TradingAccount/Partials/DemoAccounts.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import Dialog from 'primevue/dialog';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Dropdown from "primevue/dropdown";
import { IconCircleCheckFilled } from '@tabler/icons-vue';

// Initialize the form with user data
const user = usePage().props.auth.user;

const form = useForm({
    user_id: user.id,
    accountType: '',
    leverage: '',
});

const tabs = ref([
    { title: 'Individual', component: h(IndividualAccounts), type: 'deposit' },
    { title: 'Managed', component: h(ManagedAccounts), type: 'withdrawal' },
    { title: 'Demo', component: h(DemoAccounts), type: 'transfer' },
]);

const selectedType = ref('deposit');
const activeIndex = ref(tabs.value.findIndex(tab => tab.type === selectedType.value));

// Watch for changes in selectedType and update the activeIndex accordingly
watch(selectedType, (newType) => {
    const index = tabs.value.findIndex(tab => tab.type === newType);
    if (index >= 0) {
        activeIndex.value = index;
    }
});

function updateType(event) {
    const selectedTab = tabs.value[event.index];
    selectedType.value = selectedTab.type;
}

const visible = ref(false);

// Functions to open and close the dialog
const openDialog = () => {
    visible.value = true;
};

const closeDialog = () => {
    visible.value = false;
    selectedAccountType.value = '';
    form.reset();
};

const leverages = [
    { label: '1:50', value: '50' },
    { label: '1:100', value: '100' },
    { label: '1:200', value: '200' },
    { label: '1:500', value: '500' }
];

const selectedAccountType = ref('');

// // Define the account options
// const accountOptions = [
//     { type: 'standard', label: 'Standard Account', description: 'Versatile account with flexible spreads, suitable for traders of all levels and trading strategies.' },
//     { type: 'premium', label: 'Premium Account', description: 'This account enabling high-quality asset master to manage your funds in the market.' }
// ];

const accountOptions = ref([]);

onMounted(async () => {
    try {
        const response = await fetch(route('account.accountOptions'));
        const data = await response.json();
        accountOptions.value = data;
    } catch (error) {
        console.error('Error fetching account options:', error);
    }
});

// Handle selection of an account
function selectAccount(type) {
    selectedAccountType.value = type;
    form.accountType = type;
    
    // Find selected account and update leverage
    const selectedAccount = accountOptions.value.find(account => account.account_group === type);
    if (selectedAccount && selectedAccount.leverage) {
        form.leverage = selectedAccount.leverage;
    } else {
        form.leverage = ''; // Clear leverage if no value
    }
}

const openLiveAccount = () => {
    form.post(route('account.create_live_account'), {
        onSuccess: () => {
            closeDialog();
        },
        onError: () => {
            console.error('Failed to update data.');
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="$t('public.transaction')">
        <div class="flex flex-col pt-3 gap-20 md:pt-5 md:gap-[100px]">
            <div class="flex flex-col items-center px-3 gap-5 self-stretch md:px-5">
                <!-- banner -->
                <div class="h-[260px] self-stretch rounded-2xl bg-white shadow-toast md:h-60 bg-[url('/img/background-account-banner.svg')] bg-no-repeat bg-right-bottom">
                    <!-- graphic -->
                    <!-- Content -->
                    <div class="w-[304px] flex flex-col items-center gap-5 md:w-[450px] md:gap-8 md:items-start lg:w-[454px] xl:w-[643px]">
                        <div class="flex flex-col justify-center items-start gap-2 self-stretch">
                            <span class="self-stretch text-gray-950 font-bold md:text-lg">Start Trading Today: Open Your Account Now</span>
                            <span class="self-stretch text-gray-700 text-xs md:text-sm">Explore seamless investing opportunities today. Begin in minutes with our simple, secure account setup.</span>
                        </div>
                        <div class="flex flex-col justify-center items-start gap-3 self-stretch md:flex-row md:justify-end md:items-center md:gap-5">
                            <Button size="sm" variant="primary-flat" class="w-[142px] md:hidden" @click="openDialog">Live Account</Button>
                            <Button size="base" variant="primary-flat" class="hidden md:block w-full" @click="openDialog">Live Account</Button>
                            <Button size="sm" variant="primary-outlined" class="w-[142px] md:hidden">Demo Account</Button>
                            <Button size="base" variant="primary-outlined" class="hidden md:block w-full">Demo Account</Button>
                        </div>
                    </div>
                </div>

                <!-- tab -->
                <div class="flex items-center gap-3 self-stretch">
                    <TabView class="flex flex-col" :activeIndex="activeIndex" @tab-change="updateType">
                        <TabPanel v-for="(tab, index) in tabs" :key="index" :header="tab.title" />
                    </TabView>
                </div>

                <component :is="tabs[activeIndex]?.component" />
            </div>
            
            <div class="flex flex-col items-center p-5 gap-3 self-stretch bg-gray-100">
                <div class="flex flex-col items-start gap-1 self-stretch">
                    <span class="text-gray-700 text-xs font-semibold"></span>
                    <span class="self-stretch text-gray-500 text text-xxs md:text-xs"></span>
                </div>
                <span class="self-stretch text-gray-500 text-xxs md:text-xs"></span>
            </div>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal header="Open Live Account" class="dialog-xs sm:dialog-sm">
        <div class="flex flex-col items-center gap-8 self-stretch sm:gap-10">
            <div class="flex flex-col items-center gap-5 self-stretch">
                <div class="flex flex-col items-start gap-2 self-stretch">
                    <InputLabel for="accountType" value="Select Account" />
                    <div class="flex flex-col items-start gap-3 self-stretch">
                        <div 
                            v-for="account in accountOptions" 
                            :key="account.account_group" 
                            @click="selectAccount(account.account_group)"
                            class="group flex flex-col items-start py-3 px-4 gap-1 self-stretch rounded-lg border shadow-input transition-colors duration-300"
                            :class="{
                                'bg-primary-50 border-primary-500': selectedAccountType === account.account_group,
                                'bg-white border-gray-300 hover:bg-primary-50 hover:border-primary-500': selectedAccountType !== account.account_group
                            }"
                        >
                            <div class="flex items-center gap-3 self-stretch">
                                <span 
                                    class="flex-grow text-sm font-semibold transition-colors duration-300 group-hover:text-primary-700"
                                    :class="{
                                        'text-primary-700': selectedAccountType === account.account_group,
                                        'text-gray-950': selectedAccountType !== account.account_group
                                    }"
                                >
                                    {{ account.name }}
                                </span>
                                <IconCircleCheckFilled v-if="selectedAccountType === account.account_group" size="20" stroke-width="1.25" color="#2970FF" />
                            </div>
                            <span 
                                class="self-stretch text-xs transition-colors duration-300 group-hover:text-primary-500"
                                :class="{
                                    'text-primary-500': selectedAccountType === account.account_group,
                                    'text-gray-500': selectedAccountType !== account.account_group
                                }"
                            >
                                {{ account.descriptions }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-start gap-1 self-stretch">
                    <InputLabel for="leverage" value="Leverage" />
                    <Dropdown
                        v-model="form.leverage"
                        :options="leverages"
                        optionLabel="label"
                        placeholder="Select"
                        class="w-full"
                        scroll-height="236px"
                        :invalid="form.errors.leverage"
                        :disabled="!!accountOptions.find(account => account.account_group === selectedAccountType)?.leverage"
                    />
                </div>
            </div>
            <div class="self-stretch">
                <span class="text-gray-500 text-xs">By proceeding, I acknowledge that I have read and agree to the </span>
                <span class="text-primary-500 text-xs font-medium">Trading Account Agreement.</span>
            </div>
        </div>
        <div class="flex justify-end items-center pt-5 gap-4 self-stretch">
            <Button variant="primary-flat" type="button" @click.prevent="openLiveAccount">Open Live Account</Button>
        </div>
    </Dialog>

</template>
