<?php


namespace PixalsCore\CoreHandlers\FloatingTextHandlers;



final class FloatingText {
    public function __construct(
		readonly public string $world,
		readonly public float $x,
		readonly public float $y,
		readonly public float $z,
		readonly public string $line
	){}
}