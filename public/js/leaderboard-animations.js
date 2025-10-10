// public/js/leaderboard-animations.js

/**
 * Leaderboard Animations Library
 * Mobile-friendly GSAP-based animations for the leaderboard components
 */

class LeaderboardAnimations {
    constructor() {
        this.initialized = false;
        this.gsapAvailable = typeof gsap !== "undefined";
        this.isMobile = this.checkMobile();
    }

    /**
     * Check if device is mobile
     */
    checkMobile() {
        return (
            window.innerWidth <= 768 ||
            /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                navigator.userAgent
            )
        );
    }

    /**
     * Initialize the animations library
     */
    init() {
        if (this.initialized) return;

        console.log("🎬 LeaderboardAnimations initialized");
        console.log("📱 GSAP available:", this.gsapAvailable);
        console.log("📱 Mobile device:", this.isMobile);

        if (!this.gsapAvailable) {
            console.warn(
                "GSAP not available. Leaderboard animations will use fallback CSS."
            );
        }

        // Update mobile detection on resize
        window.addEventListener("resize", () => {
            this.isMobile = this.checkMobile();
        });

        this.initialized = true;
    }

    /**
     * Main animation trigger with reduced motion support
     */
    triggerAllAnimations() {
        console.log("🚀 Triggering all leaderboard animations");

        if (this.supportsReducedMotion()) {
            console.log("♿ Reduced motion preferred, skipping animations");
            this.skipAnimations();
            return;
        }

        console.log("✨ Starting animations sequence");
        this.animatePodium();
        this.animateRankList();
        this.animateStickyFooter();
    }

    /**
     * Mobile-friendly podium animation
     */
    animatePodium() {
        console.log("🏆 Animating podium elements");

        const podiumElements = document.querySelectorAll(".podium-element");
        console.log("📊 Found podium elements:", podiumElements.length);

        if (podiumElements.length === 0) {
            console.warn("❌ No podium elements found for animation");
            return;
        }

        if (!this.gsapAvailable) {
            console.log("🔄 Using fallback podium animation");
            this.animatePodiumFallback();
            return;
        }

        // Reset to initial state to ensure animation works
        gsap.set(".podium-element", {
            opacity: 0,
            y: 50,
            scale: 0.8,
        });

        // Different animations for mobile vs desktop
        if (this.isMobile) {
            console.log("📱 Using mobile podium animation");
            gsap.to(".podium-element", {
                duration: 0.8,
                y: 0,
                scale: 1,
                opacity: 1,
                ease: "back.out(1.5)",
                stagger: 0.2,
                onComplete: () =>
                    console.log("✅ Podium mobile animation complete"),
            });
        } else {
            console.log("🖥️ Using desktop podium animation");
            gsap.to(".podium-element", {
                duration: 1,
                y: 0,
                scale: 1,
                opacity: 1,
                ease: "elastic.out(1, 0.8)",
                stagger: {
                    amount: 0.4,
                    from: "center",
                },
                onComplete: () =>
                    console.log("✅ Podium desktop animation complete"),
            });

            // Subtle floating animation only on desktop
            gsap.to(".podium-element > div:first-child", {
                duration: 2,
                y: -3,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut",
                delay: 1,
                stagger: 0.2,
            });
        }

        // Podium image animation
        const podiumImage = document.getElementById("podiumImage");
        if (podiumImage) {
            gsap.fromTo(
                podiumImage,
                { scale: 0.9, opacity: 0 },
                {
                    duration: 0.8,
                    scale: 1,
                    opacity: 1,
                    ease: "power2.out",
                    delay: 0.3,
                    onComplete: () =>
                        console.log("✅ Podium image animation complete"),
                }
            );
        }
    }

    /**
     * Mobile-friendly rank list animation
     */
    animateRankList() {
        console.log("📊 Animating rank list elements");

        const rankElements = document.querySelectorAll(".rank-element");
        console.log("📈 Found rank elements:", rankElements.length);

        if (rankElements.length === 0) {
            console.warn("❌ No rank elements found for animation");
            return;
        }

        if (!this.gsapAvailable) {
            console.log("🔄 Using fallback rank list animation");
            this.animateRankListFallback();
            return;
        }

        // Reset to initial state
        gsap.set(".rank-element", {
            opacity: 0,
            y: 30,
        });

        // Different animations for mobile vs desktop
        if (this.isMobile) {
            console.log("📱 Using mobile rank list animation");
            gsap.to(".rank-element", {
                duration: 0.6,
                y: 0,
                opacity: 1,
                ease: "power2.out",
                stagger: 0.1,
                delay: 0.5, // Start after podium
                onComplete: () =>
                    console.log("✅ Rank list mobile animation complete"),
            });
        } else {
            console.log("🖥️ Using desktop rank list animation");
            gsap.to(".rank-element", {
                duration: 0.8,
                y: 0,
                opacity: 1,
                ease: "power2.out",
                stagger: {
                    each: 0.1,
                    from: "start",
                },
                delay: 0.5, // Start after podium
                onComplete: () =>
                    console.log("✅ Rank list desktop animation complete"),
            });
        }

        // Only add hover effects on non-touch devices
        if (!this.isMobile) {
            this.addRankItemHoverEffects();
        }
    }

    /**
     * Sticky footer animation
     */
    animateStickyFooter() {
        console.log("🔗 Animating sticky footer");

        const footer = document.querySelector(".sticky-footer-element");
        if (!footer) {
            console.log("❌ No sticky footer found");
            return;
        }

        if (!this.gsapAvailable) {
            console.log("🔄 Using fallback sticky footer animation");
            this.animateStickyFooterFallback();
            return;
        }

        // Reset to initial state
        gsap.set(".sticky-footer-element", {
            opacity: 0,
            y: 50,
        });

        if (this.isMobile) {
            console.log("📱 Using mobile sticky footer animation");
            gsap.to(".sticky-footer-element", {
                duration: 0.6,
                y: 0,
                opacity: 1,
                ease: "power2.out",
                delay: 1.0, // Start after rank list
                onComplete: () =>
                    console.log("✅ Sticky footer mobile animation complete"),
            });
        } else {
            console.log("🖥️ Using desktop sticky footer animation");
            gsap.to(".sticky-footer-element", {
                duration: 0.7,
                y: 0,
                opacity: 1,
                ease: "back.out(1.7)",
                delay: 1.2, // Start after rank list
                onComplete: () =>
                    console.log("✅ Sticky footer desktop animation complete"),
            });

            // Pulse animation only on desktop
            gsap.to("#footerRank, #footerPoints", {
                duration: 0.5,
                scale: 1.1,
                repeat: 1,
                yoyo: true,
                ease: "power2.inOut",
                delay: 2.0,
                onComplete: () =>
                    console.log("✅ Footer pulse animation complete"),
            });
        }
    }

    /**
     * Add hover effects to rank list items (desktop only)
     */
    addRankItemHoverEffects() {
        const rankItems = document.querySelectorAll(".rank-element");
        console.log(
            "🎯 Adding hover effects to",
            rankItems.length,
            "rank items"
        );

        rankItems.forEach((item) => {
            item.addEventListener("mouseenter", () => {
                if (!this.gsapAvailable || this.isMobile) return;

                gsap.to(item, {
                    duration: 0.2,
                    scale: 1.02,
                    y: -1,
                    boxShadow: "0 8px 20px rgba(0,0,0,0.15)",
                    ease: "power2.out",
                });
            });

            item.addEventListener("mouseleave", () => {
                if (!this.gsapAvailable || this.isMobile) return;

                gsap.to(item, {
                    duration: 0.2,
                    scale: 1,
                    y: 0,
                    boxShadow: "0 4px 6px rgba(0,0,0,0.1)",
                    ease: "power2.out",
                });
            });
        });
    }

    /**
     * Prefers-reduced-motion support
     */
    supportsReducedMotion() {
        const reducedMotion = window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;
        console.log("♿ Reduced motion preference:", reducedMotion);
        return reducedMotion;
    }

    /**
     * Skip animations and show content immediately
     */
    skipAnimations() {
        console.log("⏩ Skipping all animations, showing content immediately");
        const elements = document.querySelectorAll(
            ".podium-element, .rank-element, .sticky-footer-element"
        );
        elements.forEach((el) => {
            el.style.opacity = "1";
            el.style.transform = "none";
            el.style.transition = "none";
        });
    }

    /**
     * Optimized fallback animations for mobile
     */
    animatePodiumFallback() {
        const podiumElements = document.querySelectorAll(".podium-element");
        const stagger = this.isMobile ? 100 : 200;

        console.log(
            "🔄 Running fallback podium animation with",
            stagger,
            "ms stagger"
        );

        podiumElements.forEach((el, index) => {
            setTimeout(() => {
                el.style.opacity = "1";
                el.style.transform = "translateY(0) scale(1)";
                el.style.transition = `all ${
                    this.isMobile ? "0.5s" : "0.8s"
                } ease-out`;
            }, index * stagger);
        });
    }

    animateRankListFallback() {
        const rankElements = document.querySelectorAll(".rank-element");
        const stagger = this.isMobile ? 50 : 100;

        console.log(
            "🔄 Running fallback rank list animation with",
            stagger,
            "ms stagger"
        );

        rankElements.forEach((el, index) => {
            setTimeout(() => {
                el.style.opacity = "1";
                el.style.transform = "translateY(0)";
                el.style.transition = `all ${
                    this.isMobile ? "0.4s" : "0.6s"
                } ease-out`;
            }, index * stagger + 600); // Start after podium
        });
    }

    animateStickyFooterFallback() {
        const footer = document.querySelector(".sticky-footer-element");
        if (footer) {
            const delay = this.isMobile ? 300 : 600;
            console.log(
                "🔄 Running fallback sticky footer animation with",
                delay,
                "ms delay"
            );

            setTimeout(() => {
                footer.style.opacity = "1";
                footer.style.transform = "translateY(0)";
                footer.style.transition = `all ${
                    this.isMobile ? "0.5s" : "0.7s"
                } ease-out`;
            }, delay + 600); // Start after rank list
        }
    }

    /**
     * Clean up animations (for page transitions)
     */
    destroy() {
        if (this.gsapAvailable) {
            gsap.killTweensOf("*");
        }
        this.initialized = false;
        console.log("🧹 Leaderboard animations destroyed");
    }
}

// Create global instance
window.LeaderboardAnimations = new LeaderboardAnimations();

// Auto-initialize when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        console.log("📄 DOM fully loaded, initializing leaderboard animations");
        window.LeaderboardAnimations.init();
    });
} else {
    console.log(
        "⚡ DOM already ready, initializing leaderboard animations immediately"
    );
    window.LeaderboardAnimations.init();
}

// Export for module usage if needed
if (typeof module !== "undefined" && module.exports) {
    module.exports = LeaderboardAnimations;
}
