/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Filament/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#65cb24',
          light: '#7dd52f',
          dark: '#52a61d',
        },
        dark: {
          DEFAULT: '#1a1a1a',
          light: '#2a2a2a',
          lighter: '#3a3a3a',
        },
        accent: {
          gold: '#FFD700',
          orange: '#FFA500',
        }
      },
    },
  },
  plugins: [],
}
