<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv;

/**
 * Builder
 *
 * @author k.holy74@gmail.com
 */
class Builder
{

	/**
	 * 1レコード分のフィールド配列をCSV形式の文字列に変換して返します。
	 *
	 * @param mixed array|Traversable フィールドの配列
	 * @param string フィールドの区切り文字
	 * @param string フィールドの囲み文字
	 * @param string フィールドに含まれる囲み文字のエスケープ文字
	 * @param bool   出力時に全てのフィールドに囲み文字を付与するかどうか
	 * @param string 文字コード
	 * @return string CSVの1レコード分の文字列
	 */
	public function build($fields, $delimiter = null, $enclosure = null, $escape = null, $enclose = null, $encoding = null)
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

		if (!isset($enclose)) {
			$enclose = false;
		}

		if (!isset($encoding)) {
			$encoding = mb_internal_encoding();
		}

		$line = '';

		$count = count($fields);
		$index = 0;

		foreach ($fields as $field) {
			$line .= $this->buildField($field, $delimiter, $enclosure, $escape, $enclose, $encoding);
			$index++;
			if ($index < $count) {
				$line .= $delimiter;
			}
		}

		return $line;
	}

	/**
	 * フィールドの文字列をCSV形式の文字列に変換して返します。
	 *
	 * @param string フィールドの文字列
	 * @param string フィールドの区切り文字
	 * @param string フィールドの囲み文字
	 * @param string フィールドに含まれる囲み文字のエスケープ文字
	 * @param bool   出力時に全てのフィールドに囲み文字を付与するかどうか
	 * @param string 文字コード
	 * @return string CSVの1フィールド分の文字列
	 */
	public function buildField($field, $delimiter = null, $enclosure = null, $escape = null, $enclose = null, $encoding = null)
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

		if (!isset($enclose)) {
			$enclose = false;
		}

		if (!isset($encoding)) {
			$encoding = mb_internal_encoding();
		}

		$csv_field = '';

		if (!$enclose && (
			(mb_strlen($escape) >= 1 && mb_strpos($field, $escape, 0, $encoding) !== false) ||
			(mb_strlen($enclosure) >= 1 && mb_strpos($field, $enclosure, 0, $encoding) !== false) ||
			(mb_strlen($delimiter) >= 1 && mb_strpos($field, $delimiter, 0, $encoding) !== false) ||
			mb_strpos($field, "\n", 0, $encoding) !== false ||
			mb_strpos($field, "\r", 0, $encoding) !== false
		)) {
			$enclose = true;
		}

		if ($enclose) {
			$char_length = mb_strlen($field, $encoding);
			for ($char_index = 0; $char_index < $char_length; $char_index++) {
				$char = mb_substr($field, $char_index, 1, $encoding);
				if (strcmp($char, $enclosure) === 0) {
					$csv_field .= $escape;
				}
				$csv_field .= $char;
			}
			$csv_field = $enclosure . $csv_field . $enclosure;
		} else {
			$csv_field = $field;
		}

		return $csv_field;
	}

}
