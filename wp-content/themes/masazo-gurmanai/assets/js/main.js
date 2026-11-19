/**
 * Masažo Gurmanai — svetainės scenarijus (be bibliotekų).
 *
 *  1. Elementų pasirodymas slenkant (IntersectionObserver, be scroll įvykių).
 *  2. Antraštės linija, kai puslapis paslinktas.
 *  3. Mobilus meniu (<dialog>).
 *  4. Žemėlapis įkeliamas tik paspaudus mygtuką.
 *  5. Kainoraštis: paslauga atidaroma pagal nuorodą (#paslauga-123).
 *  6. Rezervacijos, skambučio ir kupono nuorodų paspaudimai → GA4 / Google Ads / Meta Pixel.
 */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var inEditor = doc.body && doc.body.classList.contains('elementor-editor-active');

	/* 1. Pasirodymas slenkant ------------------------------------------------ */

	function initReveal() {
		var items = Array.prototype.slice.call(doc.querySelectorAll('[data-mg-reveal]'));
		if (!items.length || reduceMotion || inEditor || !('IntersectionObserver' in window)) {
			return;
		}

		var viewport = window.innerHeight || root.clientHeight;

		// Tai, kas jau matoma įkėlus puslapį, lieka matoma — nemirksi.
		items.forEach(function (el) {
			if (el.getBoundingClientRect().top < viewport * 0.92) {
				el.classList.add('is-in');
			}
		});

		// Greta esantys elementai pasirodo vienas po kito.
		items.forEach(function (el) {
			if (el.classList.contains('is-in') || !el.parentElement) {
				return;
			}
			var siblings = Array.prototype.filter.call(el.parentElement.children, function (child) {
				return child.hasAttribute('data-mg-reveal');
			});
			var index = siblings.indexOf(el);
			if (index > 0) {
				el.style.setProperty('--mg-delay', Math.min(index, 4) * 70 + 'ms');
			}
		});

		root.classList.add('mg-js');

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-in');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);

		items.forEach(function (el) {
			if (!el.classList.contains('is-in')) {
				observer.observe(el);
			}
		});

		// Atsarginis variantas: jei stebėtojas dėl kokios nors priežasties nesuveikė (pvz. puslapis
		// buvo fone), baigus slinkti ar grįžus į skirtuką parodome viską, kas jau yra ekrane ar aukščiau.
		function revealPassed() {
			var limit = (window.innerHeight || root.clientHeight) * 0.95;
			items.forEach(function (el) {
				if (!el.classList.contains('is-in') && el.getBoundingClientRect().top < limit) {
					el.classList.add('is-in');
					observer.unobserve(el);
				}
			});
		}

		window.addEventListener('scrollend', revealPassed, { passive: true });
		window.addEventListener('pageshow', revealPassed);
		doc.addEventListener('visibilitychange', function () {
			if (!doc.hidden) {
				revealPassed();
			}
		});
	}

	/* 2. Antraštė ------------------------------------------------------------- */

	function initHeader() {
		var header = doc.getElementById('mg-header');
		var sentinel = doc.querySelector('.mg-header-sentinel');
		if (!header || !sentinel || !('IntersectionObserver' in window)) {
			return;
		}

		new IntersectionObserver(function (entries) {
			header.classList.toggle('is-stuck', !entries[0].isIntersecting);
		}).observe(sentinel);
	}

	/* 3. Mobilus meniu -------------------------------------------------------- */

	function initDrawer() {
		var drawer = doc.getElementById('mg-drawer');
		var opener = doc.querySelector('[data-mg-drawer-open]');
		if (!drawer || !opener || typeof drawer.showModal !== 'function') {
			return;
		}

		opener.addEventListener('click', function () {
			drawer.showModal();
		});

		drawer.addEventListener('click', function (event) {
			var target = event.target;
			// Paspaudus už meniu ribų (ant užtamsinto fono), uždarymo mygtuką ar nuorodą — uždarome.
			if (target === drawer || target.closest('[data-mg-drawer-close]') || target.closest('a')) {
				drawer.close();
			}
		});

		drawer.addEventListener('close', function () {
			opener.focus();
		});
	}

	/* 4. Žemėlapis ------------------------------------------------------------ */

	function initMaps() {
		Array.prototype.forEach.call(doc.querySelectorAll('[data-mg-map]'), function (box) {
			var button = box.querySelector('button');
			if (!button) {
				return;
			}
			button.addEventListener('click', function () {
				var frame = doc.createElement('iframe');
				frame.src = box.getAttribute('data-mg-map');
				frame.title = box.getAttribute('data-mg-map-title') || 'Žemėlapis';
				frame.loading = 'lazy';
				frame.referrerPolicy = 'no-referrer-when-downgrade';
				frame.setAttribute('allowfullscreen', '');
				frame.tabIndex = 0;
				box.insertBefore(frame, box.firstChild);
				button.remove();
				frame.focus();
			});
		});
	}

	/* 5. Kainoraštis ---------------------------------------------------------- */

	function openServiceFromHash() {
		var hash = window.location.hash;
		if (!/^#paslauga-\d+$/.test(hash)) {
			return;
		}
		var row = doc.querySelector(hash);
		var details = row && row.querySelector('details');
		if (details) {
			details.open = true;
		}
	}

	/* 6. Paspaudimų sekimas --------------------------------------------------- */

	function send(name, params) {
		try {
			if (typeof window.gtag === 'function') {
				window.gtag('event', name, params);
			} else if (window.dataLayer && typeof window.dataLayer.push === 'function') {
				var payload = { event: name };
				Object.keys(params).forEach(function (key) {
					payload[key] = params[key];
				});
				window.dataLayer.push(payload);
			}
		} catch (e) {}
	}

	function trackBooking(link) {
		var config = window.mgConfig || {};
		var service = link.getAttribute('data-mg-service') || 'Bendra rezervacija';
		var place = link.getAttribute('data-mg-place') || '';

		send(config.ga4Event || 'rezervacija_treatwell', {
			event_category: 'Rezervacija',
			event_label: service,
			service: service,
			link_place: place,
			link_url: link.href,
			transport_type: 'beacon'
		});

		if (config.adsSendTo && typeof window.gtag === 'function') {
			try {
				window.gtag('event', 'conversion', { send_to: config.adsSendTo, transport_type: 'beacon' });
			} catch (e) {}
		}

		if (config.metaEvent && typeof window.fbq === 'function') {
			try {
				window.fbq('track', config.metaEvent, { content_name: service, content_category: 'Rezervacija' });
			} catch (e) {}
		}
	}

	function initTracking() {
		doc.addEventListener(
			'click',
			function (event) {
				var link = event.target && event.target.closest ? event.target.closest('a[href]') : null;
				if (!link) {
					return;
				}
				var config = window.mgConfig || {};
				var href = link.getAttribute('href') || '';

				if (link.hasAttribute('data-mg-book')) {
					trackBooking(link);
				} else if (href.indexOf('tel:') === 0 && config.trackCalls) {
					send('skambutis', { event_category: 'Kontaktai', event_label: href.replace('tel:', ''), transport_type: 'beacon' });
					if (typeof window.fbq === 'function') {
						try {
							window.fbq('track', 'Contact');
						} catch (e) {}
					}
				} else if (link.hasAttribute('data-mg-voucher')) {
					send('kupono_nuoroda', { event_category: 'Dovanų kuponai', event_label: link.getAttribute('data-mg-voucher'), transport_type: 'beacon' });
				}
			},
			true
		);
	}

	/* Paleidimas ------------------------------------------------------------- */

	function init() {
		initReveal();
		initHeader();
		initDrawer();
		initMaps();
		openServiceFromHash();
		initTracking();
		window.addEventListener('hashchange', openServiceFromHash);
	}

	if (doc.readyState === 'loading') {
		doc.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
