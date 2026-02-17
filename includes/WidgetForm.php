<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Includes;

use Zabbix\Widgets\CWidgetForm;
use Zabbix\Widgets\Fields\CWidgetFieldMultiSelectGroup;
use Zabbix\Widgets\Fields\CWidgetFieldMultiSelectHost;
use Zabbix\Widgets\Fields\CWidgetFieldTextBox;

class WidgetForm extends CWidgetForm {
	private const DEFAULT_ROW_COUNT = 2;
	private const DEFAULT_PORTS_PER_ROW = 12;
	private const MAX_ROW_COUNT = 24;
	private const MAX_PORTS_PER_ROW = 48;
	private const MAX_SFP_PORTS = 32;
	private const MAX_TOTAL_PORTS = 256;

	public function addFields(): self {
		$row_count = $this->clamp(
			$this->getRequestedInt('row_count', self::DEFAULT_ROW_COUNT),
			1,
			self::MAX_ROW_COUNT
		);
		$ports_per_row = $this->clamp(
			$this->getRequestedInt('ports_per_row', self::DEFAULT_PORTS_PER_ROW),
			1,
			self::MAX_PORTS_PER_ROW
		);
		$sfp_ports = $this->clamp(
			$this->getRequestedInt('sfp_ports', 0),
			0,
			self::MAX_SFP_PORTS
		);
		$total_ports = min(self::MAX_TOTAL_PORTS, ($row_count * $ports_per_row) + $sfp_ports);

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
		$value = $_REQUEST['fields'][$key] ?? ($_REQUEST[$key] ?? $default);
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
}
