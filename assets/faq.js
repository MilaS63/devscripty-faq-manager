/* DevScripty FAQ Manager 1.1: accordion, category tabs and search. No network requests. */
(function () {
	'use strict';

	function init(root) {
		var single = root.getAttribute('data-single') === '1';
		var items = Array.prototype.slice.call(root.querySelectorAll('[data-ds-faq-item]'));
		var tabs = Array.prototype.slice.call(root.querySelectorAll('.ds-faq__tab'));
		var input = root.querySelector('[data-ds-faq-search]');
		var empty = root.querySelector('[data-ds-faq-empty]');
		var category = 'all';
		var term = '';

		function setOpen(item, open) {
			var button = item.querySelector('.ds-faq__q');
			item.classList.toggle('is-open', open);
			if (button) {
				button.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		}

		function applyFilters() {
			var visible = 0;
			items.forEach(function (item) {
				var cats = (item.getAttribute('data-categories') || '').split(' ');
				var matchCat = category === 'all' || cats.indexOf(category) !== -1;
				var matchTerm = term === '' || item.textContent.toLowerCase().indexOf(term) !== -1;
				var show = matchCat && matchTerm;
				item.hidden = !show;
				if (show) {
					visible++;
				}
			});
			if (empty) {
				empty.hidden = visible !== 0;
			}
		}

		root.addEventListener('click', function (event) {
			var question = event.target.closest('.ds-faq__q');
			if (question && root.contains(question)) {
				var item = question.closest('[data-ds-faq-item]');
				var willOpen = !item.classList.contains('is-open');
				if (willOpen && single) {
					items.forEach(function (other) {
						if (other !== item) {
							setOpen(other, false);
						}
					});
				}
				setOpen(item, willOpen);
				return;
			}

			var tab = event.target.closest('.ds-faq__tab');
			if (tab && root.contains(tab)) {
				category = tab.getAttribute('data-category') || 'all';
				tabs.forEach(function (other) {
					var active = other === tab;
					other.classList.toggle('is-active', active);
					other.setAttribute('aria-pressed', active ? 'true' : 'false');
				});
				applyFilters();
			}
		});

		if (input) {
			input.addEventListener('input', function () {
				term = input.value.trim().toLowerCase();
				applyFilters();
			});
		}

		root.classList.add('is-ready');
	}

	function ready() {
		Array.prototype.forEach.call(document.querySelectorAll('[data-ds-faq]'), init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', ready);
	} else {
		ready();
	}
})();
