/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./app/sprinkles/**/templates/*/**/*.html.twig"],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Geist']
      }
    }
  },
  plugins: [],
}