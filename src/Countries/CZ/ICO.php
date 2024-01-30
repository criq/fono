<?php

namespace Fono\Countries\CZ;

class ICO extends \Fono\Fono
{
	public function getRegexFilter(): string
	{
		return "/^([0-9]{8})$/";
	}
}
