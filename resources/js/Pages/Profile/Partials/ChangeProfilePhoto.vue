<script setup>
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue"
import AvatarInput from "@/Pages/Profile/Partials/AvatarInput.vue";
import {ref} from "vue";

const profile_photo = usePage().props.auth.profile_photo;
const removeImg = ref(false);

const form = useForm({
    profile_photo: null
})

const submitForm = () => {
    form.post(route('profile.updateProfilePhoto'))
}

const removeProfilePhoto = () => {
    removeImg.value = true
};
</script>

<template>
    <div class="p-4 md:py-6 md:px-8 flex flex-col gap-5 md:gap-8 items-center self-stretch rounded-2xl shadow-toast w-full">
        <div class="flex flex-col gap-1 items-start justify-center w-full">
            <span class="text-gray-950 font-bold">{{ $t('public.change_profile') }}</span>
            <span class="text-gray-500 text-xs">{{ $t('public.change_profile_caption') }}</span>
        </div>

        <div class="flex flex-col gap-5 md:gap-8 items-center self-stretch">
            <div class="w-[100px] h-[100px] rounded-full overflow-hidden shrink-0 grow-0">
                <AvatarInput
                    class="h-24 w-24 rounded-full"
                    v-model="form.profile_photo"
                    :default-src="profile_photo ? profile_photo : ''"
                    :removeImg="removeImg"
                    @update:profile_pic="form.profile_photo = $event"
                />
            </div>

            <div class="flex items-center gap-4">
                <Button
                    type="button"
                    variant="primary-flat"
                    size="sm"
                    :disabled="form.processing"
                    @click="submitForm"
                >
                    {{ $t('public.upload') }}
                </Button>
                <Button
                    type="button"
                    variant="error-outlined"
                    size="sm"
                    :disabled="form.processing"
                    @click="removeProfilePhoto"
                >
                    {{ $t('public.remove') }}
                </Button>
            </div>

            <span class="text-xs text-gray-400">{{ $t('public.change_profile_help_text') }}</span>
        </div>
    </div>
</template>
