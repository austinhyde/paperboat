<?php
namespace PaperBoat\OutputStream;
use PaperBoat\OutputStream;

/**
 * An OutputStream that prints to STDOUT via echo
 */
class StdoutStream implements OutputStream
{
  /**
   * {@inheritDoc}
   */
  public function write($str)
  {
    echo $str;
  }

  /**
   * {@inheritDoc}
   */
  public function flush()
  {
    flush();
  }
}