/*
 * Current Date.
 *
 * Re-renders the server-rendered value in the browser so a cached page cannot
 * show a stale date, and so a clock keeps ticking.
 *
 * It formats using the SAME PHP format string PHP used, rather than a parallel
 * set of Intl options — one source of truth, so the two renderers cannot drift
 * apart. Timezone-correct fields come from Intl.DateTimeFormat.formatToParts()
 * rather than from date arithmetic, which is what makes an arbitrary IANA zone
 * (and its DST) correct without shipping a timezone database.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-current-date]';

	const pad = (n) => String(n).padStart(2, '0');

	/**
	 * Numeric date fields for a given IANA timezone.
	 *
	 * Read through the en-US locale on purpose: this is parsing, not display, and
	 * en-US gives stable ASCII digits. Locale-specific NAMES are fetched
	 * separately below with the site's own locale.
	 */
	const zonedParts = (date, timeZone) => {
		const fmt = new Intl.DateTimeFormat('en-US', {
			timeZone,
			hour12: false,
			year: 'numeric',
			month: '2-digit',
			day: '2-digit',
			hour: '2-digit',
			minute: '2-digit',
			second: '2-digit'
		});

		const out = {};
		fmt.formatToParts(date).forEach((p) => {
			if (p.type !== 'literal') {
				out[p.type] = p.value;
			}
		});

		// Some engines render midnight as hour 24 under hour12:false.
		if (out.hour === '24') {
			out.hour = '00';
		}

		return {
			year: parseInt(out.year, 10),
			month: parseInt(out.month, 10),
			day: parseInt(out.day, 10),
			hour: parseInt(out.hour, 10),
			minute: parseInt(out.minute, 10),
			second: parseInt(out.second, 10)
		};
	};

	const named = (date, timeZone, locale, options) => {
		try {
			return new Intl.DateTimeFormat(locale, Object.assign({ timeZone }, options)).format(date);
		} catch (error) {
			return new Intl.DateTimeFormat('en', Object.assign({ timeZone }, options)).format(date);
		}
	};

	const tzAbbr = (date, timeZone, locale) => {
		try {
			const parts = new Intl.DateTimeFormat(locale, {
				timeZone,
				timeZoneName: 'short'
			}).formatToParts(date);
			const found = parts.find((p) => p.type === 'timeZoneName');
			return found ? found.value : timeZone;
		} catch (error) {
			return timeZone;
		}
	};

	// English ordinal suffix, matching PHP's `S`.
	const ordinal = (d) => {
		if (d > 3 && d < 21) {
			return 'th';
		}
		return ['th', 'st', 'nd', 'rd'][d % 10] || 'th';
	};

	/**
	 * Render a PHP date format string.
	 *
	 * Covers the tokens people actually put in a format; anything unrecognised is
	 * emitted verbatim, which is what PHP does too.
	 */
	const phpDate = (format, date, timeZone, locale) => {
		const p = zonedParts(date, timeZone);

		// Weekday index without arithmetic on a shifted date: ask Intl.
		const weekdayShort = named(date, timeZone, 'en-US', { weekday: 'short' });
		const dayIndex = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].indexOf(weekdayShort);

		const hour12 = p.hour % 12 === 0 ? 12 : p.hour % 12;

		let out = '';

		for (let i = 0; i < format.length; i++) {
			const ch = format[i];

			if (ch === '\\') {
				i++;
				if (i < format.length) {
					out += format[i];
				}
				continue;
			}

			switch (ch) {
				case 'd': out += pad(p.day); break;
				case 'j': out += String(p.day); break;
				case 'S': out += ordinal(p.day); break;
				case 'D': out += named(date, timeZone, locale, { weekday: 'short' }); break;
				case 'l': out += named(date, timeZone, locale, { weekday: 'long' }); break;
				case 'N': out += String(dayIndex === 0 ? 7 : dayIndex); break;
				case 'w': out += String(dayIndex); break;
				case 'm': out += pad(p.month); break;
				case 'n': out += String(p.month); break;
				case 'M': out += named(date, timeZone, locale, { month: 'short' }); break;
				case 'F': out += named(date, timeZone, locale, { month: 'long' }); break;
				case 'y': out += pad(p.year % 100); break;
				case 'Y': out += String(p.year); break;
				case 'g': out += String(hour12); break;
				case 'h': out += pad(hour12); break;
				case 'G': out += String(p.hour); break;
				case 'H': out += pad(p.hour); break;
				case 'i': out += pad(p.minute); break;
				case 's': out += pad(p.second); break;
				case 'A': out += p.hour < 12 ? 'AM' : 'PM'; break;
				case 'a': out += p.hour < 12 ? 'am' : 'pm'; break;
				case 'T': out += tzAbbr(date, timeZone, locale); break;
				case 'e': out += timeZone; break;
				case 'U': out += String(Math.floor(date.getTime() / 1000)); break;
				case 'L': out += ((p.year % 4 === 0 && p.year % 100 !== 0) || p.year % 400 === 0) ? '1' : '0'; break;
				default: out += ch;
			}
		}

		return out;
	};

	// ISO-8601 with the zone's real offset, for the `datetime` attribute.
	const machine = (date, timeZone) => {
		const p = zonedParts(date, timeZone);
		const asUtc = Date.UTC(p.year, p.month - 1, p.day, p.hour, p.minute, p.second);
		const offsetMin = Math.round((asUtc - Math.floor(date.getTime() / 1000) * 1000) / 60000);
		const sign = offsetMin >= 0 ? '+' : '-';
		const abs = Math.abs(offsetMin);

		return `${p.year}-${pad(p.month)}-${pad(p.day)}T${pad(p.hour)}:${pad(p.minute)}:${pad(p.second)}`
			+ `${sign}${pad(Math.floor(abs / 60))}:${pad(abs % 60)}`;
	};

	const setup = (el) => {
		if (el.dataset.eapCdBound === '1') {
			return;
		}
		el.dataset.eapCdBound = '1';

		let cfg;
		try {
			cfg = JSON.parse(el.getAttribute('data-eap-current-date') || '');
		} catch (error) {
			return;
		}
		if (!cfg || !cfg.format) {
			return;
		}

		const timeZone = cfg.timeZone || 'UTC';
		const locale = cfg.locale || 'en';
		const interval = Math.max(1000, parseInt(cfg.interval, 10) || 60000);

		let last = '';

		const tick = () => {
			const now = new Date();
			let text;
			try {
				text = phpDate(cfg.format, now, timeZone, locale);
			} catch (error) {
				// A bad timezone would otherwise throw every interval; leave the
				// server-rendered value in place and stop.
				window.clearInterval(timer);
				return;
			}

			if (text !== last) {
				last = text;
				el.textContent = text;
				el.setAttribute('datetime', machine(now, timeZone));
			}
		};

		tick();
		const timer = window.setInterval(tick, interval);
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('current-date', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
