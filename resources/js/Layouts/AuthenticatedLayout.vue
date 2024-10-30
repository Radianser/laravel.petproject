<script setup>
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import { Link, useForm, usePage } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import library from '@/myLibraryObject.js';
    import { useSessionStore } from '@/Stores/sessionStore.js';

    let props = defineProps(['localization', 'session']);
    const showingNavigationDropdown = ref(false);
    const pixelsToTop = ref(0);
    const anchor = ref(0);
    const screenWidth = ref(window.screen.width);
    let html = document.querySelector('html');
    const store = useSessionStore();
    let url = window.location.href;

    store.theme = computed(() => {return props.session.theme});
    store.language = computed(() => {return props.session.language});
    store.sorting = computed(() => {return usePage().props.auth.user.sorting});

    const form = useForm({
        theme: computed(() => {return store.theme}),
        language: computed(() => {return store.language}),
        sorting: computed(() => {return store.sorting}),
    });

    ;(() => {
        window.addEventListener('scroll', library.throttle(checkPosition, 1000));
        window.addEventListener('resize', library.throttle(() => {
            screenWidth.value = window.screen.width;
        }, 1000));
    })();
    function checkPosition() {
        pixelsToTop.value = Math.round(document.documentElement.scrollTop);

        if(pixelsToTop.value > 0) {
            anchor.value = 0;
        }
    }
    function scrollTo() {
        if(pixelsToTop.value > 0) {
            anchor.value = pixelsToTop.value;
            document.documentElement.scrollTop = 0;
        } else {
            if(anchor.value != 0) {
                document.documentElement.scrollTop = anchor.value;
            }
        }
    }
    function toggleResponsiveNavMenu() {
        showingNavigationDropdown.value = !showingNavigationDropdown.value;
        html.classList.toggle('overflow-hidden');
    }
    function toggle(param, target) {
        setTimeout(() => {
            if(param == 'theme') {
                props.session.theme = target.previousElementSibling.checked ? 1 : 0;
            }
            
            if(param == 'language') {
                props.session.language = target.previousElementSibling.checked ? 'ru' : 'en';
            }

            axios.put(route('profile.update_session'), form);
        }, 0);
    }
    
    // console.log(store.language);
</script>

<template>
    <div>
        <div class="min-h-screen bg-light dark:bg-dark" :class="{ 'dark': store.theme }">
            <div v-if="showingNavigationDropdown" @click="toggleResponsiveNavMenu()" class="fixed left-0 top-0 w-full h-screen bg-black opacity-30 z-10"></div>
            <nav class="fixed top-0 left-0 w-screen bg-white shadow z-20 dark:shadow-none dark:bg-dark-primary dark:border-b dark:border-dark">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home.index')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-dark dark:text-light"
                                    />
                                </Link>
                            </div>
                            <!-- Navigation Links -->
                            <div v-if="$page.props.auth.user" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('feed.show')" :active="route().current('feed.show')">
                                    {{ localization[session.language].feed }}
                                </NavLink>
                                <NavLink :href="route('home.index')" :active="route().current('home.index')">
                                    {{ localization[session.language].home }}
                                </NavLink>
                                <NavLink :href="route('messenger')" :active="route().current('messenger')">
                                    {{ localization[session.language].messenger }}
                                </NavLink>
                                <NavLink :href="route('gallery', usePage().props.auth.user.id)" :active="route('gallery', usePage().props.auth.user.id) == url">
                                    {{ localization[session.language].gallery }}
                                </NavLink>
                                <NavLink :href="route('search.index')" :active="route().current('search.index')">
                                    {{ localization[session.language].search }}
                                </NavLink>
                            </div>
                        </div>
                        <div v-if="$page.props.auth.user" class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown>
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-dark dark:text-light hover:text-darker dark:hover:text-lighter focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}
                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> {{ localization[session.language].profile }} </DropdownLink>
                                        <DropdownLink @click.stop="">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    {{ localization[session.language]['language'] }}
                                                </div>
                                                <div>
                                                    <input class="dropdown-input hidden" type="checkbox" id="language-switch" :checked="store.language === 'ru'"/>
                                                    <label class="dropdown-label" for="language-switch" @click="toggle('language', $event.target)"></label>
                                                </div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink @click.stop="">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    {{ localization[session.language]['theme'] }}
                                                </div>
                                                <div>
                                                    <input class="dropdown-input hidden" type="checkbox" id="theme-switch" :checked="store.theme === 1"/>
                                                    <label class="dropdown-label" for="theme-switch" @click="toggle('theme', $event.target)"></label>
                                                </div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            {{ localization[session.language].logout }}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="toggleResponsiveNavMenu()"
                                class="inline-flex items-center justify-center p-2 mr-2 rounded-md text-dark dark:text-light hover:text-darker dark:hover:text-lighter hover:bg-light dark:hover:bg-dark-hover focus:outline-none focus:bg-light dark:focus:bg-dark-hover focus:text-darker dark:focus:text-lighter transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="md:hidden"
                >
                    <div v-if="$page.props.auth.user" class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('feed.show')" :active="route().current('feed.show')">
                            {{ localization[session.language].feed }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('home.index')" :active="route().current('home.index')">
                            {{ localization[session.language].home }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('messenger')" :active="route().current('messenger')">
                            {{ localization[session.language].messenger }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('gallery', usePage().props.auth.user.id)" :active="route('gallery', usePage().props.auth.user.id) == url">
                            {{ localization[session.language].gallery }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('search.index')" :active="route().current('search.index')">
                            {{ localization[session.language].search }}
                        </ResponsiveNavLink>
                    </div>
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-light dark:border-dark">
                        <div class="px-4">
                            <div class="font-medium text-base text-darker dark:text-lighter">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-dark dark:text-light">{{ $page.props.auth.user.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('profile.edit')" :active="route().current('profile.edit')">
                                {{ localization[session.language].profile }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink @click.stop="">
                                <div class="flex justify-between items-center">
                                    <div>
                                        {{ localization[session.language]['language'] }}
                                    </div>
                                    <div>
                                        <input class="dropdown-input hidden" type="checkbox" id="language-switch-mobile" :checked="store.language === 'ru'"/>
                                        <label class="dropdown-label" for="language-switch-mobile" @click="toggle('language', $event.target)"></label>
                                    </div>
                                </div>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink @click.stop="">
                                <div class="flex justify-between items-center">
                                    <div>
                                        {{ localization[session.language]['theme'] }}
                                    </div>
                                    <div>
                                        <input class="dropdown-input hidden" type="checkbox" id="theme-switch-mobile" :checked="store.theme === 1"/>
                                        <label class="dropdown-label" for="theme-switch-mobile" @click="toggle('theme', $event.target)"></label>
                                    </div>
                                </div>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink @click="toggleResponsiveNavMenu()" :href="route('logout')" method="post" as="button">
                                {{ localization[session.language].logout }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <!-- <main class="pt-16">
                <slot />
            </main> -->

            <main class="min-h-screen pt-16 gap-0 xl:gap-4" :class="{ 'grid grid-cols-main': screenWidth > 1280, 'block': screenWidth <= 1280 }">
                <div v-if="screenWidth > 1280">
                    <div
                        class="fixed left-0 top-0 w-28 h-full text-darker dark:text-light bg-button-to-top bg-no-repeat bg-top bg-center dark:bg-dark-primary opacity-0 hover:opacity-80 cursor-pointer overflow-hidden transition-all"
                        :class="{
                            'hidden': pixelsToTop == 0 && anchor == 0,
                            'lg:block': pixelsToTop != 0 || anchor != 0,
                            'bg-icon-up': pixelsToTop != 0,
                            'bg-icon-down': pixelsToTop == 0,
                        }"
                        @click="scrollTo()"
                    ></div>
                </div>
                <slot />
                <div v-if="screenWidth > 1280"></div>
            </main>
        </div>
    </div>
</template>