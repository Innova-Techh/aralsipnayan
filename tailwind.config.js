/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    prefix: "",
    theme: {
        container: {
            center: true,
            padding: "2rem",
            screens: {
                "2xl": "1400px",
            },
        },
        extend: {
            screens: {
                xs: "375px", // Small phones
                sm: "425px", // Larger phones
                md: "640px", // Small tablets (was sm by default)
                lg: "768px", // iPads
                xl: "1024px", // Larger tablets / laptops
                "2xl": "1280px",
            },
            fontFamily: {
                baloo: ['"Baloo 2"', "cursive"],
            },
            colors: {
                "primary-blue": "#1E3A8A",
                "primary-red": "#D62839",
                "primary-yellow": "#FBBF24",
                "custom-purple": "#4F46E5",
                "custom-cyan": "#06B6D4",
                border: "#E2E8F0", // hsl(var(--border)) replaced with hex
                input: "#E2E8F0", // hsl(var(--input)) replaced with hex
                ring: "#E2E8F0", // hsl(var(--ring)) replaced with hex
                background: "#F8FAFC", // hsl(var(--background)) replaced with hex
                foreground: "#1E293B", // hsl(var(--foreground)) replaced with hex
                primary: {
                    DEFAULT: "#449EFF", // Primary Accent
                    foreground: "#ffffff",
                    50: "#f0f9ff",
                    100: "#e0f2fe",
                    200: "#bae6fd",
                    300: "#7dd3fc",
                    400: "#38bdf8",
                    500: "#449EFF",
                    600: "#0284c7",
                    700: "#0369a1",
                    800: "#075985",
                    900: "#0c4a6e",
                    accent: "#449EFF", // Primary Accent
                    first: "#1E3A8A", // Primary Base
                },
                secondary: {
                    DEFAULT: "#F59E0B", // Complementary
                    foreground: "#1E293B",
                    50: "#fffbeb",
                    100: "#fef3c7",
                    200: "#fde68a",
                    300: "#fcd34d",
                    400: "#fbbf24",
                    500: "#F59E0B",
                    600: "#d97706",
                    700: "#b45309",
                    800: "#92400e",
                    900: "#78350f",
                    light: "#F8FAFC", // Secondary Light
                    medium: "#E2E8F0", // Secondary Medium
                },
                accent: {
                    DEFAULT: "#F8FAFC", // Secondary Light
                    foreground: "#1E293B",
                    cyan: "#06B6D4", // Accent Cyan
                    navy: "#1E293B", // Accent Navy
                },
                muted: {
                    DEFAULT: "#E2E8F0", // Secondary Medium
                    foreground: "#1E293B",
                },
                dark: {
                    DEFAULT: "#1E293B", // Accent Navy
                    foreground: "#F8FAFC",
                },
                blue: {
                    bright: "#449EFF", // Primary Accent
                    deep: "#1E3A8A", // Primary Base
                },
                success: {
                    DEFAULT: "#10b981",
                    foreground: "#ffffff",
                },
                destructive: {
                    DEFAULT: "#EF4444", // hsl(var(--destructive)) replaced with hex
                    foreground: "#ffffff", // hsl(var(--destructive-foreground)) replaced with hex
                },
                popover: {
                    DEFAULT: "#F8FAFC", // hsl(var(--popover)) replaced with hex
                    foreground: "#1E293B", // hsl(var(--popover-foreground)) replaced with hex
                },
                card: {
                    DEFAULT: "#F8FAFC", // hsl(var(--card)) replaced with hex
                    foreground: "#1E293B", // hsl(var(--card-foreground)) replaced with hex
                },
                // Palette-specific colors
                palette: {
                    "primary-accent": "#449EFF",
                    "primary-base": "#1E3A8A",
                    complementary: "#F59E0B",
                    "secondary-light": "#F8FAFC",
                    "secondary-medium": "#E2E8F0",
                    "accent-cyan": "#06B6D4",
                    "accent-navy": "#1E293B",
                },
                // Achievement card colors
                achievement: {
                    // Blue theme (like your current cards)
                    "blue-light": "#165A9A",
                    "blue-dark": "#104373",
                    // Brown/Orange theme
                    "brown-light": "#913311",
                    "brown-dark": "#591E09",
                    // Gray theme
                    "gray-light": "#646565",
                    "gray-dark": "#2E343C",
                    // Green theme
                    "green-light": "#1E8646",
                    "green-dark": "#163522",
                    // Purple theme
                    "purple-light": "#2C1B68",
                    "purple-dark": "#100A23",
                    // Gold/Yellow theme
                    "gold-light": "#D17A09",
                    "gold-dark": "#512500",
                },
            },
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },
            keyframes: {
                "accordion-down": {
                    from: { height: "0" },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: "0" },
                },
                float: {
                    "0%, 100%": { transform: "translateY(0px)" },
                    "50%": { transform: "translateY(-20px)" },
                },
                "pulse-slow": {
                    "0%, 100%": { opacity: "1" },
                    "50%": { opacity: "0.5" },
                },
                "gradient-x": {
                    "0%, 100%": {
                        "background-size": "200% 200%",
                        "background-position": "left center",
                    },
                    "50%": {
                        "background-size": "200% 200%",
                        "background-position": "right center",
                    },
                },
                flip: {
                    "0%": { transform: "rotateY(0deg)" },
                    "100%": { transform: "rotateY(180deg)" },
                },
                "flip-back": {
                    "0%": { transform: "rotateY(180deg)" },
                    "100%": { transform: "rotateY(0deg)" },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
                float: "float 6s ease-in-out infinite",
                "pulse-slow": "pulse-slow 4s ease-in-out infinite",
                "gradient-x": "gradient-x 15s ease infinite",
                float: "float 6s ease-in-out infinite",
                "pulse-slow": "pulse-slow 4s ease-in-out infinite",
                flip: "flip 0.7s ease-in-out",
                "flip-back": "flip-back 0.7s ease-in-out",
            },
            backgroundImage: {
                "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
                "gradient-conic":
                    "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
                "mesh-gradient":
                    "radial-gradient(at 40% 20%, #449EFF 0px, transparent 50%), radial-gradient(at 80% 0%, #F59E0B 0px, transparent 50%), radial-gradient(at 0% 50%, #1E293B 0px, transparent 50%), radial-gradient(at 80% 50%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 100%, #F59E0B 0px, transparent 50%), radial-gradient(at 80% 100%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 0%, #1E3A8A 0px, transparent 50%)",
                "blue-mesh-gradient":
                    "radial-gradient(at 40% 20%, #449EFF 0px, transparent 50%), radial-gradient(at 80% 0%, #1E3A8A 0px, transparent 50%), radial-gradient(at 0% 50%, #06B6D4 0px, transparent 50%), radial-gradient(at 80% 50%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 100%, #1E3A8A 0px, transparent 50%), radial-gradient(at 80% 100%, #06B6D4 0px, transparent 50%), radial-gradient(at 0% 0%, #1E293B 0px, transparent 50%)",
            },
        },
    },
    plugins: [],
};
