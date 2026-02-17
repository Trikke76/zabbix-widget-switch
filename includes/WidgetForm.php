<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Includes;

use Zabbix\Widgets\CWidgetForm;
use Zabbix\Widgets\Fields\CWidgetFieldMultiSelectGroup;
use Zabbix\Widgets\Fields\CWidgetFieldMultiSelectHost;
use Zabbix\Widgets\Fields\CWidgetFieldSelect;
use Zabbix\Widgets\Fields\CWidgetFieldTextBox;

class WidgetForm extends CWidgetForm {
	private const DEFAULT_ROW_COUNT = 2;
	private const DEFAULT_PORTS_PER_ROW = 12;
	private const MAX_ROW_COUNT = 24;
	private const MAX_PORTS_PER_ROW = 48;
	private const MAX_SFP_PORTS = 32;
	private const MAX_TOTAL_PORTS = 256;

	public function addFields(): self {
		$total_ports = self::MAX_TOTAL_PORTS;

		$this->addField(
			(new CWidgetFieldMultiSelectGroup('groupids', _('Host group')))
				->setMultiple(false)
		);

		$this->addField(
			(new CWidgetFieldMultiSelectHost('hostids', _('Host')))
				->setMultiple(false)
		);

		$this->addField(
			(new CWidgetFieldTextBox('legend_text', _('Legend text')))
				->setDefault('')
		);
		$this->addField(
			(new CWidgetFieldSelect('preset', _('Profile'), [
				0 => _('Custom'),
				1 => $this->getRequestedString('profile1_name', _('Access 24')),
				2 => $this->getRequestedString('profile2_name', _('Access 48')),
				3 => $this->getRequestedString('profile3_name', _('Core 48')),
				4 => $this->getRequestedString('profile4_name', _('Compact 12')),
				5 => $this->getRequestedString('profile5_name', _('User profile 1')),
				6 => $this->getRequestedString('profile6_name', _('User profile 2')),
				7 => $this->getRequestedString('profile7_name', _('User profile 3'))
			]))->setDefault(0)
		);
		$this->addField(
			(new CWidgetFieldTextBox('switch_brand', _('Brand')))
				->setDefault('NETSWITCH')
		);
		$this->addField(
			(new CWidgetFieldTextBox('switch_model', _('Model')))
				->setDefault('SW-24G')
		);
		$this->addField(
			(new CWidgetFieldTextBox('switch_size', _('Size (%)')))
				->setDefault('100')
		);

		$this
			->addField(
				(new CWidgetFieldTextBox('row_count', _('Rows')))
					->setDefault((string) self::DEFAULT_ROW_COUNT)
			)
			->addField(
				(new CWidgetFieldTextBox('ports_per_row', _('Ports per row')))
					->setDefault((string) self::DEFAULT_PORTS_PER_ROW)
			)
			->addField(
				(new CWidgetFieldTextBox('sfp_ports', _('SFP ports')))
					->setDefault('0')
			);

		$profile_defaults = [
			1 => ['name' => 'Access 24', 'row_count' => 2, 'ports_per_row' => 12, 'sfp_ports' => 2, 'switch_size' => 90],
			2 => ['name' => 'Access 48', 'row_count' => 4, 'ports_per_row' => 12, 'sfp_ports' => 4, 'switch_size' => 85],
			3 => ['name' => 'Core 48', 'row_count' => 2, 'ports_per_row' => 24, 'sfp_ports' => 4, 'switch_size' => 80],
			4 => ['name' => 'Compact 12', 'row_count' => 2, 'ports_per_row' => 6, 'sfp_ports' => 2, 'switch_size' => 70],
			5 => ['name' => 'User profile 1', 'row_count' => self::DEFAULT_ROW_COUNT, 'ports_per_row' => self::DEFAULT_PORTS_PER_ROW, 'sfp_ports' => 0, 'switch_size' => 100],
			6 => ['name' => 'User profile 2', 'row_count' => self::DEFAULT_ROW_COUNT, 'ports_per_row' => self::DEFAULT_PORTS_PER_ROW, 'sfp_ports' => 0, 'switch_size' => 100],
			7 => ['name' => 'User profile 3', 'row_count' => self::DEFAULT_ROW_COUNT, 'ports_per_row' => self::DEFAULT_PORTS_PER_ROW, 'sfp_ports' => 0, 'switch_size' => 100]
		];

		for ($p = 1; $p <= 7; $p++) {
			$defaults = $profile_defaults[$p];
			$this
				->addField(
					(new CWidgetFieldTextBox('profile'.$p.'_name', sprintf(_('Profile %d name'), $p)))
						->setDefault($defaults['name'])
				)
				->addField(
					(new CWidgetFieldTextBox('profile'.$p.'_row_count', sprintf(_('Profile %d rows'), $p)))
						->setDefault((string) $defaults['row_count'])
				)
				->addField(
					(new CWidgetFieldTextBox('profile'.$p.'_ports_per_row', sprintf(_('Profile %d ports per row'), $p)))
						->setDefault((string) $defaults['ports_per_row'])
				)
				->addField(
					(new CWidgetFieldTextBox('profile'.$p.'_sfp_ports', sprintf(_('Profile %d SFP ports'), $p)))
						->setDefault((string) $defaults['sfp_ports'])
				)
				->addField(
					(new CWidgetFieldTextBox('profile'.$p.'_switch_size', sprintf(_('Profile %d size (%%)'), $p)))
						->setDefault((string) $defaults['switch_size'])
				);
		}

		for ($i = 1; $i <= $total_ports; $i++) {
			$this
				->addField(
					(new CWidgetFieldTextBox('port'.$i.'_name', sprintf(_('Port %d name'), $i)))
						->setDefault(sprintf('Port %d', $i))
				)
				->addField(
					(new CWidgetFieldTextBox('port'.$i.'_triggerid', sprintf(_('Port %d trigger'), $i)))
						->setDefault('')
				)
				->addField(
					(new CWidgetFieldTextBox('port'.$i.'_default_color', sprintf(_('Port %d default color'), $i)))
						->setDefault('#2f855a')
				)
				->addField(
					(new CWidgetFieldTextBox('port'.$i.'_trigger_color', sprintf(_('Port %d trigger color'), $i)))
						->setDefault('#e53e3e')
				);
		}

		return $this;
	}

	private function getRequestedInt(string $key, int $default): int {
		$value = $_REQUEST['fields'][$key] ?? ($_REQUEST[$key] ?? ($this->fields_values[$key] ?? $default));
		if (is_array($value)) {
			$value = reset($value);
		}

		if (is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0) {
			return (int) $value;
		}

		return $default;
	}

	private function clamp(int $value, int $min, int $max): int {
		return max($min, min($max, $value));
	}

	private function getRequestedString(string $key, string $default): string {
		$value = $_REQUEST['fields'][$key] ?? ($_REQUEST[$key] ?? ($this->fields_values[$key] ?? $default));
		if (is_array($value)) {
			$value = reset($value);
		}

		if (is_scalar($value)) {
			$value = trim((string) $value);
			return $value !== '' ? $value : $default;
		}

		return $default;
	}
}
