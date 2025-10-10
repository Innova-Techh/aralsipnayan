// Assessment Animations with GSAP - Simplified Motion (No Flip)
class AssessmentAnimations {
    constructor() {
        this.init();
    }

    init() {
        // Load GSAP from CDN if not already available
        this.loadGSAP().then(() => {
            if (typeof gsap !== "undefined") {
                this.setupAnimations();
            }
        });
    }

    loadGSAP() {
        return new Promise((resolve, reject) => {
            if (typeof gsap !== "undefined") {
                resolve();
                return;
            }

            const script = document.createElement("script");
            script.src =
                "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js";
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    setupAnimations() {
        // Wait for content to load
        const checkContent = setInterval(() => {
            const content = document.getElementById("assessmentsContent");
            if (content && !content.classList.contains("hidden")) {
                clearInterval(checkContent);
                this.animateHeader();
                this.animateFeatureCards();
                this.animateAssessmentCards();
            }
        }, 100);
    }

    // Simple slide down animation for header
    animateHeader() {
        const header = document.querySelector(".bg-gradient-purple");
        if (!header) return;

        gsap.fromTo(
            header,
            {
                y: -100,
                opacity: 0,
            },
            {
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power2.out",
            }
        );

        // Header text animation
        gsap.fromTo(
            ".bg-gradient-purple h1",
            {
                y: -30,
                opacity: 0,
            },
            {
                y: 0,
                opacity: 1,
                duration: 0.8,
                delay: 0.3,
                ease: "power2.out",
            }
        );

        gsap.fromTo(
            ".bg-gradient-purple p",
            {
                y: -20,
                opacity: 0,
            },
            {
                y: 0,
                opacity: 1,
                duration: 0.6,
                delay: 0.5,
                ease: "power2.out",
            }
        );
    }

    // Superhero spawning animation for feature cards (without rotation)
    animateFeatureCards() {
        const featureCards = gsap.utils.toArray(
            "#assessmentsContent .grid.grid-cols-1.lg\\:grid-cols-3 > div"
        );

        if (featureCards.length === 0) return;

        // Set initial state - cards start small and below
        gsap.set(featureCards, {
            scale: 0,
            opacity: 0,
            y: 100,
        });

        // Superhero spawning sequence without rotation
        const tl = gsap.timeline({
            delay: 0.8,
        });

        tl.to(featureCards, {
            scale: 1.2,
            opacity: 1,
            y: -15,
            duration: 0.6,
            stagger: {
                each: 0.15,
                from: "start",
                ease: "back.out(1.8)",
            },
        }).to(featureCards, {
            scale: 1,
            y: 0,
            duration: 0.4,
            stagger: {
                each: 0.15,
                from: "start",
                ease: "power2.out",
            },
        });

        // Add subtle glow effect during spawn
        featureCards.forEach((card, index) => {
            gsap.to(card, {
                boxShadow: "0 0 25px rgba(255, 255, 255, 0.6)",
                duration: 0.3,
                repeat: 1,
                yoyo: true,
                delay: 0.8 + index * 0.15,
            });
        });
    }

    // Simple animated motion for assessment categories
    animateAssessmentCards() {
        const assessmentCards = gsap.utils.toArray(
            "#assessmentsContent .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3 > div"
        );

        if (assessmentCards.length === 0) return;

        // Set initial state
        gsap.set(assessmentCards, {
            opacity: 0,
            y: 50,
        });

        // Simple staggered slide-up animation
        const tl = gsap.timeline({
            delay: 1.5,
        });

        tl.to(assessmentCards, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: {
                each: 0.2,
                from: "start",
                ease: "power2.out",
            },
        });
    }
}

// Initialize animations
document.addEventListener("DOMContentLoaded", function () {
    // Start animations when assessments content loads
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (
                mutation.type === "attributes" &&
                mutation.attributeName === "class"
            ) {
                if (mutation.target.classList.contains("content-loaded")) {
                    window.assessmentAnimations = new AssessmentAnimations();
                    observer.disconnect();
                }
            }
        });
    });

    const contentElement = document.getElementById("assessmentsContent");
    if (contentElement) {
        observer.observe(contentElement, { attributes: true });
    }
});
