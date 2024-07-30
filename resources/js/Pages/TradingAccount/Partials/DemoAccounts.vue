<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/Button.vue";
import { ref, h, watch, computed, onMounted } from "vue";

</script>

<template>
    <div class="flex flex-col items-center px-3 gap-5 self-stretch md:px-5">
        <!-- banner -->
        <div class="h-[260px] self-stretch rounded-2xl bg-white shadow-toast md:h-60">
            <!-- graphic -->
            <!-- Content -->
            <div class="w-[304px] flex flex-col items-center gap-5 md:w-[450px] md:gap-8 md:items-start lg:w-[454px] xl:w-[643px]">
                <div class="flex flex-col justify-center items-start gap-2 self-stretch">
                    <span class="self-stretch text-gray-950 font-bold md:text-lg">Start Trading Today: Open Your Account Now</span>
                    <span class="self-stretch text-gray-700 text-xs md:text-sm">Explore seamless investing opportunities today. Begin in minutes with our simple, secure account setup.</span>
                </div>
                <div class="flex flex-col justify-center items-start gap-3 self-stretch md:flex-row md:justify-end md:items-center md:gap-5">
                    <Button size="sm" variant="primary-flat" class="w-[142px] md:hidden">Live Account</Button>
                    <Button size="base" variant="primary-flat" class="hidden md:block w-full">Live Account</Button>
                    <Button size="sm" variant="primary-outlined" class="w-[142px] md:hidden">Demo Account</Button>
                    <Button size="base" variant="primary-outlined" class="hidden md:block w-full">Demo Account</Button>
                </div>
            </div>
        </div>

    </div>
    
    <div class="flex flex-col items-center p-5 gap-3 self-stretch bg-gray-100">
        <div class="flex flex-col items-start gap-1 self-stretch">
            <span class="text-gray-700 text-xs font-semibold"></span>
            <span class="self-stretch text-gray-500 text text-xxs md:text-xs"></span>
        </div>
        <span class="self-stretch text-gray-500 text-xxs md:text-xs"></span>
    </div>
</template>