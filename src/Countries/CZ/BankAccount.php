<?php

namespace Fono\Countries\CZ;

class BankAccount extends \Fono\Fono
{
	const PREG_FILTER = '#^([0-9]{0,6})?\-?([0-9]{1,10})\/([0-9]{4})$#';
}
