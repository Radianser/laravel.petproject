<script>
    import { defineComponent } from "vue";
    import { Link } from "@inertiajs/vue3";
    export default defineComponent({
        components: {
            Link,
        },
        props: {
            links: Array,
        },
    });
</script>

<template>
    <!-- <div v-if="links.length > 3" class="mt-2 bg-white rounded-lg"> -->
        <div class="flex flex-wrap justify-center p-1.5">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="mr-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded cursor-default select-none"
                    v-html="link.label"
                />

                <Link
                    v-else
                    class="mr-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-700 hover:text-white focus:border-primary focus:text-primary select-none"
                    :class="{ 'bg-gray-800 text-white': link.active }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    <!-- </div> -->
</template>