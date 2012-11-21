<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv;

use Volcanus\Csv\Configuration;
use Volcanus\Csv\Parser;

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
			'skipHeaderLine' => false,
			'parseByPcre'    => true,
		));
		if (!empty($configurations)) {
			$this->config->parameters($configurations);
		}
		$this->filters = new Configuration();
		$this->file = null;
		$this->parser = new Parser();
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
	 * skipHeaderLine  : ヘッダ行を無視するかどうか
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
			return $this->config->get($name);
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
				case 'skipHeaderLine':
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
				$this->config->set($name, $value);
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
			return $this->filters->get($index);
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
	public function applyFilters($fields)
	{

		if (!is_array($fields) && !($fields instanceof \ArrayAccess)) {
			throw new \InvalidArgumentException(
				sprintf('The fields accepts an array or Traversable. invalid type:"%s"', gettype($fields)));
		}

		if (count($this->filters) >= 1) {
			foreach ($this->filters->getIterator() as $filter) {
				$fields = $filter($fields);
			}
		}

		return $fields;
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
	 * @return mixed CSV1レコード分の配列
	 */
	public function convert($line)
	{
		$outputEncoding = $this->config->get('outputEncoding');
		$inputEncoding = $this->config->get('inputEncoding');

		$line = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $line);

		if (isset($outputEncoding)) {
			if (!isset($inputEncoding)) {
				$line = mb_convert_encoding($line, $outputEncoding, 'auto');
			} elseif (strcmp($outputEncoding, $inputEncoding) !== 0) {
				$line = mb_convert_encoding($line, $outputEncoding, $inputEncoding);
			}
		}

		$delimiter = $this->config->get('delimiter');
		$enclosure = $this->config->get('enclosure');
		$escape = $this->config->get('escape');

		if (!$this->config->get('parseByPcre')) {
			return str_getcsv($line, $delimiter, $enclosure, $escape);
		}

		return $this->parser->parse($line, $delimiter, $enclosure, $escape);
	}

	/**
	 * ファイルを開きます。
	 *
	 * @param string ファイル名
	 * @return object SplFileObject
	 */
	public function open($filename)
	{
		$this->file = new \SplFileObject($filename, 'r+');
		return $this->file;
	}

	/**
	 * ファイルオブジェクトを返します。
	 *
	 * @return SplFileObject
	 */
	public function getFile()
	{
		if (!isset($this->file)) {
			throw new \RuntimeException('File is not open.');
		}
		return $this->file;
	}

	/**
	 * 1件分のCSVデータをファイルから読み込んで処理します。
	 *
	 * @return mixed array | Traversable レコード
	 */
	public function fetch()
	{

		if (!isset($this->file)) {
			throw new \RuntimeException('File is not open.');
		}

		if ($this->config('skipHeaderLine') && $this->file->key() === 0) {
			$this->file->seek(1);
		}

		$record = $this->applyFilters($this->convert($this->file->current()));

		$this->file->next();

		return $record;
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
