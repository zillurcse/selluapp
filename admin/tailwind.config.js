export default {
    darkMode: 'class',
    content: [
        "./app/**/*.vue",
        "./app/**/*.js",
        "./app/**/*.ts",
        "./app/**/*.mjs",
        "./app/app.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#6366f1',
                    700: '#4f46e5',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                }
            }
        },
    },
    plugins: [],
}
