<script setup>
    import { usePage } from '@inertiajs/vue3';
    import library from '@/myLibraryObject.js';

    const props = defineProps(['object', 'objectType']);

    Echo.channel('likes-count-change').listen('LikesCountChange', (e) => {
        if(props.object.id == e.likes.likeable_id && props.objectType == e.likes.likeable_type) {
            if(!e.likes.response) {
                for(let i = 0; i < props.object.likes.length; i++) {
                    if(props.object.likes[i].id == e.likes.user.id) {
                        props.object.likes.splice(i, 1);
                    }
                }
            } else {
                props.object.likes.push(e.likes.user);
            }
        }
    });

    function smashTheLike(objectId) {
        let authUser = usePage().props.auth.user;
        if(!usePage().props.auth.user) {
            window.location.href = route('login');
            return;
        }

        let data = {
            likeable_id: objectId,
            likeable_type: props.objectType,
            user_id: authUser.id
        };

        axios.post(route('like.store'), data)
        .then(result => {
            if(result.data == false) {
                for(let i = 0; i < props.object.likes.length; i++) {
                    if(props.object.likes[i].id == authUser.id) {
                        props.object.likes.splice(i, 1);
                    }
                }
            } else {
                let user = {
                    id: authUser.id,
                    image: authUser.image,
                    last_action: authUser.last_action,
                    name: authUser.name
                };

                props.object.likes.push(user);
            }
        });
    }
    function checkTheLike(likes) {
        let flag = false;
        
        if(likes) {
            if(usePage().props.auth.user) {
                if(likes.length > 0) {
                    likes.forEach(user => {
                        if(user.id == usePage().props.auth.user.id) {
                            flag = true;
                        }
                    });
                }
            }
        }
        

        return flag;
    }
    
    // console.log(usePage().props.auth.user);
</script>
 
<template>
    <div class="text-gray-400 flex items-center text-[11px]">
        <a v-if="object.likes != null && object.likes.length > 0" :href="'/likes/' + objectType + '/' + object.id" class="mr-1 font-bold">
            {{ library.roundValue(object.likes.length) }}
        </a>
        <div v-if="checkTheLike(object.likes)" @click="smashTheLike(object.id)" class="h-4 w-4 cursor-pointer bg-heart-red hover:bg-heart-pink bg-no-repeat bg-center bg-contain"></div>
        <div v-else @click="smashTheLike(object.id)" class="h-4 w-4 cursor-pointer bg-heart-gray hover:bg-heart-pink bg-no-repeat bg-center bg-contain"></div>
    </div>
</template>