/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                'ocean-dark': '#041C32',
                'ocean-mid': '#04293A',
                'ocean-light': '#064663',
                'coral-accent': '#ECB365',
            },
            fontFamily: {
                sans: ['Inter', 'Noto Sans Bengali', 'Hind Siliguri', 'system-ui', 'sans-serif'],
                display: ['Sora', 'Noto Sans Bengali', 'Hind Siliguri', 'sans-serif'],
            },
            boxShadow: {
                glow: '0 0 20px rgba(6, 70, 99, 0.45)',
                'glow-coral': '0 0 24px rgba(236, 179, 101, 0.35)',
            },
            animation: {
                'bubble-rise': 'bubble-rise 8s ease-in-out infinite',
                'fade-in': 'fade-in 0.6s ease-out',
            },
            keyframes: {
                'bubble-rise': {
                    '0%': { transform: 'translateY(0)', opacity: '0.6' },
                    '100%': { transform: 'translateY(-120vh)', opacity: '0' },
                },
                'fade-in': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
