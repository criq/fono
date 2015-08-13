<?php

namespace Fono;

class Fono {

	public $input;

	public function __construct($input) {
		$this->input = $input;
	}

	public function __toString() {
		return $this->input;
	}

	static function validate($country, $input) {
		try {

			$class = "\\Fono\\Countries\\" . strtoupper($country);
			if (class_exists($class)) {
				return (new $class($input))->isValid();
			}

			throw new \Exception;

		} catch (\Exception $e) {

			return null;

		}
	}

	public function isValid() {
		return (bool) preg_match(static::PREG_FILTER, $this->sanitize());
	}

}
