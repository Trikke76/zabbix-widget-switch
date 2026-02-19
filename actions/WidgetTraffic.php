<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Actions;

use API;
use CController;
use CControllerResponseData;

class WidgetTraffic extends CController {
	private const POINT_LIMIT = 36;
	private const LOOKBACK_SECONDS = 3600;

	protected function init(): void {
		$this->disableCsrfValidation();
	}

	protected function checkInput(): bool {
		return $this->validateInput([
			'hostid' => 'required|int32',
			'in_key' => 'string',
			'out_key' => 'string'
		]);
	}

	protected function checkPermissions(): bool {
		return $this->getUserType() >= USER_TYPE_ZABBIX_USER;
	}

	protected function doAction(): void {
		$hostid = (int) $this->getInput('hostid');
		$in_key = trim((string) $this->getInput('in_key', ''));
		$out_key = trim((string) $this->getInput('out_key', ''));

		if ($hostid <= 0) {
			$this->respond(false, 'Invalid host.');
			return;
		}

		$keys = [];
		if ($in_key !== '') {
			$keys[] = $in_key;
		}
		if ($out_key !== '' && $out_key !== $in_key) {
			$keys[] = $out_key;
		}

		if ($keys === []) {
			$this->respond(true, '', [
				'in' => [],
				'out' => []
			]);
			return;
		}

		$items = API::Item()->get([
			'output' => ['itemid', 'key_', 'value_type'],
			'hostids' => [$hostid],
			'filter' => ['key_' => $keys]
		]);

		$item_by_key = [];
		foreach ($items as $item) {
			$item_by_key[(string) $item['key_']] = [
				'itemid' => (string) $item['itemid'],
				'value_type' => (int) $item['value_type']
			];
		}

		$this->respond(true, '', [
			'in' => $this->loadSeries($item_by_key[$in_key] ?? null),
			'out' => $this->loadSeries($item_by_key[$out_key] ?? null)
		]);
	}

	private function loadSeries(?array $item_meta): array {
		if ($item_meta === null) {
			return [];
		}

		$value_type = (int) $item_meta['value_type'];
		// Keep only numeric history types supported by history.get.
		if (!in_array($value_type, [0, 3], true)) {
			return [];
		}

		$rows = API::History()->get([
			'output' => ['clock', 'value'],
			'itemids' => [$item_meta['itemid']],
			'history' => $value_type,
			'time_from' => time() - self::LOOKBACK_SECONDS,
			'sortfield' => 'clock',
			'sortorder' => 'DESC',
			'limit' => self::POINT_LIMIT
		]);

		if (!is_array($rows) || $rows === []) {
			return [];
		}

		$rows = array_reverse($rows);
		$series = [];
		foreach ($rows as $row) {
			$series[] = (float) $row['value'];
		}

		return $series;
	}

	private function respond(bool $ok, string $error = '', array $payload = []): void {
		$this->setResponse(new CControllerResponseData([
			'main_block' => json_encode([
				'ok' => $ok,
				'error' => $error,
				'data' => $payload
			])
		]));
	}
}
