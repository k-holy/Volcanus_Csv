<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv;

/**
 * Parser
 *
 * @author k.holy74@gmail.com
 */
class Parser
{

	/**
	 * CSV1レコード分の文字列を配列に変換して返します。(PCRE正規表現版)
	 *
	 * 正規表現パターンは [Perlメモ] を参考
	 * http://www.din.or.jp/~ohzaki/perl.htm#CSV2Values
	 *
	 * @param string CSV1レコード分の文字列
	 * @param string フィールドの区切り文字
	 * @param string フィールドの囲み文字
	 * @param string フィールドに含まれる囲み文字のエスケープ文字
	 * @return array CSV1レコード分の配列
	 */
	public function parse($line, $delimiter = null, $enclosure = null, $escape = null)
	{

		if (!isset($delimiter)) {
			$delimiter = ',';
		}

		if (!isset($enclosure)) {
			$enclosure = '"';
		}

		if (!isset($escape)) {
			$escape = '"';
		}

		// 行末の復帰・改行を削除し、正規表現パターン簡略化のためデリミタを付与
		$line = preg_replace('/(?:\x0D\x0A|[\x0D\x0A])?$/', $delimiter, rtrim($line, "\x0A\x0D"));

		$delimiter_quoted = preg_quote($delimiter);
		$enclosure_quoted = preg_quote($enclosure);
		$escape_quoted    = preg_quote($escape);

		$line_pattern = sprintf('/(%s[^%s]*(?:%s%s[^%s]*)*%s|[^%s]*)%s/',
			$enclosure_quoted,
			$enclosure_quoted,
			$escape_quoted,
			$enclosure_quoted,
			$enclosure_quoted,
			$enclosure_quoted,
			$delimiter_quoted,
			$delimiter_quoted
		);

		preg_match_all($line_pattern, $line, $matches);

		$field_pattern = sprintf('/^%s(.*)%s$/s',
			$enclosure_quoted, $enclosure_quoted);

		$fields = array();
		foreach ($matches[1] as $value) {
			$fields[] = str_replace($escape . $enclosure,
				$enclosure, preg_replace($field_pattern, '$1', $value));
		}

		return $fields;
	}

}
