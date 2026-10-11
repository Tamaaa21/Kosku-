/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        "primary": "#004ac6",
        "on-primary": "#ffffff",
        "primary-container": "#2563eb",
        "on-primary-container": "#eeefff",
        "inverse-primary": "#b4c5ff",
        "primary-fixed": "#dbe1ff",
        "primary-fixed-dim": "#b4c5ff",
        "on-primary-fixed": "#00174b",
        "on-primary-fixed-variant": "#003ea8",

        "secondary": "#585f67",
        "on-secondary": "#ffffff",
        "secondary-container": "#dce3ec",
        "on-secondary-container": "#5e656d",
        "secondary-fixed": "#dce3ec",
        "secondary-fixed-dim": "#c0c7d0",
        "on-secondary-fixed": "#151c23",
        "on-secondary-fixed-variant": "#40484f",

        "tertiary": "#006329",
        "on-tertiary": "#ffffff",
        "tertiary-container": "#007f36",
        "on-tertiary-container": "#c7ffca",
        "tertiary-fixed": "#7ffc97",
        "tertiary-fixed-dim": "#62df7d",
        "on-tertiary-fixed": "#002109",
        "on-tertiary-fixed-variant": "#005320",

        "error": "#ba1a1a",
        "on-error": "#ffffff",
        "error-container": "#ffdad6",
        "on-error-container": "#93000a",

        "surface": "#f8f9ff",
        "surface-dim": "#cbdbf5",
        "surface-bright": "#f8f9ff",
        "surface-container-lowest": "#ffffff",
        "surface-container-low": "#eff4ff",
        "surface-container": "#e5eeff",
        "surface-container-high": "#dce9ff",
        "surface-container-highest": "#d3e4fe",
        "surface-variant": "#d3e4fe",
        "surface-tint": "#0053db",

        "background": "#f8f9ff",
        "on-background": "#0b1c30",
        "on-surface": "#0b1c30",
        "on-surface-variant": "#434655",
        "inverse-surface": "#213145",
        "inverse-on-surface": "#eaf1ff",
        "outline": "#737686",
        "outline-variant": "#c3c6d7",
      },
      spacing: {
        "gutter": "1.5rem",
        "margin": "1.5rem",
        "space-xs": "0.25rem",
        "space-sm": "0.5rem",
        "space-md": "1rem",
        "space-lg": "1.5rem",
        "space-xl": "2rem",
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        'display-hero': ['Inter', 'sans-serif'],
      },
      borderRadius: {
        DEFAULT: '0.5rem',
        lg: '0.75rem',
        xl: '1rem',
        '2xl': '1.25rem',
        '3xl': '1.5rem',
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
