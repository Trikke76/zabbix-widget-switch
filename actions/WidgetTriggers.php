<?php declare(strict_types = 1);

namespace Modules\SwitchWidget\Actions;

use API;
use CController;
use CControllerResponseData;

class WidgetTriggers extends CController {
	protected function init(): void {
		$this->disableCsrfValidation();
	}

	protected function checkInput(): bool {
		return $this->validateInput([
			'hostid' => 'required|id'
		]);
	}

	protected function checkPermissions(): bool {
		return $this->getUserType() >= USER_TYPE_ZABBIX_USER;
	}

	protected function doAction(): void {
		$hostid = (string) $this->getInput('hostid');

		$rows = API::Trigger()->get([
			'output' => ['triggerid', 'description'],
			'hostids' => [$hostid],
			'filter' => ['status' => 0],
			'sortfield' => 'description'
		]);

		$result = [];
		foreach ($rows as $row) {
			$result[] = [
				'id' => (string) $row['triggerid'],
				'name' => (string) $row['description']
			];
		}

		// Fallback for hosts that only have trigger prototypes (LLD) and no resolved triggers yet.
		if ($result === []) {
			$prototype_rows = API::TriggerPrototype()->get([
				'output' => ['triggerid', 'description'],
				'hostids' => [$hostid]
			]);

			foreach ($prototype_rows as $row) {
				$description = (string) $row['description'];
				// Skip unresolved template-style prototypes with LLD macros.
				if (strpos($description, '{#') !== false) {
					continue;
				}

				$result[] = [
					'id' => (string) $row['triggerid'],
					'name' => '[Prototype] '.$description
				];
			}
		}

		$this->setResponse(new CControllerResponseData([
			'main_block' => json_encode([
				'triggers' => $result
			])
		]));
	}
}
