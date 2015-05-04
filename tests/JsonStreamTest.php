<?php
namespace PaperBoat\Test;

use PaperBoat\JsonStream;

class JsonStreamTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider valueDataProvider
   */
  public function testValueString($value)
  {
    list($stream, $json) = $this->getObjects();
    $json->value($value);
    $this->assertEquals(json_encode($value), $stream->getOutput());
  }

  public function valueDataProvider()
  {
    return [
      ['hello world'],
      [42],
      [true],
      [false],
      [null],
      [[1,2,3]],
      [['asdf'=>'xyz']],
      [(object)['asdf'=>'xyz']]
    ];
  }

  public function testObjectPropertyInline()
  {
    list($stream, $json) = $this->getObjects();

    $json->startObject()
      ->property('foo', 'bar')
      ->property('xyz', 42)
      ->stopObject();

    $expected = json_encode([
      'foo' => 'bar',
      'xyz' => 42
    ]);

    $this->assertEquals($expected, $stream->getOutput());
  }

  public function testNestedObject()
  {
    list($stream, $json) = $this->getObjects();

    $json->startObject()
      ->property('foo')
      ->value('bar')
      ->property('xyz')
        ->startObject()
          ->property('asdf')
          ->value(42)
          ->property('mary')
          ->value('little lamb')
        ->stopObject()
      ->stopObject();

    $expected = json_encode([
      'foo' => 'bar',
      'xyz' => [
        'asdf' => 42,
        'mary' => 'little lamb'
      ]
    ]);

    $this->assertEquals($expected, $stream->getOutput());
  }

  public function testStopArrayOutOfOrder()
  {
    $this->setExpectedException('LogicException');
    list($stream, $json) = $this->getObjects();
    $json->startObject()->stopArray(); 
  }

  public function testStopObjectOutOfOrder()
  {
    $this->setExpectedException('LogicException');
    list($stream, $json) = $this->getObjects();
    $json->startArray()->stopObject(); 
  }

  public function testPropertyOutOfOrder()
  {
    $this->setExpectedException('LogicException');
    list($stream, $json) = $this->getObjects();
    $json->startObject()->property('one')->property('two'); 
  }

  public function testPropertyInArray()
  {
    $this->setExpectedException('LogicException');
    list($stream, $json) = $this->getObjects();
    $json->startArray()->property('one'); 
  }

  public function testPropertyOutOfObject()
  {
    $this->setExpectedException('LogicException');
    list($stream, $json) = $this->getObjects();
    $json->property('one'); 
  }

  private function getObjects()
  {
    $stream = new StringOutputStream();
    $json = new JsonStream($stream);
    return [$stream, $json];
  }
}