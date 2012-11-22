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
		$reader->parseByPcre = false;
 		$this->assertFalse($reader->parseByPcre);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetParseByPcreRaiseInvalidArgumentException()
	{
		$reader = new Reader();
		$reader->parseByPcre = 'false';
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
		$reader->appendFilter($filter2);

		$this->assertEquals($filter1, $reader->filter(0));
		$this->assertEquals($filter2, $reader->filter(1));
	}

	public function testApplyFilters()
	{
		$reader = new Reader();

		$reader->filter(0, function($item) {
			$user = new \Stdclass();
			$user->id        = $item[0];
			$user->surname   = $item[1];
			$user->firstname = $item[2];
			$user->age       = (int)$item[3];
			return $user;
		});

		$user = $reader->applyFilters(array('1', '田中', '一郎', '22'));

		$this->assertEquals('1'   , $user->id);
		$this->assertEquals('田中', $user->surname);
		$this->assertEquals('一郎', $user->firstname);
		$this->assertEquals(22    , $user->age);
	}

	public function testConvert()
	{
		$reader = new Reader();

		$this->assertEquals(array('1', '田中'),
			$reader->convert("1,田中\r\n"));
	}

	public function testConvertIgnoreNullByte()
	{
		$reader = new Reader();
		$this->assertEquals(array('1', '田中'),
			$reader->convert("1,田中\x00"));
	}

	public function testConvertEncloseDelimiter()
	{
		$reader = new Reader();

		$this->assertEquals(array('1', '田中,'),
			$reader->convert('1,"田中,"'));
	}

	public function testConvertEscapedEnclosure()
	{
		$reader = new Reader();

		$this->assertEquals(array('1', '田中"'),
			$reader->convert('1,"田中"""'));
	}

	public function testConvertEnclosedCarriageReturnAndLineFeed()
	{
		$reader = new Reader();
		$this->assertEquals(array('1', "田中\r\n"),
			$reader->convert("1,\"田中\r\n\""));
	}

	public function testConvertOnlySpaceField()
	{
		$reader = new Reader();

		$this->assertEquals(array(' ', '1', '田中'),
			$reader->convert(' ,1,田中'));
	}

	public function testConvertNotClosedEnclosure()
	{
		$reader = new Reader();

		$this->assertEquals(array('"', '1', '田中'),
			$reader->convert('",1,田中'));
	}

	public function testConvertNotClosedEnclosureByParseStrGetCsv()
	{
		$reader = new Reader();
		$reader->parseByPcre = false;
		$this->assertEquals(array(',1,田中'),
			$reader->convert('",1,田中'));

	}

	public function testConvertNotOpenedEnclosure()
	{
		$reader = new Reader();

		$this->assertEquals(array('', '"', '1', '田中'),
			$reader->convert(',","1","田中"'));
	}

	public function testConvertNotOpenedEnclosureByParseStrGetCsv()
	{
		$reader = new Reader();
		$reader->parseByPcre = false;
		$this->assertEquals(array('', ',1"', '田中'),
			$reader->convert(',","1","田中"'));
	}

	public function testConvertNotClosedEnclosureAndSpaceBeforeDelimiter()
	{
		$reader = new Reader();

		$this->assertEquals(array('" ', '"1" ', '田中"'),
			$reader->convert('" ,"1" ,"田中""'));
	}

	public function testConvertNotClosedEnclosureAndSpaceBeforeDelimiterByParseStrGetCsv()
	{
		$reader = new Reader();
		$reader->parseByPcre = false;

		$this->assertEquals(array(' ,1" ', '田中"'),
			$reader->convert('" ,"1" ,"田中""'));
	}

	public function testConvertNotClosedEnclosureAndSpaceAfterDelimiter()
	{
		$reader = new Reader();

		$this->assertEquals(array('"', ' "1"', ' "田中"'),
			$reader->convert('", "1", "田中""'));
	}

	public function testConvertNotClosedEnclosureAndSpaceAfterDelimiterByParseStrGetCsv()
	{
		$reader = new Reader();
		$reader->parseByPcre = false;

		$this->assertEquals(array(', 1"', '田中"'),
			$reader->convert('", "1", "田中""'));
	}

	public function testConvertTabSeparatedValues()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$reader->enclosure = '"';

		$this->assertEquals(array('1', '田中'),
			$reader->convert("1\t\"田中\""));
	}

	public function testConvertTabSeparatedValuesAndEscapedEnclosure()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$reader->enclosure = '"';
		$reader->escape    = '\\';

		$this->assertEquals(array('1', '田中"'),
			$reader->convert("1\t\"田中\\\"\""));
	}

	public function testConvertTabSeparatedValuesAndEscapedEnclosureByStrGetCsv()
	{
		$reader = new Reader();
		$reader->delimiter = "\t";
		$reader->enclosure = '"';
		$reader->escape    = '\\';
		// str_getcsv()はescape対応NG (PHP5.4.8現在)
		$reader->parseByPcre = false;

		$this->assertNotEquals(array('1', '田中"'),
			$reader->convert("1\t\"田中\\\"\""));
	}

	public function testConvertWithEncoding()
	{
		$reader = new Reader();
		$reader->inputEncoding = 'UTF-8';
		$reader->outputEncoding = 'SJIS';

		$fields = array('1', 'ソ十貼能表暴予');
		mb_convert_variables('SJIS', 'UTF-8', $fields);

		$this->assertEquals($fields,
			$reader->convert('1,ソ十貼能表暴予'));
	}

	public function testSetFile()
	{
		$reader = new Reader();
		$file = new \SplFileObject('php://memory', '+r');
		$reader->setFile($file);
		$this->assertSame($file, $reader->getFile());
		$this->assertSame($file, $reader->file);
	}

	public function testFetch()
	{
		$reader = new Reader();
		$reader->file = new \SplFileObject('php://memory', '+r');
		$reader->file->fwrite("1,田中\r\n");
		$reader->file->rewind();

		$this->assertEquals(array('1', '田中'),
			$reader->fetch());
	}

	public function testFetchWithSkipHeaderLine()
	{
		$reader = new Reader();
		$reader->file = new \SplFileObject('php://memory', '+r');
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
		$reader->file = new \SplFileObject('php://memory', '+r');
		$reader->file->fwrite("1,田中,一郎,22\r\n");
		$reader->file->fwrite("2,山田,老人,91\r\n");
		$reader->file->fwrite("3,田中,次郎,45\r\n");
		$reader->file->fwrite("4,佐藤,ウメ,95\r\n");
		$reader->file->rewind();

		$reader->appendFilter(function($item) {
			$user = new \Stdclass();
			$user->id        = $item[0];
			$user->surname   = $item[1];
			$user->firstname = $item[2];
			$user->age       = (int)$item[3];
			return $user;
		});

		// 1st record
		$user = $reader->fetch();
		$this->assertEquals('1'   , $user->id);
		$this->assertEquals('田中', $user->surname);
		$this->assertEquals('一郎', $user->firstname);
		$this->assertEquals(22    , $user->age);

		// 2nd record
		$user = $reader->fetch();
		$this->assertEquals('2'   , $user->id);
		$this->assertEquals('山田', $user->surname);
		$this->assertEquals('老人', $user->firstname);
		$this->assertEquals(91    , $user->age);

		// 3rd record
		$user = $reader->fetch();
		$this->assertEquals('3'   , $user->id);
		$this->assertEquals('田中', $user->surname);
		$this->assertEquals('次郎', $user->firstname);
		$this->assertEquals(45    , $user->age);

		// 4th record
		$user = $reader->fetch();
		$this->assertEquals('4'   , $user->id);
		$this->assertEquals('佐藤', $user->surname);
		$this->assertEquals('ウメ', $user->firstname);
		$this->assertEquals(95    , $user->age);
	}

	public function testFetchWithFilterAndEncoding()
	{
		$reader = new Reader();
		$reader->file = new \SplFileObject('php://memory', '+r');
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

	public function testFetchWithSkipHeaderLineAndSomeFilters()
	{
		$reader = new Reader();
		$reader->file = new \SplFileObject('php://memory', '+r');
		$reader->file->fwrite("ユーザーID,姓,名,年齢\r\n");
		$reader->file->fwrite("1,田中,一郎,22\r\n");
		$reader->file->fwrite("2,山田,老人,91\r\n");
		$reader->file->fwrite("3,田中,次郎,45\r\n");
		$reader->file->fwrite("4,佐藤,ウメ,95\r\n");
		$reader->file->rewind();

		$reader->appendFilter(function($item) {
			$user = new \Stdclass();
			$user->id        = $item[0];
			$user->surname   = $item[1];
			$user->firstname = $item[2];
			$user->age       = (int)$item[3];
			return $user;
		});

		$reader->appendFilter(function($user) {
			if ($user->age > 90) {
				return $user;
			}
			return false;
		});

		$reader->skipHeaderLine = true;

		// 1st record
		$elder = $reader->fetch();
		$this->assertFalse($elder);

		// 2nd record
		$elder = $reader->fetch();
		$this->assertEquals('2'   , $elder->id);
		$this->assertEquals('山田', $elder->surname);
		$this->assertEquals('老人', $elder->firstname);
		$this->assertGreaterThan(90, $elder->age);

		// 3rd record
		$elder = $reader->fetch();
		$this->assertFalse($elder);

		// 4th record
		$elder = $reader->fetch();
		$this->assertEquals('4'   , $elder->id);
		$this->assertEquals('佐藤', $elder->surname);
		$this->assertEquals('ウメ', $elder->firstname);
		$this->assertGreaterThan(90, $elder->age);

	}

}
