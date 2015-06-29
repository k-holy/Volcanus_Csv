<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv;

/**
 * Reader
 *
 * @author k.holy74@gmail.com
 */
class Reader
{

	/**
	 * @var Configuration 設定値のコレクション
	 */
	private $config;

	/**
	 * @var Configuration レコードフィルタのコレクション
	 */
	private $filters;

	/**
	 * @var File 入力対象ファイル
	 */
	private $file;

	/**
	 * @var Parser CSVパーサ
	 */
	private $parser;

	/**
	 * @var int パース済みレコード件数
	 */
	private $parsed;

	/**
	 * @var int フェッチ済みレコード件数
	 */
	private $fetched;

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
	 * オブジェクトを初期化します。
	 *
	 * @param array 設定オプション
	 */
	public function initialize(array $configurations = array())
	{
		$this->config = new Configuration(array(
			'delimiter' => ',',
			'enclosure' => '"',
			'escape'    => '"',
			'inputEncoding'  => mb_internal_encoding(),
			'outputEncoding' => mb_internal_encoding(),
			'parseByPcre'    => true,
		));
		if (!empty($configurations)) {
			foreach ($configurations as $name => $value) {
				$this->config[$name] = $value;
			}
		}
		$this->filters = new Configuration();
		$this->file = null;
		$this->parser = new Parser();
		$this->parsed = 0;
		$this->fetched = 0;
		return $this;
	}

	/**
	 * 引数1の場合は指定された設定の値を返します。
	 * 引数2の場合は指定された設置の値をセットして$thisを返します。
	 *
	 * delimiter       : フィールドの区切り文字 ※1文字のみ対応
	 * enclosure       : フィールドの囲み文字 ※1文字のみ対応
	 * escape          : フィールドに含まれる囲み文字のエスケープ文字 ※1文字のみ対応
	 * inputEncoding   : 入力文字コード（CSVファイルの文字コード）
	 * outputEncoding  : 出力文字コード（データの文字コード）
	 * parseByPcre     : PCRE関数による独自のCSV解析処理を行うかどうか
	 *
	 * str_getcsv() には delimiter と escape に異なる文字を指定しても delimiter が
	 * エスケープ文字として使われるバグがあります。
	 * また、囲み文字のペアが一致していない場合など、不正なCSV文字列の解析結果も
	 * str_getcsv() と parseByPcre() の場合で異なりますが、おおむね後者の方が
	 * 分かりやすい結果を返していると思います。
	 * バグを承知の上で標準関数の方が好ましいのであれば、parseByPcreを無効にしてね。
	 *
	 * @param string 設定名
	 * @return mixed 設定値 または $this
	 */
	public function config($name)
	{
		switch (func_num_args()) {
		case 1:
			return $this->config[$name];
		case 2:
			$value = func_get_arg(1);
			if (isset($value)) {
				switch ($name) {
				case 'delimiter':
				case 'enclosure':
				case 'escape':
					if (!is_string($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts string.', $name));
					}
					if (strlen($value) > 1) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" accepts one character.', $name));
					}
					break;
				case 'parseByPcre':
					if (is_int($value) || ctype_digit($value)) {
						$value = (bool)$value;
					}
					if (!is_bool($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts boolean.', $name));
					}
					break;
				case 'inputEncoding':
				case 'outputEncoding':
					if (!is_string($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts string.', $name));
					}
					break;
				}
				$this->config[$name] = $value;
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 引数1の場合は指定されたインデックスのレコードフィルタを返します。
	 * 引数2の場合は指定されたインデックスのレコードフィルタをセットして$thisを返します。
	 *
	 * @param int      インデックス
	 * @param callable レコードフィルタ
	 * @return mixed 設定値 または $this
	 */
	public function filter($index)
	{
		switch (func_num_args()) {
		case 1:
			return $this->filters->offsetGet($index);
		case 2:
			$filter = func_get_arg(1);
			if (!is_callable($filter)) {
				throw new \InvalidArgumentException(
					sprintf('The fieldFilter for index:%d is not a callable.', $index));
			}
			$this->filters->define($index, $filter);
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * レコードフィルタを追加します。
	 *
	 * @param callable レコードフィルタ
	 * @return $this
	 */
	public function appendFilter($filter)
	{
		$this->filter(count($this->filters), $filter);
		return $this;
	}

	/**
	 * 1レコード分の配列に全てのフィルタを実行した結果を返します。
	 *
	 * @param mixed array | ArrayAccess フィールド配列
	 * @return array 1レコード分の配列
	 */
	public function applyFilters($columns)
	{

		if (!is_array($columns) && !($columns instanceof \ArrayAccess)) {
			throw new \InvalidArgumentException(
				sprintf('The columns accepts an array or Traversable. invalid type:"%s"', gettype($columns)));
		}

		if (count($this->filters) >= 1) {
			foreach ($this->filters->getIterator() as $filter) {
				$columns = $filter($columns);
			}
		}

		return $columns;
	}

	/**
	 * CSV1レコード分の文字列を配列に変換して返します。
	 *
	 * 以下の順に処理されます。
	 * (1)復帰・改行・水平タブ・スペース以外の制御コードを削除
	 * (2)出力エンコーディングへの変換
	 * (3)区切り文字・囲み文字・エスケープ文字を解析して文字列から配列に変換
	 *
	 * @param string CSV1レコード分の文字列
	 * @return array CSV1レコード分の配列
	 */
	public function convert($line)
	{
		$outputEncoding = $this->config->offsetGet('outputEncoding');
		$inputEncoding  = $this->config->offsetGet('inputEncoding');

		$line = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $line);

		if (isset($outputEncoding)) {
			if (!isset($inputEncoding)) {
				$line = mb_convert_encoding($line, $outputEncoding, 'auto');
			} elseif (strcmp($outputEncoding, $inputEncoding) !== 0) {
				$line = mb_convert_encoding($line, $outputEncoding, $inputEncoding);
			}
		}

		$delimiter = $this->config->offsetGet('delimiter');
		$enclosure = $this->config->offsetGet('enclosure');
		$escape = $this->config->offsetGet('escape');

		if (!$this->config->offsetGet('parseByPcre')) {
			return str_getcsv($line, $delimiter, $enclosure, $escape);
		}

		return $this->parser->parse($line, $delimiter, $enclosure, $escape);
	}

	/**
	 * ファイルオブジェクトをセットします。
	 *
	 * @param SplFileObject
	 * @return $this
	 */
	public function setFile($file)
	{
		if (!$file instanceof \SplFileObject) {
			throw new \InvalidArgumentException(
				sprintf('The file accepts a instanceof SplFileObject. invalid type:"%s"', gettype($file)));
		}

		$this->file = $file;
		return $this;
	}

	/**
	 * ファイルオブジェクトを返します。
	 *
	 * @return SplFileObject
	 */
	public function getFile()
	{
		if (!isset($this->file)) {
			throw new \RuntimeException('File is not set.');
		}
		return $this->file;
	}

	/**
	 * 1件分のCSVデータを現在のインデックスから読み込み、パース結果を取得します。
	 * 囲み文字があれば、次の囲み文字までまとめて読み込みます。
	 *
	 * 以下の条件の場合に FALSE を返します。
	 *   処理対象の文字列がない場合
	 *
	 * フィルタが登録されていればフィルタで処理し、結果を返します。
	 *
	 * @return mixed FALSE またはフィルタ処理結果
	 */
	public function parse()
	{

		if (!isset($this->file)) {
			throw new \RuntimeException('File is not set.');
		}

		$enclosure = $this->config->offsetGet('enclosure');
		$endOfLine = false;
		$line = '';

		while (!$endOfLine && !$this->file->eof()) {
			$line .= $this->file->fgets();
			if (substr_count($line, $enclosure) % 2 === 0) {
				$endOfLine = true;
			}
		}

		if ($this->file->eof() && strlen($line) === 0) {
			return false;
		}

		$columns = $this->convert($line);

		$this->parsed++;

		return $columns;
	}

	/**
	 * CSVデータをファイルから読み込み、1件ずつ処理した結果を配列で返します。
	 * パース結果が FALSE の場合は結果を配列に取得しません。
	 *
	 * @return array 処理結果の配列
	 */
	public function fetchAll()
	{

		if (!isset($this->file)) {
			throw new \RuntimeException('File is not set.');
		}

		$this->rewind();

		$fetchedResults = array();

		while (!$this->file->eof()) {
			$columns = $this->parse();
			if ($columns !== false) {
				$columns = $this->applyFilters($columns);
				if ($columns !== false) {
					$fetchedResults[] = $columns;
					$this->fetched++;
				}
			}
		}

		return $fetchedResults;
	}

	/**
	 * パース済みレコードの件数を返します。
	 *
	 * @return int パース済みレコード件数
	 */
	public function getParsed()
	{
		return $this->parsed;
	}

	/**
	 * フェッチ済みレコードの件数を返します。
	 *
	 * @return int フェッチ済みレコード件数
	 */
	public function getFetched()
	{
		return $this->fetched;
	}

	/**
	 * ファイルの読み込み状態を巻き戻します。
	 */
	public function rewind()
	{
		if (!isset($this->file)) {
			throw new \RuntimeException('File is not set.');
		}

		$this->file->rewind();
		$this->parsed = 0;
		$this->fetched = 0;
	}

	/**
	 * magic setter
	 *
	 * @param string 設定名
	 * @param mixed 設定値
	 */
	public function __set($name, $value)
	{
		if (method_exists($this, 'set' . ucfirst($name))) {
			return $this->{'set' . ucfirst($name)}($value);
		}
		$this->config($name, $value);
	}

	/**
	 * magic getter
	 *
	 * @param string 設定名
	 */
	public function __get($name)
	{
		if (method_exists($this, 'get' . ucfirst($name))) {
			return $this->{'get' . ucfirst($name)}();
		}
		return $this->config($name);
	}

}
