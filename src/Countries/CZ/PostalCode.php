<?php

namespace Fono\Countries\CZ;

class PostalCode extends \Fono\Fono
{
	const PREG_FILTER = "/^[0-9]{3}\s?[0-9]{2}$/";

	public function getPlain(): string
	{
		return (int)preg_replace("/\s/", "", $this->getSanitized());
	}

	public function getFormatted(): string
	{
		return implode(" ", [
			substr($this->getSanitized(), 0, 3),
			substr($this->getSanitized(), 3, 2),
		]);
	}
}
