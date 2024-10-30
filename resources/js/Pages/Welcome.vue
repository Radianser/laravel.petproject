<script setup>
    import { Head } from '@inertiajs/vue3';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import LoginForm from '@/Components/LoginForm.vue';
    import RegisterForm from '@/Components/RegisterForm.vue';
    import { ref } from 'vue';

    let props = defineProps({
        canResetPassword: {
            type: Boolean,
        },
        status: {
            type: String,
        },
        localization: {
            type: Object,
        },
        session: {
            type: Object,
            required: true,
        },
    });

    const switcher = ref(true);
</script>

<template>
    <Head :title="localization[session.language].welcome" />
    <GuestLayout :localization="localization" :session="session">
        <div
            class="flex justify-center items-center h-[calc(100vh-64px)]"
        >
            <div class="max-w-4xl p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-6 lg:gap-8">

                    <!-- picture -->
                    <img v-if="session.theme == 0"
                        src="/storage/icons/welcome_light.webp"
                        class="h-full object-cover sm:block hidden row-start-1 row-end-4 col-start-1 col-end-1 scale-100 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                    >
                    <img v-else
                        src="/storage/icons/welcome_dark.webp"
                        class="h-full object-cover sm:block hidden row-start-1 row-end-4 col-start-1 col-end-1 scale-100 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                    >

                    <div class="grid grid-rows-[auto_1fr] gap-6 lg:gap-8 row-start-1 row-end-4 col-start-2 col-end-2 mt-1">
                        <!-- switcher -->
                        <div
                            class="scale-100 p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                        >
                            <div class="text-lg">
                                <span class="cursor-pointer hover:underline decoration-additional decoration-2" @click="switcher = true">
                                    {{ localization[session.language]['log_in'] }}
                                </span>
                                /
                                <span class="cursor-pointer hover:underline decoration-additional decoration-2" @click="switcher = false">
                                    {{ localization[session.language]['register'] }}
                                </span>
                            </div>
                        </div>

                        <!-- login -->
                        <div
                            class="flex flex-col h-fit scale-100 p-6 rounded-xl text-darker dark:text-light bg-light-primary dark:bg-dark-primary shadow dark:shadow-none dark:border dark:border-dark flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                        >
                            <span class="mb-6 text-lg font-semibold">{{ switcher == true ? localization[session.language]['log_in'] : localization[session.language]['register'] }}</span>
                            <LoginForm v-if="switcher"
                                :canResetPassword="canResetPassword"
                                :status="status"
                                :localization="localization"
                                :session="session"
                            />
                            <RegisterForm v-if="!switcher"
                                :localization="localization"
                                :session="session"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>