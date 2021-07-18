<?php

namespace Fono\Countries\CZ;

class PostalCode extends \Fono\Fono
{
	const PREG_FILTER = '#^[0-9]{3}\s?[0-9]{2}$#';

	public function getFormatted()
	{
		return implode(' ', [
			substr($this->input, 0, 3),
			substr($this->input, 3, 2),
		]);
	}
}
