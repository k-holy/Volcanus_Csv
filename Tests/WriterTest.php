<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv\Tests;

use Volcanus\Csv\Writer;

/**
 * WriterTest
 *
 * @author k.holy74@gmail.com
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{

	public function testDefaultConfigParameter()
	{
		$writer = new Writer();
		$this->assertEquals(',', $writer->delimiter);
		$this->assertEquals('"', $writer->enclosure);
		$this->assertEquals('"', $writer->escape);
		$this->assertFalse($writer->enclose);
		$this->assertEquals("\r\n", $writer->newLine);
		$this->assertEquals(mb_internal_encoding(), $writer->inputEncoding);
		$this->assertEquals(mb_internal_encoding(), $writer->outputEncoding);
		$this->assertFalse($writer->writeHeaderLine);
		$this->assertNull($writer->responseFilename);
	}

	public function testConstructWithConfigParameters()
	{
		$writer = new Writer(array(
			'delimiter'       => "\t",
			'enclosure'       => "'",
			'escape'          => '\\',
			'enclose'         => true,
			'newLine'         => "\n",
			'inputEncoding'   => 'EUC-JP',
			'outputEncoding'  => 'SJIS-win',
			'writeHeaderLine' => true,
			'responseFilename' => 'test.csv',
		));
		$this->assertEquals("\t", $writer->delimiter);
		$this->assertEquals("'" , $writer->enclosure);
		$this->assertEquals('\\', $writer->escape);
		$this->assertTrue($writer->enclose);
		$this->assertEquals("\n", $writer->newLine);
		$this->assertEquals('EUC-JP', $writer->inputEncoding);
		$this->assertEquals('SJIS-win', $writer->outputEncoding);
		$this->assertTrue($writer->writeHeaderLine);
		$this->assertEquals('test.csv', $writer->responseFilename);
	}

	public function testSetConfig()
	{
		$writer = new Writer();
		$writer->config('delimiter', "\t");
		$this->assertEquals("\t", $writer->delimiter);
	}

	public function testGetConfig()
	{
		$writer = new Writer();
		$writer->delimiter = "\t";
		$this->assertEquals("\t", $writer->config('delimiter'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetConfigRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->config('NOT-DEFINED-CONFIG', true);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetConfigRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->config('NOT-DEFINED-CONFIG');
	}

	public function testSetDelimiter()
	{
		$writer = new Writer();
		$writer->delimiter = "\t";
		$this->assertEquals("\t", $writer->delimiter);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new Writer();
		$writer->delimiter = ',,';
	}

	public function testSetEnclosure()
	{
		$writer = new Writer();
		$writer->enclosure = "'";
		$this->assertEquals("'", $writer->enclosure);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->enclosure = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new Writer();
		$writer->enclosure = '""';
	}

	public function testSetEscape()
	{
		$writer = new Writer();
		$writer->escape = '\\';
		$this->assertEquals('\\', $writer->escape);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->escape = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new Writer();
		$writer->escape = '\\\\';
	}

	public function testSetEnclose()
	{
		$writer = new Writer();
		$writer->enclose = true;
		$this->assertTrue($writer->enclose);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEncloseRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->enclose = 'true';
	}

	public function testSetNewLine()
	{
		$writer = new Writer();
		$writer->newLine = "\n";
		$this->assertEquals("\n", $writer->newLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetNewLineRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->newLine = array();
	}

	public function testSetInputEncoding()
	{
		$writer = new Writer();
		$writer->inputEncoding = 'EUC-JP';
 		$this->assertEquals('EUC-JP', $writer->inputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetInputEncodingRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->inputEncoding = array();
	}

	public function testSetOutputEncoding()
	{
		$writer = new Writer();
		$writer->outputEncoding = 'SJIS-win';
 		$this->assertEquals('SJIS-win', $writer->outputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetOutputEncodingRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->outputEncoding = array();
	}

	public function testSetWriteHeaderLine()
	{
		$writer = new Writer();
		$writer->writeHeaderLine = true;
 		$this->assertTrue($writer->writeHeaderLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetWriteHeaderLineRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->writeHeaderLine = 'true';
	}

	public function testSetContentFilename()
	{
		$writer = new Writer();
		$writer->responseFilename = 'test.csv';
 		$this->assertEquals('test.csv', $writer->responseFilename);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetContentFilenameRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->responseFilename = array();
	}

	public function testBuildField()
	{
		$writer = new Writer();
		$field = '田中';
		$this->assertEquals("田中", $writer->buildField($field));
	}

	public function testBuildFieldIncludesDelimiter()
	{
		$writer = new Writer();
		$field = '田中,';
		$this->assertEquals("\"田中,\"", $writer->buildField($field));
	}

	public function testBuildFieldIncludesCarriageReturnAndLineFeed()
	{
		$writer = new Writer();
		$field = "田中\r\n\r\n以上";
		$this->assertEquals("\"田中\r\n\r\n以上\"", $writer->buildField($field));
	}

	public function testBuildFieldIncludesCarriageReturn()
	{
		$writer = new Writer();
		$field = "田中\r\r以上";
		$this->assertEquals("\"田中\r\r以上\"", $writer->buildField($field));
	}

	public function testBuildFieldIncludesLineFeed()
	{
		$writer = new Writer();
		$field = "田中\n\n以上";
		$this->assertEquals("\"田中\n\n以上\"", $writer->buildField($field));
	}

	public function testBuildFieldEscapeIncludesEnclosure()
	{
		$writer = new Writer();
		$field = '田中"';
		$this->assertEquals('"田中"""', $writer->buildField($field));

		$writer->enclosure = '"';
		$writer->escape = '\\';
		$this->assertEquals('"田中\""', $writer->buildField($field));
	}

	public function testBuildFieldEscapeIncludesRepetitionOfEnclosure()
	{
		$writer = new Writer();
		$field = '"田"中""';
		$this->assertEquals('"""田""中"""""', $writer->buildField($field));

		$writer->enclosure = '"';
		$writer->escape = '\\';
		$this->assertEquals('"\"田\"中\"\""', $writer->buildField($field));
	}

	public function testBuildFieldWithEncoding()
	{
		$writer = new Writer();
		$writer->inputEncoding = 'UTF-8';
		$writer->outputEncoding = 'SJIS';
		$field = 'ソ十貼能表暴予';
		$this->assertEquals(
			mb_convert_encoding($field, 'SJIS', 'UTF-8'),
			$writer->buildField($field)
		);
	}

	public function testBuildLine()
	{
		$writer = new Writer();
		$this->assertEquals("1,田中\r\n",
			$writer->buildLine(array('1', '田中')));
	}

	public function testBuildLineIncludesDelimiter()
	{
		$writer = new Writer();
		$this->assertEquals("1,\"田中,\"\r\n",
			$writer->buildLine(array('1', '田中,')));
	}

	public function testBuildLineWithEncoding()
	{
		$writer = new Writer();
		$writer->inputEncoding = 'UTF-8';
		$writer->outputEncoding = 'SJIS';
		$fields = array('1', 'ソ十貼能表暴予');
		$this->assertEquals(
			mb_convert_encoding("1,ソ十貼能表暴予\r\n", 'SJIS', 'UTF-8'),
			$writer->buildLine($fields)
		);
	}

	public function testBuildLineWithEnclose()
	{
		$writer = new Writer();
		$writer->enclose = true;
		$this->assertEquals("\"1\",\"田中\"\r\n",
			$writer->buildLine(array('1', '田中')));
	}

	public function testFieldName()
	{
		$writer = new Writer();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$this->assertEquals('ユーザーID', $writer->fieldName(0));
		$this->assertEquals('ユーザー名', $writer->fieldName(1));
	}

	public function testBuildHeaderLine()
	{
		$writer = new Writer();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$this->assertEquals("ユーザーID,ユーザー名\r\n", $writer->buildHeaderLine());
	}

	public function testFieldFilter()
	{
		$writer = new Writer();
		$filter1 = function($item) {
			return $item['surname'] . $item['firstname'];
		};
		$filter2 = function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		};
		$writer->fieldFilter(0, $filter1);
		$writer->fieldFilter(1, $filter2);
		$this->assertEquals($filter1, $writer->fieldFilter(0));
		$this->assertEquals($filter2, $writer->fieldFilter(1));
	}

	public function testField()
	{
		$writer = new Writer();
		$filter1 = function($item) {
			return $item['surname'] . $item['firstname'];
		};
		$filter2 = function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		};
		$writer->field(0, $filter1, 'ユーザー名');
		$writer->field(1, $filter2, '年齢');
		$this->assertEquals('ユーザー名', $writer->fieldName(0));
		$this->assertEquals('年齢'      , $writer->fieldName(1));
		$this->assertEquals($filter1, $writer->fieldFilter(0));
		$this->assertEquals($filter2, $writer->fieldFilter(1));
	}

	public function testAppendField()
	{
		$writer = new Writer();
		$filter1 = function($item) {
			return $item['surname'] . $item['firstname'];
		};
		$filter2 = function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		};
		$writer->appendField($filter1, 'ユーザー名');
		$writer->appendField($filter2, '年齢' );
		$this->assertEquals('ユーザー名', $writer->fieldName(0));
		$this->assertEquals('年齢'      , $writer->fieldName(1));
		$this->assertEquals($filter1, $writer->fieldFilter(0));
		$this->assertEquals($filter2, $writer->fieldFilter(1));
	}

	public function testBuildFields()
	{
		$writer = new Writer();
		$writer->appendField(function($item) {
			return $item['surname'] . $item['firstname'];
		}, 'ユーザー名');
		$writer->appendField(function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		}, '年齢');
		$this->assertEquals(array('田中一郎', '22'),
			$writer->buildFields(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
			))
		);
		$this->assertEquals(array('山田花子', '不詳'),
			$writer->buildFields(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
			))
		);
	}

	public function testBuildContentLine()
	{
		$writer = new Writer();
		$writer->appendField(function($item) {
			return $item['surname'] . $item['firstname'];
		}, 'ユーザー名');
		$writer->appendField(function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		}, '年齢');
		$this->assertEquals("田中一郎,22\r\n",
			$writer->buildContentLine(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
		)));
		$this->assertEquals("山田花子,不詳\r\n",
			$writer->buildContentLine(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
		)));
	}

	public function testOpen()
	{
		$writer = new Writer();
		$this->assertInstanceOf('\SplFileInfo', $writer->open('php://memory'));
	}

	public function testGetFile()
	{
		$writer = new Writer();
		$file = $writer->open('php://memory');
		$this->assertSame($file, $writer->getFile());
		$this->assertSame($file, $writer->file);
	}

	public function testWrite()
	{
		$writer = new Writer();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$writer->open('php://memory');
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertEquals("1,田中\r\n", $writer->content());
	}

	public function testWriteWithHeaderLine()
	{
		$writer = new Writer();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$writer->open('php://memory');
		$writer->writeHeaderLine = true;
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertStringStartsWith("ユーザーID,ユーザー名\r\n", $writer->content());
	}

	public function testBuildResponseHeaders()
	{
		$writer = new Writer();
		$writer->open('php://memory');
		$writer->write(array(
			array('1', '田中'),
		));
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], 'application/octet-stream');
		$this->assertEquals($headers['Content-Disposition'], 'attachement');
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

	public function testBuildResponseHeadersWithResponseFilename()
	{
		$writer = new Writer();
		$writer->open('php://memory');
		$writer->write(array(
			array('1', '田中'),
		));
		$writer->responseFilename = 'test.csv';
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], 'application/octet-stream; name="test.csv"');
		$this->assertEquals($headers['Content-Disposition'], 'attachement; filename="test.csv"');
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

	public function testBuildResponseHeadersWithMultibyteResponseFilename()
	{
		$writer = new Writer();
		$writer->open('php://memory');
		$writer->write(array(
			array('1', '田中'),
		));
		$writer->responseFilename = 'ソ十貼能表暴予.csv';
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'],
			sprintf('application/octet-stream; name="%s"', mb_convert_encoding('ソ十貼能表暴予.csv', 'SJIS-win')));
		$this->assertEquals($headers['Content-Disposition'],
			sprintf('attachement; filename="%s"', mb_convert_encoding('ソ十貼能表暴予.csv', 'SJIS-win')));
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

}
