<?php

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;
/**
 * Uses PHP's zlib.inflate filter to inflate deflate or gzipped content.
 *
 * This stream decorator skips the first 10 bytes of the given stream to remove
 * the gzip header, converts the provided stream to a PHP stream resource,
 * then appends the zlib.inflate filter. The stream is then converted back
 * to a Guzzle stream resource to be used as a Guzzle stream.
 *
 * @link http://tools.ietf.org/html/rfc1952
 * @link http://php.net/manual/en/filters.compression.php
 */
class InflateStream implements StreamInterface
{
    public function __construct(StreamInterface $stream)
    {
        // read the first 10 bytes, ie. gzip header
        $header = $stream->read(10);
        $filenameHeaderLength = $this->getLengthOfPossibleFilenameHeader($stream, $header);
        // Skip the header, that is 10 + length of filename + 1 (nil) bytes
        $stream = new LimitStream($stream, -1, 10 + $filenameHeaderLength);
        $resource = StreamWrapper::getResource($stream);
        stream_filter_append($resource, 'zlib.inflate', STREAM_FILTER_READ);
        $this->stream = new Stream($resource);
    }
    /**
     * @param StreamInterface $stream
     * @param $header
     * @return int
     */
    private function getLengthOfPossibleFilenameHeader(StreamInterface $stream, $header)
    {
        $filename_header_length = 0;
        if (substr(bin2hex($header), 6, 2) === '08') {
            // we have a filename, read until nil
            $filename_header_length = 1;
            while ($stream->read(1) !== chr(0)) {
                $filename_header_length++;
            }
        }
        return $filename_header_length;
    }
    /**
     * Magic method used to create a new stream if streams are not added in
     * the constructor of a decorator (e.g., LazyOpenStream).
     *
     * @param string $name Name of the property (allows "stream" only).
     *
     * @return StreamInterface
     */
    public function __get($name)
    {
        if ($name == 'stream') {
            $this->stream = $this->createStream();
            return $this->stream;
        }
        throw new \UnexpectedValueException("{$name} not found on class");
    }
    public function __toString()
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
            return $this->getContents();
        } catch (\Exception $e) {
            // Really, PHP? https://bugs.php.net/bug.php?id=53648
            trigger_error('StreamDecorator::__toString exception: ' . (string) $e, E_USER_ERROR);
            return '';
        }
    }
    public function getContents()
    {
        return copy_to_string($this);
    }
    /**
     * Allow decorators to implement custom methods
     *
     * @param string $method Missing method name
     * @param array  $args   Method arguments
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        $result = call_user_func_array(array($this->stream, $method), $args);
        // Always return the wrapped object if the result is a return $this
        return $result === $this->stream ? $this : $result;
    }
    public function close()
    {
        $this->stream->close();
    }
    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata($key);
    }
    public function detach()
    {
        return $this->stream->detach();
    }
    public function getSize()
    {
        return $this->stream->getSize();
    }
    public function eof()
    {
        return $this->stream->eof();
    }
    public function tell()
    {
        return $this->stream->tell();
    }
    public function isReadable()
    {
        return $this->stream->isReadable();
    }
    public function isWritable()
    {
        return $this->stream->isWritable();
    }
    public function isSeekable()
    {
        return $this->stream->isSeekable();
    }
    public function rewind()
    {
        $this->seek(0);
    }
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->stream->seek($offset, $whence);
    }
    public function read($length)
    {
        return $this->stream->read($length);
    }
    public function write($string)
    {
        return $this->stream->write($string);
    }
    /**
     * Implement in subclasses to dynamically create streams when requested.
     *
     * @return StreamInterface
     * @throws \BadMethodCallException
     */
    protected function createStream()
    {
        throw new \BadMethodCallException('Not implemented');
    }
}