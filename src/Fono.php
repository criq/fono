<?php

namespace Fono;

use Katu\Types\TString;

abstract class Fono
{
	abstract public function getRegexFilter(): string;

	protected $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function __toString()
	{
		return $this->getValue();
	}

	public function setValue(string $value): Fono
	{
		$this->value = $value;

		return $this;
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function getIsValid(): bool
	{
		return (bool)preg_match(static::getRegexFilter(), $this->getSanitized());
	}

	public function getSanitized(): string
	{
		$string = new TString($this->getValue());
		$string = $string->getWithNormalizedSpaces();
		$string = preg_replace("/\s/", "", $string);

		return new static($string);
	}

	public function getFormatted(): string
	{
		$string = (string)$this->getSanitized();

		return $string;
	}
}
