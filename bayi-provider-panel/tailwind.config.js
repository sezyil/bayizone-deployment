/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      backgroundColor: {
        "bayi-red": "#ff5c5c",
        "bayi-red-hover": "#ff7676",
      },
      gradientColorStops: (theme) => ({
        "auth-gradient-start": "#ff5c5c",
        //darker
        "auth-gradient-end": "#c32020",
      }),
    },
  },
  plugins: [],
};
