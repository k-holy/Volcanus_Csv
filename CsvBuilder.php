<?php
/**
 * Volcanus\Csv
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv;

use Volcanus\Csv\Configuration;

/**
 * CsvBuilder
 *
 * @author k.holy74@gmail.com
 */
class CsvBuilder
{

	/**
	 * @var Configuration CSVの設定
	 */
	private $config;

	/**
	 * @var Configuration CSVのカラムフィルタ
	 */
	private $columnFilters;

	/**
	 * @var Configuration CSVのカラムラベル
	 */
	private $columnNames;

	/**
	 * @var mixed array or Traversable CSVを作成するレコード
	 */
	private $records;

	/**
	 * @var SplFileObject ファイル
	 */
	private $file;

	/**
	 * constructor
	 *
	 * @param array 設定オプション
	 */
	public function __construct(array $configurations = array())
	{
		$this->initialize($configurations);
	}

	/**
	 * destructor
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param array 設定オプション
	 */
	public function initialize(array $configurations = array())
	{
		$this->config = new Configuration(array(
				'delimiter'      => ',',
				'enclosure'      => '"',
				'escape'         => '"',
				'enclose'        => false,
				'newLine'        => "\r\n",
				'inputEncoding'  => mb_internal_encoding(),
				'outputEncoding' => mb_internal_encoding(),
		));
		if (!empty($configurations)) {
			$this->config->parameters($configurations);
		}
		$this->columnFilters = new Configuration();
		$this->columnNames = new Configuration();
		$this->records = null;
		$this->file = null;
		return $this;
	}

	/**
	 * 引数1の場合は指定された設定の値を返します。
	 * 引数2の場合は指定された設置の値をセットして$thisを返します。
	 *
	 * delimiter      : カラムの区切り文字 ※1文字のみ対応
	 * enclosure      : カラムの囲み文字 ※1文字のみ対応
	 * escape         : カラムに含まれる囲み文字のエスケープ文字 ※1文字のみ対応
	 * enclose        : 出力時に全てのカラムに囲み文字を付与するかどうか
	 * newLine        : 改行文字
	 * inputEncoding  : 入力文字コード（データの文字コード）
	 * outputEncoding : 出力文字コード（CSVファイルの文字コード）
	 *
	 * @param string 設定名
	 * @return mixed 設定値 または $this
	 */
	public function config($name)
	{
		switch (func_num_args()) {
		case 1:
			return $this->config->get($name);
		case 2:
			$value = func_get_arg(1);
			switch ($name) {
			case 'delimiter':
			case 'enclosure':
			case 'escape':
				if (!is_string($value)) {
					throw new \InvalidArgumentException(
						sprintf('The configuration "%s" only accepts string.', $name));
				}
				if (strlen($value) > 1) {
					throw new \InvalidArgumentException(
						sprintf('The configuration "%s" accepts one character.', $name));
				}
				break;
			case 'enclose':
				if (!is_bool($value)) {
					throw new \InvalidArgumentException(
						sprintf('The configuration "%s" only accepts boolean.', $name));
				}
				break;
			case 'newLine':
			case 'inputEncoding':
			case 'outputEncoding':
				if (!is_string($value)) {
					throw new \InvalidArgumentException(
						sprintf('The configuration "%s" only accepts string.', $name));
				}
				break;
			}
			$this->config->set($name, $value);
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 1レコード分の配列をCSV形式の文字列に変換して返します。
	 *
	 * @param array  1レコード分の配列
	 * @return string
	 */
	public function buildLine($columns) 
	{
		$line = '';

		$delimiter = $this->config->get('delimiter');
		$newLine   = $this->config->get('newLine');

		$count = count($columns);
		$index = 0;
		foreach ($columns as $column) {
			$line .= $this->buildColumn($column);
			$index++;
			if ($index < $count) {
				$line .= $delimiter;
			}
		}

		return $line . $newLine;
	}

	/**
	 * 1カラム分の文字列をCSV形式の文字列に変換して返します。
	 *
	 * @param string 1カラム分の文字列
	 * @return string
	 */
	public function buildColumn($column)
	{
		$csv_column = '';

		$delimiter      = $this->config->get('delimiter');
		$enclosure      = $this->config->get('enclosure');
		$escape         = $this->config->get('escape');
		$enclose        = $this->config->get('enclose');
		$outputEncoding = $this->config->get('outputEncoding');
		$inputEncoding  = $this->config->get('inputEncoding');

		if (!$enclose && (
			mb_strpos($column, $delimiter, 0, $inputEncoding) !== false ||
			mb_strpos($column, $enclosure, 0, $inputEncoding) !== false ||
			mb_strpos($column, $escape, 0, $inputEncoding) !== false ||
			mb_strpos($column, "\n", 0, $inputEncoding) !== false ||
			mb_strpos($column, "\r", 0, $inputEncoding) !== false
		)) {
			$enclose = true;
		}

		if ($enclose) {
			$char_length = mb_strlen($column, $inputEncoding);
			for ($char_index = 0; $char_index < $char_length; $char_index++) {
				$char = mb_substr($column, $char_index, 1, $inputEncoding);
				if (strcmp($char, $enclosure) === 0) {
					$csv_column .= $escape;
				}
				$csv_column .= $char;
			}
			$csv_column = $enclosure . $csv_column . $enclosure;
		} else {
			$csv_column = $column;
		}

		if (isset($outputEncoding)) {
			if (!isset($inputEncoding)) {
				$csv_column = mb_convert_encoding($csv_column, $outputEncoding, 'auto');
			} elseif (strcmp($outputEncoding, $inputEncoding) !== 0) {
				$csv_column = mb_convert_encoding($csv_column, $outputEncoding, $inputEncoding);
			}
		}

		return $csv_column;
	}

	/**
	 * CSVのカラムを設定します。
	 *
	 * @param int    カラムインデックス
	 * @param string カラム名
	 * @param callable カラムの値を生成するコールバック
	 * @return $this
	 */
	public function column($index, $name, $filter = null)
	{
		$this->columnName($index, $name);
		if (isset($filter)) {
			$this->columnFilter($index, $filter);
		}
		return $this;
	}

	/**
	 * 引数1の場合は指定されたカラムインデックスの名前を返します。
	 * 引数2の場合は指定されたカラムインデックスの名前をセットして$thisを返します。
	 *
	 * @param int    カラムインデックス
	 * @param string カラム名
	 * @return mixed 設定値 または $this
	 */
	public function columnName($index)
	{
		switch (func_num_args()) {
		case 1:
			return $this->columnNames->get($index);
		case 2:
			$name = func_get_arg(1);
			if (!is_string($name)) {
				throw new \InvalidArgumentException(
					sprintf('The columnName for index:%d is not a string.', $index));
			}
			$this->columnNames->initParameter($index, $name);
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 引数1の場合は指定されたカラムインデックスのフィルタを返します。
	 * 引数2の場合は指定されたカラムインデックスのフィルタをセットして$thisを返します。
	 *
	 * @param int      カラムインデックス
	 * @param callable カラムフィルタ
	 * @return mixed 設定値 または $this
	 */
	public function columnFilter($index)
	{
		switch (func_num_args()) {
		case 1:
			return $this->columnFilters->get($index);
		case 2:
			$filter = func_get_arg(1);
			if (!is_callable($filter)) {
				throw new \InvalidArgumentException(
					sprintf('The columnFilter for index:%d is not a callable.', $index));
			}
			$this->columnFilters->initParameter($index, $filter);
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * カラムの設定を元に1レコード分のデータを配列に変換して返します。
	 *
	 * @param mixed array | ArrayAccess 1レコード分のデータ
	 * @return array 1レコード分の配列
	 */
	public function buildColumns($record)
	{

		if (!is_array($record) && !($record instanceof \ArrayAccess)) {
			throw new \InvalidArgumentException(
				sprintf('The record accepts an array or Traversable. invalid type:"%s"', gettype($record)));
		}

		$columns = array();
		foreach ($this->columnNames->keys() as $index) {
			$column = null;
			if ($this->columnFilters->has($index)) {
				$filter = $this->columnFilters->get($index);
				$column = $filter($record);
			} else {
				$column = $record[$index];
			}
			$columns[] = $column;
		}
		return $columns;
	}

	/**
	 * カラムのヘッダ行をCSV形式の文字列で返します。
	 *
	 * @return string
	 */
	public function buildHeaderLine()
	{
		return $this->buildLine($this->columnNames->getIterator());
	}

	/**
	 * 引数0の場合はレコードを返します。
	 * 引数1の場合は指定されたレコードをセットして$thisを返します。
	 *
	 * @param mixed array | Traversable レコード
	 * @return mixed レコード または $this
	 */
	public function records()
	{
		switch (func_num_args()) {
		case 0:
			return $this->records;
		case 1:
			$records = func_get_arg(0);
			if (!is_array($records) && !($records instanceof \Traversable)) {
				throw new \InvalidArgumentException(
					sprintf('The records accepts an array or Traversable. invalid type:"%s"', gettype($records)));
			}
			$this->records = $records;
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * CSVデータを書き込むためのファイルを開きます。
	 *
	 * @param string ファイルパス または ファイルプロトコル
	 * @return object SplFileObject
	 */
	public function open($filename = 'php://temp')
	{
		$this->file = new \SplFileObject($filename, 'r+');
		$this->file->setFlags(\SplFileObject::READ_CSV);
		$this->file->setCsvControl(
			$this->config->get('delimiter'),
			$this->config->get('enclosure'),
			$this->config->get('escape'));
		$this->file->flock(LOCK_EX);
		return $this->file;
	}

	/**
	 * ファイルを解放します。
	 *
	 * @return object SplFileObject
	 */
	public function close()
	{
		if (isset($this->file)) {
			$this->file->flock(LOCK_UN);
		}
		return $this->file;
	}

	/**
	 * ファイルに書き込まれたCSVデータのバイト数を返します。
	 *
	 * @return int CSVデータのバイト数
	 */
	public function contentLength()
	{
		if (!isset($this->file)) {
			throw new \RuntimeException('CSV file is not open.');
		}
		$status = $this->file->fstat();
		return $status['size'];
	}

	/**
	 * データをCSVに変換してファイルに出力します。
	 *
	 * @param string ファイルパス または ファイルプロトコル
	 * @param mixed array | Traversable レコード
	 * @return $this
	 */
	public function write($filename = null, $records = null)
	{

		if (isset($records)) {
			$this->records($records);
		}

		if (isset($filename)) {
			$this->open($filename);
		}

		if (!isset($this->records)) {
			throw new \RuntimeException('Records for build CSV is not set.');
		}

		if (!isset($this->file)) {
			throw new \RuntimeException('File for build CSV is not open.');
		}

		foreach ($this->records as $record) {
			$this->file->fwrite($this->buildLine($this->buildColumns($record)));
		}
		return $this;
	}

	/**
	 * magic setter
	 *
	 * @param string 設定名
	 * @param mixed 設定値
	 */
	public function __set($name, $value)
	{
		$this->config($name, $value);
	}

	/**
	 * magic getter
	 *
	 * @param string 設定名
	 */
	public function __get($name)
	{
		return $this->config($name);
	}

}
