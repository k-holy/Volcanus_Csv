<?php
/**
 * Volcanus\Csv
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv\Tests;

use Volcanus\Csv\CsvBuilder;

/**
 * CsvBuilderTest
 *
 * @author k.holy74@gmail.com
 */
class CsvBuilderTest extends \PHPUnit_Framework_TestCase
{

	public function testDefaultParameters()
	{
		$builder = new CsvBuilder();
		$this->assertEquals(',', $builder->delimiter);
		$this->assertEquals('"', $builder->enclosure);
		$this->assertEquals('"', $builder->escape);
		$this->assertFalse($builder->enclose);
		$this->assertEquals("\r\n", $builder->newLine);
		$this->assertEquals(mb_internal_encoding(), $builder->inputEncoding);
		$this->assertEquals(mb_internal_encoding(), $builder->outputEncoding);
	}

	public function testConstructWithParameters()
	{
		$builder = new CsvBuilder(array(
			'delimiter' => "\t",
			'enclosure' => "'",
			'escape'    => '\\',
		));
		$this->assertEquals("\t", $builder->delimiter);
		$this->assertEquals("'" , $builder->enclosure);
		$this->assertEquals('\\', $builder->escape);
	}

	public function testSetDelimiter()
	{
		$builder = new CsvBuilder();
		$builder->delimiter = "\t";
		$this->assertEquals("\t", $builder->delimiter);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseInvalidArgumentExceptionWhenInvalidDelimiterWasSpecified()
	{
		$builder = new CsvBuilder();
		$builder->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecifiedForDelimiter()
	{
		$builder = new CsvBuilder();
		$builder->delimiter = ',,';
	}

	public function testSetEnclosure()
	{
		$builder = new CsvBuilder();
		$builder->enclosure = "'";
		$this->assertEquals("'", $builder->enclosure);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseInvalidArgumentExceptionWhenInvalidEnclosureWasSpecified()
	{
		$builder = new CsvBuilder();
		$builder->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecifiedForEnclosure()
	{
		$builder = new CsvBuilder();
		$builder->delimiter = '""';
	}

	public function testSetEscape()
	{
		$builder = new CsvBuilder();
		$builder->escape = '\\';
		$this->assertEquals('\\', $builder->escape);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseInvalidArgumentExceptionWhenInvalidEscapeWasSpecified()
	{
		$builder = new CsvBuilder();
		$builder->escape = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecifiedForEscape()
	{
		$builder = new CsvBuilder();
		$builder->escape = '\\\\';
	}

	public function testBuildColumn()
	{
		$builder = new CsvBuilder();
		$column = '田中';
		$this->assertEquals("田中", $builder->buildColumn($column));
	}

	public function testBuildColumnIncludesDelimiter()
	{
		$builder = new CsvBuilder();
		$column = '田中,';
		$this->assertEquals("\"田中,\"", $builder->buildColumn($column));
	}

	public function testBuildColumnIncludesCarriageReturnAndLineFeed()
	{
		$builder = new CsvBuilder();
		$column = "田中\r\n\r\n以上";
		$this->assertEquals("\"田中\r\n\r\n以上\"", $builder->buildColumn($column));
	}

	public function testBuildColumnIncludesCarriageReturn()
	{
		$builder = new CsvBuilder();
		$column = "田中\r\r以上";
		$this->assertEquals("\"田中\r\r以上\"", $builder->buildColumn($column));
	}

	public function testBuildColumnIncludesLineFeed()
	{
		$builder = new CsvBuilder();
		$column = "田中\n\n以上";
		$this->assertEquals("\"田中\n\n以上\"", $builder->buildColumn($column));
	}

	public function testBuildColumnEscapeIncludesEnclosure()
	{
		$builder = new CsvBuilder();
		$column = '田中"';
		$this->assertEquals('"田中"""', $builder->buildColumn($column));

		$builder->enclosure = '"';
		$builder->escape = '\\';
		$this->assertEquals('"田中\""', $builder->buildColumn($column));
	}

	public function testBuildColumnEscapeIncludesRepetitionOfEnclosure()
	{
		$builder = new CsvBuilder();
		$column = '"田"中""';
		$this->assertEquals('"""田""中"""""', $builder->buildColumn($column));

		$builder->enclosure = '"';
		$builder->escape = '\\';
		$this->assertEquals('"\"田\"中\"\""', $builder->buildColumn($column));
	}

	public function testBuildColumnWithConvertEncoding()
	{
		$builder = new CsvBuilder();
		$builder->inputEncoding = 'UTF-8';
		$builder->outputEncoding = 'SJIS';
		$column = 'ソ十貼能表暴予';
		$this->assertEquals(mb_convert_encoding($column, 'SJIS', 'UTF-8'), $builder->buildColumn($column));
	}

	public function testBuildLine()
	{
		$builder = new CsvBuilder();
		$this->assertEquals("1,田中\r\n",
			$builder->buildLine(array('1', '田中')));
	}

	public function testBuildLineIncludesDelimiter()
	{
		$builder = new CsvBuilder();
		$this->assertEquals("1,\"田中,\"\r\n",
			$builder->buildLine(array('1', '田中,')));
	}

	public function testBuildLineWithConvertEncoding()
	{
		$builder = new CsvBuilder();
		$builder->inputEncoding = 'UTF-8';
		$builder->outputEncoding = 'SJIS';
		$columns = array('1', 'ソ十貼能表暴予');
		$this->assertEquals(mb_convert_encoding("1,ソ十貼能表暴予\r\n", 'SJIS', 'UTF-8'), $builder->buildLine($columns));
	}

	public function testBuildLineWithEnclose()
	{
		$builder = new CsvBuilder();
		$builder->enclose = true;
		$this->assertEquals("\"1\",\"田中\"\r\n",
			$builder->buildLine(array('1', '田中')));
	}

	public function testColumnName()
	{
		$builder = new CsvBuilder();
		$builder->columnName(0, 'ユーザーID');
		$builder->columnName(1, 'ユーザー名');
		$this->assertEquals('ユーザーID', $builder->columnName(0));
		$this->assertEquals('ユーザー名', $builder->columnName(1));
	}

	public function testColumnFilter()
	{
		$builder = new CsvBuilder();
		$idFilter = function($item) {
			return $item['id'];
		};
		$nameFilter = function($item) {
			return $item['surname'] . $item['firstname'];
		};
		$builder->columnFilter(0, $idFilter);
		$builder->columnFilter(1, $nameFilter);
		$this->assertEquals($idFilter  , $builder->columnFilter(0));
		$this->assertEquals($nameFilter, $builder->columnFilter(1));
	}

	public function testBuildColumns()
	{
		$builder = new CsvBuilder();
		$builder->column(0, 'ユーザー名', function($item) {
			return $item['surname'] . $item['firstname'];
		});
		$builder->column(1, '年齢', function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		});
		$this->assertEquals(array('田中一郎', '22'),
			$builder->buildColumns(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
			))
		);
		$this->assertEquals(array('山田花子', '不詳'),
			$builder->buildColumns(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
			))
		);
	}

	public function testBuildHeaderLine()
	{
		$builder = new CsvBuilder();
		$builder->column(0, 'ユーザーID');
		$builder->column(1, 'ユーザー名');
		$this->assertEquals("ユーザーID,ユーザー名\r\n", $builder->buildHeaderLine());
	}

	public function testRecords()
	{
		$builder = new CsvBuilder();
		$records = array(
			array('id' => 1, 'name' => 'test1'),
			array('id' => 2, 'name' => 'test2'),
		);
		$builder->records($records);
		$this->assertEquals($records, $builder->records());
	}

	public function testOpen()
	{
		$builder = new CsvBuilder();
		$this->assertInstanceOf('\SplFileObject', $builder->open('php://memory'));
	}

	public function testClose()
	{
		$builder = new CsvBuilder();
		$builder->open('php://memory');
		$this->assertInstanceOf('\SplFileObject', $builder->close());
	}

	public function testContentLength()
	{
		$builder = new CsvBuilder();

		$file = $builder->open('php://memory');
		$this->assertEquals(0, $builder->contentLength());

		$file->fwrite('123456789');
		$this->assertEquals(9, $builder->contentLength());
	}

	public function testWrite()
	{
		$builder = new CsvBuilder();
		$builder->columnName(0, 'ユーザーID');
		$builder->columnName(1, 'ユーザー名');
		$builder->write('php://temp', array(
			array('1', '田中'),
		));
		$this->assertGreaterThan(1, $builder->contentLength());
	}

}
