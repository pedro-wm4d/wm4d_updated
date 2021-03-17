<?php

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;
/**
 * Stream that when read returns bytes for a streaming multipart or
 * multipart/form-data stream.
 */
class MultipartStream implements StreamInterface
{
    private $boundary;
    /**
     * @param array  $elements Array of associative arrays, each containing a
     *                         required "name" key mapping to the form field,
     *                         name, a required "contents" key mapping to a
     *                         StreamInterface/resource/string, an optional
     *                         "headers" associative array of custom headers,
     *                         and an optional "filename" key mapping to a
     *                         string to send as the filename in the part.
     * @param string $boundary You can optionally provide a specific boundary
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $elements = array(), $boundary = null)
    {
        $this->boundary = $boundary ?: sha1(uniqid('', true));
        $this->stream = $this->createStream($elements);
    }
    /**
     * Get the boundary
     *
     * @return string
     */
    public function getBoundary()
    {
        return $this->boundary;
    }
    public function isWritable()
    {
        return false;
    }
    /**
     * Get the headers needed before transferring the content of a POST file
     */
    private function getHeaders(array $headers)
    {
        $str = '';
        foreach ($headers as $key => $value) {
            $str .= "{$key}: {$value}\r\n";
        }
        return "--{$this->boundary}\r\n" . trim($str) . '

';
    }
    /**
     * Create the aggregate stream that will be used to upload the POST data
     */
    protected function createStream(array $elements)
    {
        $stream = new AppendStream();
        foreach ($elements as $element) {
            $this->addElement($stream, $element);
        }
        // Add the trailing boundary with CRLF
        $stream->addStream(stream_for("--{$this->boundary}--\r\n"));
        return $stream;
    }
    private function addElement(AppendStream $stream, array $element)
    {
        foreach (array('contents', 'name') as $key) {
            if (!array_key_exists($key, $element)) {
                throw new \InvalidArgumentException("A '{$key}' key is required");
            }
        }
        $element['contents'] = stream_for($element['contents']);
        if (empty($element['filename'])) {
            $uri = $element['contents']->getMetadata('uri');
            if (substr($uri, 0, 6) !== 'php://') {
                $element['filename'] = $uri;
            }
        }
        list($body, $headers) = $this->createElement($element['name'], $element['contents'], isset($element['filename']) ? $element['filename'] : null, isset($element['headers']) ? $element['headers'] : array());
        $stream->addStream(stream_for($this->getHeaders($headers)));
        $stream->addStream($body);
        $stream->addStream(stream_for('
'));
    }
    /**
     * @return array
     */
    private function createElement($name, StreamInterface $stream, $filename, array $headers)
    {
        // Set a default content-disposition header if one was no provided
        $disposition = $this->getHeader($headers, 'content-disposition');
        if (!$disposition) {
            $headers['Content-Disposition'] = $filename === '0' || $filename ? sprintf('form-data; name="%s"; filename="%s"', $name, basename($filename)) : "form-data; name=\"{$name}\"";
        }
        // Set a default content-length header if one was no provided
        $length = $this->getHeader($headers, 'content-length');
        if (!$length) {
            if ($length = $stream->getSize()) {
                $headers['Content-Length'] = (string) $length;
            }
        }
        // Set a default Content-Type if one was not supplied
        $type = $this->getHeader($headers, 'content-type');
        if (!$type && ($filename === '0' || $filename)) {
            if ($type = mimetype_from_filename($filename)) {
                $headers['Content-Type'] = $type;
            }
        }
        return array($stream, $headers);
    }
    private function getHeader(array $headers, $key)
    {
        $lowercaseHeader = strtolower($key);
        foreach ($headers as $k => $v) {
            if (strtolower($k) === $lowercaseHeader) {
                return $v;
            }
        }
        return null;
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
}