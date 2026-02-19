<?php declare(strict_types = 1);

$css = implode('', [
	'.port24-legend{display:inline-block;font-size:calc(var(--port24-legend-size,14px) * var(--port24-scale));line-height:1.35;color:#4b5563;',
	'background:linear-gradient(180deg,#f8fafc 0%,#eef2f7 100%);border:1px solid #d7dee8;border-radius:6px;',
	'padding:calc(6px * var(--port24-scale)) calc(10px * var(--port24-scale));margin-bottom:calc(10px * var(--port24-scale));}',
	'.port24-switch{position:relative;background:linear-gradient(180deg,#5a6571 0%,#3d4651 22%,#2b323b 100%);',
	'border:1px solid #212831;border-radius:10px;padding:calc(14px * var(--port24-scale));box-shadow:inset 0 1px 0 rgba(255,255,255,.2),0 4px 16px rgba(0,0,0,.25);}',
	'.port24-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:calc(8px * var(--port24-scale));color:#cfd8e2;font-size:calc(10px * var(--port24-scale));letter-spacing:.08em;text-transform:uppercase;}',
	'.port24-brand{font-weight:700;}',
	'.port24-model{opacity:.9;}',
	'.port24-face{display:flex;align-items:flex-start;gap:calc(16px * var(--port24-scale));}',
	'.port24-main{flex:1 1 auto;min-width:0;}',
	'.port24-uplink{flex:0 0 auto;width:max-content;max-width:100%;}',
	'.port24-grid{display:grid;grid-template-columns:repeat(var(--port24-columns),minmax(0,1fr));gap:calc(7px * var(--port24-scale));}',
	'.port24-sfp-grid{display:grid;grid-template-columns:repeat(var(--port24-sfp-columns),minmax(calc(56px * var(--port24-scale)),calc(72px * var(--port24-scale))));gap:calc(7px * var(--port24-scale));justify-content:start;}',
	'@media (max-width:1100px){.port24-grid{grid-template-columns:repeat(auto-fit,minmax(calc(78px * var(--port24-scale)),1fr));}}',
	'@media (max-width:1100px){.port24-face{flex-direction:column;}}',
	'@media (max-width:1100px){.port24-uplink{width:100%;}}',
	'@media (max-width:1100px){.port24-sfp-grid{grid-template-columns:repeat(auto-fit,minmax(calc(78px * var(--port24-scale)),1fr));}}',
	'@media (max-width:700px){.port24-grid{grid-template-columns:repeat(auto-fit,minmax(calc(68px * var(--port24-scale)),1fr));}}',
	'.port24-card{position:relative;display:block;text-decoration:none;color:#d8e1ea;background:#11161b;border:1px solid #2b3642;',
	'border-radius:4px;padding:calc(4px * var(--port24-scale)) calc(4px * var(--port24-scale)) calc(12px * var(--port24-scale)) calc(4px * var(--port24-scale));min-height:calc(44px * var(--port24-scale));box-shadow:inset 0 -1px 0 rgba(255,255,255,.04);}',
	'.port24-card:hover{border-color:#7b8794;}',
	'.port24-card.port24-heatmap{box-shadow:inset 0 -3px 0 var(--util-c,#64748B), inset 0 -1px 0 rgba(255,255,255,.04);}',
	'.port24-jack{height:calc(22px * var(--port24-scale));position:relative;border:1px solid #1f2730;border-radius:2px 2px 4px 4px;',
	'background:linear-gradient(180deg,#eef3f8 0 20%,#0d1318 20% 100%);overflow:hidden;}',
	'.port24-jack:before{content:"";position:absolute;left:calc(6px * var(--port24-scale));right:calc(6px * var(--port24-scale));top:0;height:calc(7px * var(--port24-scale));background:#06090d;',
	'clip-path:polygon(12% 100%,88% 100%,100% 0,0 0);} ',
	'.port24-jack:after{content:"";position:absolute;left:calc(5px * var(--port24-scale));right:calc(5px * var(--port24-scale));bottom:calc(2px * var(--port24-scale));height:calc(5px * var(--port24-scale));',
	'background:repeating-linear-gradient(90deg,#c4ccd5 0 2px,transparent 2px 4px);opacity:.85;}',
		'.port24-jack-sfp{height:calc(22px * var(--port24-scale));position:relative;border:1px solid #98a6b6;border-radius:2px;',
		'background:linear-gradient(180deg,#dce4ec 0%,#b8c5d3 24%,#6f7f90 25%,#586678 100%);overflow:hidden;',
		'box-shadow:inset 0 1px 0 rgba(255,255,255,.55),inset 0 -1px 0 rgba(0,0,0,.35),0 0 0 1px rgba(20,30,42,.28);}',
		'.port24-jack-sfp:before{content:"";position:absolute;left:calc(3px * var(--port24-scale));right:calc(3px * var(--port24-scale));top:calc(3px * var(--port24-scale));',
		'height:calc(12px * var(--port24-scale));border:1px solid #1f2a36;border-radius:1px;background:linear-gradient(180deg,#111820 0%,#060b11 100%);',
		'box-shadow:inset 0 1px 0 rgba(255,255,255,.06),inset 0 -2px 0 rgba(0,0,0,.55);}',
		'.port24-jack-sfp:after{content:"";position:absolute;left:50%;transform:translateX(-50%);bottom:calc(2px * var(--port24-scale));',
		'width:calc(12px * var(--port24-scale));height:calc(3px * var(--port24-scale));background:#252f3b;border-top:1px solid #9fb0c2;',
		'clip-path:polygon(0 0,100% 0,88% 100%,12% 100%);box-shadow:0 -1px 0 rgba(0,0,0,.35);}',
	'.port24-led{position:absolute;right:calc(4px * var(--port24-scale));top:calc(4px * var(--port24-scale));width:calc(8px * var(--port24-scale));height:calc(8px * var(--port24-scale));border-radius:50%;background:var(--port-color,#2F855A);',
	'box-shadow:0 0 0 1px rgba(255,255,255,.2),0 0 calc(12px * var(--port24-scale)) var(--port-color,#2F855A),0 0 calc(20px * var(--port24-scale)) var(--port-color,#2F855A);}',
	'.port24-label{position:absolute;left:calc(4px * var(--port24-scale));right:calc(4px * var(--port24-scale));bottom:calc(2px * var(--port24-scale));text-align:center;font-size:calc(9px * var(--port24-scale));white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#c9d3de;}',
	'.port24-util-track{position:absolute;left:calc(4px * var(--port24-scale));right:calc(4px * var(--port24-scale));bottom:0;height:calc(2px * var(--port24-scale));border-radius:2px;background:rgba(148,163,184,.22);overflow:hidden;}',
	'.port24-util-fill{display:block;height:100%;width:100%;background:var(--util-c,#22C55E);}',
	'.port24-tooltip{position:fixed;z-index:100000;pointer-events:none;min-width:190px;background:#0f1722;color:#d9e3ee;border:1px solid #2e3c4d;border-radius:8px;padding:8px;box-shadow:0 10px 28px rgba(0,0,0,.45);font-size:11px;line-height:1.3;display:none;}',
	'.port24-tooltip-title{font-weight:700;margin-bottom:6px;color:#f8fbff;}',
	'.port24-tip-meta{margin:2px 0;opacity:.95;}',
	'.port24-tip-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:4px 0;}',
	'.port24-tip-label{opacity:.9;min-width:24px;}',
	'.port24-tip-value{opacity:.95;font-variant-numeric:tabular-nums;}',
		'.port24-tip-svg{display:block;width:120px;height:26px;background:#0a1119;border:1px solid #223041;border-radius:4px;overflow:hidden;}',
		'.port24-tip-area-in{fill:rgba(56,189,248,0.35);}',
		'.port24-tip-area-out{fill:rgba(245,158,11,0.30);}',
		'.port24-tip-path-in{fill:none;stroke:#4fb9ff;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}',
		'.port24-tip-path-out{fill:none;stroke:#ffb020;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}',
		'.port24-tip-dot{fill:#334155;stroke:#dbeafe;stroke-width:1.1;}',
	'.port24-card-wrap:hover .port24-hover-tip{display:block;}',
	'.port24-hover-tip{position:absolute;z-index:40;left:calc(14px * var(--port24-scale));top:calc(42px * var(--port24-scale));min-width:200px;max-width:300px;background:#0f1722;color:#d9e3ee;border:1px solid #2e3c4d;border-radius:8px;padding:8px;box-shadow:0 10px 28px rgba(0,0,0,.45);font-size:11px;line-height:1.3;display:none;pointer-events:none;}',
	'.port24-util-grid-wrap{margin-top:10px;}',
	'.port24-util-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(54px,1fr));gap:4px;}',
	'.port24-util-cell{border:1px solid rgba(15,23,34,.28);border-radius:3px;padding:4px 3px;text-align:center;font-size:10px;line-height:1.2;color:#0f1722;}',
	'.port24-util-cell-port{display:block;font-weight:700;opacity:.8;margin-bottom:2px;}',
	'.port24-util-cell-val{display:block;font-weight:700;}',
	'.port24-denied{display:inline-block;padding:10px 12px;border:1px solid #e7b7b7;border-radius:6px;background:#fff5f5;color:#9b2c2c;font-size:13px;}'
]);

$container = new CDiv();
$scale = max(0.4, min(1.0, ((int) ($data['switch_size'] ?? 100)) / 100));
$legend_size = max(12, min(18, (int) ($data['legend_size'] ?? 14)));
if ($data['legend_text'] !== '') {
	$container->addItem(
		(new CDiv($data['legend_text']))
			->addClass('port24-legend')
			->setAttribute('style', '--port24-scale: '.$scale.'; --port24-legend-size: '.$legend_size.'px;')
	);
}

if (!empty($data['access_denied'])) {
	$container->addItem((new CDiv(_('Access denied: no permissions for selected host.')))->addClass('port24-denied'));

	(new CWidgetView($data))
		->addItem(new CTag('style', true, $css))
		->addItem($container)
		->show();

	return;
}

$columns = max(1, (int) ($data['ports_per_row'] ?? 12));
$row_count = max(1, (int) ($data['row_count'] ?? 1));
$port_color_mode = (int) ($data['port_color_mode'] ?? 0);
$utilization_overlay_enabled = (int) ($data['utilization_overlay_enabled'] ?? 1);
$show_utilization_overlay = ($utilization_overlay_enabled === 1);
$util_low_color = (string) ($data['utilization_low_color'] ?? '#22C55E');
$util_warn_color = (string) ($data['utilization_warn_color'] ?? '#FCD34D');
$util_high_color = (string) ($data['utilization_high_color'] ?? '#DB2777');
$util_na_color = (string) ($data['utilization_na_color'] ?? '#94A3B8');
$util_low_threshold = (float) ($data['utilization_low_threshold'] ?? 5);
$util_warn_threshold = (float) ($data['utilization_warn_threshold'] ?? 40);
$util_high_threshold = (float) ($data['utilization_high_threshold'] ?? 70);
$switch = (new CDiv())->addClass('port24-switch')->setAttribute('style', '--port24-scale: '.$scale.';');
$head = (new CDiv())->addClass('port24-head');
$head
	->addItem((new CDiv($data['switch_brand'] !== '' ? $data['switch_brand'] : 'NETSWITCH'))->addClass('port24-brand'))
	->addItem((new CDiv($data['switch_model'] !== '' ? $data['switch_model'] : 'SW-24G'))->addClass('port24-model'));
$switch->addItem($head);
$utp_ports = [];
$sfp_ports = [];
foreach ($data['ports'] as $port) {
	if (!empty($port['is_sfp'])) {
		$sfp_ports[] = $port;
	}
	else {
		$utp_ports[] = $port;
	}
}

$util_color_for = static function(?float $util) use ($util_low_threshold, $util_warn_threshold, $util_high_threshold, $util_low_color, $util_warn_color, $util_high_color, $util_na_color): string {
	if ($util === null) {
		return $util_na_color;
	}
	if ($util >= $util_high_threshold) {
		return $util_high_color;
	}
	if ($util >= $util_warn_threshold) {
		return $util_warn_color;
	}
	if ($util >= $util_low_threshold) {
		return $util_low_color;
	}
	return '#22C55E';
};

$make_card = static function(array $port) use ($show_utilization_overlay, $util_color_for): CTag {
	$active_color = (string) ($port['active_color'] ?? '');
	if (preg_match('/^#[0-9A-Fa-f]{6}$/', $active_color) !== 1) {
		$active_color = '#22C55E';
	}
	$utilization = isset($port['utilization_percent']) && $port['utilization_percent'] !== null
		? (float) $port['utilization_percent']
		: null;
	$util_color = isset($port['utilization_color']) && is_string($port['utilization_color'])
		? (string) $port['utilization_color']
		: $util_color_for($utilization);

	if (empty($port['has_trigger'])) {
		$state = _('No trigger');
	}
	elseif ($port['is_problem']) {
		$state = _('Problem');
	}
	else {
		$state = _('OK');
	}
	$port_type = !empty($port['is_sfp']) ? 'SFP' : 'RJ45';
	$tooltip = $port['name']."\n".sprintf(_('State: %s'), $state);
	$tooltip .= "\n".sprintf(_('Type: %s'), $port_type);
	if (isset($port['utilization_percent']) && $port['utilization_percent'] !== null) {
		$tooltip .= "\n".sprintf(_('Utilization: %s%%'), number_format((float) $port['utilization_percent'], 1));
	}
	if ($port['triggerid'] !== '') {
		$trigger_name = $port['trigger_name'] !== '' ? $port['trigger_name'] : '#'.$port['triggerid'];
		$tooltip .= "\n".sprintf(_('Trigger: %s'), $trigger_name);
	}
	else {
		$tooltip .= "\n"._('Trigger: not configured');
		$trigger_name = _('not configured');
	}

		$spark_geom = static function(array $values, int $width = 120, int $height = 26, int $padding = 3): array {
			$count = count($values);
			if ($count < 1) {
				return ['line' => '', 'area' => '', 'last_x' => 0.0, 'last_y' => 0.0];
			}
			if ($count === 1) {
				$y = round($height / 2, 2);
				$line = 'M'.$padding.','.$y.' L'.($width - $padding).','.$y;
				$area = 'M'.$padding.','.($height - $padding).' L'.$padding.','.$y.' L'.($width - $padding).','.$y.' L'.($width - $padding).','.($height - $padding).' Z';
				return ['line' => $line, 'area' => $area, 'last_x' => (float) ($width - $padding), 'last_y' => (float) $y];
			}

			$min = min($values);
			$max = max($values);
			$is_flat = abs($max - $min) < 0.0000001;
			$span = $is_flat ? 1.0 : ($max - $min);
			$dw = $width - ($padding * 2);
			$dh = $height - ($padding * 2);

			$parts = [];
			$last_index = $count - 1;
			$last_x = (float) $padding;
			$last_y = (float) ($height - $padding);
			foreach ($values as $idx => $value) {
				$x = $padding + ($dw * $idx / $last_index);
				$y = $is_flat
					? ($padding + ($dh / 2))
					: ($padding + $dh - ((($value - $min) / $span) * $dh));
				$parts[] = ($idx === 0 ? 'M' : 'L').round($x, 2).','.round($y, 2);
				$last_x = (float) round($x, 2);
				$last_y = (float) round($y, 2);
			}

			$line = implode(' ', $parts);
			$first_x = (float) $padding;
			$base_y = (float) ($height - $padding);
			$area = $line.' L'.$last_x.','.$base_y.' L'.$first_x.','.$base_y.' Z';

			return ['line' => $line, 'area' => $area, 'last_x' => $last_x, 'last_y' => $last_y];
		};
	$fmt_last = static function(array $values): string {
		if ($values === []) {
			return 'n/a';
		}
		$last = (float) $values[count($values) - 1];
		if ($last >= 1000000) {
			return number_format($last / 1000000, 2).'M';
		}
		if ($last >= 1000) {
			return number_format($last / 1000, 1).'k';
		}
		return (string) round($last);
	};

	$in_series = is_array($port['traffic_in_series'] ?? null) ? $port['traffic_in_series'] : [];
	$out_series = is_array($port['traffic_out_series'] ?? null) ? $port['traffic_out_series'] : [];
		$in_geom = $spark_geom($in_series);
		$out_geom = $spark_geom($out_series);

		$content = new CDiv();
			$make_spark_svg = static function(array $geom, string $line_class, string $area_class): CTag {
				$svg = (new CTag('svg', true))
					->addClass('port24-tip-svg')
					->setAttribute('viewBox', '0 0 120 26');

			$baseline = (new CTag('line', true))
				->setAttribute('x1', '3')
				->setAttribute('y1', '13')
				->setAttribute('x2', '117')
				->setAttribute('y2', '13')
				->setAttribute('stroke', 'rgba(148,163,184,0.25)')
				->setAttribute('stroke-width', '1');
			$svg->addItem($baseline);

				if (($geom['area'] ?? '') !== '') {
					$svg->addItem(
						(new CTag('path', true))
							->addClass($area_class)
							->setAttribute('d', (string) $geom['area'])
					);
				}

				if (($geom['line'] ?? '') !== '') {
					$svg->addItem(
						(new CTag('path', true))
							->addClass($line_class)
							->setAttribute('d', (string) $geom['line'])
					);

					$svg->addItem(
						(new CTag('circle', true))
							->addClass('port24-tip-dot')
							->setAttribute('cx', (string) ($geom['last_x'] ?? 0))
							->setAttribute('cy', (string) ($geom['last_y'] ?? 0))
							->setAttribute('r', '2.8')
					);
				}

			return $svg;
		};

			$content
				->addItem((new CDiv())->addClass(!empty($port['is_sfp']) ? 'port24-jack-sfp' : 'port24-jack'))
				->addItem(
					(new CDiv())
						->addClass('port24-led')
						->setAttribute(
							'style',
							'background: '.$active_color.';'
							.'box-shadow:0 0 0 1px rgba(255,255,255,.2),'
							.'0 0 calc(12px * var(--port24-scale)) '.$active_color.','
							.'0 0 calc(20px * var(--port24-scale)) '.$active_color.';'
						)
				)
				->addItem((new CDiv($port['name']))->addClass('port24-label'));
	if ($show_utilization_overlay) {
		$content->addItem(
			(new CDiv(
				(new CSpan())->addClass('port24-util-fill')
			))
				->addClass('port24-util-track')
				->setAttribute(
					'style',
					'--util-c: '.$util_color.';'
				)
		);
	}

		$hover_tip = (new CDiv(
			(new CDiv($port['name']))->addClass('port24-tooltip-title')
		))
			->addClass('port24-hover-tip')
			->addItem((new CDiv(sprintf(_('State: %s'), $state)))->addClass('port24-tip-meta'))
			->addItem((new CDiv(sprintf(_('Type: %s'), $port_type)))->addClass('port24-tip-meta'))
			->addItem(
				(new CDiv(sprintf(
					_('Utilization: %s'),
					(isset($port['utilization_percent']) && $port['utilization_percent'] !== null)
						? number_format((float) $port['utilization_percent'], 1).'%'
						: 'n/a'
				)))->addClass('port24-tip-meta')
			)
			->addItem((new CDiv(sprintf(_('Trigger: %s'), $trigger_name)))->addClass('port24-tip-meta'))
			->addItem(
				(new CDiv())
					->addClass('port24-tip-row')
					->addItem((new CSpan('IN'))->addClass('port24-tip-label'))
					->addItem($make_spark_svg($in_geom, 'port24-tip-path-in', 'port24-tip-area-in'))
					->addItem((new CSpan($fmt_last($in_series)))->addClass('port24-tip-value'))
			)
			->addItem(
				(new CDiv())
					->addClass('port24-tip-row')
					->addItem((new CSpan('OUT'))->addClass('port24-tip-label'))
					->addItem($make_spark_svg($out_geom, 'port24-tip-path-out', 'port24-tip-area-out'))
					->addItem((new CSpan($fmt_last($out_series)))->addClass('port24-tip-value'))
			);

	if ($port['url'] !== '') {
		$card = new CLink($content, $port['url']);
	}
	else {
		$card = new CDiv($content);
	}

	$card
		->addClass('port24-card')
		->setAttribute('style', '--port-color: '.$active_color.';')
		->setAttribute('data-port-name', (string) $port['name']);
	if ($show_utilization_overlay) {
		$card
			->addClass('port24-heatmap')
			->setAttribute('style', '--port-color: '.$active_color.'; --util-c: '.$util_color.';');
	}

	if (!empty($port['hostid'])) {
		$card->setAttribute('data-hostid', (string) $port['hostid']);
	}
	if (!empty($port['traffic_in_item_key'])) {
		$card->setAttribute('data-traffic-in-key', (string) $port['traffic_in_item_key']);
	}
	if (!empty($port['traffic_out_item_key'])) {
		$card->setAttribute('data-traffic-out-key', (string) $port['traffic_out_item_key']);
	}

		return (new CDiv())
			->addClass('port24-card-wrap')
			->addItem($card)
			->addItem($hover_tip);
	};

$main_grid = (new CDiv())
	->addClass('port24-grid')
	->setAttribute('style', '--port24-columns: '.$columns.';');

foreach ($utp_ports as $port) {
	$main_grid->addItem($make_card($port));
}

$face = (new CDiv())->addClass('port24-face');
$face->addItem((new CDiv($main_grid))->addClass('port24-main'));

if ($sfp_ports !== []) {
	$sfp_count = count($sfp_ports);
	$sfp_columns = max(1, (int) ceil($sfp_count / $row_count));
	if ($sfp_count > 1) {
		$sfp_columns = max(2, $sfp_columns);
	}
	$sfp_columns = min($sfp_count, $sfp_columns);
	$uplink_grid = (new CDiv())
		->addClass('port24-sfp-grid')
		->setAttribute('style', '--port24-sfp-columns: '.$sfp_columns.';');

	foreach ($sfp_ports as $port) {
		$uplink_grid->addItem($make_card($port));
	}

	$face->addItem((new CDiv($uplink_grid))->addClass('port24-uplink'));
}

	$switch->addItem($face);
	$container->addItem($switch);

if ($show_utilization_overlay) {
	$util_grid = (new CDiv())->addClass('port24-util-grid');
	foreach ($data['ports'] as $idx => $port) {
		$util = isset($port['utilization_percent']) && $port['utilization_percent'] !== null
			? (float) $port['utilization_percent']
			: null;
		$util_text = $util !== null ? number_format($util, 1).'%' : 'n/a';
		$cell_color = isset($port['utilization_color']) && is_string($port['utilization_color'])
			? (string) $port['utilization_color']
			: $util_color_for($util);
		$port_label = 'P'.($idx + 1);

		$util_grid->addItem(
			(new CDiv())
				->addClass('port24-util-cell')
				->setAttribute('style', 'background: '.$cell_color.';')
				->addItem((new CSpan($port_label))->addClass('port24-util-cell-port'))
				->addItem((new CSpan($util_text))->addClass('port24-util-cell-val'))
		);
	}

	$container->addItem(
		(new CDiv($util_grid))->addClass('port24-util-grid-wrap')
	);
}

(new CWidgetView($data))
	->addItem(new CTag('style', true, $css))
	->addItem($container)
	->show();
