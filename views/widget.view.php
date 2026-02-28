<?php declare(strict_types = 1);

$css = implode('', [
	'.port24-switch{position:relative;background:linear-gradient(180deg,#5a6571 0%,#3d4651 22%,#2b323b 100%);',
	'border:1px solid #212831;border-radius:10px;padding:calc(14px * var(--port24-scale));box-shadow:inset 0 1px 0 rgba(255,255,255,.2),0 4px 16px rgba(0,0,0,.25);}',
	'.port24-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:calc(8px * var(--port24-scale));color:#cfd8e2;font-size:calc(10px * var(--port24-scale));letter-spacing:.08em;text-transform:uppercase;}',
	'.port24-brand{font-weight:700;}',
	'.port24-head-right{display:flex;align-items:center;gap:calc(8px * var(--port24-scale));min-width:0;}',
	'.port24-head-legend{display:inline-flex;align-items:center;max-width:min(54vw,620px);padding:calc(3px * var(--port24-scale)) calc(8px * var(--port24-scale));',
	'font-size:calc(var(--port24-legend-size,14px) * var(--port24-scale));line-height:1.2;letter-spacing:0;text-transform:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;',
	'border-radius:999px;border:1px solid rgba(203,213,225,.28);background:rgba(15,23,34,.22);color:#e5edf7;}',
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
	'.port24-tip-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:4px 0;order:20;}',
	'.port24-tip-label{opacity:.9;min-width:24px;}',
	'.port24-tip-value{opacity:.95;font-variant-numeric:tabular-nums;}',
	'.port24-tip-state-row{margin:6px 0 4px 0;order:30;}',
	'.port24-tip-state-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;}',
	'.port24-tip-state-title{font-size:10px;opacity:.9;letter-spacing:.03em;}',
	'.port24-tip-state-grid{display:grid;grid-template-columns:repeat(48,1fr);gap:1px;height:8px;background:#1b2430;padding:1px;border-radius:2px;}',
	'.port24-tip-state-seg{display:block;border-radius:1px;background:#64748B;}',
	'.port24-tip-state-seg.ok{background:#22C55E;}',
	'.port24-tip-state-seg.problem{background:#EF4444;}',
		'.port24-tip-svg{display:block;width:150px;height:36px;background:linear-gradient(180deg,rgba(90,101,113,.22) 0%,rgba(43,50,59,.34) 100%);border:1px solid rgba(168,184,202,.30);border-radius:4px;overflow:hidden;}',
		'.port24-tip-area-in{fill:rgba(56,189,248,0.35);}',
		'.port24-tip-area-out{fill:rgba(245,158,11,0.30);}',
		'.port24-tip-path-in{fill:none;stroke:#4fb9ff;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}',
		'.port24-tip-path-out{fill:none;stroke:#ffb020;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}',
		'.port24-tip-dot{fill:#334155;stroke:#dbeafe;stroke-width:1.1;}',
	'.port24-util-grid-wrap{margin-top:10px;padding:0 calc(14px * var(--port24-scale));}',
	'.port24-util-slot-grid{display:grid;grid-auto-flow:row;column-gap:0;row-gap:4px;width:100%;}',
	'.port24-util-sep{justify-self:center;align-self:stretch;width:1px;border-radius:1px;',
	'background:linear-gradient(180deg,rgba(203,213,225,.08),rgba(203,213,225,.32),rgba(203,213,225,.08));}',
	'.port24-util-cell{border:1px solid rgba(15,23,34,.28);border-radius:3px;padding:4px 3px;text-align:center;font-size:10px;line-height:1.2;color:#0f1722;}',
	'.port24-util-cell-port{display:block;font-weight:700;opacity:.8;margin-bottom:2px;}',
	'.port24-util-cell-val{display:block;font-weight:700;}',
	'.port24-summary{margin-top:8px;padding:6px 0 0 0;border:0;border-radius:0;background:transparent;box-shadow:none;color:#d5deea;}',
	'.port24-summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:6px 10px;}',
	'.port24-summary-item{display:flex;align-items:center;gap:6px;min-width:0;}',
	'.port24-summary-k{font-weight:700;color:#c8d4e3;white-space:nowrap;}',
	'.port24-summary-v{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#f4f7fb;}',
	'.port24-summary-live{margin-top:8px;padding-top:7px;border-top:1px solid rgba(203,213,225,.25);max-width:740px;}',
	'.port24-summary-live-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;max-width:620px;}',
	'.port24-summary-live-title{font-weight:700;color:#e5edf7;}',
	'.port24-summary-live-sub{font-size:11px;color:#b8c7da;}',
	'.port24-maintenance-badge{display:inline-flex;align-items:center;gap:6px;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;border:1px solid rgba(255,255,255,.2);}',
	'.port24-maintenance-badge.off{background:#334155;color:#dbeafe;}',
	'.port24-maintenance-badge.on{background:#f59e0b;color:#111827;border-color:#fbbf24;box-shadow:0 0 10px rgba(245,158,11,.45);}',
	'.port24-summary-live-body{display:grid;grid-template-columns:minmax(220px,300px) minmax(220px,300px);gap:12px;align-items:start;max-width:620px;}',
	'@media (max-width:860px){.port24-summary-live-body{grid-template-columns:1fr;}}',
	'.port24-summary-live-row{display:grid;grid-template-columns:58px 150px 72px;align-items:center;justify-content:start;gap:8px;margin:4px 0;max-width:360px;}',
	'.port24-summary-live-spark{display:block;width:150px;height:36px;background:linear-gradient(180deg,rgba(90,101,113,.22) 0%,rgba(43,50,59,.34) 100%);border:1px solid rgba(168,184,202,.30);border-radius:4px;overflow:hidden;}',
	'.port24-summary-live-spacer{display:block;width:150px;height:1px;}',
	'.port24-summary-live-value{min-width:52px;text-align:right;font-variant-numeric:tabular-nums;color:#e5edf7;}',
	'.port24-status-value{display:inline-flex;align-items:center;gap:6px;}',
	'.port24-status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;}',
	'.port24-status-dot.ok{background:#22C55E;box-shadow:0 0 0 1px rgba(255,255,255,.25),0 0 8px rgba(34,197,94,.55);}',
	'.port24-status-dot.problem{background:#EF4444;box-shadow:0 0 0 1px rgba(255,255,255,.25),0 0 8px rgba(239,68,68,.55);}',
	'.port24-summary-live-state{max-width:300px;}',
	'.port24-summary-bars .port24-tip-state-row{margin:0 0 8px 0;}',
	'.port24-summary-bars .port24-tip-state-row:last-child{margin-bottom:0;}',
	'.port24-denied{display:inline-block;padding:10px 12px;border:1px solid #e7b7b7;border-radius:6px;background:#fff5f5;color:#9b2c2c;font-size:13px;}'
]);

$container = new CDiv();
$scale = max(0.4, min(1.0, ((int) ($data['switch_size'] ?? 100)) / 100));
$legend_size = max(12, min(18, (int) ($data['legend_size'] ?? 14)));

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
$utilization_overlay_enabled = (int) ($data['utilization_overlay_enabled'] ?? 1);
$show_utilization_overlay = ($utilization_overlay_enabled === 1);
$traffic_unit_mode = (int) ($data['traffic_unit_mode'] ?? 0);
$util_low_color = (string) ($data['utilization_low_color'] ?? '#22C55E');
$util_warn_color = (string) ($data['utilization_warn_color'] ?? '#FCD34D');
$util_high_color = (string) ($data['utilization_high_color'] ?? '#DB2777');
$util_na_color = (string) ($data['utilization_na_color'] ?? '#94A3B8');
$util_low_threshold = (float) ($data['utilization_low_threshold'] ?? 5);
$util_warn_threshold = (float) ($data['utilization_warn_threshold'] ?? 40);
$util_high_threshold = (float) ($data['utilization_high_threshold'] ?? 70);
$widget_uid = 'port24_'.str_replace('.', '_', uniqid('', true));
$switch = (new CDiv())->addClass('port24-switch')
	->setAttribute('id', $widget_uid)
	->setAttribute('style', '--port24-scale: '.$scale.'; --port24-legend-size: '.$legend_size.'px;');
$head = (new CDiv())->addClass('port24-head');
$head_right = (new CDiv())->addClass('port24-head-right');
if ($data['legend_text'] !== '') {
	$head_right->addItem(
		(new CSpan($data['legend_text']))
			->addClass('port24-head-legend')
			->setAttribute('title', $data['legend_text'])
	);
}
$head_right->addItem((new CDiv($data['switch_model'] !== '' ? $data['switch_model'] : 'SW-24G'))->addClass('port24-model'));
$head
	->addItem((new CDiv($data['switch_brand'] !== '' ? $data['switch_brand'] : 'NETSWITCH'))->addClass('port24-brand'))
	->addItem($head_right);
$switch->addItem($head);
$utp_ports = [];
$sfp_ports = [];
foreach ($data['ports'] as $port) {
	$port['__display_index'] = count($utp_ports) + count($sfp_ports) + 1;
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
	return '#BBF7D0';
};
$live_select_js = 'var r=this.closest(".port24-switch");if(!r)return;'
	.'var t=r.querySelector(".port24-summary-live-title"),is=r.querySelector("[data-live-spark=\"in\"]"),os=r.querySelector("[data-live-spark=\"out\"]"),iv=r.querySelector("[data-live-value=\"in\"]"),ov=r.querySelector("[data-live-value=\"out\"]"),uv=r.querySelector("[data-live-util=\"1\"]"),ev=r.querySelector("[data-live-errors=\"1\"]"),dv=r.querySelector("[data-live-discards=\"1\"]");'
	.'var sg=r.querySelector("[data-live-state-grid=\"1\"]"),eg=r.querySelector("[data-live-errors-grid=\"1\"]"),dg=r.querySelector("[data-live-discards-grid=\"1\"]");'
	.'if(!t||!is||!os||!iv||!ov||!uv||!ev||!dv||!sg||!eg||!dg)return;'
	.'var mk=function(k,l,a,x,y){var s=\'<svg viewBox="0 0 120 26" class="port24-tip-svg"><line x1="3" y1="13" x2="117" y2="13" stroke="rgba(148,163,184,0.25)" stroke-width="1"></line>\';'
	.'if(a){s+=\'<path d="\'+a+\'" class="\'+(k==="in"?"port24-tip-area-in":"port24-tip-area-out")+\'"></path>\';}'
	.'if(l){s+=\'<path d="\'+l+\'" class="\'+(k==="in"?"port24-tip-path-in":"port24-tip-path-out")+\'"></path>\';s+=\'<circle cx="\'+(x||0)+\'" cy="\'+(y||0)+\'" r="2.8" class="port24-tip-dot"></circle>\';}'
	.'return s+"</svg>";};'
	.'var sh=function(v){var a=(v||"").split(","),h=\'<div class="port24-tip-state-grid">\';'
	.'if(!v||a.length===0){for(var j=0;j<48;j++){h+=\'<span class="port24-tip-state-seg"></span>\';}return h+"</div>";}'
	.'for(var i=0;i<a.length;i++){var c="port24-tip-state-seg";if(a[i]==="1"){c+=" problem";}else if(a[i]==="0"){c+=" ok";}h+=\'<span class="\'+c+\'"></span>\';}'
	.'return h+"</div>";};'
	.'var shc=function(v,k){var a=(v||"").split(","),m=0,h=\'<div class="port24-tip-state-grid">\';for(var i=0;i<a.length;i++){var n=parseFloat(a[i]);if(!isNaN(n)&&n>m){m=n;}}'
	.'if(!v||a.length===0){for(var j=0;j<48;j++){h+=\'<span class="port24-tip-state-seg"></span>\';}return h+"</div>";}'
	.'for(var z=0;z<a.length;z++){var d=parseFloat(a[z]);var c="port24-tip-state-seg";if(!isNaN(d)&&d>0&&m>0){var p=d/m;if(k==="e"){if(p>0.66){c+=" problem";}else if(p>0.2){c+=" ok";}}else{if(p>0.66){c+=" ok";}else if(p>0.2){c+=" problem";}}}h+=\'<span class="\'+c+\'"></span>\';}'
	.'return h+"</div>";};'
	.'t.textContent=this.getAttribute("data-port-name")||"Port";'
	.'is.innerHTML=mk("in",this.dataset.liveInLine||"",this.dataset.liveInArea||"",this.dataset.liveInLastX||"0",this.dataset.liveInLastY||"0");'
	.'os.innerHTML=mk("out",this.dataset.liveOutLine||"",this.dataset.liveOutArea||"",this.dataset.liveOutLastX||"0",this.dataset.liveOutLastY||"0");'
	.'iv.textContent=this.dataset.liveInValue||"n/a";ov.textContent=this.dataset.liveOutValue||"n/a";'
	.'uv.textContent=this.dataset.liveUtil||"0.0%";'
	.'ev.textContent=this.dataset.liveErrors||"n/a";dv.textContent=this.dataset.liveDiscards||"n/a";'
	.'sg.innerHTML=sh(this.dataset.liveState||"");'
	.'eg.innerHTML=shc(this.dataset.liveErrorsBars||"","e");'
	.'dg.innerHTML=shc(this.dataset.liveDiscardsBars||"","d");';

$make_card = static function(array $port) use ($show_utilization_overlay, $util_color_for, $traffic_unit_mode, $live_select_js): CTag {
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
	$display_color = $active_color;

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
	$fmt_last = static function(array $values) use ($traffic_unit_mode): string {
		if ($values === []) {
			return 'n/a';
		}
		$last = (float) $values[count($values) - 1];
		$divisor = 1000.0;
		$suffixes = ['', 'k', 'M', 'G', 'T'];
		$unit_suffix = $traffic_unit_mode === 1 ? 'bps' : 'B/s';
		$value = max(0.0, $last);
		$idx = 0;
		while ($value >= $divisor && $idx < count($suffixes) - 1) {
			$value /= $divisor;
			$idx++;
		}
		if ($idx === 0) {
			return (string) round($value).$unit_suffix;
		}
		$decimals = $value >= 100 ? 0 : ($value >= 10 ? 1 : 2);
		return number_format($value, $decimals).$suffixes[$idx].$unit_suffix;
	};
	$fmt_counter = static function(float $value): string {
		$normalized = max(0.0, $value);
		if ($normalized >= 1000000) {
			return number_format($normalized / 1000000, 2).'M';
		}
		if ($normalized >= 1000) {
			return number_format($normalized / 1000, 1).'k';
		}
		return (string) round($normalized);
	};
	$fmt_trend = static function(string $trend): string {
		if ($trend === 'rising') {
			return _('rising');
		}
		if ($trend === 'stable') {
			return _('stable');
		}
		return _('n/a');
	};

	$in_series = is_array($port['traffic_in_series'] ?? null) ? $port['traffic_in_series'] : [];
	$out_series = is_array($port['traffic_out_series'] ?? null) ? $port['traffic_out_series'] : [];
	$state_24h = is_array($port['state_24h'] ?? null) ? $port['state_24h'] : [];
		$errors_24h_total = (float) ($port['errors_24h_total'] ?? 0.0);
		$errors_24h_in = (float) ($port['errors_24h_in'] ?? 0.0);
		$errors_24h_out = (float) ($port['errors_24h_out'] ?? 0.0);
		$errors_24h_trend = (string) ($port['errors_24h_trend'] ?? 'n/a');
		$errors_24h_buckets = is_array($port['errors_24h_buckets'] ?? null)
			? $port['errors_24h_buckets']
			: array_fill(0, 48, 0.0);
		$discards_24h_total = (float) ($port['discards_24h_total'] ?? 0.0);
		$discards_24h_in = (float) ($port['discards_24h_in'] ?? 0.0);
		$discards_24h_out = (float) ($port['discards_24h_out'] ?? 0.0);
		$discards_24h_trend = (string) ($port['discards_24h_trend'] ?? 'n/a');
		$discards_24h_buckets = is_array($port['discards_24h_buckets'] ?? null)
			? $port['discards_24h_buckets']
			: array_fill(0, 48, 0.0);
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
							'background: '.$display_color.';'
							.'box-shadow:0 0 0 1px rgba(255,255,255,.2),'
							.'0 0 calc(12px * var(--port24-scale)) '.$display_color.','
							.'0 0 calc(20px * var(--port24-scale)) '.$display_color.';'
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

	if ($port['url'] !== '') {
		$card = new CLink($content, $port['url']);
	}
	else {
		$card = new CDiv($content);
	}

	$card
		->addClass('port24-card')
		->setAttribute('style', '--port-color: '.$display_color.';')
		->setAttribute('data-port-name', (string) $port['name'])
		->setAttribute('onmouseenter', $live_select_js)
		->setAttribute('onclick', $live_select_js)
		->setAttribute('onfocus', $live_select_js);
	if ($show_utilization_overlay) {
		$card
			->addClass('port24-heatmap')
			->setAttribute('style', '--port-color: '.$display_color.'; --util-c: '.$util_color.';');
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
	$card->setAttribute('data-live-in-value', $fmt_last($in_series));
	$card->setAttribute('data-live-out-value', $fmt_last($out_series));
	$card->setAttribute(
		'data-live-util',
		(isset($port['utilization_percent']) && $port['utilization_percent'] !== null)
			? number_format((float) $port['utilization_percent'], 1).'%'
			: '0.0%'
	);
	$card->setAttribute('data-live-in-line', (string) ($in_geom['line'] ?? ''));
	$card->setAttribute('data-live-in-area', (string) ($in_geom['area'] ?? ''));
	$card->setAttribute('data-live-in-last-x', (string) ($in_geom['last_x'] ?? '0'));
	$card->setAttribute('data-live-in-last-y', (string) ($in_geom['last_y'] ?? '0'));
	$card->setAttribute('data-live-out-line', (string) ($out_geom['line'] ?? ''));
	$card->setAttribute('data-live-out-area', (string) ($out_geom['area'] ?? ''));
	$card->setAttribute('data-live-out-last-x', (string) ($out_geom['last_x'] ?? '0'));
	$card->setAttribute('data-live-out-last-y', (string) ($out_geom['last_y'] ?? '0'));
	$card->setAttribute('data-live-errors', sprintf(
		'%1$s (in %2$s / out %3$s), %4$s',
		$fmt_counter($errors_24h_total),
		$fmt_counter($errors_24h_in),
		$fmt_counter($errors_24h_out),
		$fmt_trend($errors_24h_trend)
	));
	$card->setAttribute('data-live-discards', sprintf(
		'%1$s (in %2$s / out %3$s), %4$s',
		$fmt_counter($discards_24h_total),
		$fmt_counter($discards_24h_in),
		$fmt_counter($discards_24h_out),
		$fmt_trend($discards_24h_trend)
	));
	$card->setAttribute(
		'data-live-errors-bars',
		implode(',', array_map(static fn($v): string => (string) max(0.0, (float) $v), $errors_24h_buckets))
	);
	$card->setAttribute(
		'data-live-discards-bars',
		implode(',', array_map(static fn($v): string => (string) max(0.0, (float) $v), $discards_24h_buckets))
	);
	$card->setAttribute(
		'data-live-state',
		implode(',', array_map(static fn($v): string => (string) ((int) $v), $state_24h !== [] ? $state_24h : array_fill(0, 48, -1)))
	);

			return (new CDiv())
				->addClass('port24-card-wrap')
				->addItem($card);
		};

$main_grid = (new CDiv())
	->addClass('port24-grid')
	->setAttribute('style', 'grid-template-columns:repeat('.$columns.',minmax(0,1fr));');

foreach ($utp_ports as $port) {
	$main_grid->addItem($make_card($port));
}

$face = (new CDiv())->addClass('port24-face');
$face->addItem((new CDiv($main_grid))->addClass('port24-main'));

$sfp_columns = 0;
if ($sfp_ports !== []) {
	$sfp_count = count($sfp_ports);
	$sfp_columns = max(1, (int) ceil($sfp_count / $row_count));
	if ($sfp_count > 1) {
		$sfp_columns = max(2, $sfp_columns);
	}
	$sfp_columns = min($sfp_count, $sfp_columns);
	$uplink_grid = (new CDiv())
		->addClass('port24-sfp-grid')
		->setAttribute(
			'style',
			'grid-template-columns:repeat('
			.$sfp_columns
			.',minmax(calc(56px * var(--port24-scale)),calc(72px * var(--port24-scale))));'
		);

	foreach ($sfp_ports as $port) {
		$uplink_grid->addItem($make_card($port));
	}

	$face->addItem((new CDiv($uplink_grid))->addClass('port24-uplink'));
}

	$switch->addItem($face);

if ($show_utilization_overlay) {
	$util_wrap = null;
	$make_util_cell = static function(array $port, string $placement_style = '') use ($util_color_for): CDiv {
		$util = isset($port['utilization_percent']) && $port['utilization_percent'] !== null
			? (float) $port['utilization_percent']
			: null;
		$util_text = $util !== null ? number_format($util, 1).'%' : 'n/a';
		$cell_color = isset($port['utilization_color']) && is_string($port['utilization_color'])
			? (string) $port['utilization_color']
			: $util_color_for($util);
		$port_label = 'P'.(int) ($port['__display_index'] ?? 0);
		$style = 'background: '.$cell_color.';';
		if ($placement_style !== '') {
			$style .= $placement_style;
		}

		return (new CDiv())
			->addClass('port24-util-cell')
			->setAttribute('style', $style)
			->addItem((new CSpan($port_label))->addClass('port24-util-cell-port'))
			->addItem((new CSpan($util_text))->addClass('port24-util-cell-val'));
	};

	$split_slot_count = ($sfp_ports !== []) ? 1 : 0;
	$heatmap_sfp_columns = 0;
	if ($sfp_ports !== []) {
		$heatmap_sfp_columns = max(
			(count($sfp_ports) > 1) ? 2 : 1,
			(int) $sfp_columns
		);
		$heatmap_sfp_columns = min(count($sfp_ports), $heatmap_sfp_columns);
	}
	$grid_template_parts = ['repeat('.$columns.',minmax(0,1fr))'];
	if ($split_slot_count > 0) {
		$grid_template_parts[] = 'repeat('.$split_slot_count.',minmax(calc(6px * var(--port24-scale)),calc(10px * var(--port24-scale))))';
	}
	if ($heatmap_sfp_columns > 0) {
		$grid_template_parts[] = 'repeat('.$heatmap_sfp_columns.',minmax(0,1fr))';
	}
	$util_slot_grid = (new CDiv())
		->addClass('port24-util-slot-grid')
		->setAttribute('style', 'grid-template-columns:'.implode(' ', $grid_template_parts).';');

	$utp_rows = max(1, (int) ceil(count($utp_ports) / $columns));
	$sfp_rows = ($heatmap_sfp_columns > 0) ? max(1, (int) ceil(count($sfp_ports) / $heatmap_sfp_columns)) : 0;
	$max_rows = max($utp_rows, $sfp_rows);

	foreach ($utp_ports as $index => $port) {
		$row = intdiv((int) $index, $columns) + 1;
		$col = ((int) $index % $columns) + 1;
		$util_slot_grid->addItem($make_util_cell(
			$port,
			'grid-column:'.$col.';grid-row:'.$row.';'
		));
	}

	if ($sfp_ports !== []) {
		$sfp_start_col = $columns + $split_slot_count + 1;
		if ($split_slot_count > 0) {
			$util_slot_grid->addItem(
				(new CDiv())
					->addClass('port24-util-sep')
					->setAttribute(
						'style',
						'grid-column:'.($columns + 1).';grid-row:1 / span '.$max_rows.';'
					)
			);
		}
		foreach ($sfp_ports as $index => $port) {
			$row = intdiv((int) $index, $heatmap_sfp_columns) + 1;
			$col = $sfp_start_col + ((int) $index % $heatmap_sfp_columns);
			$util_slot_grid->addItem($make_util_cell(
				$port,
				'grid-column:'.$col.';grid-row:'.$row.';'
			));
		}
	}

	$util_wrap = (new CDiv($util_slot_grid))
		->addClass('port24-util-grid-wrap')
		->setAttribute('style', '--port24-scale: '.$scale.';');
}

$summary = is_array($data['switch_summary'] ?? null) ? $data['switch_summary'] : [];
$summary_rows = [];
$monitoring_enabled = (array_key_exists('monitoring_enabled', $summary) && (bool) $summary['monitoring_enabled']);
$maintenance_active = (array_key_exists('maintenance_active', $summary) && (bool) $summary['maintenance_active']);
$summary_rows[] = [_('Utilization'), sprintf(
	'%s avg, %s peak',
	(isset($summary['avg_utilization']) && $summary['avg_utilization'] !== null)
		? number_format((float) $summary['avg_utilization'], 1).'%' : 'n/a',
	(isset($summary['peak_utilization']) && $summary['peak_utilization'] !== null)
		? number_format((float) $summary['peak_utilization'], 1).'%' : 'n/a'
)];

$hardware = trim((string) ($summary['hardware'] ?? ''));
if ($hardware !== '') {
	$summary_rows[] = [_('Hardware'), $hardware];
}
$vlans = trim((string) ($summary['vlans'] ?? ''));
if ($vlans !== '') {
	$summary_rows[] = [_('VLANs'), $vlans];
}
$cpu = trim((string) ($summary['cpu'] ?? ''));
if ($cpu !== '') {
	$summary_rows[] = [_('CPU'), $cpu];
}
$fan = trim((string) ($summary['fan'] ?? ''));
if ($fan !== '') {
	$summary_rows[] = [_('Fan'), $fan];
}
$uptime = trim((string) ($summary['uptime'] ?? ''));
if ($uptime !== '') {
	$summary_rows[] = [_('Uptime'), $uptime];
}
$serial = trim((string) ($summary['serial'] ?? ''));
if ($serial !== '') {
	$summary_rows[] = [_('Serial'), $serial];
}
$software = trim((string) ($summary['software'] ?? ''));
if ($software !== '') {
	$summary_rows[] = [_('Software'), $software];
}
$os = trim((string) ($summary['os'] ?? ''));
if ($os !== '') {
	$summary_rows[] = [_('OS'), $os];
}

$summary_grid = (new CDiv())->addClass('port24-summary-grid');
$summary_grid->addItem(
	(new CDiv())
		->addClass('port24-summary-item')
		->addItem((new CSpan(_('Monitoring').':'))->addClass('port24-summary-k'))
		->addItem(
			(new CSpan(
				(new CSpan())
					->addClass('port24-status-dot')
					->addClass($monitoring_enabled ? 'ok' : 'problem')
			))
				->addClass('port24-status-value')
		)
);
foreach ($summary_rows as [$key, $value]) {
	if ($value === '') {
		continue;
	}

	$summary_grid->addItem(
		(new CDiv())
			->addClass('port24-summary-item')
			->addItem((new CSpan($key.':'))->addClass('port24-summary-k'))
			->addItem((new CSpan($value))->addClass('port24-summary-v'))
	);
}
$live = (new CDiv())->addClass('port24-summary-live')
	->addItem(
		(new CDiv())
			->addClass('port24-summary-live-head')
			->addItem((new CSpan(_('Port traffic')))->addClass('port24-summary-live-title'))
			->addItem(
				(new CSpan($maintenance_active ? _('Maintenance: ON') : _('Maintenance: OFF')))
					->addClass('port24-maintenance-badge')
					->addClass($maintenance_active ? 'on' : 'off')
			)
	)
	->addItem(
		(new CDiv())
			->addClass('port24-summary-live-body')
			->addItem(
				(new CDiv())
					->addItem(
						(new CDiv())
							->addClass('port24-summary-live-row')
							->addItem((new CSpan('IN'))->addClass('port24-summary-k'))
							->addItem((new CDiv())->addClass('port24-summary-live-spark')->setAttribute('data-live-spark', 'in'))
							->addItem((new CSpan('n/a'))->addClass('port24-summary-live-value')->setAttribute('data-live-value', 'in'))
					)
						->addItem(
							(new CDiv())
								->addClass('port24-summary-live-row')
								->addItem((new CSpan('OUT'))->addClass('port24-summary-k'))
								->addItem((new CDiv())->addClass('port24-summary-live-spark')->setAttribute('data-live-spark', 'out'))
								->addItem((new CSpan('n/a'))->addClass('port24-summary-live-value')->setAttribute('data-live-value', 'out'))
						)
						->addItem(
							(new CDiv())
								->addClass('port24-summary-live-row')
								->addItem((new CSpan('Port utilization'))->addClass('port24-summary-k'))
								->addItem((new CSpan(''))->addClass('port24-summary-live-spacer'))
								->addItem((new CSpan('n/a'))->addClass('port24-summary-live-value')->setAttribute('data-live-util', '1'))
						)
				)
			->addItem(
				(new CDiv())
					->addClass('port24-summary-bars')
					->addItem(
						(new CDiv())
							->addClass('port24-tip-state-row')
							->addItem(
								(new CDiv())
									->addClass('port24-tip-state-head')
									->addItem((new CSpan('24h online state'))->addClass('port24-tip-state-title'))
									->addItem((new CSpan('now'))->addClass('port24-tip-state-title'))
							)
							->addItem(
								(new CDiv(
									(new CDiv())->addClass('port24-tip-state-grid')
								))
									->addClass('port24-summary-live-state')
									->setAttribute('data-live-state-grid', '1')
							)
					)
					->addItem(
						(new CDiv())
							->addClass('port24-tip-state-row')
							->addItem(
								(new CDiv())
									->addClass('port24-tip-state-head')
									->addItem((new CSpan('Errors 24h'))->addClass('port24-tip-state-title'))
									->addItem((new CSpan('n/a'))->addClass('port24-tip-state-title')->setAttribute('data-live-errors', '1'))
							)
							->addItem(
								(new CDiv(
									(new CDiv())->addClass('port24-tip-state-grid')
								))
									->addClass('port24-summary-live-state')
									->setAttribute('data-live-errors-grid', '1')
							)
					)
					->addItem(
						(new CDiv())
							->addClass('port24-tip-state-row')
							->addItem(
								(new CDiv())
									->addClass('port24-tip-state-head')
									->addItem((new CSpan('Discards 24h'))->addClass('port24-tip-state-title'))
									->addItem((new CSpan('n/a'))->addClass('port24-tip-state-title')->setAttribute('data-live-discards', '1'))
							)
							->addItem(
								(new CDiv(
									(new CDiv())->addClass('port24-tip-state-grid')
								))
									->addClass('port24-summary-live-state')
									->setAttribute('data-live-discards-grid', '1')
							)
					)
			)
	);

$summary_panel = (new CDiv())
	->addClass('port24-summary')
	->addItem($summary_grid)
	->addItem($live);

$switch->addItem($summary_panel);
$container->addItem($switch);
if (isset($util_wrap) && $util_wrap instanceof CDiv) {
	$container->addItem($util_wrap);
}

$live_script = <<<JS
(function() {
	const root = document.getElementById(%s);
	if (!root) {
		return;
	}
	const titleNode = root.querySelector('.port24-summary-live-title');
	const inSpark = root.querySelector('[data-live-spark="in"]');
	const outSpark = root.querySelector('[data-live-spark="out"]');
	const inValue = root.querySelector('[data-live-value="in"]');
	const outValue = root.querySelector('[data-live-value="out"]');
	const utilValue = root.querySelector('[data-live-util="1"]');
	const errorsValue = root.querySelector('[data-live-errors="1"]');
	const discardsValue = root.querySelector('[data-live-discards="1"]');
	const stateGrid = root.querySelector('[data-live-state-grid="1"]');
	const errorsGrid = root.querySelector('[data-live-errors-grid="1"]');
	const discardsGrid = root.querySelector('[data-live-discards-grid="1"]');
	if (!titleNode || !inSpark || !outSpark || !inValue || !outValue || !utilValue || !errorsValue || !discardsValue
			|| !stateGrid || !errorsGrid || !discardsGrid) {
		return;
	}

		const svgNS = 'http://www.w3.org/2000/svg';
		const makeSpark = function(kind, line, area, lastX, lastY) {
			const svg = document.createElementNS(svgNS, 'svg');
			svg.setAttribute('viewBox', '0 0 120 26');
			svg.classList.add('port24-tip-svg');

			const baseline = document.createElementNS(svgNS, 'line');
			baseline.setAttribute('x1', '3');
			baseline.setAttribute('y1', '13');
			baseline.setAttribute('x2', '117');
			baseline.setAttribute('y2', '13');
			baseline.setAttribute('stroke', 'rgba(148,163,184,0.25)');
			baseline.setAttribute('stroke-width', '1');
			svg.appendChild(baseline);

			if (area) {
				const areaPath = document.createElementNS(svgNS, 'path');
				areaPath.setAttribute('d', area);
				areaPath.setAttribute('class', kind === 'in' ? 'port24-tip-area-in' : 'port24-tip-area-out');
				svg.appendChild(areaPath);
			}
			if (line) {
				const linePath = document.createElementNS(svgNS, 'path');
				linePath.setAttribute('d', line);
				linePath.setAttribute('class', kind === 'in' ? 'port24-tip-path-in' : 'port24-tip-path-out');
				svg.appendChild(linePath);

				const dot = document.createElementNS(svgNS, 'circle');
				dot.setAttribute('cx', String(lastX || 0));
				dot.setAttribute('cy', String(lastY || 0));
				dot.setAttribute('r', '2.8');
				dot.setAttribute('class', 'port24-tip-dot');
				svg.appendChild(dot);
			}

			return svg;
		};
		const renderStateGrid = function(csv) {
			const values = (csv || '').split(',');
			let html = '<div class="port24-tip-state-grid">';
			if (!csv || values.length === 0) {
				for (let i = 0; i < 48; i++) {
					html += '<span class="port24-tip-state-seg"></span>';
				}
				return html + '</div>';
			}
			for (let i = 0; i < values.length; i++) {
				let cls = 'port24-tip-state-seg';
				if (values[i] === '1') {
					cls += ' problem';
				}
				else if (values[i] === '0') {
					cls += ' ok';
				}
				html += '<span class="' + cls + '"></span>';
			}
			return html + '</div>';
		};
		const renderCounterGrid = function(csv, kind) {
			const values = (csv || '').split(',');
			let max = 0;
			for (let i = 0; i < values.length; i++) {
				const n = parseFloat(values[i]);
				if (!Number.isNaN(n) && n > max) {
					max = n;
				}
			}
			let html = '<div class="port24-tip-state-grid">';
			if (!csv || values.length === 0) {
				for (let i = 0; i < 48; i++) {
					html += '<span class="port24-tip-state-seg"></span>';
				}
				return html + '</div>';
			}
			for (let i = 0; i < values.length; i++) {
				const n = parseFloat(values[i]);
				let cls = 'port24-tip-state-seg';
				if (!Number.isNaN(n) && n > 0 && max > 0) {
					const pct = n / max;
					if (kind === 'errors') {
						if (pct > 0.66) {
							cls += ' problem';
						}
						else if (pct > 0.2) {
							cls += ' ok';
						}
					}
					else {
						if (pct > 0.66) {
							cls += ' ok';
						}
						else if (pct > 0.2) {
							cls += ' problem';
						}
					}
				}
				html += '<span class="' + cls + '"></span>';
			}
			return html + '</div>';
		};

		const reset = function() {
			titleNode.textContent = 'Port traffic';
			inSpark.innerHTML = '';
			outSpark.innerHTML = '';
			inValue.textContent = 'n/a';
			outValue.textContent = 'n/a';
			utilValue.textContent = 'n/a';
			errorsValue.textContent = 'n/a';
			discardsValue.textContent = 'n/a';
		};

		const getText = function(el) {
			return el && typeof el.textContent === 'string' ? el.textContent.trim() : '';
		};

		const setFromCard = function(wrap) {
			if (!wrap) {
				return;
			}
			const labelEl = wrap.querySelector('.port24-label');
			const name = getText(labelEl) || 'Port';
			const card = wrap.querySelector('.port24-card');
			if (!card || !card.dataset) {
				return;
			}

			titleNode.textContent = name;
			inSpark.innerHTML = '';
			outSpark.innerHTML = '';
			inSpark.appendChild(makeSpark(
				'in',
				card.dataset.liveInLine || '',
				card.dataset.liveInArea || '',
				card.dataset.liveInLastX || '0',
				card.dataset.liveInLastY || '0'
			));
			outSpark.appendChild(makeSpark(
				'out',
				card.dataset.liveOutLine || '',
				card.dataset.liveOutArea || '',
				card.dataset.liveOutLastX || '0',
				card.dataset.liveOutLastY || '0'
			));
			inValue.textContent = card.dataset.liveInValue || 'n/a';
			outValue.textContent = card.dataset.liveOutValue || 'n/a';
			utilValue.textContent = card.dataset.liveUtil || 'n/a';
			errorsValue.textContent = card.dataset.liveErrors || 'n/a';
			discardsValue.textContent = card.dataset.liveDiscards || 'n/a';
				stateGrid.innerHTML = renderStateGrid(card.dataset.liveState || '');
				errorsGrid.innerHTML = renderCounterGrid(card.dataset.liveErrorsBars || '', 'errors');
				discardsGrid.innerHTML = renderCounterGrid(card.dataset.liveDiscardsBars || '', 'discards');
			};

		const handleWrapEnter = function(event) {
			const wrap = event.currentTarget;
			setFromCard(wrap);
		};

		const bindLivePanel = function(attempt) {
			const wraps = root.querySelectorAll('.port24-card-wrap');
			if (!wraps || wraps.length === 0) {
				if (attempt < 20) {
					window.setTimeout(function() { bindLivePanel(attempt + 1); }, 150);
				}
				else {
					reset();
				}
				return;
			}

			for (let i = 0; i < wraps.length; i++) {
				if (wraps[i].dataset.port24LiveBound === '1') {
					continue;
				}
				wraps[i].addEventListener('mouseenter', handleWrapEnter);
				wraps[i].addEventListener('click', handleWrapEnter);
				wraps[i].addEventListener('focusin', handleWrapEnter);
				wraps[i].dataset.port24LiveBound = '1';
			}

			const first = wraps[0];
			setFromCard(first);
		};

		bindLivePanel(0);
})();
JS;
$live_script = sprintf($live_script, json_encode($widget_uid));

(new CWidgetView($data))
	->addItem(new CTag('style', true, $css))
	->addItem($container)
	->addItem(new CTag('script', true, $live_script))
	->show();
