<script setup>
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import {usePage} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue"

const profile_photo = usePage().props.auth.profile_photo;
</script>

<template>
    <div class="p-4 md:py-6 md:px-8 flex flex-col gap-5 md:gap-8 items-center self-stretch rounded-2xl shadow-toast w-full">
        <div class="flex flex-col gap-1 items-start justify-center w-full">
            <span class="text-gray-950 font-bold">{{ $t('public.change_profile') }}</span>
            <span class="text-gray-500 text-xs">{{ $t('public.change_profile_caption') }}</span>
        </div>

        <div class="flex flex-col gap-5 md:gap-8 items-center self-stretch">
            <div class="w-[100px] h-[100px] rounded-full overflow-hidden shrink-0 grow-0">
                <div v-if="profile_photo">
                    <img :src="profile_photo" alt="Profile Photo" />
                </div>
                <div v-else>
                    <DefaultProfilePhoto />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button
                    type="button"
                    variant="primary-flat"
                    size="sm"
                >
                    {{ $t('public.upload') }}
                </Button>
                <Button
                    type="button"
                    variant="error-outlined"
                    size="sm"
                >
                    {{ $t('public.remove') }}
                </Button>
            </div>

            <span class="text-xs text-gray-400">{{ $t('public.change_profile_help_text') }}</span>
        </div>
    </div>
</template>
