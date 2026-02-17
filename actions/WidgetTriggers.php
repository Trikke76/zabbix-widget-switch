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
		return true;
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

		$this->setResponse(new CControllerResponseData([
			'main_block' => json_encode([
				'triggers' => $result
			])
		]));
	}
}
