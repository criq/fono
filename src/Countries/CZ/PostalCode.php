<?php

namespace Fono\Countries\CZ;

class PostalCode extends \Fono\Fono
{
	const PREG_FILTER = '/^[0-9]{3}\s?[0-9]{2}$/';

	public function getPlain() : string
	{
		return (int)preg_replace('/\s/', '', $this->getValue());
	}

	public function getFormatted() : string
	{
		return implode(' ', [
			substr($this->getValue(), 0, 3),
			substr($this->getValue(), 3, 2),
		]);
	}
}
