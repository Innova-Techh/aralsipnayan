/** @type {import('tailwindcss').Config} */
import plugin from "tailwindcss/plugin";

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
                md: "640px", // Small tablets
                lg: "768px", // iPads
                xl: "1024px", // Laptops
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
                border: "#E2E8F0",
                input: "#E2E8F0",
                ring: "#E2E8F0",
                background: "#F8FAFC",
                foreground: "#1E293B",
                primary: {
                    DEFAULT: "#449EFF",
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
                    accent: "#449EFF",
                    first: "#1E3A8A",
                },
                secondary: {
                    DEFAULT: "#F59E0B",
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
                    light: "#F8FAFC",
                    medium: "#E2E8F0",
                },
                accent: {
                    DEFAULT: "#F8FAFC",
                    foreground: "#1E293B",
                    cyan: "#06B6D4",
                    navy: "#1E293B",
                },
                muted: {
                    DEFAULT: "#E2E8F0",
                    foreground: "#1E293B",
                },
                dark: {
                    DEFAULT: "#1E293B",
                    foreground: "#F8FAFC",
                },
                blue: {
                    bright: "#449EFF",
                    deep: "#1E3A8A",
                },
                success: {
                    DEFAULT: "#10b981",
                    foreground: "#ffffff",
                },
                destructive: {
                    DEFAULT: "#EF4444",
                    foreground: "#ffffff",
                },
                popover: {
                    DEFAULT: "#F8FAFC",
                    foreground: "#1E293B",
                },
                card: {
                    DEFAULT: "#F8FAFC",
                    foreground: "#1E293B",
                },
                palette: {
                    "primary-accent": "#449EFF",
                    "primary-base": "#1E3A8A",
                    complementary: "#F59E0B",
                    "secondary-light": "#F8FAFC",
                    "secondary-medium": "#E2E8F0",
                    "accent-cyan": "#06B6D4",
                    "accent-navy": "#1E293B",
                },
                achievement: {
                    "blue-light": "#165A9A",
                    "blue-dark": "#104373",
                    "brown-light": "#913311",
                    "brown-dark": "#591E09",
                    "gray-light": "#646565",
                    "gray-dark": "#2E343C",
                    "green-light": "#1E8646",
                    "green-dark": "#163522",
                    "purple-light": "#2C1B68",
                    "purple-dark": "#100A23",
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
                flip: "flip 0.7s ease-in-out",
                "flip-back": "flip-back 0.7s ease-in-out",
            },
            backgroundImage: {
                "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
                "radial-center":
                    "radial-gradient(ellipse at center, var(--tw-gradient-stops))",
                "radial-blur-blue": `
          radial-gradient(circle at center,
            #2563EB 0%,
            rgba(37, 99, 235, 0.5) 40%,
            transparent 70%),
          radial-gradient(circle at center,
            #5D90FF 0%,
            rgba(93, 144, 255, 0.5) 60%,
            transparent 100%)
        `,
                "gradient-conic":
                    "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
                "mesh-gradient":
                    "radial-gradient(at 40% 20%, #449EFF 0px, transparent 50%), radial-gradient(at 80% 0%, #F59E0B 0px, transparent 50%), radial-gradient(at 0% 50%, #1E293B 0px, transparent 50%), radial-gradient(at 80% 50%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 100%, #F59E0B 0px, transparent 50%), radial-gradient(at 80% 100%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 0%, #1E3A8A 0px, transparent 50%)",
                "blue-mesh-gradient":
                    "radial-gradient(at 40% 20%, #449EFF 0px, transparent 50%), radial-gradient(at 80% 0%, #1E3A8A 0px, transparent 50%), radial-gradient(at 0% 50%, #06B6D4 0px, transparent 50%), radial-gradient(at 80% 50%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 100%, #1E3A8A 0px, transparent 50%), radial-gradient(at 80% 100%, #06B6D4 0px, transparent 50%), radial-gradient(at 0% 0%, #1E293B 0px, transparent 50%)",
                "avatar-selection":
                    "radial-gradient(ellipse at center, #2563EB 0%, #1E3A8A 100%)",
                "on-board-earn": "linear-gradient(to bottom, #F59E0B, #FBBF24)",
                "on-board-progress":
                    "linear-gradient(to bottom, #4F46E5, #06B6D4)",
                "on-board-learn":
                    "linear-gradient(to bottom, #D62839, #E9742F, #FBBF24)",
                "gradient-secondary":
                    "linear-gradient(to bottom, #F6510C, #F5D70B)",
                "hover-secondary":
                    "linear-gradient(to bottom, #D9440B, #E6C308)",
                "select-avatar":
                    "linear-gradient(to bottom, #F59E0B 0%, #FBBF24 100%)",
                "avatar-1":
                    "linear-gradient(to bottom, #FFA500 0%, #FFD588 100%)",
                "avatar-2":
                    "linear-gradient(to bottom, #FF657F 0%, #FFC0CB 100%)",
                "avatar-3":
                    "linear-gradient(to bottom, #EF362A 0%, #FF8780 100%)",
                "avatar-4":
                    "linear-gradient(to bottom, #A9A9A9 0%, #E8E8E8 100%)",
                "avatar-5":
                    "linear-gradient(to bottom, #1C8582 0%, #43FFFA 100%)",
                "avatar-6":
                    "linear-gradient(to bottom, #EF7436 0%, #FFA273 100%)",
            },
            dropShadow: {
                "select-avatar": "0 6px 0 #CE8E21",
                "on-board-earn": "0 4px 0 #C47E06",
                "on-board-learn": "0 4px 0 #C1321F",
                "on-board-progress": "0 4px 0 #1E18CB",
                "on-welcome": "0 4px 0 #1E3A8A",
                "avatar-1": "6px 4px 0 #FFA705",
                "avatar-2": "6px 4px 0 #FF6D87",
                "avatar-3": "6px 4px 0 #F0392D",
                "avatar-4": "6px 4px 0 #928F8F",
                "avatar-5": "6px 4px 0 #20908C",
                "avatar-6": "6px 4px 0 #F07639",
            },
        },
    },

    plugins: [
        plugin(function ({ matchUtilities, theme }) {
            // Text outline with variable thickness
            matchUtilities(
                {
                    "text-outline-custom": (value) => {
                        return {
                            textShadow: `
                -1px -1px 0 ${value},
                1px -1px 0 ${value},
                -1px  1px 0 ${value},
                1px  1px 0 ${value},
                -1px  4px 0 ${value},   /* left bottom extended */
                1px  4px 0 ${value},   /* right bottom extended */
                0px  4px 0 ${value}    /* straight bottom */`,
                        };
                    },
                },
                { values: theme("colors"), type: "color" }
            );
        }),
    ],
};
