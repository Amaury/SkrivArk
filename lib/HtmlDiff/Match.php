<?php

namespace HtmlDiff;

class Match {

	public $StartInOld;
	public $StartInNew;
	public $Size;

	public function __construct( $startInOld, $startInNew, $size ) {
		$this->StartInOld = $startInOld;
		$this->StartInNew = $startInNew;
		$this->Size = $size;
	}

	public function EndInOld() {
		return $this->StartInOld + $this->Size;
	}

	public function EndInNew() {
		return $this->StartInNew + $this->Size;
	}

	public function count() {
		return (int)$this->Size;
	}
}

