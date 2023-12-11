<?php

namespace Fono\Countries\CZ;

class DIC extends \Fono\Fono
{
	public function getRegexFilter(): string
	{
		return "/^CZ([0-9]{8})$/";
	}
}
