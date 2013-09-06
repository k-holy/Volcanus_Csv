<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv;

/**
 * 設定クラス
 *
 * @author k.holy74@gmail.com
 */
class Configuration implements \ArrayAccess, \IteratorAggregate, \Countable
{

	/**
	 * @var array 属性の配列
	 */
	private $attributes;

	/**
	 * コンストラクタ
	 *
	 * @param array 属性の配列
	 */
	public function __construct($attributes = array())
	{
		$this->attributes = array();
		if (!empty($attributes)) {
			if (!is_array($attributes) && !($attributes instanceof \Traversable)) {
				throw new \InvalidArgumentException(
					'The attributes is not Array and not Traversable.'
				);
			}
			foreach ($attributes as $name => $value) {
				$this->define($name, $value);
			}
		}
	}

	/**
	 * 属性名および初期値をセットします。
	 *
	 * @param string 属性名
	 * @param mixed 初期値
	 * @return $this
	 */
	public function define($name, $value = null)
	{
		if (array_key_exists($name, $this->attributes)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" already exists.', $name));
		}
		if (method_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" is already defined as a method.', $name)
			);
		}
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($offset, $value)
	{
		if (!array_key_exists($offset, $this->attributes)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset));
		}
		$this->attributes[$offset] = $value;
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if (!array_key_exists($offset, $this->attributes)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset));
		}
		return $this->attributes[$offset];
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($offset)
	{
		if (array_key_exists($offset, $this->attributes)) {
			$this->attributes[$offset] = null;
		}
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->attributes[$offset]);
	}

	/**
	 * magic setter
	 *
	 * @param string 属性名
	 * @param mixed 属性値
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * magic getter
	 *
	 * @param string 属性名
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * magic call method
	 *
	 * @param string
	 * @param array
	 */
	public function __call($name, $args)
	{
		if (array_key_exists($name, $this->attributes)) {
			$value = $this->attributes[$name];
			if (is_callable($value)) {
				return call_user_func_array($value, $args);
			}
		}
		throw new \BadMethodCallException(
			sprintf('Undefined Method "%s" called.', $name)
		);
	}

	/**
	 * __toString
	 */
	public function __toString()
	{
		return var_export(iterator_to_array($this->getIterator()), true);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->attributes);
	}

	/**
	 * Countable::count()
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->attributes);
	}

}
