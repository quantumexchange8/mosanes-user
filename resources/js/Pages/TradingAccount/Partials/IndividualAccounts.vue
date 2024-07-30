<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/Button.vue";
import { ref, h, watch, computed, onMounted } from "vue";
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { IconDots, IconCreditCardPay, IconScale, IconHistory, IconTrash } from '@tabler/icons-vue';
</script>

<template>
<div class="w-full grid grid-cols-1 gap-5 md:grid-cols-2">
    <div class="min-w-[300px] flex flex-col justify-center items-center py-4 pl-6 pr-3 flex-grow md:pr-6 rounded-2xl border-l-8 border-success-500 bg-white shadow-toast">
            <div class="flex items-center gap-5 self-stretch">
                <div class="flex items-center content-center gap-3 md:gap-4 flex-grow flex-wrap">
                    <span class="text-gray-950 font-semibold md:text-lg">#8000381</span>
                    <StatusBadge value="success">
                        {{ 'Standard Account' }}
                    </StatusBadge>
                </div>
                <Dropdown align="left">
                    <template #trigger>
                        <Button iconOnly variant="gray-text" type="button" class="inline-flex" size="sm">
                            <IconDots size="20" stroke-width="1.25" color="#667085" />                        
                        </Button>
                    </template>
                    <template #content>
                        <!-- <DropdownLink class="inline-flex items-center gap-3 cursor-pointer" @click.prevent="openDialog('balance', item)">
                            <IconCreditCardPay size="20" stroke-width="1.25" color="#667085" />                        
                            <span class="text-gray-950 text-sm font-medium">Withdrawal</span>
                        </DropdownLink>
                        <DropdownLink class="inline-flex items-center gap-3 cursor-pointer" @click.prevent="openDialog('credit', item)">
                            <IconScale size="20" stroke-width="1.25" color="#667085" />                        
                            <span class="text-gray-950 text-sm font-medium">Change Leverage</span>
                        </DropdownLink>
                        <DropdownLink class="inline-flex items-center gap-3 cursor-pointer" @click.prevent="openDialog('credit', item)">
                            <IconHistory size="20" stroke-width="1.25" color="#667085" />                        
                            <span class="text-gray-950 text-sm font-medium">Account Report</span>
                        </DropdownLink>
                        <div class="h-1 self-stretch bg-gray-200"></div>
                        <DropdownLink class="inline-flex items-center gap-3 cursor-pointer" @click.prevent="requireConfirmation()">
                            <IconTrash size="20" stroke-width="1.25" color="#F04438" />                        
                            <span class="text-error-500 text-sm font-medium">Delete Account</span>
                        </DropdownLink> -->
                    </template>
                </Dropdown>
            </div>
            <div class="grid grid-cols-2 gap-2 self-stretch">
                <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
                    <span class="w-16 text-gray-500 text-xs">Balance:</span>
                    <span class="text-gray-950 text-xs font-medium">$ 1,372.19</span>
                </div>
                <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
                    <span class="w-16 text-gray-500 text-xs">Equity:</span>
                    <span class="text-gray-950 text-xs font-medium">$ 1,200.20</span>
                </div>
                <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
                    <span class="w-16 text-gray-500 text-xs">Credit:</span>
                    <span class="text-gray-950 text-xs font-medium">$ 0</span>
                </div>
                <div class="min-w-[140px] md:min-w-[100px] flex items-center gap-1 flex-grow">
                    <span class="w-16 text-gray-500 text-xs">Leverage:</span>
                    <span class="text-gray-950 text-xs font-medium">1:1</span>
                </div>
            </div>
            <div class="flex justify-end items-center gap-3 self-stretch"></div>
        </div>
        
    </div>
</template>