<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {
	private const DEFAULT_ROW_COUNT = 2;
	private const DEFAULT_PORTS_PER_ROW = 12;
	private const DEFAULT_TRAFFIC_IN_PATTERN = 'ifInOctets[*]';
	private const DEFAULT_TRAFFIC_OUT_PATTERN = 'ifOutOctets[*]';
	private const DEFAULT_SPEED_PATTERN = 'ifHighSpeed[*]';
	private const DEFAULT_IN_ERRORS_PATTERN = 'ifInErrors[*]';
	private const DEFAULT_OUT_ERRORS_PATTERN = 'ifOutErrors[*]';
	private const DEFAULT_IN_DISCARDS_PATTERN = 'ifInDiscards[*]';
	private const DEFAULT_OUT_DISCARDS_PATTERN = 'ifOutDiscards[*]';
	private const TRAFFIC_UNIT_BYTES = 0;
	private const TRAFFIC_UNIT_BITS = 1;
	private const MAX_SPEED_PATTERN_LENGTH = 30;
	private const TRAFFIC_POINTS = 24;
	private const TRAFFIC_LOOKBACK_SECONDS = 1800;
	private const COUNTER_LOOKBACK_SECONDS = 86400;
	private const COUNTER_POINTS = 240;
	private const STATE_BAR_WINDOW_SECONDS = 86400;
	private const STATE_BAR_BUCKETS = 48;
	private const MAX_ROW_COUNT = 24;
	private const MAX_PORTS_PER_ROW = 48;
	private const MAX_SFP_PORTS = 32;
	private const MAX_TOTAL_PORTS = 96;
	protected function doAction(): void {
		$layout = $this->getLayout();
		$hostid = $this->extractHostId();
		$traffic_in_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['traffic_in_item_pattern'] ?? self::DEFAULT_TRAFFIC_IN_PATTERN), self::DEFAULT_TRAFFIC_IN_PATTERN);
		$traffic_out_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['traffic_out_item_pattern'] ?? self::DEFAULT_TRAFFIC_OUT_PATTERN), self::DEFAULT_TRAFFIC_OUT_PATTERN);
		$traffic_unit_mode = ((int) ($this->fields_values['traffic_unit_mode'] ?? self::TRAFFIC_UNIT_BYTES)) === self::TRAFFIC_UNIT_BITS
			? self::TRAFFIC_UNIT_BITS
			: self::TRAFFIC_UNIT_BYTES;
		$speed_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['speed_item_pattern'] ?? self::DEFAULT_SPEED_PATTERN), self::DEFAULT_SPEED_PATTERN);
		$in_errors_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['in_errors_item_pattern'] ?? self::DEFAULT_IN_ERRORS_PATTERN), self::DEFAULT_IN_ERRORS_PATTERN);
		$out_errors_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['out_errors_item_pattern'] ?? self::DEFAULT_OUT_ERRORS_PATTERN), self::DEFAULT_OUT_ERRORS_PATTERN);
		$in_discards_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['in_discards_item_pattern'] ?? self::DEFAULT_IN_DISCARDS_PATTERN), self::DEFAULT_IN_DISCARDS_PATTERN);
		$out_discards_pattern = $this->sanitizeItemPattern((string) ($this->fields_values['out_discards_item_pattern'] ?? self::DEFAULT_OUT_DISCARDS_PATTERN), self::DEFAULT_OUT_DISCARDS_PATTERN);
		$speed_pattern = substr($speed_pattern, 0, self::MAX_SPEED_PATTERN_LENGTH);
		$speed_pattern_alt = $this->getAlternateSpeedPattern($speed_pattern);
		$port_color_mode_raw = $this->fields_values['port_color_mode'] ?? 0;
		if (is_array($port_color_mode_raw)) {
			$port_color_mode_raw = reset($port_color_mode_raw);
		}
		$port_color_mode_int = (int) $port_color_mode_raw;
		$port_color_mode = ($port_color_mode_int === 1 || (string) $port_color_mode_raw === 'utilization')
			? 1
			: 0;
		$utilization_overlay_enabled = ((int) ($this->fields_values['utilization_overlay_enabled'] ?? 1)) === 1 ? 1 : 0;
		$util_low_threshold = $this->clampFloat($this->extractFloat($this->fields_values['utilization_low_threshold'] ?? 5.0), 0.0, 100.0);
		$util_warn_threshold = $this->clampFloat($this->extractFloat($this->fields_values['utilization_warn_threshold'] ?? 40.0), 0.0, 100.0);
		$util_high_threshold = $this->clampFloat($this->extractFloat($this->fields_values['utilization_high_threshold'] ?? 70.0), 0.0, 100.0);
		if ($util_warn_threshold < $util_low_threshold) {
			$util_warn_threshold = $util_low_threshold;
		}
		if ($util_high_threshold < $util_warn_threshold) {
			$util_high_threshold = $util_warn_threshold;
		}
		$util_low_color = $this->safeColor((string) ($this->fields_values['utilization_low_color'] ?? '#22C55E'), '#22C55E');
		$util_warn_color = $this->safeColor((string) ($this->fields_values['utilization_warn_color'] ?? '#FCD34D'), '#FCD34D');
		$util_high_color = $this->safeColor((string) ($this->fields_values['utilization_high_color'] ?? '#DB2777'), '#DB2777');
		$util_na_color = $this->safeColor((string) ($this->fields_values['utilization_na_color'] ?? '#94a3b8'), '#94A3B8');
		$legend_text = trim((string) ($this->fields_values['legend_text'] ?? ''));
		if ($legend_text === '') {
			$legend_text = sprintf(
				'Heatmap: green < %1$s%%, low >= %1$s%%, warn >= %2$s%%, high >= %3$s%%',
				$this->formatThreshold($util_low_threshold),
				$this->formatThreshold($util_warn_threshold),
				$this->formatThreshold($util_high_threshold)
			);
		}
		$ports = $this->loadPortsFromFields($layout['total_ports']);
		$trigger_meta = $this->loadTriggerMeta($ports);
		$widget_name = trim((string) $this->getInput('name', ''));
		if ($widget_name === '') {
			$widget_name = trim((string) ($this->fields_values['name'] ?? ''));
		}
		if ($widget_name === '' && method_exists($this->widget, 'getName')) {
			$widget_name = trim((string) $this->widget->getName());
		}
		if ($widget_name === '') {
			$widget_name = $this->widget->getDefaultName();
		}

		if ($hostid !== '' && !$this->hasHostAccess($hostid)) {
			$this->setResponse(new CControllerResponseData([
				'name' => $widget_name,
				'access_denied' => true,
				'legend_text' => $legend_text,
				'traffic_in_item_pattern' => $traffic_in_pattern,
					'traffic_out_item_pattern' => $traffic_out_pattern,
					'traffic_unit_mode' => $traffic_unit_mode,
					'in_errors_item_pattern' => $in_errors_pattern,
					'out_errors_item_pattern' => $out_errors_pattern,
					'in_discards_item_pattern' => $in_discards_pattern,
					'out_discards_item_pattern' => $out_discards_pattern,
					'speed_item_pattern' => $speed_pattern,
				'port_color_mode' => $port_color_mode,
				'utilization_overlay_enabled' => $utilization_overlay_enabled,
				'utilization_low_threshold' => $util_low_threshold,
				'utilization_warn_threshold' => $util_warn_threshold,
				'utilization_high_threshold' => $util_high_threshold,
				'utilization_low_color' => $util_low_color,
				'utilization_warn_color' => $util_warn_color,
				'utilization_high_color' => $util_high_color,
				'utilization_na_color' => $util_na_color,
				'hostid' => $hostid,
				'legend_size' => $this->clamp(
					$this->extractPositiveInt($this->fields_values['legend_size'] ?? 14),
					12,
					18
				),
				'switch_brand' => trim((string) ($this->fields_values['switch_brand'] ?? 'NETSWITCH')),
				'switch_model' => trim((string) ($this->fields_values['switch_model'] ?? 'SW-24G')),
				'switch_size' => $this->clamp(
					$this->extractPositiveInt($this->fields_values['switch_size'] ?? 100),
					40,
					100
				),
				'row_count' => $layout['row_count'],
				'ports_per_row' => $layout['ports_per_row'],
				'sfp_ports' => $layout['sfp_ports'],
				'ports' => [],
				'user' => [
					'debug_mode' => $this->getDebugMode()
				]
			]));

			return;
		}

		$sfp_start_index = max(1, $layout['total_ports'] - $layout['sfp_ports'] + 1);

		foreach ($ports as $index => &$port) {
			$triggerid = $port['triggerid'];
			$meta = $triggerid !== '' ? ($trigger_meta[$triggerid] ?? null) : null;
			$is_problem = $meta !== null ? $meta['is_problem'] : false;
			$has_trigger = ($triggerid !== '' && $meta !== null);
			$port['is_problem'] = $is_problem;
			$port['has_trigger'] = $has_trigger;
			$port['url'] = $triggerid !== ''
				? 'zabbix.php?action=problem.view&filter_set=1&triggerids%5B0%5D='.$triggerid
				: '';
			$port['trigger_name'] = $meta !== null ? $meta['description'] : '';
			$port['is_sfp'] = ($layout['sfp_ports'] > 0 && ($index + 1) >= $sfp_start_index);
			$port['hostid'] = $hostid;
			$port['traffic_in_item_key'] = $this->resolvePortItemKey($traffic_in_pattern, $index + 1);
			$port['traffic_out_item_key'] = $this->resolvePortItemKey($traffic_out_pattern, $index + 1);
			$port['speed_item_key'] = $this->resolvePortItemKey($speed_pattern, $index + 1);
			$port['speed_item_key_alt'] = $this->resolvePortItemKey($speed_pattern_alt, $index + 1);
			$port['in_errors_item_key'] = $this->resolvePortItemKey($in_errors_pattern, $index + 1);
			$port['out_errors_item_key'] = $this->resolvePortItemKey($out_errors_pattern, $index + 1);
			$port['in_discards_item_key'] = $this->resolvePortItemKey($in_discards_pattern, $index + 1);
			$port['out_discards_item_key'] = $this->resolvePortItemKey($out_discards_pattern, $index + 1);
		}
		unset($port);
		$traffic_series = $this->loadTrafficSeries($hostid, $ports);
		$counter_deltas = $this->loadCounterDeltas24h(
			$hostid,
			array_values(array_unique(array_filter(array_merge(
				array_map(static fn(array $port): string => (string) ($port['in_errors_item_key'] ?? ''), $ports),
				array_map(static fn(array $port): string => (string) ($port['out_errors_item_key'] ?? ''), $ports),
				array_map(static fn(array $port): string => (string) ($port['in_discards_item_key'] ?? ''), $ports),
				array_map(static fn(array $port): string => (string) ($port['out_discards_item_key'] ?? ''), $ports)
			), static fn(string $key): bool => $key !== '')))
		);
		$speed_values = $this->loadLatestItemValues($hostid, array_values(array_unique(array_filter(array_map(
			static fn(array $port): string => (string) ($port['speed_item_key'] ?? ''),
			$ports
		), static fn(string $key): bool => $key !== ''))));
		$speed_values_alt = $this->loadLatestItemValues($hostid, array_values(array_unique(array_filter(array_map(
			static fn(array $port): string => (string) ($port['speed_item_key_alt'] ?? ''),
			$ports
		), static fn(string $key): bool => $key !== ''))));
		$state_bars = $this->loadTriggerStateBars($ports, $trigger_meta);

		foreach ($ports as &$port) {
			$port['traffic_in_series'] = $traffic_series[$port['traffic_in_item_key']] ?? [];
			$port['traffic_out_series'] = $traffic_series[$port['traffic_out_item_key']] ?? [];
			$in_last = $port['traffic_in_series'] !== []
				? (float) $port['traffic_in_series'][count($port['traffic_in_series']) - 1]
				: 0.0;
			$out_last = $port['traffic_out_series'] !== []
				? (float) $port['traffic_out_series'][count($port['traffic_out_series']) - 1]
				: 0.0;
			$traffic_bps = max($in_last, $out_last);
			$speed_key_used = (string) ($port['speed_item_key'] ?? '');
			$speed_raw = (float) ($speed_values[$speed_key_used] ?? 0.0);
			if ($speed_raw <= 0.0) {
				$speed_key_alt = (string) ($port['speed_item_key_alt'] ?? '');
				if ($speed_key_alt !== '') {
					$speed_key_used = $speed_key_alt;
					$speed_raw = (float) ($speed_values_alt[$speed_key_alt] ?? 0.0);
				}
			}
			$speed_bps = $this->toSpeedBps($speed_raw, $speed_key_used);
			$utilization = ($speed_bps > 0.0) ? (($traffic_bps / $speed_bps) * 100.0) : null;
			$port['utilization_percent'] = $utilization;
			if ($utilization === null) {
				$port['utilization_color'] = $util_na_color;
			}
			elseif ($utilization >= $util_high_threshold) {
				$port['utilization_color'] = $util_high_color;
			}
			elseif ($utilization >= $util_warn_threshold) {
				$port['utilization_color'] = $util_warn_color;
			}
			elseif ($utilization >= $util_low_threshold) {
				$port['utilization_color'] = $util_low_color;
			}
			else {
				$port['utilization_color'] = '#BBF7D0';
			}
			if (!$port['has_trigger']) {
				$port['active_color'] = $port['default_color'];
			}
			elseif ($port['is_problem']) {
				$port['active_color'] = $port['trigger_color'];
			}
			else {
				$port['active_color'] = $port['trigger_ok_color'];
			}
			$port['state_24h'] = $state_bars[$port['triggerid']] ?? [];
			$in_errors_delta = (float) ($counter_deltas[$port['in_errors_item_key']]['delta'] ?? 0.0);
			$out_errors_delta = (float) ($counter_deltas[$port['out_errors_item_key']]['delta'] ?? 0.0);
			$in_discards_delta = (float) ($counter_deltas[$port['in_discards_item_key']]['delta'] ?? 0.0);
			$out_discards_delta = (float) ($counter_deltas[$port['out_discards_item_key']]['delta'] ?? 0.0);
			$in_errors_buckets = $counter_deltas[$port['in_errors_item_key']]['buckets'] ?? array_fill(0, self::STATE_BAR_BUCKETS, 0.0);
			$out_errors_buckets = $counter_deltas[$port['out_errors_item_key']]['buckets'] ?? array_fill(0, self::STATE_BAR_BUCKETS, 0.0);
			$in_discards_buckets = $counter_deltas[$port['in_discards_item_key']]['buckets'] ?? array_fill(0, self::STATE_BAR_BUCKETS, 0.0);
			$out_discards_buckets = $counter_deltas[$port['out_discards_item_key']]['buckets'] ?? array_fill(0, self::STATE_BAR_BUCKETS, 0.0);
			$errors_24h_buckets = [];
			$discards_24h_buckets = [];
			for ($i = 0; $i < self::STATE_BAR_BUCKETS; $i++) {
				$errors_24h_buckets[] = max(0.0, (float) ($in_errors_buckets[$i] ?? 0.0) + (float) ($out_errors_buckets[$i] ?? 0.0));
				$discards_24h_buckets[] = max(0.0, (float) ($in_discards_buckets[$i] ?? 0.0) + (float) ($out_discards_buckets[$i] ?? 0.0));
			}
			$error_delta_total = max(0.0, $in_errors_delta + $out_errors_delta);
			$discard_delta_total = max(0.0, $in_discards_delta + $out_discards_delta);
			$errors_trend = $counter_deltas[$port['in_errors_item_key']]['trend'] ?? 'n/a';
			$out_errors_trend = $counter_deltas[$port['out_errors_item_key']]['trend'] ?? 'n/a';
			if ($errors_trend === 'rising' || $out_errors_trend === 'rising') {
				$errors_trend = 'rising';
			}
			elseif ($errors_trend === 'stable' || $out_errors_trend === 'stable') {
				$errors_trend = 'stable';
			}

			$discards_trend = $counter_deltas[$port['in_discards_item_key']]['trend'] ?? 'n/a';
			$out_discards_trend = $counter_deltas[$port['out_discards_item_key']]['trend'] ?? 'n/a';
			if ($discards_trend === 'rising' || $out_discards_trend === 'rising') {
				$discards_trend = 'rising';
			}
			elseif ($discards_trend === 'stable' || $out_discards_trend === 'stable') {
				$discards_trend = 'stable';
			}

			$port['errors_24h_total'] = $error_delta_total;
			$port['errors_24h_in'] = max(0.0, $in_errors_delta);
			$port['errors_24h_out'] = max(0.0, $out_errors_delta);
			$port['errors_24h_trend'] = $errors_trend;
			$port['errors_24h_buckets'] = $errors_24h_buckets;
			$port['discards_24h_total'] = $discard_delta_total;
			$port['discards_24h_in'] = max(0.0, $in_discards_delta);
			$port['discards_24h_out'] = max(0.0, $out_discards_delta);
			$port['discards_24h_trend'] = $discards_trend;
			$port['discards_24h_buckets'] = $discards_24h_buckets;
		}
		unset($port);

		$this->setResponse(new CControllerResponseData([
				'name' => $widget_name,
				'access_denied' => false,
				'legend_text' => $legend_text,
				'traffic_in_item_pattern' => $traffic_in_pattern,
				'traffic_out_item_pattern' => $traffic_out_pattern,
				'traffic_unit_mode' => $traffic_unit_mode,
				'in_errors_item_pattern' => $in_errors_pattern,
				'out_errors_item_pattern' => $out_errors_pattern,
				'in_discards_item_pattern' => $in_discards_pattern,
				'out_discards_item_pattern' => $out_discards_pattern,
				'speed_item_pattern' => $speed_pattern,
				'port_color_mode' => $port_color_mode,
				'utilization_overlay_enabled' => $utilization_overlay_enabled,
				'utilization_low_threshold' => $util_low_threshold,
				'utilization_warn_threshold' => $util_warn_threshold,
				'utilization_high_threshold' => $util_high_threshold,
				'utilization_low_color' => $util_low_color,
				'utilization_warn_color' => $util_warn_color,
				'utilization_high_color' => $util_high_color,
				'utilization_na_color' => $util_na_color,
				'hostid' => $hostid,
				'legend_size' => $this->clamp(
					$this->extractPositiveInt($this->fields_values['legend_size'] ?? 14),
					12,
					18
				),
				'switch_brand' => trim((string) ($this->fields_values['switch_brand'] ?? 'NETSWITCH')),
				'switch_model' => trim((string) ($this->fields_values['switch_model'] ?? 'SW-24G')),
				'switch_size' => $this->clamp(
					$this->extractPositiveInt($this->fields_values['switch_size'] ?? 100),
					40,
					100
				),
				'row_count' => $layout['row_count'],
				'ports_per_row' => $layout['ports_per_row'],
				'sfp_ports' => $layout['sfp_ports'],
				'ports' => $ports,
				'user' => [
					'debug_mode' => $this->getDebugMode()
				]
			]));
	}

	private function hasHostAccess(string $hostid): bool {
		if ($hostid === '') {
			return true;
		}

		$rows = API::Host()->get([
			'output' => ['hostid'],
			'hostids' => [$hostid],
			'limit' => 1
		]);

		return is_array($rows) && $rows !== [];
	}

	private function loadPortsFromFields(int $total_ports): array {
		$ports = [];

		for ($i = 1; $i <= $total_ports; $i++) {
			$triggerid_raw = (int) ($this->fields_values['port'.$i.'_triggerid'] ?? 0);

			$ports[] = [
				'name' => trim((string) ($this->fields_values['port'.$i.'_name'] ?? sprintf('Port %d', $i))),
				'triggerid' => $triggerid_raw > 0 ? (string) $triggerid_raw : '',
				'default_color' => $this->safeColor((string) ($this->fields_values['port'.$i.'_default_color'] ?? '#d1d5db'), '#d1d5db'),
				'trigger_ok_color' => $this->safeColor((string) ($this->fields_values['port'.$i.'_trigger_ok_color'] ?? '#22c55e'), '#22c55e'),
				'trigger_color' => $this->safeColor((string) ($this->fields_values['port'.$i.'_trigger_color'] ?? '#e53e3e'), '#e53e3e')
			];
		}

		return $ports;
	}

	private function getLayout(): array {
		$row_count = $this->clamp(
			$this->extractPositiveInt($this->fields_values['row_count'] ?? self::DEFAULT_ROW_COUNT),
			1,
			self::MAX_ROW_COUNT
		);
		$ports_per_row = $this->clamp(
			$this->extractPositiveInt($this->fields_values['ports_per_row'] ?? self::DEFAULT_PORTS_PER_ROW),
			1,
			self::MAX_PORTS_PER_ROW
		);
		$sfp_ports = $this->clamp(
			$this->extractPositiveInt($this->fields_values['sfp_ports'] ?? 0),
			0,
			self::MAX_SFP_PORTS
		);
		$total_ports = min(self::MAX_TOTAL_PORTS, ($row_count * $ports_per_row) + $sfp_ports);
		$sfp_ports = min($sfp_ports, $total_ports);

		return [
			'row_count' => $row_count,
			'ports_per_row' => $ports_per_row,
			'sfp_ports' => $sfp_ports,
			'total_ports' => $total_ports
		];
	}

	private function loadTriggerMeta(array $ports): array {
		$triggerids = [];
		foreach ($ports as $port) {
			if ($port['triggerid'] !== '') {
				$triggerids[] = $port['triggerid'];
			}
		}

		$triggerids = array_values(array_unique($triggerids));
		if ($triggerids === []) {
			return [];
		}

		$rows = API::Trigger()->get([
			'output' => ['triggerid', 'value', 'description'],
			'triggerids' => $triggerids,
			'preservekeys' => true
		]);

		$result = [];
		foreach ($rows as $row) {
			$result[(string) $row['triggerid']] = [
				'is_problem' => ((int) $row['value'] === 1),
				'description' => (string) $row['description']
			];
		}

		$missing_ids = [];
		foreach ($triggerids as $triggerid) {
			if (!isset($result[$triggerid])) {
				$missing_ids[] = $triggerid;
			}
		}
		if ($missing_ids !== []) {
			$prototype_rows = API::TriggerPrototype()->get([
				'output' => ['triggerid', 'description'],
				'triggerids' => $missing_ids,
				'preservekeys' => true
			]);
			foreach ($prototype_rows as $row) {
				$result[(string) $row['triggerid']] = [
					'is_problem' => false,
					'description' => '[Prototype] '.(string) $row['description']
				];
			}
		}

		return $result;
	}

	private function safeColor(string $value, string $fallback): string {
		$value = trim($value);

		if (preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1) {
			return strtoupper($value);
		}

		if (preg_match('/^[0-9a-fA-F]{6}$/', $value) === 1) {
			return '#'.strtoupper($value);
		}

		return $fallback;
	}

	private function extractPositiveInt($value): int {
		if (is_array($value)) {
			$value = reset($value);
		}

		if (is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0) {
			return (int) $value;
		}

		return 0;
	}

	private function clamp(int $value, int $min, int $max): int {
		return max($min, min($max, $value));
	}

	private function clampFloat(float $value, float $min, float $max): float {
		return max($min, min($max, $value));
	}

	private function extractFloat($value): float {
		if (is_array($value)) {
			$value = reset($value);
		}

		if (is_int($value) || is_float($value)) {
			return (float) $value;
		}

		if (!is_scalar($value)) {
			return 0.0;
		}

		$text = str_replace(',', '.', trim((string) $value));
		if ($text === '' || !is_numeric($text)) {
			return 0.0;
		}

		return (float) $text;
	}

	private function formatThreshold(float $value): string {
		$text = number_format($value, 2, '.', '');
		$text = rtrim(rtrim($text, '0'), '.');
		return $text !== '' ? $text : '0';
	}

	private function sanitizeItemPattern(string $value, string $fallback): string {
		$value = trim($value);
		if ($value === '') {
			return $fallback;
		}

		return substr($value, 0, 255);
	}

	private function getAlternateSpeedPattern(string $pattern): string {
		if (stripos($pattern, 'ifhighspeed') !== false) {
			return preg_replace('/ifhighspeed/i', 'ifSpeed', $pattern) ?? $pattern;
		}

		if (stripos($pattern, 'ifspeed') !== false) {
			return preg_replace('/ifspeed/i', 'ifHighSpeed', $pattern) ?? $pattern;
		}

		return $pattern;
	}

	private function resolvePortItemKey(string $pattern, int $port_index): string {
		if (strpos($pattern, '*') !== false) {
			return str_replace('*', (string) $port_index, $pattern);
		}

		return $pattern;
	}

	private function loadTrafficSeries(string $hostid, array $ports): array {
		if ($hostid === '') {
			return [];
		}

		$keys = [];
		foreach ($ports as $port) {
			if (!empty($port['traffic_in_item_key'])) {
				$keys[] = (string) $port['traffic_in_item_key'];
			}
			if (!empty($port['traffic_out_item_key'])) {
				$keys[] = (string) $port['traffic_out_item_key'];
			}
		}
		$keys = array_values(array_unique(array_filter($keys, static fn(string $k): bool => $k !== '')));
		if ($keys === []) {
			return [];
		}

		$rows = API::Item()->get([
			'output' => ['itemid', 'key_', 'value_type'],
			'hostids' => [$hostid],
			'filter' => ['key_' => $keys]
		]);

		$result = [];
		foreach ($rows as $row) {
			$key = (string) $row['key_'];
			$value_type = (int) $row['value_type'];
			if (!in_array($value_type, [0, 3], true)) {
				continue;
			}

			$history = API::History()->get([
				'output' => ['clock', 'value'],
				'itemids' => [(string) $row['itemid']],
				'history' => $value_type,
				'time_from' => time() - self::TRAFFIC_LOOKBACK_SECONDS,
				'sortfield' => 'clock',
				'sortorder' => 'DESC',
				'limit' => self::TRAFFIC_POINTS
			]);

			if (!is_array($history) || $history === []) {
				$result[$key] = [];
				continue;
			}

			$history = array_reverse($history);
			$series = [];
			foreach ($history as $point) {
				$series[] = (float) $point['value'];
			}
			$result[$key] = $series;
		}

		return $result;
	}

	private function loadLatestItemValues(string $hostid, array $keys): array {
		if ($hostid === '' || $keys === []) {
			return [];
		}

		$rows = API::Item()->get([
			'output' => ['key_', 'lastvalue'],
			'hostids' => [$hostid],
			'filter' => ['key_' => $keys]
		]);

		$result = [];
		foreach ($rows as $row) {
			$key = (string) ($row['key_'] ?? '');
			if ($key === '') {
				continue;
			}

			$result[$key] = $this->toFloat($row['lastvalue'] ?? 0);
		}

		return $result;
	}

	private function loadCounterDeltas24h(string $hostid, array $keys): array {
		if ($hostid === '' || $keys === []) {
			return [];
		}

		$rows = API::Item()->get([
			'output' => ['itemid', 'key_', 'value_type'],
			'hostids' => [$hostid],
			'filter' => ['key_' => $keys]
		]);

		$result = [];
		$time_from = time() - self::COUNTER_LOOKBACK_SECONDS;

		foreach ($rows as $row) {
			$key = (string) ($row['key_'] ?? '');
			$value_type = (int) ($row['value_type'] ?? -1);
			if ($key === '' || !in_array($value_type, [0, 3], true)) {
				continue;
			}

			$history = API::History()->get([
				'output' => ['clock', 'value'],
				'itemids' => [(string) $row['itemid']],
				'history' => $value_type,
				'time_from' => $time_from,
				'sortfield' => 'clock',
				'sortorder' => 'ASC',
				'limit' => self::COUNTER_POINTS
			]);

			if (!is_array($history) || $history === []) {
				$result[$key] = [
					'delta' => 0.0,
					'trend' => 'n/a',
					'buckets' => array_fill(0, self::STATE_BAR_BUCKETS, 0.0)
				];
				continue;
			}

			$buckets = array_fill(0, self::STATE_BAR_BUCKETS, 0.0);
			$bucket_span = self::COUNTER_LOOKBACK_SECONDS / self::STATE_BAR_BUCKETS;
			for ($i = 1, $n = count($history); $i < $n; $i++) {
				$prev_value = (float) ($history[$i - 1]['value'] ?? 0);
				$curr_value = (float) ($history[$i]['value'] ?? 0);
				$curr_clock = (int) ($history[$i]['clock'] ?? 0);
				$diff = $curr_value - $prev_value;
				if ($diff < 0) {
					$diff = max(0.0, $curr_value);
				}
				if ($diff <= 0.0) {
					continue;
				}

				$idx = (int) floor(($curr_clock - $time_from) / $bucket_span);
				$idx = max(0, min(self::STATE_BAR_BUCKETS - 1, $idx));
				$buckets[$idx] += $diff;
			}
			$delta = array_sum($buckets);
			$tail = array_slice($buckets, -6);
			$prev = array_slice($buckets, -12, 6);
			$tail_sum = array_sum($tail);
			$prev_sum = array_sum($prev);
			$trend = 'stable';
			if ($tail_sum > 0.0 && ($prev_sum <= 0.0 || $tail_sum > ($prev_sum * 1.2))) {
				$trend = 'rising';
			}
			elseif ($delta <= 0.0) {
				$trend = 'stable';
			}

			$result[$key] = [
				'delta' => $delta,
				'trend' => $trend,
				'buckets' => $buckets
			];
		}

		return $result;
	}

	private function loadTriggerStateBars(array $ports, array $trigger_meta): array {
		$triggerids = [];
		foreach ($ports as $port) {
			if (!empty($port['triggerid']) && isset($trigger_meta[$port['triggerid']])) {
				$triggerids[] = (string) $port['triggerid'];
			}
		}
		$triggerids = array_values(array_unique($triggerids));
		if ($triggerids === []) {
			return [];
		}

		$time_to = time();
		$time_from = $time_to - self::STATE_BAR_WINDOW_SECONDS;
		$bucket_count = self::STATE_BAR_BUCKETS;
		$bucket_span = self::STATE_BAR_WINDOW_SECONDS / $bucket_count;

		$events = API::Event()->get([
			'output' => ['eventid', 'objectid', 'clock', 'value'],
			'source' => 0,
			'object' => 0,
			'objectids' => $triggerids,
			'time_from' => $time_from,
			'time_till' => $time_to,
			'sortfield' => ['clock', 'eventid'],
			'sortorder' => ZBX_SORT_DOWN
		]);

		$by_trigger = [];
		foreach ($events as $event) {
			$triggerid = (string) ($event['objectid'] ?? '');
			if ($triggerid === '') {
				continue;
			}
			$by_trigger[$triggerid][] = [
				'clock' => (int) ($event['clock'] ?? 0),
				'value' => (int) ($event['value'] ?? 0)
			];
		}

		$bars = [];
		foreach ($triggerids as $triggerid) {
			$current_state = !empty($trigger_meta[$triggerid]['is_problem']) ? 1 : 0;
			$buckets = array_fill(0, $bucket_count, $current_state);
			$cursor = $time_to;
			$trigger_events = $by_trigger[$triggerid] ?? [];

			foreach ($trigger_events as $event) {
				$event_time = max($time_from, min($time_to, (int) $event['clock']));
				if ($event_time >= $cursor) {
					continue;
				}

				$this->fillStateBuckets($buckets, $time_from, $bucket_span, $event_time, $cursor, $current_state);

				// Walk backward in time: invert event transition to estimate prior state.
				$current_state = ((int) $event['value'] === 1) ? 0 : 1;
				$cursor = $event_time;
			}

			if ($cursor > $time_from) {
				$this->fillStateBuckets($buckets, $time_from, $bucket_span, $time_from, $cursor, $current_state);
			}

			$bars[$triggerid] = $buckets;
		}

		return $bars;
	}

	private function fillStateBuckets(array &$buckets, int $time_from, float $bucket_span, int $start_ts, int $end_ts, int $state): void {
		if ($end_ts <= $start_ts || $bucket_span <= 0) {
			return;
		}

		$start_idx = (int) floor(($start_ts - $time_from) / $bucket_span);
		$end_idx = (int) ceil(($end_ts - $time_from) / $bucket_span) - 1;
		$last_idx = count($buckets) - 1;
		$start_idx = max(0, min($last_idx, $start_idx));
		$end_idx = max(0, min($last_idx, $end_idx));

		for ($i = $start_idx; $i <= $end_idx; $i++) {
			$buckets[$i] = $state;
		}
	}

	private function toFloat($value): float {
		if (is_int($value) || is_float($value)) {
			return (float) $value;
		}

		if (!is_string($value)) {
			return 0.0;
		}

		$text = trim($value);
		if ($text === '') {
			return 0.0;
		}

		return is_numeric($text) ? (float) $text : 0.0;
	}

	private function toSpeedBps(float $speed_value, string $speed_key): float {
		if ($speed_value <= 0.0) {
			return 0.0;
		}

		// Common pattern: ifHighSpeed is in Mbit/s, ifSpeed is in bit/s.
		if (stripos($speed_key, 'ifhighspeed') !== false) {
			return $speed_value * 1000000.0;
		}

		return $speed_value;
	}

	private function extractHostId(): string {
		$value = $this->fields_values['hostids'] ?? [];
		$candidates = $this->collectPositiveNumericScalars($value);
		return $candidates !== [] ? (string) $candidates[0] : '';
	}

	private function collectPositiveNumericScalars($value): array {
		$result = [];
		$stack = [$value];

		while ($stack !== []) {
			$current = array_pop($stack);

			if (is_array($current)) {
				foreach ($current as $k => $v) {
					if (is_scalar($k)) {
						$key = trim((string) $k);
						if (ctype_digit($key) && (int) $key > 0) {
							$result[] = $key;
						}
					}
					$stack[] = $v;
				}
				continue;
			}

			if (is_scalar($current)) {
				$text = trim((string) $current);
				if (ctype_digit($text) && (int) $text > 0) {
					$result[] = $text;
				}
			}
		}

		return array_values(array_unique($result));
	}
}
