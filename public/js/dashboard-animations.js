// Dashboard Animations using GSAP
// Make sure to include GSAP in your HTML: <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

// Wait for GSAP to be loaded
const waitForGSAP = (callback) => {
    if (typeof gsap !== "undefined") {
        callback();
    } else {
        setTimeout(() => waitForGSAP(callback), 100);
    }
};

// Initialize animations - will be called manually after content loads
const initDashboardAnimations = () => {
    waitForGSAP(() => {
        console.log("Dashboard animations initialized");
        animateAllComponents();
    });
};

const animateAllComponents = () => {
    console.log("Starting all component animations");
    animateWelcomeHeader();
    animateProgressCard();
    animateStatsGrid();
    animateAssignments();
    animateAchievements();
    animateLeaderboard();
    addHoverAnimations();
    addScrollAnimations();
};

// Animate Welcome Header
const animateWelcomeHeader = () => {
    const header = document.querySelector(".welcome-header");
    const title = header?.querySelector("h1");
    const subtitle = header?.querySelector("p");

    if (title) {
        gsap.from(title, {
            duration: 0.8,
            opacity: 0,
            y: -50,
            scale: 0.9,
            ease: "back.out(1.7)",
            delay: 0.2,
        });
    }

    if (subtitle) {
        gsap.from(subtitle, {
            duration: 0.6,
            opacity: 0,
            y: 20,
            ease: "power2.out",
            delay: 0.5,
        });
    }

    // Animate floating circles
    const circles = header?.querySelectorAll(".floating-circles div");
    circles?.forEach((circle, index) => {
        gsap.from(circle, {
            duration: 1,
            opacity: 0,
            scale: 0,
            ease: "elastic.out(1, 0.5)",
            delay: 0.3 + index * 0.1,
        });
    });
};

// Animate Progress Card (Level Card)
const animateProgressCard = () => {
    const progressSection = document.querySelector(".rounded-xl.p-4.lg\\:mx-6");
    const levelCard = progressSection?.querySelector(
        '[style*="linear-gradient(to right, #101093, #931093)"]'
    );

    if (levelCard) {
        // Animate the entire card - slide from LEFT to RIGHT
        gsap.from(levelCard, {
            duration: 1,
            opacity: 0,
            x: -200, // Start further left
            ease: "power3.out",
            delay: 0.6,
        });

        // Animate rank image
        const rankImage = levelCard.querySelector("img");
        if (rankImage) {
            gsap.from(rankImage, {
                duration: 1,
                opacity: 0,
                scale: 0,
                rotation: -360,
                ease: "elastic.out(1, 0.6)",
                delay: 0.9,
            });
        }

        // Animate progress bar
        const progressBar = document.getElementById("levelProgressBar");
        if (progressBar) {
            const targetWidth = progressBar.style.width || "0%";
            progressBar.style.width = "0%";

            gsap.to(progressBar, {
                duration: 1.5,
                width: targetWidth,
                ease: "power2.out",
                delay: 1.2,
            });

            // Animate progress handle
            const handle = progressBar.querySelector(".level-progress-handle");
            if (handle) {
                gsap.from(handle, {
                    duration: 0.5,
                    scale: 0,
                    opacity: 0,
                    ease: "back.out(1.7)",
                    delay: 2,
                });

                // Add pulsing animation to handle
                gsap.to(handle, {
                    duration: 1.5,
                    scale: 1.1,
                    ease: "power1.inOut",
                    repeat: -1,
                    yoyo: true,
                    delay: 2.5,
                });
            }
        }
    }
};

// Animate Stats Grid
const animateStatsGrid = () => {
    const statsCards = document.querySelectorAll(
        ".grid.grid-cols-2.md\\:grid-cols-4 > div"
    );

    statsCards.forEach((card, index) => {
        // Animate card entrance
        gsap.from(card, {
            duration: 0.6,
            opacity: 0,
            y: 50,
            scale: 0.8,
            ease: "back.out(1.7)",
            delay: 1 + index * 0.1,
        });

        // Animate icon
        const icon = card.querySelector("img");
        if (icon) {
            gsap.from(icon, {
                duration: 0.8,
                opacity: 0,
                scale: 0,
                rotation: 180,
                ease: "elastic.out(1, 0.5)",
                delay: 1.2 + index * 0.1,
            });
        }

        // Animate number with counting effect
        const numberElement = card.querySelector(".text-xl, .text-2xl");
        if (numberElement) {
            const finalValue =
                parseInt(numberElement.textContent.replace(/,/g, "")) || 0;
            const counter = { value: 0 };

            gsap.to(counter, {
                duration: 1.5,
                value: finalValue,
                ease: "power1.out",
                delay: 1.4 + index * 0.1,
                onUpdate: function () {
                    numberElement.textContent = Math.floor(
                        counter.value
                    ).toLocaleString();
                },
            });
        }
    });
};

// Animate Assignments Section
const animateAssignments = () => {
    const assignmentsSection = document
        .querySelector('[style*="background-color: #B91E2A"]')
        ?.closest(".rounded-xl");

    if (assignmentsSection) {
        // Animate entire container - slide from LEFT to RIGHT
        gsap.from(assignmentsSection, {
            duration: 1,
            opacity: 0,
            x: -200, // Start from left
            ease: "power3.out",
            delay: 1.5,
        });

        // Animate assignment items with stagger after container appears
        const assignmentItems = assignmentsSection.querySelectorAll(
            '[style*="background-color: #FFEAEA"] > div > div'
        );
        assignmentItems.forEach((item, index) => {
            gsap.from(item, {
                duration: 0.6,
                opacity: 0,
                x: -50,
                ease: "power2.out",
                delay: 2 + index * 0.15,
            });

            // Animate buttons
            const button = item.querySelector("button");
            if (button) {
                gsap.from(button, {
                    duration: 0.5,
                    opacity: 0,
                    scale: 0.8,
                    ease: "back.out(1.7)",
                    delay: 2.2 + index * 0.15,
                });
            }
        });
    }
};

// Animate Achievements Section
const animateAchievements = () => {
    const achievementsSection = document
        .querySelector(
            '[style*="background: linear-gradient(to right, #3B82F6"]'
        )
        ?.closest(".rounded-xl");

    if (achievementsSection) {
        // Animate section entrance
        gsap.from(achievementsSection, {
            duration: 0.8,
            opacity: 0,
            y: 50,
            ease: "power2.out",
            delay: 1.8,
        });

        // Animate achievement items
        const achievementItems =
            achievementsSection.querySelectorAll(".grid > div");
        achievementItems.forEach((item, index) => {
            gsap.from(item, {
                duration: 0.7,
                opacity: 0,
                y: 30,
                scale: 0.5,
                rotation: -180,
                ease: "back.out(1.7)",
                delay: 2 + index * 0.1,
            });

            // Animate achievement images
            const img = item.querySelector("img");
            if (img) {
                gsap.from(img, {
                    duration: 0.8,
                    opacity: 0,
                    scale: 0,
                    rotation: 360,
                    ease: "elastic.out(1, 0.5)",
                    delay: 2.2 + index * 0.1,
                });
            }
        });
    }
};

// Animate Leaderboard
const animateLeaderboard = () => {
    const leaderboard = document
        .querySelector(".bg-gradient-to-r.from-orange-500")
        ?.closest(".rounded-xl");

    if (leaderboard) {
        // Animate leaderboard entrance
        gsap.from(leaderboard, {
            duration: 0.8,
            opacity: 0,
            x: 100,
            ease: "power2.out",
            delay: 2,
        });

        // Animate leaderboard items
        const leaderboardItems = leaderboard.querySelectorAll(
            ".space-y-2 > div, .space-y-3 > div"
        );
        leaderboardItems.forEach((item, index) => {
            gsap.from(item, {
                duration: 0.6,
                opacity: 0,
                x: 50,
                ease: "power2.out",
                delay: 2.2 + index * 0.1,
            });

            // Animate avatar with bounce
            const avatar = item.querySelector("img");
            if (avatar) {
                gsap.from(avatar, {
                    duration: 0.8,
                    scale: 0,
                    ease: "elastic.out(1, 0.6)",
                    delay: 2.3 + index * 0.1,
                });
            }

            // Animate rank badge
            const badge = item.querySelector(".px-2.py-1, .px-3.py-1");
            if (badge) {
                gsap.from(badge, {
                    duration: 0.5,
                    scale: 0,
                    rotation: 360,
                    ease: "back.out(1.7)",
                    delay: 2.4 + index * 0.1,
                });
            }
        });
    }
};

// Add Hover Animations
const addHoverAnimations = () => {
    // Stats cards hover
    const statsCards = document.querySelectorAll(
        ".grid.grid-cols-2.md\\:grid-cols-4 > div"
    );
    statsCards.forEach((card) => {
        card.addEventListener("mouseenter", () => {
            gsap.to(card, {
                duration: 0.3,
                y: -10,
                scale: 1.05,
                boxShadow: "0 20px 40px rgba(0,0,0,0.2)",
                ease: "power2.out",
            });

            const icon = card.querySelector("img");
            if (icon) {
                gsap.to(icon, {
                    duration: 0.3,
                    rotation: 360,
                    scale: 1.2,
                    ease: "back.out(1.7)",
                });
            }
        });

        card.addEventListener("mouseleave", () => {
            gsap.to(card, {
                duration: 0.3,
                y: 0,
                scale: 1,
                boxShadow: "0 0 0 rgba(0,0,0,0)",
                ease: "power2.out",
            });

            const icon = card.querySelector("img");
            if (icon) {
                gsap.to(icon, {
                    duration: 0.3,
                    rotation: 0,
                    scale: 1,
                    ease: "power2.out",
                });
            }
        });
    });

    // Assignment buttons hover
    const assignmentButtons = document.querySelectorAll(".bg-gradient-primary");
    assignmentButtons.forEach((button) => {
        button.addEventListener("mouseenter", () => {
            gsap.to(button, {
                duration: 0.3,
                scale: 1.05,
                y: -2,
                ease: "power2.out",
            });
        });

        button.addEventListener("mouseleave", () => {
            gsap.to(button, {
                duration: 0.3,
                scale: 1,
                y: 0,
                ease: "power2.out",
            });
        });
    });

    // Achievement items hover
    const achievementItems = document.querySelectorAll(
        ".grid.grid-cols-2.sm\\:grid-cols-3 > div"
    );
    achievementItems.forEach((item) => {
        item.addEventListener("mouseenter", () => {
            gsap.to(item, {
                duration: 0.3,
                y: -15,
                scale: 1.1,
                ease: "back.out(1.7)",
            });

            const img = item.querySelector("img");
            if (img) {
                gsap.to(img, {
                    duration: 0.5,
                    rotation: 360,
                    scale: 1.2,
                    ease: "elastic.out(1, 0.5)",
                });
            }
        });

        item.addEventListener("mouseleave", () => {
            gsap.to(item, {
                duration: 0.3,
                y: 0,
                scale: 1,
                ease: "power2.out",
            });

            const img = item.querySelector("img");
            if (img) {
                gsap.to(img, {
                    duration: 0.3,
                    rotation: 0,
                    scale: 1,
                    ease: "power2.out",
                });
            }
        });
    });

    // Leaderboard items hover
    const leaderboardItems = document.querySelectorAll(
        ".bg-gradient-to-b.from-yellow-100 .space-y-2 > div, .bg-gradient-to-b.from-yellow-100 .space-y-3 > div"
    );
    leaderboardItems.forEach((item) => {
        item.addEventListener("mouseenter", () => {
            gsap.to(item, {
                duration: 0.3,
                x: 10,
                scale: 1.03,
                boxShadow: "0 10px 20px rgba(0,0,0,0.1)",
                ease: "power2.out",
            });
        });

        item.addEventListener("mouseleave", () => {
            gsap.to(item, {
                duration: 0.3,
                x: 0,
                scale: 1,
                boxShadow: "0 0 0 rgba(0,0,0,0)",
                ease: "power2.out",
            });
        });
    });
};

// Add Scroll Animations
const addScrollAnimations = () => {
    // Create scroll trigger animations for elements that appear on scroll
    const scrollElements = document.querySelectorAll(
        ".lg\\:col-span-2, .space-y-6.lg\\:space-y-8"
    );

    scrollElements.forEach((element) => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        gsap.from(entry.target, {
                            duration: 0.8,
                            opacity: 0,
                            y: 50,
                            ease: "power2.out",
                        });
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.1,
            }
        );

        observer.observe(element);
    });
};

// Animate progress bar update (called from main dashboard script)
const animateProgressBarUpdate = (
    targetPercentage,
    currentPoints,
    pointsNeeded
) => {
    const progressBar = document.getElementById("levelProgressBar");
    const currentPointsText = document.getElementById("currentPointsText");
    const pointsNeededValue = document.getElementById("pointsNeededValue");

    if (progressBar) {
        gsap.to(progressBar, {
            duration: 1.5,
            width: targetPercentage + "%",
            ease: "elastic.out(1, 0.5)",
        });
    }

    if (currentPointsText && currentPoints !== undefined) {
        const counter = {
            value:
                parseInt(currentPointsText.textContent.replace(/,/g, "")) || 0,
        };
        gsap.to(counter, {
            duration: 1,
            value: currentPoints,
            ease: "power2.out",
            onUpdate: function () {
                currentPointsText.textContent = Math.floor(
                    counter.value
                ).toLocaleString();
            },
        });
    }

    if (pointsNeededValue && pointsNeeded !== undefined) {
        const counter = {
            value:
                parseInt(pointsNeededValue.textContent.replace(/,/g, "")) || 0,
        };
        gsap.to(counter, {
            duration: 1,
            value: pointsNeeded,
            ease: "power2.out",
            onUpdate: function () {
                pointsNeededValue.textContent = Math.floor(
                    counter.value
                ).toLocaleString();
            },
        });
    }
};

// Particle effect for achievements
const createParticleEffect = (element) => {
    const particleCount = 20;
    const particles = [];

    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement("div");
        particle.style.position = "absolute";
        particle.style.width = "6px";
        particle.style.height = "6px";
        particle.style.borderRadius = "50%";
        particle.style.backgroundColor = `hsl(${
            Math.random() * 360
        }, 70%, 60%)`;
        particle.style.pointerEvents = "none";
        particle.style.left = "50%";
        particle.style.top = "50%";

        element.appendChild(particle);
        particles.push(particle);

        gsap.to(particle, {
            duration: 1,
            x: (Math.random() - 0.5) * 200,
            y: (Math.random() - 0.5) * 200,
            opacity: 0,
            scale: 0,
            ease: "power2.out",
            onComplete: () => particle.remove(),
        });
    }
};

// Export functions for use in main dashboard script
window.dashboardAnimations = {
    animateProgressBarUpdate,
    createParticleEffect,
    initDashboardAnimations,
};

console.log("Dashboard animations script loaded");
