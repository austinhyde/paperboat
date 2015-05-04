# PaperBoat - Streaming JSON Output

[![Build Status](https://img.shields.io/travis/austinhyde/paperboat/master.svg?style=flat)](https://travis-ci.org/austinhyde/paperboat)
[![Latest Version](https://img.shields.io/github/release/austinhyde/paperboat.svg?style=flat)](https://github.com/austinhyde/paperboat/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](https://github.com/austinhyde/paperboat/blob/master/LICENSE.md)

This is an extremely simple, and **very alpha quality** implementation of a Streaming JSON outputter.

# Show me the goods

```php
$stream = new PaperBoat\JsonStream();
$stream
  ->startObject()
  ->property('data')
  ->startArray();

$i = 0;
while ($row = $pdoStmt->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  $stream->value($row);
}

$stream
  ->stopArray()
  ->property('meta')
  ->startObject()
    ->property('count', $i)
  ->stopObject()
  ->stopObject();  
```

This outputs, for example,

```json
{"data":[{"id":1,"name":"Bill Murray"},{"id":2,"name":"Tom Hanks"},{"id":3,"name":"Sigourney Weaver"}],"meta":{"count":3}}
```

# Usage

The primary class you interact with is `PaperBoat\JsonStream`. The constructor takes an optional `PaperBoat\OutputStream` parameter, which tells it where to output JSON to. By default, this is a `PaperBoat\OutputStream\StdoutStream`, which just prints to STDOUT or the HTTP response.

The following methods construct JSON output:

* `startArray()` - Begins outputting an array.
* `stopArray()` - Closes an array.
* `startObject()` - Begins outputting an object.
* `stopObject()` - Closes an object.
* `property($name[, $data])` - Adds a property to an object. If you do not provide the value here, you must call `startArray()`, `startObject()`, or `value()` next.
* `value($data)` - Adds data to the stream, by JSON encoding it.

The following methods control how data is output:

* `setAutomaticFlushing($value)` - Controls whether the `OutputStream` is flushed automatically after data is written to the stream. Defaults to true. If you set this false, you are responsible for calling `->flush()` when appropriate.
* `setJsonFlags($value)` - Sets the flags passed to [`json_encode()`](http://us1.php.net/manual/en/function.json-encode.php)
* `flush()` - Simply calls the provided `OutputStream`'s `flush()` method.

# Installation

Via Composer.

```bash
$ composer require austinhyde/paperboat
```

# Contributing

See [CONTRIBUTING](https://github.com/austinhyde/paperboat/blob/master/CONTRIBUTING.md)

# FAQ

##### Why PaperBoat?

Streams of lightweight data => paper boats floating down a stream of water

##### Why do I need this?

You would use this if you need to output a large amount of JSON data without holding the whole data structure in memory at once.

Most people probably don't need this.

##### Why did you make this?

1. I was bored
2. It didn't exist yet
3. Someone might need it, someday

##### Are these really frequently asked questions?

No, this is a sham, just like all the other FAQs on GitHub.
