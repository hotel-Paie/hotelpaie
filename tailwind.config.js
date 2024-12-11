/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./*.php', './Pages/**/*.php', './**/*.php'],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}