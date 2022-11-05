<?php

namespace Fono\Countries\SK;

class Phone extends \Fono\Countries\CZ\Phone
{
	public function getIntlPrefix(): string
	{
		return "421";
	}
}
