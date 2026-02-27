import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Color principal IMD: #017C80 (turquesa/teal)
                primary: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#017C80',  // Color principal
                    600: '#016668',
                    700: '#015155',
                    800: '#014144',
                    900: '#013639',
                    950: '#002328',
                },
                // Reemplazar los colores indigo/blue de Jetstream con nuestro color
                indigo: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#017C80',
                    600: '#016668',
                    700: '#015155',
                    800: '#014144',
                    900: '#013639',
                    950: '#002328',
                },
            },
        },
    },

    plugins: [forms, typography],
};
