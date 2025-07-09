/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
    ],
    theme: {
        extend: {},
    },
    plugins: [require("daisyui")],
    daisyui: {
        themes: ["light", "dark"],
    },
};
