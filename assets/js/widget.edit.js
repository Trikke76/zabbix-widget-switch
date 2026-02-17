(function() {
	function getNumericValue(value) {
		const text = String(value || '').trim();
		return /^\d+$/.test(text) ? text : '';
	}

	function extractHostId(value) {
		const text = String(value || '').trim();
		if (text === '') {
			return '';
		}

		const direct = getNumericValue(text);
		if (direct !== '') {
			return direct;
		}

		const idMatch = text.match(/"id"\s*:\s*"(\d+)"/);
		if (idMatch) {
			return idMatch[1];
		}

		const arrayMatch = text.match(/\[\s*"(\d+)"\s*\]/);
		if (arrayMatch) {
			return arrayMatch[1];
		}

		const fallbackNumber = text.match(/\b(\d{3,})\b/);
		return fallbackNumber ? fallbackNumber[1] : '';
	}

	function getHostId() {
		const selectors = [
			'input[name="fields[hostids][]"]',
			'input[name^="fields[hostids]["]',
			'input[name="hostids[]"]',
			'input[name^="hostids["]',
			'input[name*="hostids"]',
			'#hostids input[type="hidden"]',
			'#hostids_ms input[type="hidden"]',
			'[id^="hostids"] input[type="hidden"]',
			'[data-name="hostids"] input[type="hidden"]'
		];

		for (const selector of selectors) {
			for (const input of document.querySelectorAll(selector)) {
				const hostid = extractHostId(input.value);
				if (hostid !== '') {
					return hostid;
				}
			}
		}

		// Fallback: read selected token id in Zabbix multiselect chip list.
		const tokenSelectors = [
			'#hostids_ms [data-id]',
			'[id^="hostids"] [data-id]',
			'[data-name="hostids"] [data-id]'
		];

		for (const selector of tokenSelectors) {
			const token = document.querySelector(selector);
			if (token && token.dataset && token.dataset.id) {
				const hostid = extractHostId(token.dataset.id);
				if (hostid !== '') {
					return hostid;
				}
			}
		}

		return '';
	}

	function getTriggerFields() {
		const selectors = [
			'[name*="triggerid"]',
			'[id*="triggerid"]'
		];

		const found = new Map();
		for (const selector of selectors) {
			for (const element of document.querySelectorAll(selector)) {
				const token = `${element.name || ''} ${element.id || ''}`;
				if (!/port\d+_triggerid/.test(token)) {
					continue;
				}

				const key = element.name || element.id || String(found.size);
				if (!found.has(key)) {
					found.set(key, element);
				}
			}
		}

		return Array.from(found.values());
	}

	function normalizeHexColor(value, fallback) {
		const text = String(value || '').trim();
		if (/^#[0-9a-fA-F]{6}$/.test(text)) {
			return text.toUpperCase();
		}
		if (/^[0-9a-fA-F]{6}$/.test(text)) {
			return `#${text.toUpperCase()}`;
		}
		return fallback;
	}

	function getColorFields() {
		const fields = [];
		const seen = new Set();
		for (const field of document.querySelectorAll('input')) {
			const token = `${field.name || ''} ${field.id || ''}`;
			if (!/port\d+_(default|trigger)_color/.test(token)) {
				continue;
			}

			const key = field.name || field.id;
			if (key && !seen.has(key)) {
				seen.add(key);
				fields.push(field);
			}
		}
		return fields;
	}

	function ensureColorPickerForField(field) {
		if (field.dataset.port24ColorInit === '1') {
			return;
		}

		const wrapper = document.createElement('span');
		wrapper.style.display = 'inline-flex';
		wrapper.style.alignItems = 'center';
		wrapper.style.gap = '8px';
		wrapper.style.width = '100%';

		const picker = document.createElement('input');
		picker.type = 'color';
		picker.style.width = '42px';
		picker.style.minWidth = '42px';
		picker.style.height = '24px';
		picker.style.padding = '0';
		picker.style.border = '0';
		picker.style.background = 'transparent';

		const parent = field.parentNode;
		if (!parent) {
			return;
		}

		const fallback = /trigger_color/.test(`${field.name || ''} ${field.id || ''}`) ? '#E53E3E' : '#2F855A';
		field.value = normalizeHexColor(field.value, fallback);
		picker.value = field.value;

		field.style.flex = '1 1 auto';
		field.style.minWidth = '0';

		parent.insertBefore(wrapper, field);
		wrapper.appendChild(field);
		wrapper.appendChild(picker);

		picker.addEventListener('input', () => {
			field.value = picker.value.toUpperCase();
			field.dispatchEvent(new Event('change', {bubbles: true}));
		});

		field.addEventListener('input', () => {
			const normalized = normalizeHexColor(field.value, picker.value || fallback);
			field.value = normalized;
			picker.value = normalized;
		});

		field.dataset.port24ColorInit = '1';
	}

	function setFieldColor(field, color) {
		field.value = color.toUpperCase();
		field.dispatchEvent(new Event('input', {bubbles: true}));
		field.dispatchEvent(new Event('change', {bubbles: true}));
	}

	function ensureBulkControls() {
		if (document.getElementById('port24-bulk-tools') !== null) {
			return;
		}

		const firstFieldset = document.querySelector('fieldset.collapsible');
		if (!firstFieldset || !firstFieldset.parentNode) {
			return;
		}

		const allColorFields = getColorFields();
		const defaultField = allColorFields.find((field) =>
			/port\d+_default_color/.test(`${field.name || ''} ${field.id || ''}`)
		);
		const triggerField = allColorFields.find((field) =>
			/port\d+_trigger_color/.test(`${field.name || ''} ${field.id || ''}`)
		);

		const panel = document.createElement('fieldset');
		panel.id = 'port24-bulk-tools';
		panel.className = 'collapsible';
		panel.style.padding = '12px';
		panel.style.border = '1px solid #dfe4ea';
		panel.style.borderRadius = '4px';
		panel.style.marginBottom = '12px';

		const title = document.createElement('legend');
		title.textContent = 'Bulk actions';
		title.style.fontWeight = '600';
		panel.appendChild(title);

		const row = document.createElement('div');
		row.style.display = 'grid';
		row.style.gridTemplateColumns = '1fr 1fr';
		row.style.gap = '12px';

		const makeAction = (labelText, initialColor, matcher) => {
			const box = document.createElement('div');
			box.style.display = 'flex';
			box.style.alignItems = 'center';
			box.style.gap = '8px';

			const label = document.createElement('label');
			label.textContent = labelText;
			label.style.minWidth = '120px';

			const picker = document.createElement('input');
			picker.type = 'color';
			picker.value = normalizeHexColor(initialColor, '#2F855A');

			const button = document.createElement('button');
			button.type = 'button';
			button.className = 'btn-alt';
			button.textContent = 'Apply to all';
			button.addEventListener('click', () => {
				const color = normalizeHexColor(picker.value, '#2F855A');
				for (const field of getColorFields()) {
					if (matcher.test(`${field.name || ''} ${field.id || ''}`)) {
						setFieldColor(field, color);
					}
				}
			});

			box.appendChild(label);
			box.appendChild(picker);
			box.appendChild(button);

			return box;
		};

		row.appendChild(
			makeAction(
				'Default color',
				defaultField ? defaultField.value : '#2F855A',
				/port\d+_default_color/
			)
		);
		row.appendChild(
			makeAction(
				'Trigger color',
				triggerField ? triggerField.value : '#E53E3E',
				/port\d+_trigger_color/
			)
		);

		panel.appendChild(row);
		firstFieldset.parentNode.insertBefore(panel, firstFieldset);
	}

	function ensureSelectForField(field) {
		if (field.tagName === 'SELECT') {
			return field;
		}

		const existing = field.parentElement
			? field.parentElement.querySelector(`select[data-port24-proxy-for="${field.name || field.id}"]`)
			: null;
		if (existing) {
			return existing;
		}

		const select = document.createElement('select');
		select.className = field.className;
		select.style.width = field.style.width || '100%';
		select.dataset.port24ProxyFor = field.name || field.id || '';

		const current = String(field.value || '');
		if (current !== '') {
			select.dataset.initialValue = current;
		}

		select.addEventListener('change', () => {
			field.value = select.value === '0' ? '' : select.value;
			field.dispatchEvent(new Event('change', {bubbles: true}));
		});

		if (field.parentNode) {
			field.parentNode.insertBefore(select, field.nextSibling);
		}
		field.style.display = 'none';

		return select;
	}

	function setSelectOptions(select, triggers, hostid) {
		const selected = select.value;
		const firstText = hostid === '' ? 'Select host first' : 'Select trigger';
		const options = [{value: '', label: firstText}];

		for (const trigger of triggers) {
			options.push({value: trigger.id, label: trigger.name});
		}

		select.innerHTML = '';
		for (const option of options) {
			const node = document.createElement('option');
			node.value = option.value;
			node.textContent = option.label;
			select.appendChild(node);
		}

		if (selected !== '' && options.some((option) => option.value === selected)) {
			select.value = selected;
		}
		else {
			select.value = '';
		}
	}

	function applyTriggers(triggers, hostid) {
		for (const field of getTriggerFields()) {
			const select = ensureSelectForField(field);
			const initial = String(field.value || select.dataset.initialValue || '');
			select.value = initial;
			setSelectOptions(select, triggers, hostid);
		}
	}

	function fetchTriggers(hostid) {
		const url = new URL('zabbix.php', window.location.origin);
		url.searchParams.set('action', 'widget.switch.triggers');
		url.searchParams.set('output', 'ajax');
		url.searchParams.set('hostid', hostid);

		return fetch(url.toString(), {
			method: 'GET',
			credentials: 'same-origin',
			headers: {'X-Requested-With': 'XMLHttpRequest'}
		})
			.then((response) => response.text())
			.then((text) => {
				const parsePayload = (raw) => {
					const payload = JSON.parse(raw);
					if (Array.isArray(payload.triggers)) {
						return payload;
					}

					if (payload.main_block) {
						const nested = JSON.parse(payload.main_block);
						if (Array.isArray(nested.triggers)) {
							return nested;
						}
					}

					return null;
				};

				const extractEmbeddedJson = (raw) => {
					const start = raw.indexOf('{"triggers":');
					if (start === -1) {
						return null;
					}

					let inString = false;
					let escaped = false;
					let depth = 0;

					for (let i = start; i < raw.length; i++) {
						const ch = raw[i];

						if (escaped) {
							escaped = false;
							continue;
						}

						if (ch === '\\') {
							escaped = true;
							continue;
						}

						if (ch === '"') {
							inString = !inString;
							continue;
						}

						if (inString) {
							continue;
						}

						if (ch === '{') {
							depth++;
						}
						else if (ch === '}') {
							depth--;
							if (depth === 0) {
								return raw.slice(start, i + 1);
							}
						}
					}

					return null;
				};

				try {
					const parsed = parsePayload(text);
					if (parsed !== null) {
						return parsed;
					}
				}
				catch (error) {}

				// Some Zabbix setups wrap AJAX response in full HTML layout.
				const embeddedJson = extractEmbeddedJson(text);
				if (embeddedJson !== null) {
					try {
						const parsed = parsePayload(embeddedJson);
						if (parsed !== null) {
							return parsed;
						}
					}
					catch (error) {}
				}

				return {triggers: []};
			});
	}

	window.switch_widget_form = {
		init() {
			let previousHostId = null;
			let inFlight = false;

			const refresh = () => {
				for (const field of getColorFields()) {
					ensureColorPickerForField(field);
				}
				ensureBulkControls();

				const hostid = getHostId();
				if (hostid === previousHostId || inFlight) {
					return;
				}

				previousHostId = hostid;
				if (hostid === '') {
					applyTriggers([], '');
					return;
				}

				inFlight = true;
				fetchTriggers(hostid)
					.then((payload) => applyTriggers(payload.triggers || [], hostid))
					.catch(() => applyTriggers([], hostid))
					.finally(() => {
						inFlight = false;
					});
			};

			refresh();
			setInterval(refresh, 400);
		}
	};
})();
