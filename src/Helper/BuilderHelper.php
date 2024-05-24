<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Helper;

use WebChemistry\Setup\ContentBuilder;

final class BuilderHelper
{

	public static function flushMultilineComments(ContentBuilder $builder, int $numberOfNewLinesAfter = 1): void
	{
		self::flushCustomMultilineComments($builder, '/**', ' * ', ' */', true, $numberOfNewLinesAfter);
	}

	public static function flushCustomMultilineComments(
		ContentBuilder $builder,
		string $start,
		string $forEach,
		string $end,
		bool $allowInline = false,
		int $numberOfNewLinesAfter = 1,
	): void
	{
		$comments = $builder->getCommentsAndFlush();

		if (!$comments) {
			return;
		}

		$builder->append($start);

		if ($allowInline && count($comments) === 1) {
			$builder->append(' ' . $comments[0]);

		} else {
			$builder->newLineIfNotExists();

			foreach ($comments as $comment) {
				$builder->append($forEach . $comment);
				$builder->newLine();
			}
		}

		$builder->append($end);

		if ($numberOfNewLinesAfter > 0) {
			$builder->newLine($numberOfNewLinesAfter);
		}
	}

	public static function flushCustomLineComments(
		ContentBuilder $builder,
		string $forEach,
		int $numberOfNewLinesAfter = 1,
	): void
	{
		$comments = $builder->getCommentsAndFlush();

		if (!$comments) {
			return;
		}

		$last = array_key_last($comments);

		foreach ($comments as $i => $comment) {
			$builder->append($forEach . $comment);

			if ($last !== $i) {
				$builder->newLine();
			}
		}

		if ($numberOfNewLinesAfter > 0) {
			$builder->newLine($numberOfNewLinesAfter);
		}
	}

	public static function countNewLines(string $str): int
	{
		$length = strlen($str);
		$count = 0;

		while ($length > 0) {
			$char = $str[$length - 1];

			if ($char === "\n") {
				$count++;
			} else if ($char !== "\r") {
				break;
			}

			$length--;
		}

		return $count;
	}

}
