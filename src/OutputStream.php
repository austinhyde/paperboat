<?php
namespace PaperBoat;

interface OutputStream
{
  /**
   * Writes a string to the stream
   * @param  string $str
   */
  public function write($str);

  /**
   * Flushes any pending buffered data to the stream
   */
  public function flush();
}