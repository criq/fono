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

	static function getCountryClass($country) {
		return "\\Fono\\Countries\\" . strtoupper($country);
	}

	static function validate($country, $input) {
		try {

			$class = static::getCountryClass($country);
			if (class_exists($class)) {
				return (new $class($input))->isValid();
			}

			throw new \Exception;

		} catch (\Exception $e) {

			return null;

		}
	}

	static function sanitize($country, $input) {
		try {

			$class = static::getCountryClass($country);
			if (class_exists($class)) {
				return (new $class($input))->getSanitized();
			}

			throw new \Exception;

		} catch (\Exception $e) {

			return null;

		}
	}

	public function isValid() {
		return (bool) preg_match(static::PREG_FILTER, $this->getSanitized());
	}

	abstract function getSanitized();
	abstract function getFormatted();

}
