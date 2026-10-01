<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Includes;

/**
 * Shared port helpers: combo parsing, link-down trigger matching, zig-zag layout.
 */
class PortSupport {

	/**
	 * Parse "25,26" or "25-28,30" into a unique list of positive port numbers.
	 *
	 * @return list<int>
	 */
	public static function parseComboPorts(string $raw): array {
		$raw = trim($raw);
		if ($raw === '') {
			return [];
		}

		$ports = [];
		foreach (explode(',', $raw) as $part) {
			$part = trim($part);
			if ($part === '') {
				continue;
			}

			if (strpos($part, '-') !== false) {
				[$start_raw, $end_raw] = array_pad(explode('-', $part, 2), 2, '');
				$start = (int) trim($start_raw);
				$end = (int) trim($end_raw);
				if ($start < 1 || $end < 1) {
					continue;
				}
				if ($end < $start) {
					[$start, $end] = [$end, $start];
				}
				for ($p = $start; $p <= $end; $p++) {
					$ports[$p] = $p;
				}
				continue;
			}

			$port = (int) $part;
			if ($port > 0) {
				$ports[$port] = $port;
			}
		}

		return array_values($ports);
	}

	/**
	 * Extract a port number from a trigger description, if present.
	 * Matches: "Port 5: Link down", "Interface 12 down", "Интерфейс 3", "Порт 7".
	 */
	public static function extractPortNumber(string $description): ?int {
		if (preg_match('/(?:Port|Interface|Порт|Интерфейс)\s*[:\-]?\s*(\d+)/iu', $description, $matches) !== 1) {
			return null;
		}

		$port = (int) $matches[1];

		return $port > 0 ? $port : null;
	}

	public static function isLinkDownDescription(string $description): bool {
		return preg_match('/link\s*down|линк\s*даун|\bdown\b/iu', $description) === 1;
	}

	/**
	 * Map port number => triggerid for link-down style triggers.
	 * Prefers descriptions that look like "link down"; otherwise keeps first match.
	 *
	 * @param list<array{id?:string,triggerid?:string,name?:string,description?:string}> $triggers
	 * @return array<int, string> port number => triggerid
	 */
	public static function buildPortTriggerSuggestions(array $triggers): array {
		$map = [];
		$is_link_down = [];

		foreach ($triggers as $trigger) {
			$id = (string) ($trigger['id'] ?? $trigger['triggerid'] ?? '');
			$name = (string) ($trigger['name'] ?? $trigger['description'] ?? '');
			if ($id === '' || $id === '0' || $name === '') {
				continue;
			}

			$port = self::extractPortNumber($name);
			if ($port === null) {
				continue;
			}

			$link_down = self::isLinkDownDescription($name);
			if (!isset($map[$port]) || ($link_down && empty($is_link_down[$port]))) {
				$map[$port] = $id;
				$is_link_down[$port] = $link_down;
			}
		}

		ksort($map, SORT_NUMERIC);

		return $map;
	}

	/**
	 * Reorder UTP ports into zig-zag faceplate order (even on top, odd on bottom),
	 * processed in blocks of two rows so multi-row layouts stay coherent.
	 *
	 * @param list<array> $ports
	 * @return list<array>
	 */
	public static function applyZigZagOrder(array $ports, int $columns): array {
		$columns = max(1, $columns);
		$count = count($ports);
		if ($count < 2) {
			return $ports;
		}

		$block_size = $columns * 2;
		$result = [];

		for ($offset = 0; $offset < $count; $offset += $block_size) {
			$block = array_slice($ports, $offset, $block_size);
			if (count($block) <= $columns) {
				// Last incomplete pair of rows: keep sequential order.
				foreach ($block as $port) {
					$result[] = $port;
				}
				continue;
			}

			$top = [];
			$bottom = [];
			foreach ($block as $index => $port) {
				// Even port numbers (1-based) → top row; odd → bottom row.
				if (($index % 2) === 1) {
					$top[] = $port;
				}
				else {
					$bottom[] = $port;
				}
			}

			foreach ($top as $port) {
				$result[] = $port;
			}
			foreach ($bottom as $port) {
				$result[] = $port;
			}
		}

		return $result;
	}
}
