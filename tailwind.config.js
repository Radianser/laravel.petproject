import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: 'selector',

    theme: {
        extend: {
            maxHeight: {
                'pc': 'calc(100vh - 80px)',
                'phone': '100vh',
            },
            height: {
                'pc': 'calc(100vh - 80px)',
                'phone': '100vh',
            },
            backgroundImage: {
                'heart-gray': "url('/storage/icons/like-gray.svg')",
                'heart-pink': "url('/storage/icons/like-pink.svg')",
                'heart-red': "url('/storage/icons/like-red.svg')",
                'answer-gray': "url('/storage/icons/answer-gray.svg')",
                'answer-light-gray': "url('/storage/icons/answer-light-gray.svg')",
                'magnifier': "url('/storage/icons/magnifier.svg')",
                'icon-up': "url('/storage/icons/arrow_up.svg')",
                'icon-down': "url('/storage/icons/arrow_down.svg')",

                'me': "url('/storage/icons/welcome.png')",
            },
            backgroundPosition: {
                bottom: 'bottom',
                center: 'center',
                left: 'left',
                right: 'right',
                top: 'center 10%',
            },
            screens: {
                'ph': '450px',
            },
            colors: {
                'dark': 'rgb(107 114 128)', //text-gray-500
                'darker': 'rgb(55 65 81)', //text-gray-700
                'light': 'rgb(209 213 219)', //text-gray-300
                'lighter': 'rgb(243 244 246)', //text-gray-100

                'additional-light': 'rgb(238 242 255)', //bg-indigo-50
                'additional': 'rgb(129 140 248)', //bg-indigo-400
                'additional-dark': 'rgb(67 56 202)', //bg-indigo-700
            },
            backgroundColor: {
                'cover-change-button': 'rgba(64, 64, 64, 1)', //bg-neutral-700
                'my-hover-bg': 'rgba(64, 64, 64, 0.3)', //bg-neutral-700

                'dark-input': 'rgb(38 38 38)', //bg-neutral-800
                'button-to-top': 'rgb(229 231 235)', //bg-gray-200

                'light-primary': 'white', //bg-white
                'light-secondary': 'rgb(23 23 23)', //bg-neutral-900
                'light': 'rgb(243 244 246)', //bg-gray-100

                'cover': 'rgb(229 231 235)', //bg-gray-100

                'dark-primary': 'rgb(23 23 23)', //bg-neutral-900
                'dark-secondary': 'rgb(243 244 246)', //bg-gray-100
                'dark': 'rgb(10 10 10)',  //bg-neutral-950
                'dark-hover': 'rgb(38 38 38)', //bg-neutral-800

                'additional-light': 'rgb(238 242 255)', //bg-indigo-50
                'additional': 'rgb(129 140 248)', //bg-indigo-400
                'additional-dark': 'rgb(67 56 202)', //bg-indigo-700
            },
            borderColor: {
                'light': 'rgb(209 213 219)', //bg-gray-300

                'light-primary': 'white', //bg-white
                'dark-primary': 'rgb(23 23 23)', //bg-neutral-900
                'dark': 'rgb(38 38 38)', //bg-neutral-800

                'additional-light': 'rgb(238 242 255)', //bg-indigo-50
                'additional': 'rgb(129 140 248)', //bg-indigo-400
                'additional-dark': 'rgb(67 56 202)', //bg-indigo-700
            },
            ringColor: {
                'dark': 'rgb(38 38 38)', //bg-neutral-800
                'light-primary': 'white', //bg-white
                'dark-primary': 'rgb(23 23 23)', //bg-neutral-900
                'additional': 'rgb(129 140 248)', //bg-indigo-400
            },
            ringOffsetColor: {
                'additional': 'rgb(129 140 248)', //bg-indigo-400
                'light': 'white', //bg-white
                'dark': 'rgb(23 23 23)', //bg-neutral-900
            },
            textDecorationColor: {
                'additional': 'rgb(129 140 248)', //bg-indigo-400
            },
            gridTemplateColumns: {
                'main': 'auto 80% auto',
                // Complex site-specific column configuration
                'babbleBottom': '7fr 1fr',
              }
        },
    },
    
    plugins: [forms],
};