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
	'.port24-jack{height:calc(22px * var(--port24-scale));position:relative;border:1px solid #1f2730;border-radius:2px 2px 4px 4px;',
	'background:linear-gradient(180deg,#eef3f8 0 20%,#0d1318 20% 100%);overflow:hidden;}',
	'.port24-jack:before{content:"";position:absolute;left:calc(6px * var(--port24-scale));right:calc(6px * var(--port24-scale));top:0;height:calc(7px * var(--port24-scale));background:#06090d;',
	'clip-path:polygon(12% 100%,88% 100%,100% 0,0 0);} ',
	'.port24-jack:after{content:"";position:absolute;left:calc(5px * var(--port24-scale));right:calc(5px * var(--port24-scale));bottom:calc(2px * var(--port24-scale));height:calc(5px * var(--port24-scale));',
	'background:repeating-linear-gradient(90deg,#c4ccd5 0 2px,transparent 2px 4px);opacity:.85;}',
	'.port24-jack-sfp{height:calc(22px * var(--port24-scale));position:relative;border:1px solid #38434f;border-radius:2px;',
	'background:linear-gradient(180deg,#131a22 0%,#0b1016 100%);overflow:hidden;}',
	'.port24-jack-sfp:before{content:"";position:absolute;left:calc(4px * var(--port24-scale));right:calc(4px * var(--port24-scale));top:calc(4px * var(--port24-scale));',
	'height:calc(10px * var(--port24-scale));border:1px solid #5d6a78;border-radius:2px;background:#090d12;}',
	'.port24-jack-sfp:after{content:"";position:absolute;left:calc(7px * var(--port24-scale));right:calc(7px * var(--port24-scale));bottom:calc(2px * var(--port24-scale));',
	'height:calc(3px * var(--port24-scale));background:#8a96a5;border-radius:1px;opacity:.85;}',
	'.port24-led{position:absolute;right:calc(4px * var(--port24-scale));top:calc(4px * var(--port24-scale));width:calc(8px * var(--port24-scale));height:calc(8px * var(--port24-scale));border-radius:50%;background:var(--port-color,#2F855A);',
	'box-shadow:0 0 0 1px rgba(255,255,255,.2),0 0 calc(12px * var(--port24-scale)) var(--port-color,#2F855A),0 0 calc(20px * var(--port24-scale)) var(--port-color,#2F855A);}',
	'.port24-label{position:absolute;left:calc(4px * var(--port24-scale));right:calc(4px * var(--port24-scale));bottom:calc(2px * var(--port24-scale));text-align:center;font-size:calc(9px * var(--port24-scale));white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#c9d3de;}',
	'.port24-tooltip{position:fixed;z-index:100000;pointer-events:none;min-width:190px;background:#0f1722;color:#d9e3ee;border:1px solid #2e3c4d;border-radius:8px;padding:8px;box-shadow:0 10px 28px rgba(0,0,0,.45);font-size:11px;line-height:1.3;display:none;}',
	'.port24-tooltip-title{font-weight:700;margin-bottom:6px;color:#f8fbff;}',
	'.port24-tip-meta{margin:2px 0;opacity:.95;}',
	'.port24-tip-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:4px 0;}',
	'.port24-tip-label{opacity:.9;min-width:24px;}',
	'.port24-tip-value{opacity:.95;font-variant-numeric:tabular-nums;}',
	'.port24-tip-svg{display:block;width:120px;height:26px;background:#0a1119;border:1px solid #223041;border-radius:4px;overflow:hidden;}',
	'.port24-tip-path-in{fill:none;stroke:#38bdf8;stroke-width:1.6;}',
	'.port24-tip-path-out{fill:none;stroke:#f59e0b;stroke-width:1.6;}',
	'.port24-card-wrap:hover .port24-hover-tip{display:block;}',
	'.port24-hover-tip{position:absolute;z-index:40;left:calc(14px * var(--port24-scale));top:calc(42px * var(--port24-scale));min-width:200px;max-width:300px;background:#0f1722;color:#d9e3ee;border:1px solid #2e3c4d;border-radius:8px;padding:8px;box-shadow:0 10px 28px rgba(0,0,0,.45);font-size:11px;line-height:1.3;display:none;pointer-events:none;}'
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

$columns = max(1, (int) ($data['ports_per_row'] ?? 12));
$row_count = max(1, (int) ($data['row_count'] ?? 1));
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

$make_card = static function(array $port): CTag {
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
	if ($port['triggerid'] !== '') {
		$trigger_name = $port['trigger_name'] !== '' ? $port['trigger_name'] : '#'.$port['triggerid'];
		$tooltip .= "\n".sprintf(_('Trigger: %s'), $trigger_name);
	}
	else {
		$tooltip .= "\n"._('Trigger: not configured');
		$trigger_name = _('not configured');
	}

	$spark_path = static function(array $values, int $width = 120, int $height = 26, int $padding = 3): string {
		$count = count($values);
		if ($count < 1) {
			return '';
		}
		if ($count === 1) {
			$y = round($height / 2, 2);
			return 'M'.$padding.','.$y.' L'.($width - $padding).','.$y;
		}

		$min = min($values);
		$max = max($values);
		$is_flat = abs($max - $min) < 0.0000001;
		$span = $is_flat ? 1.0 : ($max - $min);
		$dw = $width - ($padding * 2);
		$dh = $height - ($padding * 2);

		$parts = [];
		$last_index = $count - 1;
		foreach ($values as $idx => $value) {
			$x = $padding + ($dw * $idx / $last_index);
			$y = $is_flat
				? ($padding + ($dh / 2))
				: ($padding + $dh - ((($value - $min) / $span) * $dh));
			$parts[] = ($idx === 0 ? 'M' : 'L').round($x, 2).','.round($y, 2);
		}

		return implode(' ', $parts);
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
	$in_path = $spark_path($in_series);
	$out_path = $spark_path($out_series);

		$content = new CDiv();
		$make_spark_svg = static function(string $path, string $line_class): CTag {
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

			if ($path !== '') {
				$svg->addItem(
					(new CTag('path', true))
						->addClass($line_class)
						->setAttribute('d', $path)
				);
			}

			return $svg;
		};

			$content
				->addItem((new CDiv())->addClass(!empty($port['is_sfp']) ? 'port24-jack-sfp' : 'port24-jack'))
				->addItem((new CDiv())->addClass('port24-led'))
				->addItem((new CDiv($port['name']))->addClass('port24-label'));

		$hover_tip = (new CDiv(
			(new CDiv($port['name']))->addClass('port24-tooltip-title')
		))
			->addClass('port24-hover-tip')
			->addItem((new CDiv(sprintf(_('State: %s'), $state)))->addClass('port24-tip-meta'))
			->addItem((new CDiv(sprintf(_('Type: %s'), $port_type)))->addClass('port24-tip-meta'))
			->addItem((new CDiv(sprintf(_('Trigger: %s'), $trigger_name)))->addClass('port24-tip-meta'))
			->addItem(
				(new CDiv())
					->addClass('port24-tip-row')
					->addItem((new CSpan('IN'))->addClass('port24-tip-label'))
					->addItem($make_spark_svg($in_path, 'port24-tip-path-in'))
					->addItem((new CSpan($fmt_last($in_series)))->addClass('port24-tip-value'))
			)
			->addItem(
				(new CDiv())
					->addClass('port24-tip-row')
					->addItem((new CSpan('OUT'))->addClass('port24-tip-label'))
					->addItem($make_spark_svg($out_path, 'port24-tip-path-out'))
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
		->setAttribute('style', '--port-color: '.$port['active_color'].';')
		->setAttribute('data-port-name', (string) $port['name']);

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

(new CWidgetView($data))
	->addItem(new CTag('style', true, $css))
	->addItem($container)
	->show();
