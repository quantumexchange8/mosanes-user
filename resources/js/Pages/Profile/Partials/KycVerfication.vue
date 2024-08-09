<script setup>
import {ref} from "vue";
import Button from "@/Components/Button.vue"

const kycVerification = ref('');
</script>

<template>
    <div class="p-4 md:py-6 md:px-8 flex flex-col gap-5 md:gap-0 md:justify-between items-end self-stretch rounded-2xl shadow-toast w-full">
        <div class="flex flex-col gap-1 items-start justify-center w-full">
            <span class="text-gray-950 font-bold">{{ $t('public.kyc_verification') }}</span>
            <span class="text-gray-500 text-xs">{{ $t('public.kyc_verification_caption') }}</span>
        </div>

        <div class="flex flex-col gap-5 md:gap-8 items-center self-stretch">
            <div
                class="px-3 py-6 flex gap-5 items-center self-stretch select-none md:min-h-[106px] cursor-pointer rounded-xl bg-gray-50 hover:bg-gray-100"
            >
                <img
                    :src="kycVerification.url || '/img/member/kyc_example_preview.svg'"
                    class="w-12 h-9"
                    alt="kyc_verification"
                />
                <div class="truncate text-gray-950 font-medium w-full">
                    {{ kycVerification.name ? kycVerification.name : $t('public.image') + '.jpg' }}
                </div>
            </div>
        </div>

        <div class="flex justify-end w-full">
            <Button
                type="button"
                variant="primary-flat"
                disabled
            >
                {{ $t('public.submit_again') }}
            </Button>
        </div>
    </div>
</template>
