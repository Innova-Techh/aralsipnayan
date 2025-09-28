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
                // Complete Level Up System Colors - 10 Tiers (Cleaned)
                // Bronze Tier (Levels 1-10)
                "level-bronze": {
                    "card-from": "#C77C3E",
                    "card-to": "#5A2E12",
                    stroke: "#3B1F0C",
                    "badge-from": "#E69B56",
                    "badge-to": "#5A2E12",
                    shadow: "#4D2A12",
                    title: "#FFFFFF",
                    "title-stroke": "#3B1F0C",
                    xp: "#FFF3E6",
                    "xp-stroke": "#4D2A12",
                    "xp-shadow": "#4D2A12",
                    labels: "#FFD9B3",
                    message: "#E0C3A0",
                    "button-from": "#E69B56",
                    "button-to": "#5A2E12",
                    "button-stroke": "#4D2A12",
                    "button-shadow": "#4D2A12",
                },
                // Silver Tier (Levels 11-20)
                "level-silver": {
                    "card-from": "#D9E3F2",
                    "card-to": "#3C4757",
                    stroke: "#2A313D",
                    "badge-from": "#F2F6FA",
                    "badge-to": "#3C4757",
                    shadow: "#2E3642",
                    title: "#FFFFFF",
                    "title-stroke": "#2A313D",
                    xp: "#F9FBFF",
                    "xp-stroke": "#2E3642",
                    "xp-shadow": "#2E3642",
                    labels: "#E2E8F3",
                    message: "#FFFFFF",
                    "button-from": "#F2F6FA",
                    "button-to": "#3C4757",
                    "button-stroke": "#2E3642",
                    "button-shadow": "#2E3642",
                },
                // Gold Tier (Levels 21-30) - CORRECTED RANGE
                "level-gold": {
                    "card-from": "#FFD55C",
                    "card-to": "#7A4B0E",
                    stroke: "#4D3009",
                    "badge-from": "#FFE58A",
                    "badge-to": "#7A4B0E",
                    shadow: "#5C3A0F",
                    title: "#FFFFFF",
                    "title-stroke": "#4D3009",
                    xp: "#FFF7E6",
                    "xp-stroke": "#5C3A0F",
                    "xp-shadow": "#5C3A0F",
                    labels: "#FFECCC",
                    message: "#FFFFFF",
                    "button-from": "#FFE58A",
                    "button-to": "#7A4B0E",
                    "button-stroke": "#5C3A0F",
                    "button-shadow": "#5C3A0F",
                },
                // Topaz Tier (Levels 31-40) - CORRECTED RANGE
                "level-topaz": {
                    "card-from": "#F6A43B",
                    "card-to": "#A64906",
                    stroke: "#7C3304",
                    "badge-from": "#FFD59E",
                    "badge-to": "#B65A0B",
                    "badge-stroke": "#9C5B0C",
                    shadow: "#663308",
                    title: "#FFFFFF",
                    "title-stroke": "#9C5B0C",
                    xp: "#FFF2E2",
                    "xp-stroke": "#9C5B0C",
                    "xp-shadow": "#9C5B0C",
                    labels: "#FFE9D1",
                    message: "#FFFFFF",
                    "button-from": "#FFB74A",
                    "button-to": "#A64906",
                    "button-stroke": "#5A2503",
                    "button-shadow": "#5A2503",
                },
                // Emerald Tier (Levels 41-50) - CORRECTED RANGE
                "level-emerald": {
                    "card-from": "#1BA34A",
                    "card-to": "#064A23",
                    stroke: "#04351A",
                    "badge-from": "#2ECC71",
                    "badge-to": "#0D5E2E",
                    shadow: "#022412",
                    title: "#FFFFFF",
                    "title-stroke": "#04351A",
                    xp: "#E6FFF0",
                    "xp-stroke": "#03361B",
                    "xp-shadow": "#03361B",
                    labels: "#C2FFD9",
                    message: "#FFFFFF",
                    "button-from": "#28D17C",
                    "button-to": "#0D5E2E",
                    "button-stroke": "#03361B",
                    "button-shadow": "#03361B",
                },
                // Ruby Tier (Levels 51-60) - CORRECTED RANGE
                "level-ruby": {
                    "card-from": "#E63946",
                    "card-to": "#5C0A0A",
                    stroke: "#3D0707",
                    "badge-from": "#FF4D6D",
                    "badge-to": "#7A0F0F",
                    shadow: "#2B0505",
                    title: "#FFFFFF",
                    "title-stroke": "#3D0707",
                    xp: "#FFE6E6",
                    "xp-stroke": "#4A0A0A",
                    "xp-shadow": "#4A0A0A",
                    labels: "#FFC2C2",
                    message: "#FFFFFF",
                    "button-from": "#FF5C5C",
                    "button-to": "#7A0F0F",
                    "button-stroke": "#4A0A0A",
                    "button-shadow": "#4A0A0A",
                },
                // Amethyst Tier (Levels 61-70) - CORRECTED RANGE
                "level-amethyst": {
                    "card-from": "#8E44AD",
                    "card-to": "#2E0B3F",
                    stroke: "#1D0629",
                    "badge-from": "#B066CC",
                    "badge-to": "#41165C",
                    shadow: "#150322",
                    title: "#FFFFFF",
                    "title-stroke": "#1D0629",
                    xp: "#F5E6FF",
                    "xp-stroke": "#2A0A3D",
                    "xp-shadow": "#2A0A3D",
                    labels: "#E1C2FF",
                    message: "#FFFFFF",
                    "button-from": "#A24DE1",
                    "button-to": "#41165C",
                    "button-stroke": "#2A0A3D",
                    "button-shadow": "#2A0A3D",
                },
                // Tanzite Tier (Levels 71-80) - CORRECTED RANGE
                "level-tanzite": {
                    "card-from": "#3A2EA1",
                    "card-to": "#0E163A",
                    stroke: "#0A0D22",
                    "badge-from": "#5C6AFF",
                    "badge-to": "#1D215B",
                    shadow: "#080C26",
                    title: "#FFFFFF",
                    "title-stroke": "#0A0D22",
                    xp: "#E4E8FF",
                    "xp-stroke": "#121637",
                    "xp-shadow": "#121637",
                    labels: "#C5D1FF",
                    message: "#FFFFFF",
                    "button-from": "#6B77FF",
                    "button-to": "#1D215B",
                    "button-stroke": "#121637",
                    "button-shadow": "#121637",
                },
                // Sapphire Tier (Levels 81-90) - CORRECTED RANGE
                "level-sapphire": {
                    "card-from": "#4A9BFF",
                    "card-to": "#0D1B3F",
                    stroke: "#081024",
                    "badge-from": "#5FB6FF",
                    "badge-to": "#0D1B3F",
                    shadow: "#09244D",
                    title: "#FFFFFF",
                    "title-stroke": "#081024",
                    xp: "#E6F2FF",
                    "xp-stroke": "#09244D",
                    "xp-shadow": "#09244D",
                    labels: "#C6E0FF",
                    message: "#FFFFFF",
                    "button-from": "#5FB6FF",
                    "button-to": "#0D1B3F",
                    "button-stroke": "#071B38",
                    "button-shadow": "#071B38",
                },
                // Prismatic/Diamond Tier (Levels 91-100) - CORRECTED RANGE
                "level-prismatic": {
                    "card-from": "#C86CFF",
                    "card-to": "#FFD447", // Multi-gradient effect
                    "card-middle": "#3FB6FF", // For 3-color gradient
                    stroke: "#2A0F38",
                    "badge-from": "#FF6BD6",
                    "badge-to": "#FFD447",
                    "badge-middle": "#3FB6FF", // For 3-color gradient
                    shadow: "#43185C",
                    title: "#FFFFFF",
                    "title-stroke": "#2A0F38",
                    xp: "#FFF7E6",
                    "xp-stroke": "#43185C",
                    "xp-shadow": "#43185C",
                    labels: "#FFE6FF",
                    message: "#FFFFFF",
                    "button-from": "#FF6BD6",
                    "button-to": "#3FB6FF",
                    "button-stroke": "#43185C",
                    "button-shadow": "#43185C",
                },
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
                    "brown-light": "#913311",
                    "gray-light": "#646565",
                    "green-light": "#1E8646",
                    "purple-light": "#2C1B68",
                    "gold-light": "#D17A09",
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
                shimmer: {
                    "0%": { transform: "translateX(-100%)" },
                    "100%": { transform: "translateX(100%)" },
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
                shimmer: "shimmer 2s ease-in-out infinite",
            },
            backgroundImage: {
                "book-icon": `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23006C2B' stroke-width='1' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'><path d='M12 7v14'/><path d='M3 18a1 1 0 0 1-1-1V4h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5v13h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z'/></svg>")`,
                "flame-icon": `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23ffffff' stroke-width='1' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'><path d='M12 3q1 4 4 6.5t3 5.5a1 1 0 0 1-14 0 5 5 0 0 1 1-3 1 1 0 0 0 5 0c0-2-1.5-3-1.5-5q0-2 2.5-4'/></svg>")`,
                "trophy-icon": `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23ffffff' stroke-width='1' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'><path d='M10 14.66v1.626a2 2 0 0 1-.976 1.696A5 5 0 0 0 7 21.978'/><path d='M14 14.66v1.626a2 2 0 0 0 .976 1.696A5 5 0 0 1 17 21.978'/><path d='M18 9h1.5a1 1 0 0 0 0-5H18'/><path d='M4 22h16'/><path d='M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z'/><path d='M6 9H4.5a1 1 0 0 1 0-5H6'/></svg>")`,
                "star-icon": `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23ffffff' stroke-width='1' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'><path d='M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z'/></svg>")`,
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
                "gradient-purple":
                    "linear-gradient(135deg, #3775FF, #6D48E8, #611E8A)",
                // Clean Level Up System Gradients - 10 Tiers
                "level-bronze-card":
                    "linear-gradient(to bottom, #C77C3E, #5A2E12)",
                "level-bronze-badge":
                    "linear-gradient(to bottom, #E69B56, #5A2E12)",
                "level-bronze-button":
                    "linear-gradient(to bottom, #E69B56, #5A2E12)",

                "level-silver-card":
                    "linear-gradient(to bottom, #D9E3F2, #3C4757)",
                "level-silver-badge":
                    "linear-gradient(to bottom, #F2F6FA, #3C4757)",
                "level-silver-button":
                    "linear-gradient(to bottom, #F2F6FA, #3C4757)",

                "level-gold-card":
                    "linear-gradient(to bottom, #FFD55C, #7A4B0E)",
                "level-gold-badge":
                    "linear-gradient(to bottom, #FFE58A, #7A4B0E)",
                "level-gold-button":
                    "linear-gradient(to bottom, #FFE58A, #7A4B0E)",

                "level-topaz-card":
                    "linear-gradient(to bottom, #F6A43B, #A64906)",
                "level-topaz-badge":
                    "linear-gradient(to bottom, #FFD59E, #B65A0B)",
                "level-topaz-button":
                    "linear-gradient(to bottom, #FFB74A, #A64906)",

                "level-emerald-card":
                    "linear-gradient(to bottom, #1BA34A, #064A23)",
                "level-emerald-badge":
                    "linear-gradient(to bottom, #2ECC71, #0D5E2E)",
                "level-emerald-button":
                    "linear-gradient(to bottom, #28D17C, #0D5E2E)",

                "level-ruby-card":
                    "linear-gradient(to bottom, #E63946, #5C0A0A)",
                "level-ruby-badge":
                    "linear-gradient(to bottom, #FF4D6D, #7A0F0F)",
                "level-ruby-button":
                    "linear-gradient(to bottom, #FF5C5C, #7A0F0F)",

                "level-amethyst-card":
                    "linear-gradient(to bottom, #8E44AD, #2E0B3F)",
                "level-amethyst-badge":
                    "linear-gradient(to bottom, #B066CC, #41165C)",
                "level-amethyst-button":
                    "linear-gradient(to bottom, #A24DE1, #41165C)",

                "level-tanzite-card":
                    "linear-gradient(to bottom, #3A2EA1, #0E163A)",
                "level-tanzite-badge":
                    "linear-gradient(to bottom, #5C6AFF, #1D215B)",
                "level-tanzite-button":
                    "linear-gradient(to bottom, #6B77FF, #1D215B)",

                "level-sapphire-card":
                    "linear-gradient(to bottom, #4A9BFF, #0D1B3F)",
                "level-sapphire-badge":
                    "linear-gradient(to bottom, #5FB6FF, #0D1B3F)",
                "level-sapphire-button":
                    "linear-gradient(to bottom, #5FB6FF, #0D1B3F)",

                "level-prismatic-card":
                    "linear-gradient(to bottom, #C86CFF, #3FB6FF, #FFD447)",
                "level-prismatic-badge":
                    "linear-gradient(to bottom, #FF6BD6, #3FB6FF, #FFD447)",
                "level-prismatic-button":
                    "linear-gradient(to bottom, #FF6BD6, #3FB6FF)",

                // Legacy gradients maintained
                "blue-mesh-gradient":
                    "radial-gradient(at 40% 20%, #449EFF 0px, transparent 50%), radial-gradient(at 80% 0%, #1E3A8A 0px, transparent 50%), radial-gradient(at 0% 50%, #06B6D4 0px, transparent 50%), radial-gradient(at 80% 50%, #449EFF 0px, transparent 50%), radial-gradient(at 0% 100%, #1E3A8A 0px, transparent 50%), radial-gradient(at 80% 100%, #06B6D4 0px, transparent 50%), radial-gradient(at 0% 0%, #1E293B 0px, transparent 50%)",
                "avatar-selection":
                    "radial-gradient(ellipse at center, #2563EB 0%, #1E3A8A 100%)",
                "on-board-earn": "linear-gradient(to bottom, #F59E0B, #FBBF24)",
                "on-board-progress":
                    "linear-gradient(to bottom, #4F46E5, #06B6D4)",
                "on-board-learn":
                    "linear-gradient(to bottom, #D62839, #E9742F, #FBBF24)",
                "gradient-primary":
                    "linear-gradient(to bottom, #4338CA, #9333EA)",
                "hover-primary": "linear-gradient(to bottom, #0284C7, #0369A1)",
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
                "stats-green":
                    "linear-gradient(to bottom, #10B981 0%, #07533A 100%)",
                "stats-blue":
                    "linear-gradient(to bottom, #3B82F6 0%, #234C90 100%)",
                "stats-yellow":
                    "linear-gradient(to bottom, #F59E0B 0%, #8F5C06 100%)",
                "stats-red":
                    "linear-gradient(to bottom, #DC2626 0%, #761414 100%)",
                "leaderboard-container": "#3B82F6",
                "leaderboard-points":
                    "linear-gradient(to bottom, #F59E0B, #FBBF24)",
            },
            boxShadow: {
                // Inner shadow utility
                "inner-violet": "inset -4px -4px 2px #4A1B74",
            },
            dropShadow: {
                "gradient-primary": "0 4px 0 #312297",
                "gradient-secondary": "0 4px 0 #7A4305",
                "select-avatar": "0 6px 0 #CE8E21",
                "on-board-earn": "0 4px 0 #C47E06",
                "on-board-learn": "0 4px 0 #C1321F",
                "on-board-progress": "0 4px 0 #1E18CB",
                "custom-purple": "0 6px 0 #30098F",
                "on-welcome": "0 4px 0 #1E3A8A",
                "avatar-1": "6px 4px 0 #FFA705",
                "avatar-2": "6px 4px 0 #FF6D87",
                "avatar-3": "6px 4px 0 #F0392D",
                "avatar-4": "6px 4px 0 #928F8F",
                "avatar-5": "6px 4px 0 #20908C",
                "avatar-6": "6px 4px 0 #F07639",
                "stats-green": "0 6px 0 #043024",
                "stats-blue": "0 6px 0 #15294D",
                "stats-yellow": "0 6px 0 #4A2E03",
                "stats-red": "0 6px 0 #3B0A0A",
                "drop-custom": "4px 4px 0 black",

                header: "0 4px 0 #1E3A8A",
                description: "0 1px 0 #1E3A8A",

                // Achievement card custom drop shadows
                "achievement-common": "4px 4px 0 #2E343C",
                "achievement-uncommon": "4px 4px 0 #163522",
                "achievement-rare": "4px 4px 0 #591E09",
                "achievement-epic": "4px 4px 0 #100A23",
                "achievement-legendary": "4px 4px 0 #512500",
                "achievement-blue": "4px 4px 0 #104373",
                "leaderboard-container": "0 8px 0 #2960BB",
                "leaderboard-points": "0 4px 0 #AE6816",

                // Level Up System Drop Shadows - Matching UI Design Specs
                "level-bronze-badge": "0 4px 0 #4D2A12",
                "level-bronze-button": "0 4px 0 #4D2A12",
                "level-bronze-text": "0 3px 0 #4D2A12",

                "level-silver-badge": "0 4px 0 #2E3642",
                "level-silver-button": "0 4px 0 #2E3642",
                "level-silver-text": "0 3px 0 #2E3642",

                "level-gold-badge": "0 4px 0 #5C3A0F",
                "level-gold-button": "0 4px 0 #5C3A0F",
                "level-gold-text": "0 3px 0 #5C3A0F",

                "level-topaz-badge": "0 4px 0 #663308",
                "level-topaz-button": "0 4px 0 #5A2503",
                "level-topaz-text": "0 3px 0 #9C5B0C",

                "level-emerald-badge": "0 4px 0 #022412",
                "level-emerald-button": "0 4px 0 #03361B",
                "level-emerald-text": "0 3px 0 #03361B",

                "level-ruby-badge": "0 4px 0 #2B0505",
                "level-ruby-button": "0 4px 0 #4A0A0A",
                "level-ruby-text": "0 3px 0 #4A0A0A",

                "level-amethyst-badge": "0 4px 0 #150322",
                "level-amethyst-button": "0 4px 0 #2A0A3D",
                "level-amethyst-text": "0 3px 0 #2A0A3D",

                "level-tanzite-badge": "0 4px 0 #080C26",
                "level-tanzite-button": "0 4px 0 #121637",
                "level-tanzite-text": "0 3px 0 #121637",

                "level-sapphire-badge": "0 4px 0 #09244D",
                "level-sapphire-button": "0 4px 0 #071B38",
                "level-sapphire-text": "0 3px 0 #071B38",

                "level-prismatic-badge": "0 4px 0 #43185C",
                "level-prismatic-button": "0 4px 0 #43185C",
                "level-prismatic-text": "0 3px 0 #43185C",
            },
        },
    },

    plugins: [
        plugin(function ({ matchUtilities, theme }) {
            // Inner shadow plugin
            matchUtilities(
                {
                    "shadow-inner-y-4": (value) => ({
                        boxShadow: `inset 0 4px 4px ${value}`,
                    }),
                },
                { values: theme("colors"), type: "color" }
            );
        }),

        plugin(function ({ addUtilities }) {
            // Achievement custom inner shadows
            addUtilities({
                ".shadow-inner-achievement-common": {
                    boxShadow:
                        "inset 4px 4px 2px #525555, inset -2px 4px 4px #82868B",
                },
                ".shadow-inner-achievement-uncommon": {
                    boxShadow:
                        "inset 4px 4px 2px #166D38, inset -2px 4px 4px #33A15E",
                },
                ".shadow-inner-achievement-rare": {
                    boxShadow:
                        "inset 4px 4px 2px #591E09, inset -2px 4px 4px #591E09",
                },
                ".shadow-inner-achievement-epic": {
                    boxShadow:
                        "inset 4px 4px 2px #21125C, inset -2px 4px 4px #4A368B",
                },
                ".shadow-inner-achievement-legendary": {
                    boxShadow:
                        "inset 4px 4px 2px #804B03, inset -2px 4px 4px #FF9F4E",
                },
                ".shadow-inner-achievement-blue": {
                    boxShadow:
                        "inset 4px 4px 2px #104373, inset -2px 4px 4px #104373",
                },
                // Level Up System Inner Shadows
                ".shadow-inner-level-bronze": {
                    boxShadow:
                        "inset 4px 4px 2px #4D2A12, inset -2px 4px 4px #8B4513",
                },
                ".shadow-inner-level-silver": {
                    boxShadow:
                        "inset 4px 4px 2px #2E3642, inset -2px 4px 4px #607A8B",
                },
                ".shadow-inner-level-gold": {
                    boxShadow:
                        "inset 4px 4px 2px #5C3A0F, inset -2px 4px 4px #B8860B",
                },
                ".shadow-inner-level-topaz": {
                    boxShadow:
                        "inset 4px 4px 2px #663308, inset -2px 4px 4px #CD853F",
                },
                ".shadow-inner-level-emerald": {
                    boxShadow:
                        "inset 4px 4px 2px #064E3B, inset -2px 4px 4px #2E8B57",
                },
                ".shadow-inner-level-ruby": {
                    boxShadow:
                        "inset 4px 4px 2px #7F1D1D, inset -2px 4px 4px #DC143C",
                },
                ".shadow-inner-level-amethyst": {
                    boxShadow:
                        "inset 4px 4px 2px #581C87, inset -2px 4px 4px #9932CC",
                },
                ".shadow-inner-level-sapphire": {
                    boxShadow:
                        "inset 4px 4px 2px #1E3A8A, inset -2px 4px 4px #4169E1",
                },
                ".shadow-inner-level-diamond": {
                    boxShadow:
                        "inset 4px 4px 2px #475569, inset -2px 4px 4px #B0C4DE",
                },
            });
        }),

        // Level-specific text shadow utilities plugin (replacing text-stroke)
        plugin(function ({ addUtilities, theme }) {
            const textShadowUtilities = {};
            const tiers = [
                "bronze",
                "silver",
                "gold",
                "topaz",
                "emerald",
                "ruby",
                "amethyst",
                "tanzite",
                "sapphire",
                "prismatic",
            ];

            tiers.forEach((tier) => {
                const titleStrokeColor =
                    theme(`colors.level-${tier}["title-stroke"]`) ||
                    theme(`colors.level-${tier}.stroke`);
                const xpStrokeColor =
                    theme(`colors.level-${tier}["xp-stroke"]`) ||
                    theme(`colors.level-${tier}["button-stroke"]`);

                // Title text shadow (2px stroke effect for Level numbers)
                textShadowUtilities[`.text-shadow-${tier}-title`] = {
                    textShadow: `
                        -2px -2px 0 ${titleStrokeColor},
                        2px -2px 0 ${titleStrokeColor},
                        -2px 2px 0 ${titleStrokeColor},
                        2px 2px 0 ${titleStrokeColor},
                        0px -2px 0 ${titleStrokeColor},
                        0px 2px 0 ${titleStrokeColor},
                        -2px 0px 0 ${titleStrokeColor},
                        2px 0px 0 ${titleStrokeColor}`,
                };

                // XP/Points text shadow (1px stroke effect for values)
                textShadowUtilities[`.text-shadow-${tier}-xp`] = {
                    textShadow: `
                        -1px -1px 0 ${xpStrokeColor},
                        1px -1px 0 ${xpStrokeColor},
                        -1px 1px 0 ${xpStrokeColor},
                        1px 1px 0 ${xpStrokeColor},
                        0px -1px 0 ${xpStrokeColor},
                        0px 1px 0 ${xpStrokeColor},
                        -1px 0px 0 ${xpStrokeColor},
                        1px 0px 0 ${xpStrokeColor}`,
                };

                // Button text shadow (1px stroke effect)
                textShadowUtilities[`.text-shadow-${tier}-button`] = {
                    textShadow: `
                        -1px -1px 0 ${xpStrokeColor},
                        1px -1px 0 ${xpStrokeColor},
                        -1px 1px 0 ${xpStrokeColor},
                        1px 1px 0 ${xpStrokeColor},
                        0px -1px 0 ${xpStrokeColor},
                        0px 1px 0 ${xpStrokeColor},
                        -1px 0px 0 ${xpStrokeColor},
                        1px 0px 0 ${xpStrokeColor}`,
                };
            });

            addUtilities(textShadowUtilities);
        }),

        plugin(function ({ matchUtilities, theme }) {
            // Enhanced text outline with proper stroke
            matchUtilities(
                {
                    "text-outline-level": (value) => {
                        return {
                            textShadow: `
                -2px -2px 0 ${value},
                2px -2px 0 ${value},
                -2px  2px 0 ${value},
                2px  2px 0 ${value},
                0px -2px 0 ${value},
                0px  2px 0 ${value},
                -2px  0px 0 ${value},
                2px  0px 0 ${value}`,
                        };
                    },
                },
                {
                    values: {
                        "bronze-title": theme(
                            "colors.level-bronze.title-stroke"
                        ),
                        "bronze-xp": theme("colors.level-bronze.xp-stroke"),
                        "silver-title": theme(
                            "colors.level-silver.title-stroke"
                        ),
                        "silver-xp": theme("colors.level-silver.xp-stroke"),
                        "gold-title": theme("colors.level-gold.title-stroke"),
                        "gold-xp": theme("colors.level-gold.xp-stroke"),
                        "topaz-title": theme("colors.level-topaz.title-stroke"),
                        "topaz-xp": theme("colors.level-topaz.xp-stroke"),
                        "emerald-title": theme(
                            "colors.level-emerald.title-stroke"
                        ),
                        "emerald-xp": theme("colors.level-emerald.xp-stroke"),
                        "ruby-title": theme("colors.level-ruby.title-stroke"),
                        "ruby-xp": theme("colors.level-ruby.xp-stroke"),
                        "amethyst-title": theme(
                            "colors.level-amethyst.title-stroke"
                        ),
                        "amethyst-xp": theme("colors.level-amethyst.xp-stroke"),
                        "tanzite-title": theme(
                            "colors.level-tanzite.title-stroke"
                        ),
                        "tanzite-xp": theme("colors.level-tanzite.xp-stroke"),
                        "sapphire-title": theme(
                            "colors.level-sapphire.title-stroke"
                        ),
                        "sapphire-xp": theme("colors.level-sapphire.xp-stroke"),
                        "prismatic-title": theme(
                            "colors.level-prismatic.title-stroke"
                        ),
                        "prismatic-xp": theme(
                            "colors.level-prismatic.xp-stroke"
                        ),
                    },
                    type: "color",
                }
            );
        }),

        plugin(function ({ matchUtilities, theme }) {
            // Level-specific gradient utilities
            matchUtilities(
                {
                    "bg-level-gradient": (value) => {
                        const gradients = {
                            bronze: "linear-gradient(to bottom, #C77C3E, #5A2E12)",
                            silver: "linear-gradient(to bottom, #D9E3F2, #3C4757)",
                            gold: "linear-gradient(to bottom, #FFD55C, #7A4B0E)",
                            topaz: "linear-gradient(to bottom, #F6A43B, #A64906)",
                            emerald:
                                "linear-gradient(to bottom, #1BA34A, #064A23)",
                            ruby: "linear-gradient(to bottom, #E63946, #5C0A0A)",
                            amethyst:
                                "linear-gradient(to bottom, #8E44AD, #2E0B3F)",
                            tanzite:
                                "linear-gradient(to bottom, #3A2EA1, #0E163A)",
                            sapphire:
                                "linear-gradient(to bottom, #4A9BFF, #0D1B3F)",
                            prismatic:
                                "linear-gradient(to bottom, #C86CFF, #3FB6FF, #FFD447)",
                        };
                        return {
                            backgroundImage:
                                gradients[value] || gradients.bronze,
                        };
                    },
                },
                {
                    values: [
                        "bronze",
                        "silver",
                        "gold",
                        "topaz",
                        "emerald",
                        "ruby",
                        "amethyst",
                        "tanzite",
                        "sapphire",
                        "prismatic",
                    ],
                }
            );
        }),
    ],
};
