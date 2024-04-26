<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

interface Block
{

	public function isEnd(): bool;

	public function isCorrectEnd(Block $block): bool;

}
