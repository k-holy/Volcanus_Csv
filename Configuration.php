<?php
/**
 * Volcanus\Csv
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv;

/**
 * Configuration
 *
 * @author k.holy74@gmail.com
 */
class Configuration implements \IteratorAggregate
{

	/**
	 * @var array 設定オプション
	 */
	private $parameters;

	/**
	 * constructor
	 *
	 * @param array 設定オプション
	 */
	public function __construct(array $parameters = array())
	{
		$this->initialize($parameters);
	}

	/**
	 * 設定を初期化します。
	 *
	 * @param array 設定オプション
	 * @return $this
	 */
	public function initialize(array $parameters = array())
	{
		$this->parameters = array();
		if (!empty($parameters)) {
			foreach ($parameters as $name => $value) {
				$this->define($name, $value);
			}
		}
		return $this;
	}

	/**
	 * 設定名および初期値をセットします。
	 *
	 * @param string 設定名
	 * @param mixed 初期値値
	 */
	public function define($name, $value = null)
	{
		$this->parameters[$name] = $value;
		return $this;
	}

	/**
	 * 引数なしの場合は全ての設定を配列で返します。
	 * 引数ありの場合は全ての設定を引数の配列からセットして$thisを返します。
	 *
	 * @param array 設定
	 * @return mixed 設定 または $this
	 */
	public function parameters()
	{
		switch (func_num_args()) {
		case 0:
			return $this->parameters;
		case 1:
			$parameters = func_get_arg(0);
			if (!is_array($parameters)) {
				throw new \InvalidArgumentException(
					'The parameters is not Array.');
			}
			foreach ($parameters as $name => $value) {
				$this->set($name, $value);
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 設定名を配列で返します。
	 *
	 * @return array 設定名の配列
	 */
	public function keys()
	{
		return array_keys($this->parameters);
	}

	/**
	 * 設定値を配列で返します。
	 *
	 * @return array 設定値の配列
	 */
	public function values()
	{
		return array_values($this->parameters);
	}

	/**
	 * 指定された設定の値をセットします。
	 *
	 * @param string 設定名
	 * @param mixed 設定値
	 */
	public function set($name, $value)
	{
		if (!array_key_exists($name, $this->parameters)) {
			throw new \InvalidArgumentException(
				sprintf('The configuration "%s" does not exists.', $name));
		}
		$this->parameters[$name] = $value;
	}

	/**
	 * 指定された設定の値を返します。
	 *
	 * @param string 設定名
	 * @return mixed 設定値
	 */
	public function get($name)
	{
		if (!array_key_exists($name, $this->parameters)) {
			throw new \InvalidArgumentException(
				sprintf('The configuration "%s" does not exists.', $name));
		}
		return $this->parameters[$name];
	}

	/**
	 * 指定された設定の値がセットされているかどうかを返します。
	 *
	 * @param string 設定名
	 * @return bool 値がセットされているかどうか
	 */
	public function has($name)
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * magic setter
	 *
	 * @param string 設定名
	 * @param mixed 設定値
	 */
	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	/**
	 * magic getter
	 *
	 * @param string 設定名
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->parameters);
	}

}
