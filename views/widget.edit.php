<?php declare(strict_types = 1);

$form = new CWidgetFormView($data);

$port_count = 0;
foreach (array_keys($data['fields']) as $field_name) {
	if (preg_match('/^port(\d+)_name$/', $field_name, $matches) === 1) {
		$port_count = max($port_count, (int) $matches[1]);
	}
}

$form->addField(new CWidgetFieldMultiSelectHostView($data['fields']['hostids']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['legend_text']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['traffic_in_item_pattern']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['traffic_out_item_pattern']));
$form->addField(new CWidgetFieldSelectView($data['fields']['traffic_unit_mode']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['in_errors_item_pattern']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['out_errors_item_pattern']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['in_discards_item_pattern']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['out_discards_item_pattern']));
$form->addField(new CWidgetFieldSelectView($data['fields']['port_color_mode']));
$form->addField(new CWidgetFieldSelectView($data['fields']['utilization_overlay_enabled']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['speed_item_pattern']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_low_threshold']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_warn_threshold']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_high_threshold']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_low_color']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_warn_color']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_high_color']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['utilization_na_color']));
$form->addField(new CWidgetFieldSelectView($data['fields']['legend_size']));
$form->addField(new CWidgetFieldSelectView($data['fields']['preset']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_name']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_row_count']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_ports_per_row']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_sfp_ports']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_switch_size']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_switch_brand']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile1_switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile2_switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile3_switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile4_switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile5_switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile6_switch_model']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['profile7_switch_model']));

for ($i = 1; $i <= $port_count; $i++) {
	$fieldset = (new CWidgetFormFieldsetCollapsibleView(sprintf(_('Port %d'), $i)))
		->addClass('switch-port-fieldset')
		->setAttribute('data-port-index', (string) $i);

	$fieldset
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_name']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_triggerid']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_default_color']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_trigger_ok_color']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_trigger_color']));

	$form->addFieldset($fieldset);
}

$widget_edit_js = file_get_contents(__DIR__.'/../assets/js/widget.edit.js');
if ($widget_edit_js !== false) {
	$manifest_version = 'dev';
	$manifest_raw = file_get_contents(__DIR__.'/../manifest.json');
	if ($manifest_raw !== false) {
		$manifest = json_decode($manifest_raw, true);
		if (is_array($manifest) && isset($manifest['version']) && is_scalar($manifest['version'])) {
			$manifest_version = (string) $manifest['version'];
		}
	}

	$form->addJavaScript('window.SWITCH_WIDGET_BUILD = '.json_encode($manifest_version).';');
	$form->addJavaScript($widget_edit_js);
}
$form->addJavaScript('window.switch_widget_form.init();');

$form->show();
