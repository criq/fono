<?php

namespace Fono\Countries;

class CZ extends \Fono\Fono {

	const PREG_FILTER = '#^00420[0-9]{9}$#';

	public function getSanitized() {
		$string = $this->input;

		// Remove all spaces, dots and dashes.
		$string = preg_replace('#[\s\.\-]#', null, $string);

		// Replace + with 00.
		$string = preg_replace('#^\+#', '00', $string);

		if (strlen($string) == 9) {
			$string = '00420' . $string;
		}

		return new static($string);
	}

}
