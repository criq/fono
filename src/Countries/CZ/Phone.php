<?php

namespace Fono\Countries\CZ;

class Phone extends \Fono\Fono
{
	const PREG_FILTER = '/^00420[0-9]{9}$/';

	public function getSanitized() : string
	{
		$string = $this->getValue();

		// Remove all spaces, dots and dashes.
		$string = preg_replace('#[\s\.\-]#u', '', $string);

		// Replace + with 00.
		$string = preg_replace('#^\+#u', '00', $string);

		if (strlen($string) == 9) {
			$string = '00420' . $string;
		}

		return new static($string);
	}

	public function getPlain() : string
	{
		$string = (string)$this->getSanitized();

		// Is 00420 and 9 digits.
		if (preg_match('#^00420(?<phone>[0-9]{9})$#', $string, $match)) {
			return $match['phone'];
		}

		return $string;
	}

	public function getFormatted() : string
	{
		$string = (string)$this->getSanitized();

		// Is 00420 and 9 digits.
		if (preg_match('#^00420(?<phone>[0-9]{9})$#', $string, $match)) {
			return implode(' ', str_split($match['phone'], 3));
		}

		return $string;
	}
}
