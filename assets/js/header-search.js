/**
 * InterCarz — поиск в шапке как поиск по артикулу модуля CPMod.
 *
 * Портирует логику из {модуль}/add/search/default/template.php:
 *  - чистит ввод, пробелы → "_";
 *  - при длине > minLen POST на {base}/search/{q}/ с параметрами CPMod;
 *  - ответ: "REDIRECT:url" → переход; "SEARCH_NOT_FOUND" → сообщение;
 *    иначе → HTML выпадающего списка в #CmSearchResult.
 *
 * Конфиг приходит из wp_localize_script (intercarzSearch).
 */
(function () {
	'use strict';

	var cfg = window.intercarzSearch || {};
	var BASE = (cfg.base || '/carparts').replace(/\/+$/, '');
	var MIN_LEN = parseInt(cfg.minLen || 2, 10);
	var NOT_FOUND = cfg.notFound || 'Ничего не найдено';

	// Тот же набор допустимых символов, что и в модуле.
	var CLEAN_RE = /[^a-zа-яА-ЯA-Z0-9ІіЄєÄäÖöẞßÜüËëĄąĆćĘęŁłŃńÓóŚśŹźŻżğĞçÇşŞıİ \/.-]+/g;

	function clean(value) {
		return value.replace(CLEAN_RE, '').replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
	}

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.querySelector('form[data-cp-search]');
		if (!form) return;
		var input = form.querySelector('#cp-art-search');
		var result = form.querySelector('#CmSearchResult');
		if (!input || !result) return;

		function hideResult() { result.style.display = 'none'; result.innerHTML = ''; }

		function run() {
			var val = clean(input.value);
			var req = val.replace(/\s+/g, '_');
			if (req.length <= MIN_LEN) { input.focus(); return; }

			form.classList.add('is-loading');
			input.disabled = true;

			var body = 'CarModAjax=Y&ShortResult=Y&HideStat=Y&WithRedirects=Y&ArtSearch=' + encodeURIComponent(req);

			fetch(BASE + '/search/' + encodeURIComponent(req) + '/', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: body,
				credentials: 'same-origin'
			})
				.then(function (r) { return r.text(); })
				.then(function (text) {
					form.classList.remove('is-loading');
					input.disabled = false;

					var parts = text.split('REDIRECT:');
					if (parts.length > 1) {
						window.location = parts[1].trim();
						return;
					}

					var probe = document.createElement('div');
					probe.innerHTML = text;
					var plain = (probe.textContent || '').trim();

					if (plain === 'SEARCH_NOT_FOUND') {
						result.innerHTML = '<div class="CmSearchBox CmSearchNotFound">' + NOT_FOUND + '</div>';
					} else {
						result.innerHTML = text;
					}
					result.style.display = 'block';
				})
				.catch(function () {
					form.classList.remove('is-loading');
					input.disabled = false;
					// Фолбэк: уводим на страницу результатов модуля.
					window.location = BASE + '/search/?ArtSearch=' + encodeURIComponent(req);
				});
		}

		// Enter в поле / отправка формы / клик по кнопке — запускаем поиск.
		form.addEventListener('submit', function (e) { e.preventDefault(); run(); });
		input.addEventListener('keyup', function (e) { if (e.key === 'Enter') { e.preventDefault(); run(); } });

		// Закрытие списка по клику вне формы / Esc.
		document.addEventListener('click', function (e) {
			if (!form.contains(e.target)) hideResult();
		});
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hideResult(); });
	});
})();
