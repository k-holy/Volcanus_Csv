<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv\Tests;

use Volcanus\Csv\Reader;
use Volcanus\Csv\File;

/**
 * ReaderTest
 *
 * @author k.holy74@gmail.com
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{

	public function testDefaultConfigParameter()
	{
		$reader = new Reader();
		$this->assertEquals(',', $reader->delimiter);
		$this->assertEquals('"', $reader->enclosure);
		$this->assertEquals('"', $reader->escape);
		$this->assertEquals(mb_internal_encoding(), $reader->inputEncoding);
		$this->assertEquals(mb_internal_encoding(), $reader->outputEncoding);
		$this->assertFalse($reader->skipHeaderLine);
		$this->assertTrue($reader->parseByPcre);
	}

	public function testConstructWithConfigParameters()
	{
		$reader = new Reader(array(
			'delimiter'       => "\t",
			'enclosure'       => "'",
			'escape'          => '\\',
			'inputEncoding'   => 'SJIS-win',
			'outputEncoding'  => 'EUC-JP',
			'skipHeaderLine'  => true,
			'parseByPcre'     => false,
		));
		$this->assertEquals("\t", $reader->delimiter);
		$this->assertEquals("'" , $reader->enclosure);
		$this->assertEquals('\\', $reader->escape);
		$this->assertEquals('SJIS-win', $reader->inputEncoding);
		$this->assertEquals('EUC-JP', $reader->outputEncoding);
		$this->assertTrue($reader->skipHeaderLine);
		$this->assertFalse($reader->parseByPcre);
	}


	public function testSetConfig()
	{
		$reader = new Reader();
		$reader->config('delimiter', "\t");
		$this->assertEquals("\t", $reader->delimiter);
	}

	public function testGetConfig()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$this->assertEquals("\t", $reader->config('delimiter'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetConfigRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->config('NOT-DEFINED-CONFIG', true);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetConfigRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->config('NOT-DEFINED-CONFIG');
	}

	public function testSetDelimiter()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$this->assertEquals("\t", $reader->delimiter);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$reader = new Reader();
		$reader->delimiter = ',,';
	}

	public function testSetEnclosure()
	{
		$reader = new Reader();
		$reader->enclosure = "'";
		$this->assertEquals("'", $reader->enclosure);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->enclosure = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$reader = new Reader();
		$reader->enclosure = '""';
	}

	public function testSetEscape()
	{
		$reader = new Reader();
		$reader->escape = '\\';
		$this->assertEquals('\\', $reader->escape);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->escape = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$reader = new Reader();
		$reader->escape = '\\\\';
	}

	public function testSetInputEncoding()
	{
		$reader = new Reader();
		$reader->inputEncoding = 'SJIS-win';
 		$this->assertEquals('SJIS-win', $reader->inputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetInputEncodingRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->inputEncoding = array();
	}

	public function testSetOutputEncoding()
	{
		$reader = new Reader();
		$reader->outputEncoding = 'EUC-JP';
 		$this->assertEquals('EUC-JP', $reader->outputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetOutputEncodingRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->outputEncoding = array();
	}

	public function testSetSkipHeaderLine()
	{
		$reader = new Reader();
		$reader->skipHeaderLine = true;
 		$this->assertTrue($reader->skipHeaderLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetSkipHeaderLineRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->skipHeaderLine = 'true';
	}

	public function testSetParseByPcre()
	{
		$reader = new Reader();
		$reader->parseByPcre = true;
 		$this->assertTrue($reader->parseByPcre);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetParseByPcreRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->parseByPcre = 'true';
	}

	public function testFilter()
	{
		$reader = new Reader();
		$filter1 = function($item) {
			$item[0] = sprintf('%02d', $item[0]);
			return $item;
		};
		$filter2 = function($item) {
			$user = new \Stdclass();
			$user->id = $item[0];
			return $user;
		};
		$reader->filter(0, $filter1);
		$reader->filter(1, $filter2);
		$this->assertEquals($filter1, $reader->filter(0));
		$this->assertEquals($filter2, $reader->filter(1));
	}

	public function testAppendFilter()
	{
		$reader = new Reader();
		$filter1 = function($item) {
			$item[0] = sprintf('%02d', $item[0]);
			return $item;
		};
		$filter2 = function($item) {
			$user = new \Stdclass();
			$user->id = $item[0];
			return $user;
		};
		$reader->appendFilter($filter1);
		$this->assertEquals($filter1, $reader->filter(0));
		$reader->appendFilter($filter2);
		$this->assertEquals($filter2, $reader->filter(1));
	}

	public function testBuildRecord()
	{
		$reader = new Reader();
		$reader->filter(0, function($item) {
			$user = new \Stdclass();
			$user->id        = $item[0];
			$user->surname   = $item[1];
			$user->firstname = $item[2];
			$user->age       = $item[3];
			return $user;
		});
		$user = $reader->buildRecord(array('1', '田中', '一郎', '22'));
		$this->assertEquals('1'   , $user->id);
		$this->assertEquals('田中', $user->surname);
		$this->assertEquals('一郎', $user->firstname);
		$this->assertEquals('22'  , $user->age);
	}

	public function testConvertCarriageReturnAndLineFeedAtEndOfLine()
	{
		$reader = new Reader();
		$line = '1,田中' . "\r\n";
		$this->assertEquals(array('1', '田中'),
			$reader->convert($line));
	}

	public function testConvertLineFeedAtEndOfLine()
	{
		$reader = new Reader();
		$line = '1,田中' . "\n";
		$this->assertEquals(array('1', '田中'),
			$reader->convert($line));
	}

	public function testConvertCarriageReturnAtEndOfLine()
	{
		$reader = new Reader();
		$line = '1,田中' . "\r";
		$this->assertEquals(array('1', '田中'),
			$reader->convert($line));
	}

	public function testConvertNoCarriageReturnAndLineFeedAtEndOfLine()
	{
		$reader = new Reader();
		$line = <<< LINE
1,田中
LINE;
		$this->assertEquals(array('1', '田中'),
			$reader->convert($line));
	}

	public function testConvertNullByteAtEndOfLine()
	{
		$reader = new Reader();
		$line = '1,田中' . "\x00";
		$this->assertEquals(array('1', '田中'),
			$reader->convert($line));
	}

	public function testConvertIncludesDelimiter()
	{
		$reader = new Reader();
		$line = <<< LINE
1,"田中,"
LINE;
		$this->assertEquals(array('1', '田中,'),
			$reader->convert($line));
	}

	public function testConvertIncludesEscapedEnclosure()
	{
		$reader = new Reader();
		$line = <<< LINE
1,"田中"""
LINE;
		$this->assertEquals(array('1', '田中"'),
			$reader->convert($line));
	}

	public function testConvertIncludesCarriageReturnAndLineFeed()
	{
		$reader = new Reader();
		$line = sprintf('1,"田中%s%s"%s', "\r\n", "\r\n", "\r\n");
		$this->assertEquals(array('1', "田中\r\n\r\n"),
			$reader->convert($line));
	}

	public function testConvertIncludesCarriageReturn()
	{
		$reader = new Reader();
		$line = sprintf('1,"田中%s%s"%s', "\r", "\r", "\r\n");
		$this->assertEquals(array('1', "田中\r\r"),
			$reader->convert($line));
	}

	public function testConvertIncludesLineFeed()
	{
		$reader = new Reader();
		$line = sprintf('1,"田中%s%s"%s', "\n", "\n", "\r\n");
		$this->assertEquals(array('1', "田中\n\n"),
			$reader->convert($line));
	}

	public function testConvertSpaceAtHeadOfLine()
	{
		$reader = new Reader();
		$line = <<< LINE
 ,1,田中
LINE;
		$this->assertEquals(array(' ', '1', '田中'),
			$reader->convert($line));
	}

	public function testConvertUnclosedEnclosureAtHeadOfLine()
	{
		$reader = new Reader();
		$line = <<< LINE
",1,田中
LINE;
		// parseByPcreオプションによって結果が異なる
		$reader->parseByPcre = true;
		$this->assertEquals(array('"', '1', '田中'),
			$reader->convert($line));

		$reader->parseByPcre = false;
		$this->assertEquals(array(',1,田中'),
			$reader->convert($line));

	}

	public function testConvertUnOpenedEnclosure()
	{
		$reader = new Reader();
		$line = <<< LINE
,","1","田中"
LINE;
		// parseByPcreオプションによって結果が異なる
		$reader->parseByPcre = true;
		$this->assertEquals(array('', '"', '1', '田中'),
			$reader->convert($line));

		$reader->parseByPcre = false;
		$this->assertEquals(array('', ',1"', '田中'),
			$reader->convert($line));
	}

	public function testConvertUnclosedEnclosureAtHeadOfLineEncloseAll()
	{
		$reader = new Reader();
		$line = <<< LINE
","1"","田中"
LINE;
		// parseByPcreオプションによって結果が異なる
		$reader->parseByPcre = true;
		$this->assertEquals(array('"', '1"', '田中'),
			$reader->convert($line));

		$reader->parseByPcre = false;
		$this->assertEquals(array(',1""', '田中'),
			$reader->convert($line));
	}

	public function testConvertUnclosedEnclosureAtHeadOfLineAndSpaceBeforeComma()
	{
		$reader = new Reader();
		$line = <<< LINE
" ,"1" ,"田中""
LINE;
		// parseByPcreオプションによって結果が異なる
		$reader->parseByPcre = true;
		$this->assertEquals(array('" ', '"1" ', '田中"'),
			$reader->convert($line));

		$reader->parseByPcre = false;
		$this->assertEquals(array(' ,1" ', '田中"'),
			$reader->convert($line));
	}

	public function testConvertUnclosedEnclosureAtHeadOfLineAndSpaceAfterComma()
	{
		$reader = new Reader();
		$line = <<< LINE
", "1", "田中""
LINE;
		// parseByPcreオプションによって結果が異なる
		$reader->parseByPcre = true;
		$this->assertEquals(array('"', ' "1"', ' "田中"'),
			$reader->convert($line));

		$reader->parseByPcre = false;
		$this->assertEquals(array(', 1"', '田中"'),
			$reader->convert($line));

	}

	public function testConvertDelimiterAtEndOfLine()
	{
		$reader = new Reader();
		$line = <<< LINE
1,田中,
LINE;
		$this->assertEquals(array('1', '田中', ''),
			$reader->convert($line));
	}

	public function testConvertEnclosureAtEndOfLine()
	{
		$reader = new Reader();
		$line = <<< LINE
1,田中"
LINE;
		$this->assertEquals(array('1', '田中"'),
			$reader->convert($line));
	}

	public function testConvertTabSeparatedValues()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$reader->enclosure = '"';
		$line = sprintf('1%s"田中%s"%s', "\t", "\t", "\r\n");
		$this->assertEquals(array('1', "田中\t"),
			$reader->convert($line));
	}

	public function testConvertTabSeparatedValuesEscapeBackslash()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$reader->enclosure = '"';
		$reader->escape    = '\\';
		$line = sprintf('1%s"田中\\""%s', "\t", "\r\n");

		// parseByPcreオプションによって結果が異なる
		// ONの場合は独自処理でescape対応OK
		$reader->parseByPcre = true;
		$this->assertEquals(array('1', '田中"'),
			$reader->convert($line));

		// str_getcsv()はescape対応NG (PHP5.4.8現在)
		$reader->parseByPcre = false;
		$this->assertEquals(array('1', '田中\\"'),
			$reader->convert($line));
	}

	public function testConvertWithEncoding()
	{
		$reader = new Reader();
		$reader->inputEncoding = 'UTF-8';
		$reader->outputEncoding = 'SJIS';
		$line = '1,ソ十貼能表暴予' . "\r\n";
		$fields = array('1', 'ソ十貼能表暴予');
		mb_convert_variables('SJIS', 'UTF-8', $fields);
		$this->assertEquals($fields,
			$reader->convert($line));
	}

	public function testOpen()
	{
		$reader = new Reader();
		$this->assertInstanceOf('\SplFileObject', $reader->open('php://memory'));
	}

	public function testGetFile()
	{
		$reader = new Reader();
		$file = $reader->open('php://memory');
		$this->assertSame($file, $reader->getFile());
		$this->assertSame($file, $reader->file);
	}

	public function testFetch()
	{
		$reader = new Reader();
		$reader->open('php://memory');
		$reader->file->fwrite("1,田中\r\n");
		$reader->file->rewind();
		$this->assertEquals(array('1', '田中'),
			$reader->fetch());
	}

	public function testFetchWithSkipHeaderLine()
	{
		$reader = new Reader();
		$reader->open('php://memory');
		$reader->file->fwrite("ユーザーID,ユーザー名\r\n");
		$reader->file->fwrite("1,田中\r\n");
		$reader->file->rewind();
		$reader->skipHeaderLine = true;
		$this->assertEquals(array('1', '田中'),
			$reader->fetch());
	}

	public function testFetchWithFilter()
	{
		$reader = new Reader();
		$reader->open('php://memory');
		$reader->file->fwrite("1,田中,一郎,22\r\n");
		$reader->file->rewind();
		$reader->appendFilter(function($item) {
			$user = new \Stdclass();
			$user->id        = $item[0];
			$user->surname   = $item[1];
			$user->firstname = $item[2];
			$user->age       = $item[3];
			return $user;
		});
		$user = $reader->fetch();
		$this->assertEquals('1'   , $user->id);
		$this->assertEquals('田中', $user->surname);
		$this->assertEquals('一郎', $user->firstname);
		$this->assertEquals('22'  , $user->age);
	}

	public function testFetchWithFilterAndEncoding()
	{
		$reader = new Reader();
		$reader->open('php://memory');
		$reader->file->fwrite("1,ソ十貼能表暴予\r\n");
		$reader->file->rewind();
		$reader->appendFilter(function($item) {
			$test = new \Stdclass();
			$test->id   = $item[0];
			$test->text = $item[1];
			return $test;
		});
		$reader->inputEncoding = 'UTF-8';
		$reader->outputEncoding = 'SJIS';
		$test = $reader->fetch();
		$this->assertEquals('1', $test->id);
		$this->assertEquals(mb_convert_encoding('ソ十貼能表暴予', 'SJIS', 'UTF-8'), $test->text);
	}

}
