<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {
	private const DEFAULT_ROW_COUNT = 2;
	private const DEFAULT_PORTS_PER_ROW = 12;
	private const MAX_ROW_COUNT = 24;
	private const MAX_PORTS_PER_ROW = 48;
	private const MAX_SFP_PORTS = 32;
	private const MAX_TOTAL_PORTS = 256;

	protected function doAction(): void {
		$layout = $this->getLayout();
		$ports = $this->loadPortsFromFields($layout['total_ports']);
		$trigger_meta = $this->loadTriggerMeta($ports);
		$sfp_start_index = max(1, $layout['total_ports'] - $layout['sfp_ports'] + 1);

		foreach ($ports as $index => &$port) {
			$triggerid = $port['triggerid'];
			$meta = $triggerid !== '' ? ($trigger_meta[$triggerid] ?? null) : null;
			$is_problem = $meta !== null ? $meta['is_problem'] : false;
			$port['active_color'] = $is_problem ? $port['trigger_color'] : $port['default_color'];
			$port['is_problem'] = $is_problem;
			$port['url'] = $triggerid !== ''
				? 'zabbix.php?action=problem.view&filter_set=1&triggerids%5B0%5D='.$triggerid
				: '';
			$port['trigger_name'] = $meta !== null ? $meta['description'] : '';
			$port['is_sfp'] = ($layout['sfp_ports'] > 0 && ($index + 1) >= $sfp_start_index);
		}
		unset($port);

		$this->setResponse(new CControllerResponseData([
			'name' => $this->widget->getDefaultName(),
			'legend_text' => trim((string) ($this->fields_values['legend_text'] ?? '')),
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

	private function loadPortsFromFields(int $total_ports): array {
		$ports = [];

		for ($i = 1; $i <= $total_ports; $i++) {
			$triggerid_raw = (int) ($this->fields_values['port'.$i.'_triggerid'] ?? 0);

			$ports[] = [
				'name' => trim((string) ($this->fields_values['port'.$i.'_name'] ?? sprintf('Port %d', $i))),
				'triggerid' => $triggerid_raw > 0 ? (string) $triggerid_raw : '',
				'default_color' => $this->safeColor((string) ($this->fields_values['port'.$i.'_default_color'] ?? '#2f855a'), '#2f855a'),
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
}
