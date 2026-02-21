<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Actions;

use API;
use CController;
use CControllerResponseData;

class WidgetItems extends CController {
	private const MAX_ROWS_SCAN = 10000;
	private const MAX_SUGGESTIONS = 50;

	protected function init(): void {
		$this->disableCsrfValidation();
	}

	protected function checkInput(): bool {
		return $this->validateInput([
			'hostid' => 'required|int32',
			'q' => 'string'
		]);
	}

	protected function checkPermissions(): bool {
		return $this->getUserType() >= USER_TYPE_ZABBIX_USER;
	}

	protected function doAction(): void {
		$hostid = (int) $this->getInput('hostid');
		$query_raw = trim((string) $this->getInput('q', ''));
		$query = substr($query_raw, 0, 120);

		if ($hostid <= 0) {
			$this->respond([]);
			return;
		}

		$search_term = $this->normalizeSearchQuery($query);

		$params = [
			'output' => ['key_'],
			'hostids' => [$hostid],
			'sortfield' => 'key_',
			'sortorder' => defined('ZBX_SORT_UP') ? ZBX_SORT_UP : 'ASC',
			'limit' => self::MAX_ROWS_SCAN
		];

		$rows = API::Item()->get($params);
		if (!is_array($rows)) {
			$rows = [];
		}

		if (!is_array($rows) || $rows === []) {
			$this->respond([]);
			return;
		}

		$exact_keys = [];
		$pattern_counts = [];
		foreach ($rows as $row) {
			$key = trim((string) ($row['key_'] ?? ''));
			if ($key === '') {
				continue;
			}

			$exact_keys[$key] = true;
			$pattern = $this->toWildcardPattern($key);
			if (!array_key_exists($pattern, $pattern_counts)) {
				$pattern_counts[$pattern] = 0;
			}
			$pattern_counts[$pattern]++;
		}

		$items = [];
		$query_l = mb_strtolower($query);
		$search_l = mb_strtolower($search_term);
		foreach ($pattern_counts as $pattern => $count) {
			if (!$this->matchesPrefix($query_l, $search_l, $pattern)) {
				continue;
			}

			$items[] = [
				'value' => $pattern,
				'label' => $count > 1 ? sprintf('%s (%d)', $pattern, $count) : $pattern,
				'type' => 'pattern'
			];
		}

		foreach (array_keys($exact_keys) as $key) {
			if (!$this->matchesPrefix($query_l, $search_l, $key)) {
				continue;
			}

			$items[] = [
				'value' => $key,
				'label' => $key,
				'type' => 'item'
			];
		}

		usort($items, static function(array $a, array $b): int {
			// Keep wildcard entries visible (e.g. ifOutOctets[*]) even with large item lists.
			$a_pattern = ((string) ($a['type'] ?? '') === 'pattern') ? 1 : 0;
			$b_pattern = ((string) ($b['type'] ?? '') === 'pattern') ? 1 : 0;
			if ($a_pattern !== $b_pattern) {
				return $b_pattern <=> $a_pattern;
			}

			return strcmp((string) ($a['value'] ?? ''), (string) ($b['value'] ?? ''));
		});

		$items = array_slice($items, 0, self::MAX_SUGGESTIONS);
		$this->respond($items);
	}

	private function normalizeSearchQuery(string $query): string {
		$needle = trim($query);
		if ($needle === '') {
			return '';
		}

		$needle = str_replace('[*]', '', $needle);
		$needle = str_replace('*', '', $needle);
		$needle = str_replace(['[', ']'], '', $needle);
		return trim($needle);
	}

	private function matchesPrefix(string $query_l, string $search_l, string $candidate): bool {
		if ($query_l === '' && $search_l === '') {
			return true;
		}

		$candidate_l = mb_strtolower($this->normalizeSearchQuery($candidate));
		if ($query_l !== '' && str_starts_with($candidate_l, $query_l)) {
			return true;
		}
		if ($search_l !== '' && str_starts_with($candidate_l, $search_l)) {
			return true;
		}

		return false;
	}

	private function toWildcardPattern(string $key): string {
		return (string) preg_replace('/\[(\d+)\]/', '[*]', $key);
	}

	private function respond(array $suggestions): void {
		$this->setResponse(new CControllerResponseData([
			'main_block' => json_encode([
				'suggestions' => $suggestions
			])
		]));
	}
}
