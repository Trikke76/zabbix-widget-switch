(function() {
	const SWITCH_WIDGET_BUILD = String(window.SWITCH_WIDGET_BUILD || 'dev');

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

	function logDebug(context, error) {
		if (window.SWITCH_WIDGET_DEBUG) {
			// eslint-disable-next-line no-console
			console.warn(`[switch-widget] ${context}`, error);
		}
	}

	function getColorFields() {
		const fields = [];
		const seen = new Set();
		for (const field of document.querySelectorAll('input')) {
			const token = `${field.name || ''} ${field.id || ''}`;
			if (!/port\d+_(default_color|trigger_ok_color|trigger_color)/.test(token)) {
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

	function getColorFallback(field) {
		const token = `${field.name || ''} ${field.id || ''}`;
		if (/utilization_low_color/.test(token)) {
			return '#22C55E';
		}
		if (/utilization_warn_color/.test(token)) {
			return '#FCD34D';
		}
		if (/utilization_high_color/.test(token)) {
			return '#DB2777';
		}
		if (/utilization_na_color/.test(token)) {
			return '#94A3B8';
		}
		if (/trigger_ok_color/.test(token)) {
			return '#22C55E';
		}
		if (/trigger_color/.test(token)) {
			return '#E53E3E';
		}
		return '#D1D5DB';
	}

		function ensureUtilizationControls() {
			const setCompactChars = (field, chars, useContentBox = false) => {
				const wrap = field.closest('.form-field');
				if (wrap) {
					wrap.style.setProperty('display', 'inline-block', 'important');
					wrap.style.setProperty('width', 'auto', 'important');
					wrap.style.setProperty('max-width', 'none', 'important');
					wrap.style.setProperty('min-width', '0', 'important');
					wrap.style.setProperty('flex', '0 0 auto', 'important');
					wrap.style.setProperty('flex-basis', 'auto', 'important');
					wrap.style.setProperty('justify-self', 'start', 'important');
				}
				field.style.setProperty('width', `${chars}ch`, 'important');
				field.style.setProperty('max-width', `${chars}ch`, 'important');
				field.style.setProperty('min-width', `${chars}ch`, 'important');
				field.style.setProperty('box-sizing', useContentBox ? 'content-box' : 'border-box', 'important');
			};
			const enforceTextFieldsByLabels = (labelTexts, chars, maxLength = null, options = {}) => {
				const {
					numericOnly = false,
					allowDecimal = false,
					contentBox = false
				} = options;
				const normalized = new Set(labelTexts.map((t) => t.trim().toLowerCase()));
				for (const label of document.querySelectorAll('label[for]')) {
					const key = String(label.textContent || '').trim().toLowerCase();
					if (!normalized.has(key)) {
						continue;
					}
					const field = document.getElementById(label.getAttribute('for'));
					if (!field) {
						continue;
					}
					setCompactChars(field, chars, contentBox);
					if (maxLength !== null) {
						field.maxLength = maxLength;
						field.setAttribute('maxlength', String(maxLength));
					}
					if (contentBox) {
						field.style.setProperty('padding-left', '0', 'important');
						field.style.setProperty('padding-right', '0', 'important');
						field.setAttribute('size', String(chars));
					}
					if (numericOnly && field.dataset.port24CompactFilterBound !== '1') {
						field.style.textAlign = 'right';
						field.addEventListener('input', () => {
							let text = String(field.value || '').replace(',', '.');
							text = allowDecimal ? text.replace(/[^0-9.]/g, '') : text.replace(/[^0-9]/g, '');
							if (allowDecimal) {
								const firstDot = text.indexOf('.');
								if (firstDot !== -1) {
									text = text.slice(0, firstDot + 1) + text.slice(firstDot + 1).replace(/\./g, '');
								}
							}
							if (maxLength !== null && text.length > maxLength) {
								text = text.slice(0, maxLength);
							}
							field.value = text;
						});
						field.dataset.port24CompactFilterBound = '1';
					}
				}
			};
			const enforcePatternFieldSizing = (labelTexts, chars, maxLength) => {
				enforceTextFieldsByLabels(labelTexts, chars, maxLength, {contentBox: true});
			};
		const legendField = findField('legend_text');
		const lowField = findField('utilization_low_threshold');
		const warnField = findField('utilization_warn_threshold');
		const highField = findField('utilization_high_threshold');
		const defaultLegendText = () => {
			const low = String(lowField?.value || '5').trim() || '5';
			const warn = String(warnField?.value || '40').trim() || '40';
			const high = String(highField?.value || '70').trim() || '70';
			return `Heatmap: green < ${low}%, low >= ${low}%, warn >= ${warn}%, high >= ${high}%`;
		};
		if (legendField && String(legendField.value || '').trim() === '') {
			legendField.value = defaultLegendText();
			legendField.dispatchEvent(new Event('input', {bubbles: true}));
			legendField.dispatchEvent(new Event('change', {bubbles: true}));
		}

			enforcePatternFieldSizing([
				'Traffic in item pattern',
				'Traffic out item pattern',
				'Errors in item pattern',
				'Errors out item pattern',
				'Discards in item pattern',
				'Discards out item pattern',
				'Speed item pattern',
				'Software item key',
				'VLANs item key',
				'CPU item key',
				'Fan item key',
				'Uptime item key',
				'Serial item key'
			], 40, 40);
			enforceTextFieldsByLabels(['Brand', 'Model'], 30, 30);
			enforceTextFieldsByLabels(['Size (%)', 'Rows', 'Ports per row', 'SFP ports'], 6, 4, {numericOnly: true});
			ensureCompactMainLayout();
			enforceTextFieldsByLabels([
				'Utilization low threshold (%)',
				'Utilization warn threshold (%)',
				'Utilization high threshold (%)'
			], 6, 4, {numericOnly: true, allowDecimal: true});
			for (const name of ['utilization_low_threshold', 'utilization_warn_threshold', 'utilization_high_threshold']) {
				const field = findField(name);
				if (field) {
					field.placeholder = '0-100';
				}
			}

		function ensureCompactMainLayout() {
			const compactNames = ['switch_size', 'row_count', 'ports_per_row', 'sfp_ports'];
			const firstField = findField(compactNames[0]);
			const formGrid = firstField ? firstField.closest('.form-grid') : null;
			if (!formGrid || formGrid.querySelector('.port24-compact-main-grid')) {
				return;
			}

			const firstLabel = formGrid.querySelector(`label[for="${firstField.id}"]`);
			if (!firstLabel) {
				return;
			}

			const compactGrid = document.createElement('div');
			compactGrid.className = 'port24-compact-main-grid';
			formGrid.insertBefore(compactGrid, firstLabel);

			for (const name of compactNames) {
				const field = findField(name);
				if (!field) {
					continue;
				}
				const label = formGrid.querySelector(`label[for="${field.id}"]`);
				const fieldWrap = field.closest('.form-field');
				if (!label || !fieldWrap) {
					continue;
				}

				const pair = document.createElement('div');
				pair.className = 'port24-compact-main-pair';
				pair.appendChild(label);
				pair.appendChild(fieldWrap);
				compactGrid.appendChild(pair);
			}

			const alignToModel = () => {
				const modelField = findField('switch_model');
				if (!modelField) {
					return;
				}
				const gridRect = formGrid.getBoundingClientRect();
				const modelRect = modelField.getBoundingClientRect();
				const leftOffset = Math.max(0, Math.round(modelRect.left - gridRect.left));
				compactGrid.style.marginLeft = `${leftOffset}px`;
			};
			alignToModel();
			requestAnimationFrame(alignToModel);
		}

		const colorFieldNames = [
			'utilization_low_color',
			'utilization_warn_color',
			'utilization_high_color',
			'utilization_na_color'
		];
		for (const name of colorFieldNames) {
			const field = findField(name);
			if (!field) {
				continue;
			}
			ensureModernPickerForField(field);
			field.maxLength = 7;
			field.style.width = '72px';
			field.style.maxWidth = '72px';
		}
	}

	function ensureModernPickerForField(field) {
		if (field.dataset.port24ModernColorInit === '1') {
			return;
		}

		const parent = field.parentNode;
		if (!parent) {
			return;
		}

		const fallback = getColorFallback(field);
		field.value = normalizeHexColor(field.value, fallback);

		const picker = createModernBulkPicker(field.value);
		const holder = document.createElement('span');
		holder.style.display = 'inline-flex';
		holder.style.marginLeft = '8px';
		holder.style.verticalAlign = 'middle';
		holder.appendChild(picker.element);

		parent.insertBefore(holder, field.nextSibling);
		field._port24ModernPicker = picker;

		picker.element.addEventListener('port24-color-change', (event) => {
			const value = normalizeHexColor(event.detail?.value, fallback);
			field.value = value;
			field.dispatchEvent(new Event('input', {bubbles: true}));
			field.dispatchEvent(new Event('change', {bubbles: true}));
		});

		field.addEventListener('input', () => {
			const normalized = normalizeHexColor(field.value, fallback);
			field.value = normalized;
			picker.setValue(normalized, false);
		});

		field.dataset.port24ModernColorInit = '1';
	}

	function ensureColorPickerForField(field) {
		ensureModernPickerForField(field);
	}

	function setFieldColor(field, color, dispatchEvents = true) {
		const normalized = normalizeHexColor(color, getColorFallback(field));
		field.value = normalized;
		if (field._port24ModernPicker && typeof field._port24ModernPicker.setValue === 'function') {
			field._port24ModernPicker.setValue(normalized, false);
		}

		if (dispatchEvents) {
			field.dispatchEvent(new Event('input', {bubbles: true}));
			field.dispatchEvent(new Event('change', {bubbles: true}));
		}
	}

	function migrateLegacyDefaultColors() {
		if (document.body.dataset.port24DefaultColorMigrated === '1') {
			return;
		}

		const legacyDefaults = new Set(['#2F855A', '#84CC16']);
		for (const field of getColorFields()) {
			const token = `${field.name || ''} ${field.id || ''}`;
			if (!/port\d+_default_color/.test(token)) {
				continue;
			}

			const normalized = normalizeHexColor(field.value, '#D1D5DB');
			if (legacyDefaults.has(normalized)) {
				setFieldColor(field, '#D1D5DB');
			}

			}

		document.body.dataset.port24DefaultColorMigrated = '1';
	}

	function ensureModernBulkPickerStyle() {
		if (document.getElementById('port24-modern-picker-style')) {
			return;
		}

		const style = document.createElement('style');
		style.id = 'port24-modern-picker-style';
		style.textContent = [
			'.port24-modern-picker{position:relative;display:inline-flex;align-items:center;}',
			'.port24-modern-picker .port24-swatch-btn{width:auto;height:auto;border:0;background:transparent;padding:0;cursor:pointer;box-shadow:none;outline:none;}',
			'.port24-modern-picker .port24-swatch-btn span{display:block;width:44px;height:22px;border-radius:6px;}',
			'.port24-modern-picker .port24-pop{position:absolute;z-index:1200;top:36px;left:0;min-width:230px;background:#141a22;border:1px solid #2f3947;border-radius:10px;box-shadow:0 12px 28px rgba(0,0,0,.45);padding:10px;color:#d9e2ec;}',
			'.port24-modern-picker .port24-pop.is-hidden{display:none;}',
			'.port24-modern-picker .port24-tabs{display:flex;gap:6px;margin:0 0 10px 0;}',
			'.port24-modern-picker .port24-tab{border:1px solid #344154;background:#1b2430;color:#c9d5e2;border-radius:6px;padding:4px 8px;cursor:pointer;}',
			'.port24-modern-picker .port24-tab.is-active{background:#2e6f47;border-color:#3f8b5f;color:#fff;}',
			'.port24-modern-picker .port24-grid{display:grid;grid-template-columns:repeat(10,18px);gap:8px;margin:0 0 10px 0;}',
			'.port24-modern-picker .port24-dot{appearance:none;-webkit-appearance:none;display:block;width:18px;height:18px;min-width:18px;min-height:18px;box-sizing:border-box;border-radius:9999px;border:1px solid rgba(255,255,255,.22);cursor:pointer;padding:0;margin:0;line-height:0;font-size:0;}',
			'.port24-modern-picker .port24-custom input{width:100%;background:#0f151d;color:#e5edf5;border:1px solid #354255;border-radius:6px;padding:6px 8px;}',
			'.port24-modern-picker .port24-custom-actions{margin-top:8px;display:flex;justify-content:flex-end;}',
			'.port24-modern-picker .port24-custom-apply{border:1px solid #3b82f6;background:#0f172a;color:#e2ecff;border-radius:6px;padding:4px 10px;cursor:pointer;}',
			'.port24-modern-picker .port24-custom.is-hidden,.port24-modern-picker .port24-colors.is-hidden{display:none;}',
			'.port24-compact-main-grid{display:grid;grid-template-columns:repeat(2,max-content);gap:8px 20px;grid-column:1 / -1;align-items:start;justify-content:start;width:fit-content;max-width:100%;}',
			'.port24-compact-main-pair{display:grid;grid-template-columns:1fr;row-gap:4px;align-items:start;}',
			'.port24-compact-main-pair > label{text-align:left;margin:0;}',
			'.port24-compact-main-pair > .form-field{margin:0 !important;width:auto !important;min-width:0 !important;max-width:none !important;}',
			'.port24-item-suggest-pop{position:absolute;left:0;right:0;top:100%;margin-top:2px;z-index:1200;background:#1b2430;border:1px solid #3a4655;border-radius:4px;box-shadow:0 10px 24px rgba(0,0,0,.35);max-height:220px;overflow:auto;}',
			'.port24-item-suggest-pop.is-hidden{display:none;}',
			'.port24-item-suggest-item{display:flex;justify-content:space-between;align-items:center;gap:8px;width:100%;border:0;background:transparent;color:#e6edf7;padding:6px 8px;text-align:left;cursor:pointer;font-size:12px;}',
			'.port24-item-suggest-item:hover{background:#2a3443;}',
			'.port24-item-suggest-kind{font-size:10px;color:#9fb2c8;white-space:nowrap;}'
		].join('');
		document.head.appendChild(style);
	}

	function ensureEditSectionStyle() {
		if (document.getElementById('switch-edit-section-style')) {
			return;
		}

		const style = document.createElement('style');
		style.id = 'switch-edit-section-style';
		style.textContent = [
			'.switch-edit-section{grid-column:1 / -1;display:flex;align-items:center;gap:10px;margin:8px 0 2px 0;}',
			'.switch-edit-section-title{font-weight:700;font-size:13px;color:#2b3a49;white-space:nowrap;}',
			'.switch-edit-section-line{height:1px;flex:1 1 auto;background:#d8e0e8;}'
		].join('');
		document.head.appendChild(style);
	}

	function ensureEditSections() {
		const formGrid = document.querySelector('.dashboard-widget-switch .form-grid, .form-grid');
		if (!formGrid) {
			return;
		}

		ensureEditSectionStyle();

		const findLabelByText = (labelText) => {
			const target = String(labelText || '').trim().toLowerCase();
			for (const label of formGrid.querySelectorAll('label[for]')) {
				const text = String(label.textContent || '').trim().toLowerCase();
				if (text === target) {
					return label;
				}
			}
			return null;
		};

		const insertSection = (key, title, beforeLabelText) => {
			const markerId = `switch-edit-section-${key}`;
			if (document.getElementById(markerId)) {
				return;
			}

			const anchor = findLabelByText(beforeLabelText);
			if (!anchor || !anchor.parentNode) {
				return;
			}

			const section = document.createElement('div');
			section.className = 'switch-edit-section';
			section.id = markerId;

			const titleNode = document.createElement('div');
			titleNode.className = 'switch-edit-section-title';
			titleNode.textContent = title;

			const line = document.createElement('div');
			line.className = 'switch-edit-section-line';

			section.appendChild(titleNode);
			section.appendChild(line);
			anchor.parentNode.insertBefore(section, anchor);
		};

		insertSection('traffic', 'Traffic Patterns', 'Traffic in item pattern');
		insertSection('errors', 'Error and Discard Patterns', 'Errors in item pattern');
		insertSection('summary', 'Switch Summary Item Keys', 'Software item key');
		insertSection('util', 'Utilization Settings', 'Show utilization overlay');
		insertSection('layout', 'Device Layout', 'Model');
	}

	function createModernBulkPicker(initialColor) {
		ensureModernBulkPickerStyle();

		const palette = [
			'#7F1D1D', '#991B1B', '#B91C1C', '#DC2626', '#EF4444', '#F87171', '#FCA5A5', '#FECACA',
			'#7C2D12', '#9A3412', '#C2410C', '#EA580C', '#F97316', '#FB923C', '#FDBA74', '#FED7AA',
			'#78350F', '#92400E', '#B45309', '#D97706', '#F59E0B', '#FBBF24', '#FCD34D', '#FDE68A',
			'#713F12', '#854D0E', '#A16207', '#CA8A04', '#EAB308', '#FACC15', '#FDE047', '#FEF08A',
			'#365314', '#3F6212', '#4D7C0F', '#65A30D', '#84CC16', '#A3E635', '#BEF264', '#D9F99D',
			'#14532D', '#166534', '#15803D', '#16A34A', '#22C55E', '#4ADE80', '#86EFAC', '#BBF7D0',
			'#134E4A', '#115E59', '#0F766E', '#0D9488', '#14B8A6', '#2DD4BF', '#5EEAD4', '#99F6E4',
			'#164E63', '#155E75', '#0369A1', '#0284C7', '#0EA5E9', '#38BDF8', '#7DD3FC', '#BAE6FD',
			'#1E3A8A', '#1D4ED8', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE', '#DBEAFE',
			'#4C1D95', '#5B21B6', '#6D28D9', '#7C3AED', '#8B5CF6', '#A78BFA', '#C4B5FD', '#DDD6FE',
			'#9F1239', '#BE123C', '#DB2777', '#EC4899', '#F472B6', '#F9A8D4', '#FBCFE8', '#FCE7F3',
			'#111827', '#1F2937', '#374151', '#4B5563', '#6B7280', '#9CA3AF', '#D1D5DB', '#F3F4F6'
		];

		const root = document.createElement('div');
		root.className = 'port24-modern-picker';

		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'port24-swatch-btn';
		button.title = 'Choose color';

		const swatch = document.createElement('span');
		button.appendChild(swatch);

		const pop = document.createElement('div');
		pop.className = 'port24-pop is-hidden';

		const tabs = document.createElement('div');
		tabs.className = 'port24-tabs';

		const tabColors = document.createElement('button');
		tabColors.type = 'button';
		tabColors.className = 'port24-tab is-active';
		tabColors.textContent = 'Colors';

		const tabCustom = document.createElement('button');
		tabCustom.type = 'button';
		tabCustom.className = 'port24-tab';
		tabCustom.textContent = 'Custom';

		tabs.appendChild(tabColors);
		tabs.appendChild(tabCustom);

		const colorsWrap = document.createElement('div');
		colorsWrap.className = 'port24-colors';
		const grid = document.createElement('div');
		grid.className = 'port24-grid';
		colorsWrap.appendChild(grid);

		const customWrap = document.createElement('div');
		customWrap.className = 'port24-custom is-hidden';
		const customInput = document.createElement('input');
		customInput.type = 'text';
		customInput.maxLength = 7;
		customInput.placeholder = '#D1D5DB';
		customWrap.appendChild(customInput);
		const customActions = document.createElement('div');
		customActions.className = 'port24-custom-actions';
		const customApply = document.createElement('button');
		customApply.type = 'button';
		customApply.className = 'port24-custom-apply';
		customApply.textContent = 'Apply';
		customActions.appendChild(customApply);
		customWrap.appendChild(customActions);

		pop.appendChild(tabs);
		pop.appendChild(colorsWrap);
		pop.appendChild(customWrap);
		root.appendChild(button);
		root.appendChild(pop);

		let value = normalizeHexColor(initialColor, '#D1D5DB');

		const setValue = (color, emit = true) => {
			value = normalizeHexColor(color, value);
			swatch.style.background = value;
			customInput.value = value;
			if (emit) {
				root.dispatchEvent(new CustomEvent('port24-color-change', {
					detail: {value}
				}));
			}
		};

		let colorsBuilt = false;
		const ensureColorDots = () => {
			if (colorsBuilt) {
				return;
			}
			for (const color of palette) {
				const dot = document.createElement('button');
				dot.type = 'button';
				dot.className = 'port24-dot';
				dot.style.background = color;
				dot.addEventListener('click', () => {
					setValue(color);
					pop.classList.add('is-hidden');
				});
				grid.appendChild(dot);
			}
			colorsBuilt = true;
		};

		const showColors = () => {
			ensureColorDots();
			tabColors.classList.add('is-active');
			tabCustom.classList.remove('is-active');
			colorsWrap.classList.remove('is-hidden');
			customWrap.classList.add('is-hidden');
		};

		const showCustom = () => {
			tabCustom.classList.add('is-active');
			tabColors.classList.remove('is-active');
			customWrap.classList.remove('is-hidden');
			colorsWrap.classList.add('is-hidden');
		};

		tabColors.addEventListener('click', showColors);
		tabCustom.addEventListener('click', showCustom);

		customInput.addEventListener('change', () => {
			setValue(customInput.value);
		});
		customInput.addEventListener('keydown', (event) => {
			if (event.key === 'Enter') {
				setValue(customInput.value);
			}
		});
		customApply.addEventListener('click', () => {
			setValue(customInput.value);
			pop.classList.add('is-hidden');
		});

		button.addEventListener('click', () => {
			ensureColorDots();
			pop.classList.toggle('is-hidden');
		});

		setValue(value, false);

		return {
			element: root,
			getValue: () => value,
			setValue
		};
	}

	function ensureGlobalPickerOutsideClick() {
		if (window.switch_widget_form._onPickerOutsideClick) {
			return;
		}

		const onPickerOutsideClick = (event) => {
			const target = event.target instanceof Element ? event.target : null;
			const ownerPicker = target ? target.closest('.port24-modern-picker') : null;
			const ownerSuggest = target ? target.closest('.port24-item-suggest-pop') : null;

			for (const pop of document.querySelectorAll('.port24-modern-picker .port24-pop')) {
				if (pop.classList.contains('is-hidden')) {
					continue;
				}
				if (ownerPicker && ownerPicker.contains(pop)) {
					continue;
				}
				pop.classList.add('is-hidden');
			}

			for (const pop of document.querySelectorAll('.port24-item-suggest-pop')) {
				if (pop.classList.contains('is-hidden')) {
					continue;
				}
				if (ownerSuggest && ownerSuggest === pop) {
					continue;
				}
				pop.classList.add('is-hidden');
			}
		};

		document.addEventListener('click', onPickerOutsideClick);
		window.switch_widget_form._onPickerOutsideClick = onPickerOutsideClick;
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
		const triggerOkField = allColorFields.find((field) =>
			/port\d+_trigger_ok_color/.test(`${field.name || ''} ${field.id || ''}`)
		);
		const triggerField = allColorFields.find((field) =>
			/port\d+_trigger_color/.test(`${field.name || ''} ${field.id || ''}`)
		);

		const panel = document.createElement('div');
		panel.id = 'port24-bulk-tools';
		panel.className = 'switch-bulk-tools';
		panel.style.padding = '12px';
		panel.style.border = '1px solid #dfe4ea';
		panel.style.borderRadius = '4px';
		panel.style.marginBottom = '12px';

		const title = document.createElement('h4');
		title.textContent = 'Bulk actions';
		title.style.fontWeight = '600';
		title.style.margin = '0 0 8px 0';
		panel.appendChild(title);

		const row = document.createElement('div');
		row.style.display = 'grid';
		row.style.gridTemplateColumns = '1fr';
		row.style.gap = '10px';

			const makeAction = (labelText, initialColor, matcher) => {
				const box = document.createElement('div');
				box.style.display = 'flex';
				box.style.alignItems = 'center';
				box.style.gap = '8px';

			const label = document.createElement('label');
			label.textContent = labelText;
			label.style.minWidth = '120px';

			const picker = createModernBulkPicker(initialColor);

				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'btn-alt';
				button.textContent = 'Apply to all';
				const feedback = document.createElement('span');
				feedback.style.fontSize = '12px';
				feedback.style.color = '#2F855A';
				feedback.style.minWidth = '80px';
				feedback.style.visibility = 'hidden';
				button.addEventListener('click', () => {
					const color = normalizeHexColor(picker.getValue(), '#D1D5DB');
					let updated = 0;
					let firstUpdatedField = null;
					for (const field of getColorFields()) {
						if (matcher.test(`${field.name || ''} ${field.id || ''}`)) {
							if (firstUpdatedField === null) {
								firstUpdatedField = field;
							}
							setFieldColor(field, color, false);
							updated++;
						}
					}
					if (firstUpdatedField !== null) {
						firstUpdatedField.dispatchEvent(new Event('change', {bubbles: true}));
					}
					feedback.textContent = `Applied (${updated})`;
					feedback.style.visibility = 'visible';
					window.setTimeout(() => {
						feedback.style.visibility = 'hidden';
					}, 1200);
				});

				box.appendChild(label);
				box.appendChild(picker.element);
				box.appendChild(button);
				box.appendChild(feedback);

				return box;
			};

		row.appendChild(
			makeAction(
				'Default color',
				defaultField ? defaultField.value : '#D1D5DB',
				/port\d+_default_color/
			)
		);
		row.appendChild(
			makeAction(
				'Trigger OK color',
				triggerOkField ? triggerOkField.value : '#22C55E',
				/port\d+_trigger_ok_color/
			)
		);
		row.appendChild(
			makeAction(
				'Trigger NOK color',
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

	function setSimpleFieldValue(fieldName, value, options = {}) {
		const {dispatchEvents = true} = options;
		const field = findField(fieldName);
		if (!field) {
			return;
		}
		const next = String(value);
		if (String(field.value || '') === next) {
			return;
		}
		field.value = next;
		if (dispatchEvents) {
			field.dispatchEvent(new Event('input', {bubbles: true}));
			field.dispatchEvent(new Event('change', {bubbles: true}));
		}
	}

	function getCsrfTokenField() {
		return document.querySelector(
			'input[type="hidden"][name="csrf_token"], input[type="hidden"][name="_csrf_token"], input[type="hidden"][name*="csrf"]'
		);
	}

	function getConfiguredPortTotal() {
		const rows = Math.max(1, Number(readIntField('row_count', '2')));
		const perRow = Math.max(1, Number(readIntField('ports_per_row', '12')));
		const sfp = Math.max(0, Number(readIntField('sfp_ports', '0')));
		return Math.max(1, Math.min(96, (rows * perRow) + sfp));
	}

	function updatePortFieldsetVisibility() {
		const visiblePorts = getConfiguredPortTotal();
		for (const fieldset of document.querySelectorAll('fieldset.switch-port-fieldset[data-port-index]')) {
			const idx = Number(fieldset.getAttribute('data-port-index') || '0');
			fieldset.style.display = (idx > 0 && idx <= visiblePorts) ? '' : 'none';
		}
	}

	function ensurePortFieldsetGrid() {
		let portFieldsets = Array.from(document.querySelectorAll('fieldset.switch-port-fieldset[data-port-index]'));
		if (portFieldsets.length === 0) {
			portFieldsets = Array.from(document.querySelectorAll('fieldset.collapsible')).filter((fieldset) => {
				const title = (fieldset.querySelector('legend, h4, .header')?.textContent || '').trim();
				return /^Port\s+\d+/i.test(title);
			});
		}
		if (portFieldsets.length === 0) {
			return;
		}

		const first = portFieldsets[0];
		let grid = first.closest('.switch-port-fieldsets-grid');
		let parent = null;
		if (grid) {
			parent = grid.parentNode;
		}
		else {
			parent = first.parentNode;
		}
		if (!parent) {
			return;
		}

		if (!grid) {
			for (const child of Array.from(parent.children || [])) {
				if (child.classList && child.classList.contains('switch-port-fieldsets-grid')) {
					grid = child;
					break;
				}
			}
		}
		let leftCol;
		let rightCol;
		if (!grid) {
			grid = document.createElement('div');
			grid.className = 'switch-port-fieldsets-grid';
			leftCol = document.createElement('div');
			rightCol = document.createElement('div');
			leftCol.className = 'switch-port-column';
			rightCol.className = 'switch-port-column';
			grid.appendChild(leftCol);
			grid.appendChild(rightCol);
			parent.insertBefore(grid, first);
		}
		else {
			const cols = Array.from(grid.children || []).filter((child) =>
				child.classList && child.classList.contains('switch-port-column')
			);
			leftCol = cols[0] || null;
			rightCol = cols[1] || null;
			if (!leftCol) {
				leftCol = document.createElement('div');
				leftCol.className = 'switch-port-column';
				grid.appendChild(leftCol);
			}
			if (!rightCol) {
				rightCol = document.createElement('div');
				rightCol.className = 'switch-port-column';
				grid.appendChild(rightCol);
			}
		}

		portFieldsets.sort((a, b) => {
			const ai = Number(a.getAttribute('data-port-index') || '0');
			const bi = Number(b.getAttribute('data-port-index') || '0');
			return ai - bi;
		});

		const splitAt = Math.ceil(portFieldsets.length / 2);
		for (const [index, fs] of portFieldsets.entries()) {
			if (!fs.getAttribute('data-port-index')) {
				fs.setAttribute('data-port-index', String(index + 1));
			}
			fs.classList.add('switch-port-fieldset');
			const target = index < splitAt ? leftCol : rightCol;
			if (target && fs.parentNode !== target) {
				target.appendChild(fs);
			}
		}

		if (!document.getElementById('switch-port-fieldsets-grid-style')) {
			const style = document.createElement('style');
			style.id = 'switch-port-fieldsets-grid-style';
				style.textContent = [
					'.switch-port-fieldsets-grid{display:flex;gap:14px;grid-column:1 / -1;justify-self:stretch;width:100%;align-items:flex-start;}',
					'.switch-port-fieldsets-grid > .switch-port-column{flex:1 1 0;min-width:0;}',
					'.switch-port-fieldsets-grid .switch-port-fieldset{min-width:0;margin:0 0 10px !important;width:100% !important;}',
					'.switch-port-fieldsets-grid .switch-port-fieldset .form-field{display:grid !important;grid-template-columns:170px minmax(0,1fr) !important;align-items:center !important;column-gap:10px !important;row-gap:6px !important;}',
					'.switch-port-fieldsets-grid .switch-port-fieldset .form-field > label{margin:0 !important;display:block !important;}',
					'.switch-port-fieldsets-grid .switch-port-fieldset .form-field > :not(label){min-width:0;}',
					'.switch-port-fieldsets-grid .switch-port-fieldset .form-field input,',
					'.switch-port-fieldsets-grid .switch-port-fieldset .form-field select{width:100% !important;max-width:none !important;}'
				].join('');
			document.head.appendChild(style);
		}
	}

	function findFieldControlByLabel(fieldset, label) {
		const forId = label.getAttribute('for');
		if (forId) {
			const escaped = (window.CSS && typeof window.CSS.escape === 'function')
				? window.CSS.escape(forId)
				: forId.replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1');
			const byFor = fieldset.querySelector(`#${escaped}`);
			if (byFor) {
				return byFor;
			}
		}

		let scan = label.parentElement;
		while (scan && scan !== fieldset) {
			const direct = scan.querySelector('input, select, textarea');
			if (direct) {
				return direct;
			}
			scan = scan.nextElementSibling;
		}

		return null;
	}

	function getDirectChildUnderRoot(node, root) {
		let current = node;
		while (current.parentElement && current.parentElement !== root) {
			current = current.parentElement;
		}
		return current;
	}

	function ensurePortFieldRowsAligned() {
		for (const fieldset of document.querySelectorAll('fieldset.switch-port-fieldset[data-port-index]')) {
			const portLabelPattern = /^Port\s+\d+\s+/i;
			const labels = Array.from(fieldset.querySelectorAll('label')).filter((label) => {
				const text = (label.textContent || '').trim();
				return portLabelPattern.test(text);
			});

			for (const label of labels) {
				if (label.closest('.switch-port-inline-row')) {
					continue;
				}

				const control = findFieldControlByLabel(fieldset, label);
				if (!control) {
					continue;
				}

				const labelBlock = getDirectChildUnderRoot(label, fieldset);
				const controlBlock = getDirectChildUnderRoot(control, fieldset);
				if (!labelBlock || !controlBlock || labelBlock === controlBlock) {
					continue;
				}
				if (labelBlock.closest('.switch-port-inline-row') || controlBlock.closest('.switch-port-inline-row')) {
					continue;
				}
				if (!labelBlock.parentElement || !controlBlock.parentElement) {
					continue;
				}

				const row = document.createElement('div');
				row.className = 'switch-port-inline-row';
				labelBlock.parentElement.insertBefore(row, labelBlock);
				row.appendChild(labelBlock);
				row.appendChild(controlBlock);
			}
		}

		if (!document.getElementById('switch-port-inline-row-style')) {
			const style = document.createElement('style');
			style.id = 'switch-port-inline-row-style';
			style.textContent = [
				'.switch-port-inline-row{display:grid;grid-template-columns:170px minmax(0,1fr);column-gap:10px;align-items:center;margin:0 0 8px 0;padding-left:48px;}',
				'.switch-port-inline-row label{margin:0 !important;}',
				'.switch-port-inline-row input,.switch-port-inline-row select,.switch-port-inline-row textarea{width:100% !important;max-width:none !important;}',
				'@media (max-width:900px){.switch-port-inline-row{grid-template-columns:1fr;row-gap:6px;padding-left:32px;}}'
			].join('');
			document.head.appendChild(style);
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
			switch_size: readIntField(`profile${profileId}_switch_size`, '100'),
			switch_brand: (findField(`profile${profileId}_switch_brand`)?.value || 'NETSWITCH').toString(),
			switch_model: (findField(`profile${profileId}_switch_model`)?.value || 'SW-24G').toString()
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

			const normalizeProfileName = (value, presetId) => {
				const cleaned = String(value || '').trim().slice(0, 15);
				return cleaned !== '' ? cleaned : `Profile ${presetId}`;
			};

			const getProfileNameField = (presetId) => findField(`profile${presetId}_name`);
			const getProfileField = (presetId, suffix) => findField(`profile${presetId}${suffix}`);
			const getPresetLabelForValue = (value) => {
				const key = String(value || '0');
				const nativeOptions = presetField.options;
				if (nativeOptions && typeof nativeOptions[Symbol.iterator] === 'function') {
					for (const option of nativeOptions) {
						if (String(option.value) === key) {
							return String(option.label || option.textContent || '').trim();
						}
					}
				}

				const raw = presetField.getAttribute('data-options');
				if (!raw) {
					return '';
				}
				try {
					const parsed = JSON.parse(raw);
					if (!Array.isArray(parsed)) {
						return '';
					}
					const match = parsed.find((entry) =>
						entry && typeof entry === 'object' && Object.prototype.hasOwnProperty.call(entry, 'value')
						&& String(entry.value) === key
					);
					return match ? String(match.label || '').trim() : '';
				}
				catch (error) {
					return '';
				}
			};
			const updatePresetRenderedLabel = () => {
				const selected = String(presetField.value || '0');
				const label = getPresetLabelForValue(selected);
				if (label === '') {
					return;
				}

				const selectors = [
					'.selected-option',
					'.selected-value',
					'.z-select-value',
					'.js-select-value',
					'.select-value',
					'.btn-dropdown-toggle'
				];

				for (const selector of selectors) {
					for (const node of presetField.querySelectorAll(selector)) {
						node.textContent = label;
					}
				}

				if (presetField.shadowRoot) {
					for (const selector of selectors) {
						for (const node of presetField.shadowRoot.querySelectorAll(selector)) {
							node.textContent = label;
						}
					}
				}
			};
			const mutatePresetDataOptions = (mutator) => {
				const raw = presetField.getAttribute('data-options');
				if (!raw) {
					return false;
				}

				let parsed;
				try {
					parsed = JSON.parse(raw);
				}
				catch (error) {
					return false;
				}

				if (!Array.isArray(parsed)) {
					return false;
				}

				const updated = mutator(parsed);
				if (!Array.isArray(updated)) {
					return false;
				}

				const json = JSON.stringify(updated);
				presetField.setAttribute('data-options', json);
				if (presetField.dataset) {
					presetField.dataset.options = json;
				}

				return true;
			};
			const rebuildPresetOptionsFromFields = () => {
				const currentValue = String(presetField.value || '0');
				const labels = {'0': 'Custom'};
				for (let presetId = 1; presetId <= 7; presetId++) {
					const hidden = getProfileNameField(presetId);
					const text = String(hidden?.value || '').trim();
					labels[String(presetId)] = text !== '' ? text : `Profile ${presetId}`;
				}

				const nativeOptions = presetField.options;
				if (nativeOptions && typeof nativeOptions[Symbol.iterator] === 'function') {
					for (const option of nativeOptions) {
						const key = String(option.value);
						if (Object.prototype.hasOwnProperty.call(labels, key)) {
							option.textContent = labels[key];
							option.label = labels[key];
						}
					}
				}
				else {
					mutatePresetDataOptions((options) => options.map((option) => {
						if (!option || typeof option !== 'object' || !Object.prototype.hasOwnProperty.call(option, 'value')) {
							return option;
						}

						const key = String(option.value);
						if (Object.prototype.hasOwnProperty.call(labels, key)) {
							return {...option, label: labels[key]};
						}

						return option;
					}));
				}

				presetField.value = currentValue;
				presetField.setAttribute('value', currentValue);
				updatePresetRenderedLabel();
			};
			const updatePresetOptionLabel = (presetId, name) => {
				const labelText = name && name.trim() !== '' ? name.trim() : `Profile ${presetId}`;
				const option = presetField.querySelector(`option[value="${presetId}"]`);
				if (option) {
					option.textContent = labelText;
					option.label = labelText;
					return;
				}

				mutatePresetDataOptions((options) => options.map((entry) => {
					if (!entry || typeof entry !== 'object' || !Object.prototype.hasOwnProperty.call(entry, 'value')) {
						return entry;
					}
					return String(entry.value) === String(presetId) ? {...entry, label: labelText} : entry;
				}));
			};
			const refreshPresetSelectUi = (options = {}) => {
				const {emitChange = true} = options;
				const currentValue = String(presetField.value || '0');
				// Force repaint of native/select-enhanced controls.
				rebuildPresetOptionsFromFields();
				presetField.value = currentValue;
				presetField.setAttribute('value', currentValue);
				updatePresetRenderedLabel();
				if (emitChange) {
					presetField.dispatchEvent(new Event('input', {bubbles: true}));
					presetField.dispatchEvent(new Event('change', {bubbles: true}));
				}
				if (window.jQuery) {
					const $preset = window.jQuery(presetField);
					if (emitChange) {
						$preset.trigger('change.select2').trigger('change');
					}
					else {
						$preset.trigger('change.select2');
					}
				}
				window.requestAnimationFrame(updatePresetRenderedLabel);
			};
		const applyProfilesFromPayload = (payload) => {
			if (!payload || typeof payload !== 'object' || !payload.profiles || typeof payload.profiles !== 'object') {
				return;
			}

			for (let presetId = 1; presetId <= 7; presetId++) {
				const key = String(presetId);
				const profile = payload.profiles[key];
				if (!profile || typeof profile !== 'object') {
					continue;
				}

					if (Object.prototype.hasOwnProperty.call(profile, 'name')) {
						setSimpleFieldValue(`profile${presetId}_name`, String(profile.name || '').slice(0, 15), {dispatchEvents: false});
					}
					if (Object.prototype.hasOwnProperty.call(profile, 'row_count')) {
						setSimpleFieldValue(`profile${presetId}_row_count`, String(profile.row_count || '2'), {dispatchEvents: false});
					}
					if (Object.prototype.hasOwnProperty.call(profile, 'ports_per_row')) {
						setSimpleFieldValue(`profile${presetId}_ports_per_row`, String(profile.ports_per_row || '12'), {dispatchEvents: false});
					}
					if (Object.prototype.hasOwnProperty.call(profile, 'sfp_ports')) {
						setSimpleFieldValue(`profile${presetId}_sfp_ports`, String(profile.sfp_ports || '0'), {dispatchEvents: false});
					}
					if (Object.prototype.hasOwnProperty.call(profile, 'switch_size')) {
						setSimpleFieldValue(`profile${presetId}_switch_size`, String(profile.switch_size || '100'), {dispatchEvents: false});
					}
					if (Object.prototype.hasOwnProperty.call(profile, 'switch_brand')) {
						setSimpleFieldValue(`profile${presetId}_switch_brand`, String(profile.switch_brand || 'NETSWITCH'), {dispatchEvents: false});
					}
					if (Object.prototype.hasOwnProperty.call(profile, 'switch_model')) {
						setSimpleFieldValue(`profile${presetId}_switch_model`, String(profile.switch_model || 'SW-24G'), {dispatchEvents: false});
					}
				}

				rebuildPresetOptionsFromFields();
				refreshPresetSelectUi({emitChange: false});
				refreshNameEditor();
			};
			const syncSelectedProfileName = () => {
			const presetId = Number(presetField.value);
			if (presetId < 1 || presetId > 7) {
				return;
			}

				const input = document.querySelector('.switch-profile-name');
				if (!input) {
					return;
				}

				const nameValue = normalizeProfileName(input.value, presetId);
				input.value = nameValue;
				const hiddenField = getProfileNameField(presetId);
				if (hiddenField) {
					hiddenField.value = nameValue;
					hiddenField.dispatchEvent(new Event('input', {bubbles: true}));
					hiddenField.dispatchEvent(new Event('change', {bubbles: true}));
				}
					updatePresetOptionLabel(presetId, nameValue);
					refreshPresetSelectUi({emitChange: false});
			};
		const saveProfileToFile = (presetId) => {
			const nameField = getProfileNameField(presetId);
			const body = new URLSearchParams();
			body.set('preset_id', String(presetId));
			body.set('name', String(nameField ? nameField.value || '' : `Profile ${presetId}`));
			body.set('row_count', readIntField(`profile${presetId}_row_count`, '2'));
				body.set('ports_per_row', readIntField(`profile${presetId}_ports_per_row`, '12'));
				body.set('sfp_ports', readIntField(`profile${presetId}_sfp_ports`, '0'));
				body.set('switch_size', readIntField(`profile${presetId}_switch_size`, '100'));
				body.set('switch_brand', (findField(`profile${presetId}_switch_brand`)?.value || '').toString());
				body.set('switch_model', (findField(`profile${presetId}_switch_model`)?.value || '').toString());
			const csrfField = getCsrfTokenField();
			if (csrfField && csrfField.name && csrfField.value) {
				body.set(csrfField.name, csrfField.value);
			}

			const url = new URL(window.location.href);
			url.search = '';
			url.searchParams.set('action', 'widget.switch.profiles.save');
			url.searchParams.set('output', 'ajax');

				return fetch(url.toString(), {
					method: 'POST',
					credentials: 'same-origin',
					headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
					body: body.toString()
				})
					.then((response) => response.text().then((text) => ({
						text,
						ok: response.ok,
						status: response.status
					})))
					.then(({text, ok, status}) => {
						const decodeHtmlEntities = (raw) => raw
							.replace(/&quot;/g, '"')
							.replace(/&#34;/g, '"')
							.replace(/&#039;/g, "'")
							.replace(/&amp;/g, '&')
							.replace(/&lt;/g, '<')
							.replace(/&gt;/g, '>');
						const normalizedText = decodeHtmlEntities(text);

						const parsePayload = (raw) => {
							const parsed = JSON.parse(raw);
						if (parsed && Object.prototype.hasOwnProperty.call(parsed, 'saved')) {
							return parsed;
						}
						if (parsed && parsed.main_block) {
							const nested = JSON.parse(parsed.main_block);
							if (nested && Object.prototype.hasOwnProperty.call(nested, 'saved')) {
								return nested;
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
						catch (error) { logDebug("silent catch", error); }
						try {
							const parsed = parsePayload(normalizedText);
							if (parsed !== null) {
								return parsed;
							}
						}
						catch (error) { logDebug("silent catch", error); }

					const extractEmbeddedJson = (raw, marker) => {
						const start = raw.indexOf(marker);
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

						const embeddedMain = extractEmbeddedJson(text, '{"main_block"');
						if (embeddedMain !== null) {
						try {
							const parsed = parsePayload(embeddedMain);
							if (parsed !== null) {
								return parsed;
							}
						}
						catch (error) { logDebug("silent catch", error); }
					}

						const start = text.indexOf('{"saved"');
						if (start !== -1) {
						try {
							let depth = 0;
							let inString = false;
							let escaped = false;
							for (let i = start; i < text.length; i++) {
								const ch = text[i];
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
										const embedded = text.slice(start, i + 1);
										const parsed = parsePayload(embedded);
										if (parsed !== null) {
											return parsed;
										}
										break;
									}
								}
							}
						}
							catch (error) { logDebug("silent catch", error); }
						}
						const embeddedMainNormalized = extractEmbeddedJson(normalizedText, '{"main_block"');
						if (embeddedMainNormalized !== null) {
							try {
								const parsed = parsePayload(embeddedMainNormalized);
								if (parsed !== null) {
									return parsed;
								}
							}
							catch (error) { logDebug("silent catch", error); }
						}

						if (
							/"saved"\s*:\s*true/.test(text)
							|| /\\"saved\\"\s*:\s*true/.test(text)
							|| /"saved"\s*:\s*true/.test(normalizedText)
						) {
							return {saved: true, error: ''};
						}
						if (
							/"saved"\s*:\s*false/.test(text)
							|| /\\"saved\\"\s*:\s*false/.test(text)
							|| /"saved"\s*:\s*false/.test(normalizedText)
						) {
							return {saved: false, error: 'Profile save failed.'};
						}

							if (ok && status >= 200 && status < 300 && !/Access denied/i.test(normalizedText)) {
								return {saved: true, error: ''};
							}

							if (/Access denied/i.test(normalizedText)) {
								return {saved: false, error: 'Access denied while saving profile.'};
							}

							return {
								saved: false,
								error: (ok && status >= 200 && status < 300)
									? 'Unexpected save response format.'
									: 'Profile save failed.'
							};
						});
			};

		const ensureNameEditor = () => {
			const row = presetField.closest('.form-field');
			if (!row) {
				return null;
			}
			const container = row.parentNode || row;

			let input = container.querySelector('.switch-profile-name');
			if (input) {
				return input;
			}

			const label = document.createElement('label');
			label.textContent = 'Profile name';
			label.className = 'switch-profile-name-label';
			label.htmlFor = 'switch_profile_name';
			label.style.display = 'none';

			const wrapper = document.createElement('div');
			wrapper.className = 'form-field switch-profile-name-wrap';
			wrapper.style.display = 'none';
			wrapper.style.alignItems = 'center';

			input = document.createElement('input');
			input.type = 'text';
			input.id = 'switch_profile_name';
			input.className = 'switch-profile-name';
			input.style.width = '15ch';
			input.style.maxWidth = '15ch';
			input.style.minWidth = '15ch';
			input.maxLength = 15;

			input.addEventListener('input', () => {
				const presetId = Number(presetField.value);
				if (presetId < 1 || presetId > 7) {
					return;
				}
				// Do not write profile fields on typing; commit only on explicit save button.
			});

			wrapper.appendChild(input);

			const brandLabel = container.querySelector('label[for="switch_brand"]');
			if (brandLabel && brandLabel.parentNode === container) {
				container.insertBefore(label, brandLabel);
				container.insertBefore(wrapper, brandLabel);
			}
			else if (row.nextSibling) {
				container.insertBefore(label, row.nextSibling);
				container.insertBefore(wrapper, row.nextSibling);
			}
			else {
				container.appendChild(label);
				container.appendChild(wrapper);
			}

			return input;
		};

		const hideInternalProfileFields = () => {
			const suffixes = ['_name', '_row_count', '_ports_per_row', '_sfp_ports', '_switch_size', '_switch_brand', '_switch_model'];

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

			const wrapper = input.closest('.switch-profile-name-wrap');
				const label = document.querySelector('.switch-profile-name-label');
				const presetId = Number(presetField.value);
				if (presetId >= 1 && presetId <= 7) {
				const nameField = getProfileNameField(presetId);
				const currentName = nameField && String(nameField.value || '').trim() !== ''
					? String(nameField.value).trim()
					: `Profile ${presetId}`;

					input.value = currentName;
					if (wrapper) {
						wrapper.style.display = 'grid';
					}
					if (label) {
						label.style.display = '';
					}
			}
			else {
				if (wrapper) {
					wrapper.style.display = 'none';
				}
				if (label) {
					label.style.display = 'none';
				}
			}
		};

		const ensureSaveStatus = () => {
			const row = presetField.closest('.form-field');
			if (!row) {
				return null;
			}

			const container = row.parentNode || row;
			let status = container.querySelector('.switch-profile-save-status');
			if (status) {
				return status;
			}

			const wrapper = document.createElement('div');
			wrapper.className = 'form-field switch-profile-save-status-wrap';
			wrapper.style.gridColumn = '2';
			wrapper.style.marginTop = '4px';
			wrapper.style.display = 'none';

			status = document.createElement('span');
			status.className = 'switch-profile-save-status';
			status.style.fontSize = '12px';
			status.style.fontWeight = '600';
			status.style.display = 'inline-block';
			status.style.padding = '2px 0';
			wrapper.appendChild(status);

			const nameLabel = container.querySelector('.switch-profile-name-label');
			if (nameLabel && nameLabel.parentNode === container) {
				container.insertBefore(wrapper, nameLabel);
			}
			else if (row.nextSibling) {
				container.insertBefore(wrapper, row.nextSibling);
			}
			else {
				container.appendChild(wrapper);
			}

			return status;
		};

		const setSaveStatus = (text, color, timeoutMs = 0) => {
			const status = ensureSaveStatus();
			if (!status) {
				return;
			}

			const wrapper = status.closest('.switch-profile-save-status-wrap');
			if (!wrapper) {
				return;
			}

			status.textContent = text;
			status.style.color = color;
			wrapper.style.display = text ? 'block' : 'none';

			if (timeoutMs > 0) {
				window.setTimeout(() => {
					if (status.textContent === text) {
						status.textContent = '';
						wrapper.style.display = 'none';
					}
				}, timeoutMs);
			}
		};

		const showSaveToast = (text, ok = true) => {
			let toast = document.getElementById('switch-profile-save-toast');
			if (!toast) {
				toast = document.createElement('div');
				toast.id = 'switch-profile-save-toast';
				toast.style.position = 'fixed';
				toast.style.top = '20px';
				toast.style.right = '20px';
				toast.style.zIndex = '99999';
				toast.style.padding = '10px 14px';
				toast.style.borderRadius = '6px';
				toast.style.fontSize = '13px';
				toast.style.fontWeight = '600';
				toast.style.boxShadow = '0 8px 20px rgba(0,0,0,.25)';
				toast.style.transition = 'opacity .2s ease';
				toast.style.opacity = '0';
				document.body.appendChild(toast);
			}

			toast.textContent = text;
			toast.style.background = ok ? '#2F855A' : '#C53030';
			toast.style.color = '#FFFFFF';
			toast.style.opacity = '1';

			window.clearTimeout(showSaveToast._timerId);
			showSaveToast._timerId = window.setTimeout(() => {
				toast.style.opacity = '0';
			}, ok ? 2200 : 3200);
		};

		const ensureBuildMarker = () => {
			const row = presetField.closest('.form-field');
			if (!row) {
				return;
			}

			if (row.querySelector('.switch-widget-build-marker')) {
				return;
			}

			const marker = document.createElement('span');
			marker.className = 'switch-widget-build-marker';
			marker.textContent = `Build ${SWITCH_WIDGET_BUILD}`;
			marker.style.marginLeft = '8px';
			marker.style.fontSize = '11px';
			marker.style.color = '#718096';
			row.appendChild(marker);
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

				const runSave = () => {
					if (button.dataset.saving === '1') {
						return false;
					}

					const presetId = Number(presetField.value);
					if (presetId < 1 || presetId > 7 || Number.isNaN(presetId)) {
						window.alert('Select a profile (1-7) first.');
						return false;
					}

					setSimpleFieldValue(`profile${presetId}_row_count`, readIntField('row_count', '2'), {dispatchEvents: false});
					setSimpleFieldValue(`profile${presetId}_ports_per_row`, readIntField('ports_per_row', '12'), {dispatchEvents: false});
					setSimpleFieldValue(`profile${presetId}_sfp_ports`, readIntField('sfp_ports', '0'), {dispatchEvents: false});
					setSimpleFieldValue(`profile${presetId}_switch_size`, readIntField('switch_size', '100'), {dispatchEvents: false});
					setSimpleFieldValue(`profile${presetId}_switch_brand`, (findField('switch_brand')?.value || 'NETSWITCH').toString(), {dispatchEvents: false});
					setSimpleFieldValue(`profile${presetId}_switch_model`, (findField('switch_model')?.value || 'SW-24G').toString(), {dispatchEvents: false});
					syncSelectedProfileName();
					refreshNameEditor();

					button.dataset.saving = '1';
					button.disabled = true;
					const originalText = button.textContent;
					button.textContent = 'Saving...';
					setSaveStatus('Saving profile...', '#4A5568');
					showSaveToast('Saving profile...', true);

				let saveOk = false;
				let saveError = '';

				saveProfileToFile(presetId).then((payload) => {
					if (!payload || payload.saved !== false) {
						applyProfilesFromPayload(payload || {});
						const currentName = String(document.querySelector('.switch-profile-name')?.value || '').trim();
						const expectedLabel = normalizeProfileName(currentName, presetId);
						const hiddenField = getProfileNameField(presetId);
							if (hiddenField) {
								hiddenField.value = expectedLabel;
							}
							updatePresetOptionLabel(presetId, currentName);
							saveOk = true;
						}
					else {
						saveError = payload.error || 'Profile save failed.';
					}
				}).catch(() => {
					saveError = 'Profile save failed.';
					}).finally(() => {
						button.dataset.saving = '0';
						button.disabled = false;
						if (saveOk) {
							button.textContent = 'Saved';
						setSaveStatus('Profile saved', '#2F855A');
						showSaveToast('Profile saved', true);
						window.setTimeout(() => {
							button.textContent = originalText;
						}, 1800);
					}
					else {
						button.textContent = 'Save failed';
						setSaveStatus(saveError || 'Profile save failed', '#C53030');
						showSaveToast(saveError || 'Profile save failed', false);
						window.setTimeout(() => {
							button.textContent = originalText;
						}, 2200);
					}
				});

					return false;
				};

				button.addEventListener('click', (event) => {
					event.preventDefault();
					runSave();
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
				setSimpleFieldValue('switch_brand', preset.switch_brand);
				setSimpleFieldValue('switch_model', preset.switch_model);
				updatePortFieldsetVisibility();
				refreshNameEditor();
			};

		presetField.addEventListener('change', applyPreset);

			presetField.dataset.switchPresetInit = '1';
			ensureBuildMarker();
			addSaveButton();
			hideInternalProfileFields();
			applyPreset();
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
		select.dataset.port24OptionsLoaded = '0';
		select.dataset.port24OptionsHost = '';

		select.addEventListener('change', () => {
			field.value = select.value === '0' ? '' : select.value;
			if (field.value !== '') {
				select.dataset.initialValue = String(field.value);
			}
				field.dispatchEvent(new Event('change', {bubbles: true}));
		});
		const lazyLoadOptions = () => {
			loadFullSelectOptions(select);
		};
		select.addEventListener('focus', lazyLoadOptions);
		select.addEventListener('mousedown', lazyLoadOptions);

		if (field.parentNode) {
			field.parentNode.insertBefore(select, field.nextSibling);
		}
		field.style.display = 'none';

		return select;
	}

	let currentTriggerHostid = '';
	let currentTriggerOptions = [];
	let currentItemSuggestionHostid = '';
	const itemSuggestionCache = new Map();
	const itemSuggestionTimers = new WeakMap();
	const itemSuggestionControllers = new WeakMap();

	const ITEM_PATTERN_FIELD_NAMES = [
		'traffic_in_item_pattern',
		'traffic_out_item_pattern',
		'in_errors_item_pattern',
		'out_errors_item_pattern',
		'in_discards_item_pattern',
		'out_discards_item_pattern',
		'speed_item_pattern',
		'summary_software_item_key',
		'summary_vlans_item_key',
		'summary_cpu_item_key',
		'summary_fan_item_key',
		'summary_uptime_item_key',
		'summary_serial_item_key'
	];

	function getItemPatternFields() {
		return ITEM_PATTERN_FIELD_NAMES
			.map((name) => findField(name))
			.filter((field) => field && field.tagName === 'INPUT');
	}

	function hideSuggestionPopupForField(field) {
		const popup = field._port24SuggestPopup;
		if (popup) {
			popup.classList.add('is-hidden');
		}
	}

	function clearSuggestionListForField(field) {
		const popup = field._port24SuggestPopup;
		if (!popup) {
			return;
		}
		popup.innerHTML = '';
		hideSuggestionPopupForField(field);
	}

	function ensureSuggestionPopupForField(field) {
		if (field._port24SuggestPopup) {
			return field._port24SuggestPopup;
		}

		const wrap = field.closest('.form-field');
		if (!wrap) {
			return null;
		}

		if (getComputedStyle(wrap).position === 'static') {
			wrap.style.position = 'relative';
		}

		const popup = document.createElement('div');
		popup.className = 'port24-item-suggest-pop is-hidden';
		wrap.appendChild(popup);
		field._port24SuggestPopup = popup;
		return popup;
	}

	function parseSuggestionsPayload(text) {
		const parsePayload = (raw) => {
			const payload = JSON.parse(raw);
			if (Array.isArray(payload.suggestions)) {
				return payload;
			}
			if (payload.main_block) {
				const nested = JSON.parse(payload.main_block);
				if (Array.isArray(nested.suggestions)) {
					return nested;
				}
			}
			return null;
		};

		const extractEmbeddedJson = (raw) => {
			const marker = '{"suggestions":';
			const start = raw.indexOf(marker);
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
		catch (error) { logDebug("silent catch", error); }

		const embedded = extractEmbeddedJson(text);
		if (embedded !== null) {
			try {
				const parsed = parsePayload(embedded);
				if (parsed !== null) {
					return parsed;
				}
			}
			catch (error) { logDebug("silent catch", error); }
		}

		return {suggestions: []};
	}

	function fetchItemSuggestions(hostid, query, signal) {
		const url = new URL('zabbix.php', window.location.origin);
		url.searchParams.set('action', 'widget.switch.items');
		url.searchParams.set('output', 'ajax');
		url.searchParams.set('hostid', hostid);
		url.searchParams.set('q', query);

		return fetch(url.toString(), {
			method: 'GET',
			credentials: 'same-origin',
			headers: {'X-Requested-With': 'XMLHttpRequest'},
			signal
		})
			.then((response) => response.text())
			.then((text) => parseSuggestionsPayload(text).suggestions || []);
	}

	function normalizeSuggestionToken(text) {
		return String(text || '')
			.trim()
			.toLowerCase()
			.replace(/\[\*\]/g, '')
			.replace(/\*/g, '')
			.replace(/[\[\]]/g, '');
	}

	function getSuggestionPrefixFallbacks(text) {
		const token = normalizeSuggestionToken(text);
		if (token.length < 3) {
			return [];
		}

		const tokens = [];
		for (let len = token.length; len >= 3; len--) {
			tokens.push(token.slice(0, len));
		}
		return tokens;
	}

	function filterSuggestionsLocally(suggestions, query, limit = 50) {
		const q = normalizeSuggestionToken(query);
		if (q === '') {
			return suggestions.slice(0, limit);
		}

		const out = [];
		for (const item of suggestions) {
			if (!item || typeof item !== 'object') {
				continue;
			}
			const value = normalizeSuggestionToken(item.value || '');
			const label = normalizeSuggestionToken(item.label || '');
			if (value.startsWith(q) || label.startsWith(q)) {
				out.push(item);
				if (out.length >= limit) {
					break;
				}
			}
		}
		return out;
	}

	function applySuggestionsToField(field, suggestions) {
		const popup = ensureSuggestionPopupForField(field);
		if (!popup) {
			return;
		}
		popup.innerHTML = '';
		if (!Array.isArray(suggestions) || suggestions.length === 0) {
			popup.classList.add('is-hidden');
			return;
		}

		for (const item of suggestions) {
			if (!item || typeof item !== 'object') {
				continue;
			}
			const value = String(item.value || '').trim();
			if (value === '') {
				continue;
			}

			const row = document.createElement('button');
			row.type = 'button';
			row.className = 'port24-item-suggest-item';

			const label = document.createElement('span');
			label.textContent = String(item.label || value);
			const kind = document.createElement('span');
			kind.className = 'port24-item-suggest-kind';
			kind.textContent = item.type === 'pattern' ? '[*]' : 'item';

			row.appendChild(label);
			row.appendChild(kind);
			const applySuggestion = () => {
				field.value = value;
				field.dispatchEvent(new Event('input', {bubbles: true}));
				field.dispatchEvent(new Event('change', {bubbles: true}));
				hideSuggestionPopupForField(field);
			};
			// Use mousedown so selection still works when the input loses focus on click.
			row.addEventListener('mousedown', (event) => {
				event.preventDefault();
				event.stopPropagation();
				applySuggestion();
			});
			row.addEventListener('click', (event) => {
				event.preventDefault();
				event.stopPropagation();
				applySuggestion();
			});
			popup.appendChild(row);
		}

		popup.classList.remove('is-hidden');
	}

	function scheduleItemSuggestionsForField(field) {
		const hostid = String(currentItemSuggestionHostid || '');
		if (hostid === '') {
			clearSuggestionListForField(field);
			return;
		}

		const query = String(field.value || '').trim();
		const seed = String(field.dataset.port24SuggestSeed || '').trim();
		const effectiveQuery = query !== '' ? query : seed;
		const fallbackTokens = getSuggestionPrefixFallbacks(effectiveQuery);
		if (fallbackTokens.length === 0) {
			clearSuggestionListForField(field);
			return;
		}

		if (itemSuggestionTimers.has(field)) {
			window.clearTimeout(itemSuggestionTimers.get(field));
		}

		const timerId = window.setTimeout(() => {
			if (itemSuggestionControllers.has(field)) {
				try {
					itemSuggestionControllers.get(field).abort();
				}
				catch (error) { logDebug("silent catch", error); }
			}

			const controller = new AbortController();
			itemSuggestionControllers.set(field, controller);

			Promise.resolve()
				.then(async () => {
					for (const token of fallbackTokens) {
						const cacheKey = `${hostid}|${token}`;
						let suggestions = itemSuggestionCache.get(cacheKey);
						if (!suggestions) {
							suggestions = await fetchItemSuggestions(hostid, token, controller.signal);
							itemSuggestionCache.set(cacheKey, suggestions);
						}

						const filtered = filterSuggestionsLocally(suggestions, effectiveQuery, 50);
						if (filtered.length > 0) {
							return filtered;
						}
					}

					return [];
				})
				.then((filtered) => {
					if (String(currentItemSuggestionHostid || '') !== hostid) {
						return;
					}
					applySuggestionsToField(field, filtered);
				})
				.catch((error) => {
					if (error && error.name === 'AbortError') {
						return;
					}
					logDebug('item suggestions request failed', error);
				});
		}, 180);

		itemSuggestionTimers.set(field, timerId);
	}

	function setItemSuggestionHost(hostid) {
		const nextHostid = String(hostid || '');
		if (nextHostid === currentItemSuggestionHostid) {
			return;
		}

		currentItemSuggestionHostid = nextHostid;
		itemSuggestionCache.clear();

		for (const field of getItemPatternFields()) {
			clearSuggestionListForField(field);
			if (nextHostid !== '') {
				scheduleItemSuggestionsForField(field);
			}
		}
	}

	function ensureItemPatternAutocomplete() {
		for (const field of getItemPatternFields()) {
			if (field.dataset.port24ItemSuggestInit === '1') {
				continue;
			}

			const token = String(field.name || field.id || '').toLowerCase();
			if (token.includes('traffic_out_item_pattern')) {
				field.dataset.port24SuggestSeed = 'ifOut';
			}
			else if (token.includes('traffic_in_item_pattern')) {
				field.dataset.port24SuggestSeed = 'ifIn';
			}
			else if (token.includes('out_errors_item_pattern')) {
				field.dataset.port24SuggestSeed = 'ifOutErrors';
			}
			else if (token.includes('in_errors_item_pattern')) {
				field.dataset.port24SuggestSeed = 'ifInErrors';
			}
			else if (token.includes('out_discards_item_pattern')) {
				field.dataset.port24SuggestSeed = 'ifOutDiscards';
			}
			else if (token.includes('in_discards_item_pattern')) {
				field.dataset.port24SuggestSeed = 'ifInDiscards';
			}

			field.dataset.port24ItemSuggestInit = '1';
			ensureSuggestionPopupForField(field);
			field.addEventListener('focus', () => scheduleItemSuggestionsForField(field));
			field.addEventListener('input', () => scheduleItemSuggestionsForField(field));
			field.addEventListener('blur', () => {
				window.setTimeout(() => hideSuggestionPopupForField(field), 120);
			});
		}
	}

	function setSelectLightOptions(select, hostid, selectedValue = '') {
		const selected = String(selectedValue || select.value || '');
		const firstText = hostid === '' ? 'Select host first' : 'Select trigger';

		select.innerHTML = '';
		const first = document.createElement('option');
		first.value = '';
		first.textContent = firstText;
		select.appendChild(first);

		if (selected !== '') {
			const selectedNode = document.createElement('option');
			selectedNode.value = selected;
			selectedNode.textContent = `#${selected}`;
			select.appendChild(selectedNode);
			select.value = selected;
		}
		else {
			select.value = '';
		}

		select.dataset.port24OptionsLoaded = '0';
		select.dataset.port24OptionsHost = hostid;
	}

	function loadFullSelectOptions(select) {
		const hostid = String(currentTriggerHostid || '');
		if (hostid === '') {
			return;
		}
		if (select.dataset.port24OptionsLoaded === '1' && select.dataset.port24OptionsHost === hostid) {
			return;
		}
		const initial = String(select.dataset.initialValue || select.value || '');
		setSelectOptions(select, currentTriggerOptions, hostid, initial);
		select.dataset.port24OptionsLoaded = '1';
		select.dataset.port24OptionsHost = hostid;
	}

	function setSelectOptions(select, triggers, hostid, selectedValue = '') {
		const selected = String(selectedValue || select.value || '');
		const firstText = hostid === '' ? 'Select host first' : 'Select trigger';
		const options = [{value: '', label: firstText}];

		for (const trigger of triggers) {
			options.push({value: String(trigger.id), label: trigger.name});
		}

		select.innerHTML = '';
		for (const option of options) {
			const node = document.createElement('option');
			node.value = option.value;
			node.textContent = option.label;
			select.appendChild(node);
		}

		if (selected !== '' && options.some((option) => String(option.value) === selected)) {
			select.value = selected;
			select.dataset.initialValue = selected;
		}
		else {
			select.value = '';
		}
	}

	function applyTriggers(triggers, hostid) {
		currentTriggerHostid = String(hostid || '');
		currentTriggerOptions = Array.isArray(triggers) ? triggers : [];

		for (const field of getTriggerFields()) {
			const select = ensureSelectForField(field);
			const initial = String(field.value || select.dataset.initialValue || '');
			select.value = initial;
			setSelectLightOptions(select, currentTriggerHostid, initial);
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
				catch (error) { logDebug("silent catch", error); }

				// Some Zabbix setups wrap AJAX response in full HTML layout.
				const embeddedJson = extractEmbeddedJson(text);
				if (embeddedJson !== null) {
					try {
						const parsed = parsePayload(embeddedJson);
						if (parsed !== null) {
							return parsed;
						}
					}
					catch (error) { logDebug("silent catch", error); }
				}

				return {triggers: []};
			});
	}

	window.switch_widget_form = {
		init() {
				const stopExisting = () => {
					if (window.switch_widget_form._refreshTimer) {
						clearInterval(window.switch_widget_form._refreshTimer);
						window.switch_widget_form._refreshTimer = null;
					}
				if (window.switch_widget_form._refreshObserver) {
					window.switch_widget_form._refreshObserver.disconnect();
					window.switch_widget_form._refreshObserver = null;
				}
					if (window.switch_widget_form._onVisibilityChange) {
						document.removeEventListener('visibilitychange', window.switch_widget_form._onVisibilityChange);
						window.switch_widget_form._onVisibilityChange = null;
					}
					if (window.switch_widget_form._onPickerOutsideClick) {
						document.removeEventListener('click', window.switch_widget_form._onPickerOutsideClick);
						window.switch_widget_form._onPickerOutsideClick = null;
					}
				};
				stopExisting();
				ensureGlobalPickerOutsideClick();

			let previousHostId = null;
			let inFlight = false;
			let uiBootstrapped = false;
			let lastLayoutKey = '';

			const getLayoutKey = () => [
				readIntField('row_count', '2'),
				readIntField('ports_per_row', '12'),
				readIntField('sfp_ports', '0'),
				String(findField('preset')?.value || '0')
			].join('|');

			const refresh = () => {
				const layoutKey = getLayoutKey();
				if (!uiBootstrapped || layoutKey !== lastLayoutKey) {
					migrateLegacyDefaultColors();
					ensurePresetControls();
					if (typeof window.switch_widget_apply_preset_if_changed === 'function') {
						window.switch_widget_apply_preset_if_changed();
					}
					ensurePortFieldsetGrid();
					ensurePortFieldRowsAligned();
					updatePortFieldsetVisibility();

						for (const field of getColorFields()) {
							ensureColorPickerForField(field);
						}
						ensureEditSections();
						ensureUtilizationControls();
						ensureBulkControls();
						ensureItemPatternAutocomplete();

					uiBootstrapped = true;
					lastLayoutKey = layoutKey;
				}
					const hostid = getHostId();
				if (hostid === previousHostId || inFlight) {
					return;
				}

				previousHostId = hostid;
				setItemSuggestionHost(hostid);
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
			const timerId = setInterval(refresh, 1200);
			window.switch_widget_form._refreshTimer = timerId;

			const onVisibilityChange = () => {
				if (document.visibilityState === 'hidden' && window.switch_widget_form._refreshTimer) {
					clearInterval(window.switch_widget_form._refreshTimer);
					window.switch_widget_form._refreshTimer = null;
				}
				else if (document.visibilityState === 'visible' && !window.switch_widget_form._refreshTimer) {
					window.switch_widget_form._refreshTimer = setInterval(refresh, 1200);
				}
			};
			document.addEventListener('visibilitychange', onVisibilityChange);
			window.switch_widget_form._onVisibilityChange = onVisibilityChange;

			const formRoot = document.getElementById('widget-dialogue-form');
			if (formRoot && formRoot.parentNode) {
				const observer = new MutationObserver(() => {
					if (!document.body.contains(formRoot)) {
						stopExisting();
					}
				});
				observer.observe(document.body, {childList: true, subtree: true});
				window.switch_widget_form._refreshObserver = observer;
			}
		}
	};
})();
