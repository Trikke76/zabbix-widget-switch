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
		$profile_defaults = $this->loadProfileDefaults();

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
			(new CWidgetFieldSelect('legend_size', _('Legend size'), [
				12 => _('Small'),
				14 => _('Normal'),
				16 => _('Large'),
				18 => _('Extra large')
			]))->setDefault(14)
		);
			$this->addField(
				(new CWidgetFieldSelect('preset', _('Profile'), [
					0 => _('Custom'),
					1 => $this->getRequestedString('profile1_name', $profile_defaults[1]['name']),
					2 => $this->getRequestedString('profile2_name', $profile_defaults[2]['name']),
					3 => $this->getRequestedString('profile3_name', $profile_defaults[3]['name']),
					4 => $this->getRequestedString('profile4_name', $profile_defaults[4]['name']),
					5 => $this->getRequestedString('profile5_name', $profile_defaults[5]['name']),
					6 => $this->getRequestedString('profile6_name', $profile_defaults[6]['name']),
					7 => $this->getRequestedString('profile7_name', $profile_defaults[7]['name'])
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
					)
					->addField(
						(new CWidgetFieldTextBox('profile'.$p.'_switch_brand', sprintf(_('Profile %d brand'), $p)))
							->setDefault($defaults['switch_brand'])
					)
					->addField(
						(new CWidgetFieldTextBox('profile'.$p.'_switch_model', sprintf(_('Profile %d model'), $p)))
							->setDefault($defaults['switch_model'])
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

	private function loadProfileDefaults(): array {
		$defaults = [
			1 => ['name' => 'Access 24', 'row_count' => 2, 'ports_per_row' => 12, 'sfp_ports' => 2, 'switch_size' => 90, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G'],
			2 => ['name' => 'Access 48', 'row_count' => 4, 'ports_per_row' => 12, 'sfp_ports' => 4, 'switch_size' => 85, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G'],
			3 => ['name' => 'Core 48', 'row_count' => 2, 'ports_per_row' => 24, 'sfp_ports' => 4, 'switch_size' => 80, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G'],
			4 => ['name' => 'Compact 12', 'row_count' => 2, 'ports_per_row' => 6, 'sfp_ports' => 2, 'switch_size' => 70, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G'],
			5 => ['name' => 'User profile 1', 'row_count' => self::DEFAULT_ROW_COUNT, 'ports_per_row' => self::DEFAULT_PORTS_PER_ROW, 'sfp_ports' => 0, 'switch_size' => 100, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G'],
			6 => ['name' => 'User profile 2', 'row_count' => self::DEFAULT_ROW_COUNT, 'ports_per_row' => self::DEFAULT_PORTS_PER_ROW, 'sfp_ports' => 0, 'switch_size' => 100, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G'],
			7 => ['name' => 'User profile 3', 'row_count' => self::DEFAULT_ROW_COUNT, 'ports_per_row' => self::DEFAULT_PORTS_PER_ROW, 'sfp_ports' => 0, 'switch_size' => 100, 'switch_brand' => 'NETSWITCH', 'switch_model' => 'SW-24G']
		];

		$file = __DIR__.'/../profiles.json';
		if (!is_readable($file)) {
			return $defaults;
		}

		$raw = file_get_contents($file);
		if ($raw === false) {
			return $defaults;
		}

		$decoded = json_decode($raw, true);
		if (!is_array($decoded)) {
			return $defaults;
		}

		for ($p = 1; $p <= 7; $p++) {
			if (!isset($decoded[(string) $p]) || !is_array($decoded[(string) $p])) {
				continue;
			}
			$item = $decoded[(string) $p];
				$defaults[$p] = [
					'name' => isset($item['name']) && trim((string) $item['name']) !== '' ? trim((string) $item['name']) : $defaults[$p]['name'],
					'row_count' => isset($item['row_count']) ? $this->clamp((int) $item['row_count'], 1, self::MAX_ROW_COUNT) : $defaults[$p]['row_count'],
					'ports_per_row' => isset($item['ports_per_row']) ? $this->clamp((int) $item['ports_per_row'], 1, self::MAX_PORTS_PER_ROW) : $defaults[$p]['ports_per_row'],
					'sfp_ports' => isset($item['sfp_ports']) ? $this->clamp((int) $item['sfp_ports'], 0, self::MAX_SFP_PORTS) : $defaults[$p]['sfp_ports'],
					'switch_size' => isset($item['switch_size']) ? $this->clamp((int) $item['switch_size'], 40, 100) : $defaults[$p]['switch_size'],
					'switch_brand' => isset($item['switch_brand']) && trim((string) $item['switch_brand']) !== '' ? trim((string) $item['switch_brand']) : $defaults[$p]['switch_brand'],
					'switch_model' => isset($item['switch_model']) && trim((string) $item['switch_model']) !== '' ? trim((string) $item['switch_model']) : $defaults[$p]['switch_model']
				];
			}

		return $defaults;
	}
}
