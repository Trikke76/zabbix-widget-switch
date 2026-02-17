<?php declare(strict_types = 1);

$form = new CWidgetFormView($data);

$port_count = 0;
foreach (array_keys($data['fields']) as $field_name) {
	if (preg_match('/^port(\d+)_name$/', $field_name, $matches) === 1) {
		$port_count = max($port_count, (int) $matches[1]);
	}
}

$form->addField(new CWidgetFieldMultiSelectGroupView($data['fields']['groupids']));
$form->addField(new CWidgetFieldMultiSelectHostView($data['fields']['hostids']));
$form->addField(new CWidgetFieldTextBoxView($data['fields']['legend_text']));
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

for ($i = 1; $i <= $port_count; $i++) {
	$fieldset = (new CWidgetFormFieldsetCollapsibleView(sprintf(_('Port %d'), $i)))
		->addClass('switch-port-fieldset')
		->setAttribute('data-port-index', (string) $i);

	$fieldset
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_name']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_triggerid']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_default_color']))
		->addField(new CWidgetFieldTextBoxView($data['fields']['port'.$i.'_trigger_color']));

	$form->addFieldset($fieldset);
}

$widget_edit_js = file_get_contents(__DIR__.'/../assets/js/widget.edit.js');
if ($widget_edit_js !== false) {
	$form->addJavaScript($widget_edit_js);
}
$form->addJavaScript('window.switch_widget_form.init();');

$form->show();
