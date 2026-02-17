<?php declare(strict_types = 1);

$css = implode('', [
	'.port24-legend{display:inline-block;font-size:calc(var(--port24-legend-size,14px) * var(--port24-scale));line-height:1.35;color:#4b5563;',
	'background:linear-gradient(180deg,#f8fafc 0%,#eef2f7 100%);border:1px solid #d7dee8;border-radius:6px;',
	'padding:calc(6px * var(--port24-scale)) calc(10px * var(--port24-scale));margin-bottom:calc(10px * var(--port24-scale));}',
	'.port24-switch{background:linear-gradient(180deg,#5a6571 0%,#3d4651 22%,#2b323b 100%);',
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
	'.port24-label{position:absolute;left:calc(4px * var(--port24-scale));right:calc(4px * var(--port24-scale));bottom:calc(2px * var(--port24-scale));text-align:center;font-size:calc(9px * var(--port24-scale));white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#c9d3de;}'
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
	$state = $port['is_problem'] ? _('Problem') : _('Normal');
	$port_type = !empty($port['is_sfp']) ? 'SFP' : 'RJ45';
	$tooltip = $port['name']."\n".sprintf(_('State: %s'), $state);
	$tooltip .= "\n".sprintf(_('Type: %s'), $port_type);
	if ($port['triggerid'] !== '') {
		$trigger_name = $port['trigger_name'] !== '' ? $port['trigger_name'] : '#'.$port['triggerid'];
		$tooltip .= "\n".sprintf(_('Trigger: %s'), $trigger_name);
	}
	else {
		$tooltip .= "\n"._('Trigger: not configured');
	}

	$content = new CDiv();
	$content
		->addItem((new CDiv())->addClass(!empty($port['is_sfp']) ? 'port24-jack-sfp' : 'port24-jack'))
		->addItem((new CDiv())->addClass('port24-led'))
		->addItem((new CDiv($port['name']))->addClass('port24-label'));

	if ($port['url'] !== '') {
		$card = new CLink($content, $port['url']);
	}
	else {
		$card = new CDiv($content);
	}

	return $card
		->addClass('port24-card')
		->setAttribute('style', '--port-color: '.$port['active_color'].';')
		->setAttribute('title', $tooltip);
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
