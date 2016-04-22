<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer_File;

class TokenHelper
{

	public static $nameTokenCodes = [
		T_NS_SEPARATOR,
		T_STRING,
	];

	public static $typeKeywordTokenCodes = [
		T_CLASS,
		T_TRAIT,
		T_INTERFACE,
	];

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextNonWhitespace(PHP_CodeSniffer_File $phpcsFile, int $startPointer, int $endPointer = null)
	{
		return self::findNextExcluding($phpcsFile, T_WHITESPACE, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int|integer[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextExcluding(PHP_CodeSniffer_File $phpcsFile, $types, int $startPointer, int $endPointer = null)
	{
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true);
		if ($token === false) {
			return null;
		}
		return $token;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextAnyToken(PHP_CodeSniffer_File $phpcsFile, int $startPointer, int $endPointer = null)
	{
		return self::findNextExcluding($phpcsFile, [], $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPreviousNonWhitespace(PHP_CodeSniffer_File $phpcsFile, int $startPointer, int $endPointer = null)
	{
		return self::findPreviousExcluding($phpcsFile, T_WHITESPACE, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int[]|integer $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPreviousExcluding(PHP_CodeSniffer_File $phpcsFile, $types, int $startPointer, int $endPointer = null)
	{
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
		if ($token === false) {
			return null;
		}
		return $token;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $pointer search starts at this token, inclusive
	 * @return int|null
	 */
	public static function findFirstTokenOnNextLine(PHP_CodeSniffer_File $phpcsFile, int $pointer)
	{
		$newLinePointer = $phpcsFile->findNext(T_WHITESPACE, $pointer, null, false, $phpcsFile->eolChar);
		if ($newLinePointer === false) {
			return null;
		}
		$tokens = $phpcsFile->getTokens();
		return isset($tokens[$newLinePointer + 1]) ? $newLinePointer + 1 : null;
	}

	public static function getContent(PHP_CodeSniffer_File $phpcsFile, int $startPointer, int $endPointer = null): string
	{
		$tokens = $phpcsFile->getTokens();
		$content = '';
		while (true) {
			$pointer = self::findNextAnyToken($phpcsFile, $startPointer, $endPointer);
			if ($pointer === null) {
				break;
			}
			$token = $tokens[$pointer];
			$content .= $token['content'];

			$startPointer = $pointer + 1;
		}

		return $content;
	}

	public static function getLastTokenPointer(PHP_CodeSniffer_File $phpcsFile): int
	{
		$tokenCount = count($phpcsFile->getTokens());
		if ($tokenCount === 0) {
			throw new \SlevomatCodingStandard\Helpers\EmptyFileException($phpcsFile->getFilename());
		}
		return $tokenCount - 1;
	}

}
