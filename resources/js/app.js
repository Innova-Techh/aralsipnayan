import './bootstrap';

// Auto-scroll for the 2nd section carousel (one full slide at a time)
document.addEventListener('DOMContentLoaded', () => {
	const carousel = document.getElementById('features-carousel');
	if (!carousel) return;

	let isPaused = false;
	const pause = () => { isPaused = true; };
	const resume = () => { isPaused = false; };

	carousel.addEventListener('mouseenter', pause);
	carousel.addEventListener('mouseleave', resume);
	carousel.addEventListener('touchstart', pause, { passive: true });
	carousel.addEventListener('touchend', resume, { passive: true });

	// Build an infinite (360) carousel by cloning edges
	const originalSlides = Array.from(carousel.children);
	if (originalSlides.length === 0) return;
	const first = originalSlides[0];
	const last = originalSlides[originalSlides.length - 1];
	const firstClone = first.cloneNode(true);
	const lastClone = last.cloneNode(true);
	carousel.insertBefore(lastClone, first);
	carousel.appendChild(firstClone);

	let index = 1; // start at first real slide

	const getSlides = () => Array.from(carousel.children);

	const scrollToCenter = (el, behavior = 'smooth') => {
		const targetLeft = el.offsetLeft - (carousel.clientWidth - el.clientWidth) / 2;
		carousel.scrollTo({ left: targetLeft, behavior });
	};

	const slideTo = (i, { behavior = 'smooth' } = {}) => {
		const slides = getSlides();
		const target = slides[i];
		if (!target) return;
		scrollToCenter(target, behavior);
		index = i;
	};

	// initial snap to first real slide (index 1 due to prepended clone)
	slideTo(index, { behavior: 'auto' });

	// Click-to-center: when a user clicks any visible card, center it
	carousel.addEventListener('click', (event) => {
		const card = event.target.closest('.carousel-card');
		if (!card || !carousel.contains(card)) return;
		const slides = getSlides();
		let clickedIndex = slides.indexOf(card);
		if (clickedIndex === -1) return;
		// If a clone is clicked, map to the corresponding real slide
		if (clickedIndex === 0) {
			clickedIndex = slides.length - 2; // last real slide
		} else if (clickedIndex === slides.length - 1) {
			clickedIndex = 1; // first real slide
		}
		slideTo(clickedIndex, { behavior: 'smooth' });
	});

	// Handle manual scroll to keep loop illusion when user swipes to clones
	let scrollTimeoutId = null;
	const onScrollSettled = () => {
		const slides = getSlides();
		// find the slide most centered
		const containerRect = carousel.getBoundingClientRect();
		const containerCenter = containerRect.left + containerRect.width / 2;
		let closestIdx = 0;
		let closestDist = Number.POSITIVE_INFINITY;
		slides.forEach((el, i) => {
			const r = el.getBoundingClientRect();
			const center = r.left + r.width / 2;
			const d = Math.abs(center - containerCenter);
			if (d < closestDist) { closestDist = d; closestIdx = i; }
		});
		// wrap if on either clone, otherwise set index to centered slide
		if (closestIdx === 0) {
			index = slides.length - 2; // last real slide
			scrollToCenter(slides[index], 'auto');
		} else if (closestIdx === slides.length - 1) {
			index = 1; // first real slide
			scrollToCenter(slides[index], 'auto');
		} else {
			index = closestIdx;
		}
	};

	carousel.addEventListener('scroll', () => {
		if (scrollTimeoutId) clearTimeout(scrollTimeoutId);
		scrollTimeoutId = setTimeout(onScrollSettled, 120);
	}, { passive: true });
});
