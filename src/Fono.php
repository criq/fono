<?php

namespace Fono;

abstract class Fono {

	public $input;

	public function __construct($input) {
		$this->input = $input;
	}

	public function __toString() {
		return $this->input;
	}

	public function validate() {
		return (bool) preg_match(static::PREG_FILTER, $this->getSanitized());
	}

	public function getSanitized() {
		$string = $this->input;

		// Remove all spaces.
		$string = preg_replace('/\s/', null, $string);

		return new static($string);
	}

	public function getFormatted() {
		$string = (string) $this->getSanitized();

		return $string;
	}

}
