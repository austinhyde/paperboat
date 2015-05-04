<?php
namespace PaperBoat;
use PaperBoat\OutputStream;
use PaperBoat\OutputStream\StdoutStream;

class JsonStream
{
  private $outputStream;
  private $automaticFlushing = true;
  private $jsonFlags = 0;

  // State tracking
  private $state = [];
  private $isFirsts = [true];
  private $isProperty = false;

  /**
   * Create a new JsonStream with a given output stream.
   * @param OutputStream|null $outputStream Defaults to PaperBoat\OutputStream\StdoutStream
   */
  public function __construct(OutputStream $outputStream = null)
  {
    $this->outputStream = $outputStream ?: new StdoutStream();
  }

  /**
   * Turns flushing after every write on or off. Defaults to true.
   * If false, JsonStream::flush() will need to be called manually.
   * @param boolean $value
   */
  public function setAutomaticFlushing($value)
  {
    $this->automaticFlushing = $value;
    return $this;
  }

  /**
   * Sets the flags used when calling json_encode() for values. Defaults to 0.
   * See: http://us1.php.net/manual/en/function.json-encode.php
   * @param integer $value
   */
  public function setJsonFlags($value)
  {
    $this->jsonFlags = $value;
    return $this;
  }

  /**
   * Begins outputting an array
   * @return PaperBoat\JsonStream $this
   */
  public function startArray()
  {
    return $this
      ->pushState('arr')
      ->output('[', $this->expectsDelimiter())
      ->isProperty(false)
      ->pushFirst();
  }

  /**
   * Finishes outputting an array
   * @return PaperBoat\JsonStream $this
   */
  public function stopArray()
  {
    return $this
      ->popState('arr')
      ->output(']', false)
      ->isProperty(false)
      ->popFirst()
      ->unFirst();
  }

  /**
   * Begins outputting an object
   * @return PaperBoat\JsonStream $this
   */
  public function startObject()
  {
    return $this
      ->pushState('obj')
      ->output('{', $this->expectsDelimiter())
      ->isProperty(false)
      ->pushFirst();
  }

  /**
   * Finishes outputting an object
   * @return PaperBoat\JsonStream $this
   */
  public function stopObject()
  {
    return $this
      ->popState('obj')
      ->output('}', false)
      ->isProperty(false)
      ->popFirst()
      ->unFirst();
  }

  /**
   * Adds a property to an object
   * @param  string $name Property name
   * @param  mixed $value Optional value to give the property. Or use ->value()
   * @return PaperBoat\JsonStream $this
   */
  public function property($name)
  {
    $this
      ->checkState('obj', false)
      ->output('"'.$name.'":', $this->expectsDelimiter())
      ->isProperty(true);
    if (func_num_args() == 2) {
      $args = func_get_args();
      $this->value($args[1]);
    }
    return $this;
  }

  /**
   * Appends data to the stream as JSON
   * @param  mixed $data
   * @return PaperBoat\JsonStream $this
   */
  public function value($data)
  {
    return $this
      ->output($this->jsonEncode($data), $this->expectsDelimiter())
      ->isProperty(false)
      ->unFirst();
  }

  /**
   * Manually flushes the output
   * @return PaperBoat\JsonStream $this
   */
  public function flush()
  {
    $this->outputStream->flush();
    return $this;
  }

  private function expectsDelimiter()
  {
    return !$this->isFirst() && !$this->isProperty;
  }

  private function pushFirst()
  {
    $this->isFirsts[] = true;
    return $this;
  }

  private function unFirst()
  {
    $this->isFirsts[count($this->isFirsts) - 1] = false;
    return $this;
  }

  private function popFirst()
  {
    array_pop($this->isFirsts);
    return $this;
  }

  private function isFirst()
  {
    return $this->isFirsts[count($this->isFirsts) - 1];
  }

  private function isProperty($value)
  {
    $this->isProperty = $value;
    return $this;
  }

  private function pushState($type)
  {
    $this->state[] = $type;
    return $this;
  }
  
  private function popState($type)
  {
    $this->checkState($type, false);
    array_pop($this->state);
    return $this;
  }

  private function checkState($type, $isProperty = null)
  {
    $word = $type=='obj' ? 'object' : 'array';
    if ($this->getState() !== $type) {
      throw new \LogicException("Invalid state: expected a value for $word");
    }
    if ($isProperty !== null && $isProperty !== $this->isProperty) {
      throw new \LogicException("Invalid state: expected a property value");
    }
    return $this;
  }

  private function getState()
  {
    return $this->state[count($this->state) - 1];
  }

  protected function output($s, $needsDelimiter)
  {
    if ($needsDelimiter) {
      $s = ',' . $s;
    }

    $this->outputStream->write($s);
    if ($this->automaticFlushing) {
      $this->outputStream->flush();
    }
    return $this;
  }

  protected function jsonEncode($data)
  {
    $r = json_encode($data, $this->jsonFlags);
    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new \RuntimeException(json_last_error_msg());
    }
    return $r;
  }
}