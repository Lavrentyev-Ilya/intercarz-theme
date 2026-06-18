/**
 * InterCarz — слайдеры главной (герой и отзывы). Без зависимостей.
 */
(function () {
	'use strict';

	function initSlider(root) {
		var track = root.querySelector('.hero-slider__track');
		if (!track) return;
		var slides = Array.prototype.slice.call(track.children);
		if (slides.length < 2) return;

		var per = parseInt(root.getAttribute('data-per') || '1', 10);
		var index = 0;
		var maxIndex = Math.max(0, slides.length - per);

		function step() {
			var first = slides[0];
			var styles = getComputedStyle(track);
			var gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
			return first.getBoundingClientRect().width + gap;
		}
		function update() {
			if (index > maxIndex) index = maxIndex;
			if (index < 0) index = 0;
			track.style.transform = 'translateX(' + (-index * step()) + 'px)';
			if (dotsWrap) {
				Array.prototype.forEach.call(dotsWrap.children, function (d, i) {
					d.classList.toggle('is-active', i === index);
				});
			}
		}
		function go(n) { index = n; update(); }
		function next() { index = index >= maxIndex ? 0 : index + 1; update(); }
		function prev() { index = index <= 0 ? maxIndex : index - 1; update(); }

		var prevBtn = root.querySelector('[data-slider-prev]');
		var nextBtn = root.querySelector('[data-slider-next]');
		if (prevBtn) prevBtn.addEventListener('click', prev);
		if (nextBtn) nextBtn.addEventListener('click', next);

		// Точки (только для одиночных слайдов).
		var dotsWrap = root.querySelector('[data-slider-dots]');
		if (dotsWrap && per === 1) {
			slides.forEach(function (_, i) {
				var b = document.createElement('button');
				b.type = 'button';
				b.addEventListener('click', function () { go(i); });
				dotsWrap.appendChild(b);
			});
		}

		// Автопрокрутка для одиночного героя.
		if (per === 1) {
			var timer = setInterval(next, 6000);
			root.addEventListener('mouseenter', function () { clearInterval(timer); });
			root.addEventListener('mouseleave', function () { timer = setInterval(next, 6000); });
		}

		window.addEventListener('resize', update);
		update();
	}

	document.addEventListener('DOMContentLoaded', function () {
		Array.prototype.forEach.call(document.querySelectorAll('[data-slider]'), initSlider);
	});
})();
