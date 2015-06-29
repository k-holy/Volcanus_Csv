<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv\Test;

use Volcanus\Csv\Builder;

/**
 * BuilderTest
 *
 * @author k.holy74@gmail.com
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

	public function testBuild()
	{
		$builder = new Builder();

		$this->assertEquals("1,田中",
			$builder->build(array('1', '田中')));
	}

	public function testBuildIncludesDelimiter()
	{
		$builder = new Builder();

		$this->assertEquals("1,\"田中,\"",
			$builder->build(array('1', '田中,')));
	}

	public function testBuildWithEnclose()
	{
		$builder = new Builder();

		$this->assertEquals("\"1\",\"田中\"",
			$builder->build(array('1', '田中'), null, null, null, true));
	}

	public function testBuildField()
	{
		$builder = new Builder();

		$this->assertEquals("田中",
			$builder->buildField('田中'));
	}

	public function testBuildFieldIncludesDelimiter()
	{
		$builder = new Builder();

		$this->assertEquals("\"田中,\"",
			$builder->buildField('田中,'));
	}

	public function testBuildFieldIncludesCarriageReturnAndLineFeed()
	{
		$builder = new Builder();

		$this->assertEquals("\"田中\r\n\r\n以上\"",
			$builder->buildField("田中\r\n\r\n以上"));
	}

	public function testBuildFieldIncludesCarriageReturn()
	{
		$builder = new Builder();

		$this->assertEquals("\"田中\r\r以上\"",
			$builder->buildField("田中\r\r以上"));
	}

	public function testBuildFieldIncludesLineFeed()
	{
		$builder = new Builder();

		$this->assertEquals("\"田中\n\n以上\"",
			$builder->buildField("田中\n\n以上"));
	}

	public function testBuildFieldEscapeIncludesEnclosure()
	{
		$builder = new Builder();

		$this->assertEquals('"田中"""',
			$builder->buildField('田中"'));

		$this->assertEquals('"田中\""',
			$builder->buildField('田中"', null, null, '\\'));
	}

	public function testBuildFieldEscapeIncludesRepetitionOfEnclosure()
	{
		$builder = new Builder();

		$this->assertEquals('"""田""中"""""',
			$builder->buildField('"田"中""'));

		$this->assertEquals('"\"田\"中\"\""',
			$builder->buildField('"田"中""', null, null, '\\'));
	}

	public function testBuildFieldNoEscapeWhenEscapeParameterIsEmpty()
	{
		$builder = new Builder();

		$this->assertEquals('田中',
			$builder->buildField('田中', "\t", '', ''));

		$this->assertEquals("田中\t",
			$builder->buildField("田中\t", "\t", '', ''));
	}


}
