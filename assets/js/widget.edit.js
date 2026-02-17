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

	function findField(fieldName) {
		const selectors = [
			`input[name="${fieldName}"], select[name="${fieldName}"]`,
			`input[name="fields[${fieldName}]"], select[name="fields[${fieldName}]"]`
		];

		for (const selector of selectors) {
			const field = document.querySelector(selector);
			if (field) {
				return field;
			}
		}

		return null;
	}

	function setSimpleFieldValue(fieldName, value) {
		const field = findField(fieldName);
		if (!field) {
			return;
		}
		field.value = String(value);
		field.dispatchEvent(new Event('input', {bubbles: true}));
		field.dispatchEvent(new Event('change', {bubbles: true}));
	}

	function getConfiguredPortTotal() {
		const rows = Math.max(1, Number(readIntField('row_count', '2')));
		const perRow = Math.max(1, Number(readIntField('ports_per_row', '12')));
		const sfp = Math.max(0, Number(readIntField('sfp_ports', '0')));
		return Math.max(1, Math.min(256, (rows * perRow) + sfp));
	}

	function updatePortFieldsetVisibility() {
		const visiblePorts = getConfiguredPortTotal();
		for (const fieldset of document.querySelectorAll('fieldset.switch-port-fieldset[data-port-index]')) {
			const idx = Number(fieldset.getAttribute('data-port-index') || '0');
			fieldset.style.display = (idx > 0 && idx <= visiblePorts) ? '' : 'none';
		}
	}

	function readIntField(fieldName, fallback) {
		const field = findField(fieldName);
		if (!field) {
			return fallback;
		}
		const value = String(field.value || '').trim();
		return /^\d+$/.test(value) ? value : fallback;
	}

	function buildProfilePreset(profileId) {
		return {
			row_count: readIntField(`profile${profileId}_row_count`, '2'),
			ports_per_row: readIntField(`profile${profileId}_ports_per_row`, '12'),
			sfp_ports: readIntField(`profile${profileId}_sfp_ports`, '0'),
			switch_size: readIntField(`profile${profileId}_switch_size`, '100')
		};
	}

	function getPresetMap() {
		const map = {0: null};
		for (let profileId = 1; profileId <= 7; profileId++) {
			map[profileId] = buildProfilePreset(profileId);
		}
		return map;
	}

	function ensurePresetControls() {
		const presetField = findField('preset');
		if (!presetField || presetField.dataset.switchPresetInit === '1') {
			return;
		}

		let lastPresetValue = String(presetField.value || '0');

		const getProfileNameField = (presetId) => findField(`profile${presetId}_name`);

		const ensureNameEditor = () => {
			const row = presetField.closest('.form-field');
			if (!row) {
				return null;
			}

			let input = row.querySelector('.switch-profile-name');
			if (input) {
				return input;
			}

			const label = document.createElement('label');
			label.textContent = 'Profile name';
			label.style.marginLeft = '8px';
			label.style.marginRight = '6px';
			label.className = 'switch-profile-name-label';

			input = document.createElement('input');
			input.type = 'text';
			input.className = 'switch-profile-name';
			input.style.width = '170px';
			input.style.verticalAlign = 'middle';
			input.style.display = 'none';

			input.addEventListener('input', () => {
				const presetId = Number(presetField.value);
				if (presetId < 1 || presetId > 7) {
					return;
				}

				const hiddenField = getProfileNameField(presetId);
				if (!hiddenField) {
					return;
				}

				hiddenField.value = input.value;
				hiddenField.dispatchEvent(new Event('input', {bubbles: true}));
				hiddenField.dispatchEvent(new Event('change', {bubbles: true}));

				const option = presetField.querySelector(`option[value="${presetId}"]`);
				if (option) {
					option.textContent = input.value.trim() !== '' ? input.value.trim() : `Profile ${presetId}`;
				}
			});

			label.style.display = 'none';
			row.appendChild(label);
			row.appendChild(input);

			return input;
		};

		const hideInternalProfileFields = () => {
			const suffixes = ['_name', '_row_count', '_ports_per_row', '_sfp_ports', '_switch_size'];

			for (let presetId = 1; presetId <= 7; presetId++) {
				for (const suffix of suffixes) {
					const forId = `profile${presetId}${suffix}`;
					const label = document.querySelector(`label[for="${forId}"]`);
					if (!label) {
						continue;
					}

					label.style.display = 'none';

					const field = label.nextElementSibling;
					if (field && field.classList && field.classList.contains('form-field')) {
						field.style.display = 'none';
					}
				}
			}
		};

		const refreshNameEditor = () => {
			const input = ensureNameEditor();
			if (!input) {
				return;
			}

			const label = input.previousElementSibling;
			const presetId = Number(presetField.value);
			if (presetId >= 1 && presetId <= 7) {
				const nameField = getProfileNameField(presetId);
				const currentName = nameField && String(nameField.value || '').trim() !== ''
					? String(nameField.value).trim()
					: `Profile ${presetId}`;

				input.value = currentName;
				input.style.display = '';
				if (label) {
					label.style.display = '';
				}
			}
			else {
				input.style.display = 'none';
				if (label) {
					label.style.display = 'none';
				}
			}
		};

		const addSaveButton = () => {
			const row = presetField.closest('.form-field');
			if (!row || row.querySelector('.switch-profile-save') !== null) {
				return;
			}

			const button = document.createElement('button');
			button.type = 'button';
			button.className = 'btn-alt switch-profile-save';
			button.textContent = 'Save current to selected profile';
			button.style.marginLeft = '8px';

			button.addEventListener('click', () => {
				const presetId = Number(presetField.value);
				if (presetId < 1 || presetId > 7) {
					window.alert('Select a profile (1-7) first.');
					return;
				}

				setSimpleFieldValue(`profile${presetId}_row_count`, readIntField('row_count', '2'));
				setSimpleFieldValue(`profile${presetId}_ports_per_row`, readIntField('ports_per_row', '12'));
				setSimpleFieldValue(`profile${presetId}_sfp_ports`, readIntField('sfp_ports', '0'));
				setSimpleFieldValue(`profile${presetId}_switch_size`, readIntField('switch_size', '100'));
			});

			row.appendChild(button);
		};

		const applyPreset = () => {
			const presetValue = String(presetField.value || '0');
			const preset = getPresetMap()[presetValue] || null;
			if (!preset) {
				refreshNameEditor();
				return;
			}

			setSimpleFieldValue('row_count', preset.row_count);
			setSimpleFieldValue('ports_per_row', preset.ports_per_row);
			setSimpleFieldValue('sfp_ports', preset.sfp_ports);
			setSimpleFieldValue('switch_size', preset.switch_size);
			updatePortFieldsetVisibility();
			refreshNameEditor();
		};

		presetField.addEventListener('change', applyPreset);

		presetField.dataset.switchPresetInit = '1';
		addSaveButton();
		hideInternalProfileFields();
		refreshNameEditor();

		window.switch_widget_apply_preset_if_changed = () => {
			const current = String(presetField.value || '0');
			if (current === lastPresetValue) {
				return;
			}

			lastPresetValue = current;
			applyPreset();
		};
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
				ensurePresetControls();
				if (typeof window.switch_widget_apply_preset_if_changed === 'function') {
					window.switch_widget_apply_preset_if_changed();
				}
				updatePortFieldsetVisibility();

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
