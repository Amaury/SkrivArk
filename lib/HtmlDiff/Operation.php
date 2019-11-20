<?php

namespace HtmlDiff;

class Operation {

	public $Action;
	public $StartInOld;
	public $EndInOld;
	public $StartInNew;
	public $EndInNew;

	public function __construct( $action, $startInOld, $endInOld, $startInNew, $endInNew ) {
		$this->Action = $action;
		$this->StartInOld = $startInOld;
		$this->EndInOld = $endInOld;
		$this->StartInNew = $startInNew;
		$this->EndInNew = $endInNew;
	}
}
