<?php
namespace PaperBoat\Test;
use PaperBoat\OutputStream;

class StringOutputStream implements OutputStream
{
  private $output;
  
  public function write($str)
  {
    $this->output .= $str;
  }

  public function flush()
  {
  }

  public function getOutput() {
    return $this->output;
  }

  public function clearOutput() {
    $this->output = '';
  }
}