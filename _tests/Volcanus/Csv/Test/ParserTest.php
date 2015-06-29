<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv\Test;

use Volcanus\Csv\Parser;

/**
 * ParserTest
 *
 * @author k.holy74@gmail.com
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{

	public function testParse()
	{
		$parser = new Parser();
		$this->assertEquals(array('1', '田中'),
			$parser->parse("1,田中\r\n"));
	}

	public function testParseEncloseDelimiter()
	{
		$parser = new Parser();
		$this->assertEquals(array('1', '田中,'),
			$parser->parse('1,"田中,"'));
	}

	public function testParseEscapedEnclosure()
	{
		$parser = new Parser();
		$this->assertEquals(array('1', '田中"'),
			$parser->parse('1,"田中"""'));
	}

	public function testParseEnclosedCarriageReturnAndLineFeed()
	{
		$parser = new Parser();
		$this->assertEquals(array('1', "田中\r\n"),
			$parser->parse(("1,\"田中\r\n\"")));
	}

	public function testParseOnlySpaceField()
	{
		$parser = new Parser();
		$this->assertEquals(array(' ', '1', '田中'),
			$parser->parse(' ,1,田中'));
	}

	public function testParseNotClosedEnclosure()
	{
		$parser = new Parser();
		$this->assertEquals(array('"', '1', '田中'),
			$parser->parse('",1,田中'));

	}

	public function testParseNotOpenedEnclosure()
	{
		$parser = new Parser();
		$this->assertEquals(array('', '"', '1', '田中'),
			$parser->parse(',","1","田中"'));
	}

	public function testParseNotClosedEnclosureAndSpaceBeforeDelimiter()
	{
		$parser = new Parser();
		$this->assertEquals(array('" ', '"1" ', '田中"'),
			$parser->parse('" ,"1" ,"田中""'));
	}

	public function testParseNotClosedEnclosureAndSpaceAfterDelimiter()
	{
		$parser = new Parser();
		$this->assertEquals(array('"', ' "1"', ' "田中"'),
			$parser->parse('", "1", "田中""'));

	}

	public function testParseTabSeparatedValues()
	{
		$parser = new Parser();
		$this->assertEquals(array('1', '田中'),
			$parser->parse("1\t\"田中\"", "\t", '"'));
	}

	public function testParseTabSeparatedValuesAndEscapedEnclosure()
	{
		$parser = new Parser();
		$this->assertEquals(array('1', '田中"'),
			$parser->parse("1\t\"田中\\\"\"", "\t", '"', '\\'));
	}

}
