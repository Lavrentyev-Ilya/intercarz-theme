/**
 * InterCarz — UI поведение оболочки: мобильное меню, выпадающая мини-корзина.
 *
 * Корзина в шапке (#cp-cart) пересоздаётся при AJAX-подмене со стороны CPMod
 * (и при WooCommerce-фрагментах), поэтому слушатели вешаем через делегирование
 * от document, а не на сами элементы.
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var overlay = document.querySelector('[data-overlay]');
		var navToggle = document.querySelector('.nav-toggle');
		var nav = document.querySelector('.main-nav');
		var desktop = window.matchMedia('(min-width: 769px)');

		function miniCart() { return document.querySelector('[data-mini-cart] .mini-cart'); }

		function closeNav() {
			if (nav) nav.classList.remove('is-open');
			if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
			syncOverlay();
		}
		function closeCart() {
			var panel = miniCart();
			if (panel) panel.classList.remove('is-open');
			var link = document.querySelector('[data-mini-cart] .cart-link');
			if (link) link.setAttribute('aria-expanded', 'false');
			syncOverlay();
		}
		function syncOverlay() {
			if (!overlay) return;
			var panel = miniCart();
			var anyOpen =
				(nav && nav.classList.contains('is-open')) ||
				(panel && panel.classList.contains('is-open'));
			overlay.classList.toggle('is-open', !!anyOpen);
		}

		/* ---------- Мобильное меню ---------- */
		if (navToggle && nav) {
			navToggle.addEventListener('click', function () {
				var open = nav.classList.toggle('is-open');
				navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
				syncOverlay();
			});
		}

		/* ---------- Мини-корзина (делегирование) ---------- */
		document.addEventListener('click', function (e) {
			var link = e.target.closest ? e.target.closest('.cart-link') : null;
			var insideCart = e.target.closest ? e.target.closest('[data-mini-cart]') : null;

			if (link && desktop.matches) {
				// На десктопе клик по иконке открывает панель, а не уходит в корзину.
				e.preventDefault();
				var panel = link.parentElement.querySelector('.mini-cart');
				if (panel) {
					var open = panel.classList.toggle('is-open');
					link.setAttribute('aria-expanded', open ? 'true' : 'false');
					syncOverlay();
				}
				return;
			}
			// Клик вне корзины — закрыть.
			if (!insideCart) closeCart();
		});

		/* ---------- Overlay / Esc ---------- */
		if (overlay) {
			overlay.addEventListener('click', function () { closeNav(); closeCart(); });
		}
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { closeNav(); closeCart(); }
		});

		/* ---------- WooCommerce fragments ---------- */
		document.body.addEventListener('wc_fragments_refreshed', function () {
			var link = document.querySelector('[data-mini-cart] .cart-link');
			if (!link) return;
			link.classList.add('cart-link--bump');
			setTimeout(function () { link.classList.remove('cart-link--bump'); }, 300);
		});
	});
})();
