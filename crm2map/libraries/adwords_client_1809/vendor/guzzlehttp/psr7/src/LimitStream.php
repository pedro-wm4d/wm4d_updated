<?php

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;
/**
 * Decorator used to return only a subset of a stream
 */
class LimitStream implements StreamInterface
{
    /** @var int Offset to start reading from */
    private $offset;
    /** @var int Limit the number of bytes that can be read */
    private $limit;
    /**
     * @param StreamInterface $stream Stream to wrap
     * @param int             $limit  Total number of bytes to allow to be read
     *                                from the stream. Pass -1 for no limit.
     * @param int             $offset Position to seek to before reading (only
     *                                works on seekable streams).
     */
    public function __construct(StreamInterface $stream, $limit = -1, $offset = 0)
    {
        $this->stream = $stream;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }
    public function eof()
    {
        // Always return true if the underlying stream is EOF
        if ($this->stream->eof()) {
            return true;
        }
        // No limit and the underlying stream is not at EOF
        if ($this->limit == -1) {
            return false;
        }
        return $this->stream->tell() >= $this->offset + $this->limit;
    }
    /**
     * Returns the size of the limited subset of data
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === ($length = $this->stream->getSize())) {
            return null;
        } elseif ($this->limit == -1) {
            return $length - $this->offset;
        } else {
            return min($this->limit, $length - $this->offset);
        }
    }
    /**
     * Allow for a bounded seek on the read limited stream
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($whence !== SEEK_SET || $offset < 0) {
            throw new \RuntimeException(sprintf('Cannot seek to offset % with whence %s', $offset, $whence));
        }
        $offset += $this->offset;
        if ($this->limit !== -1) {
            if ($offset > $this->offset + $this->limit) {
                $offset = $this->offset + $this->limit;
            }
        }
        $this->stream->seek($offset);
    }
    /**
     * Give a relative tell()
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->stream->tell() - $this->offset;
    }
    /**
     * Set the offset to start limiting from
     *
     * @param int $offset Offset to seek to and begin byte limiting from
     *
     * @throws \RuntimeException if the stream cannot be seeked.
     */
    public function setOffset($offset)
    {
        $current = $this->stream->tell();
        if ($current !== $offset) {
            // If the stream cannot seek to the offset position, then read to it
            if ($this->stream->isSeekable()) {
                $this->stream->seek($offset);
            } elseif ($current > $offset) {
                throw new \RuntimeException("Could not seek to stream offset {$offset}");
            } else {
                $this->stream->read($offset - $current);
            }
        }
        $this->offset = $offset;
    }
    /**
     * Set the limit of bytes that the decorator allows to be read from the
     * stream.
     *
     * @param int $limit Number of bytes to allow to be read from the stream.
     *                   Use -1 for no limit.
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function read($length)
    {
        if ($this->limit == -1) {
            return $this->stream->read($length);
        }
        // Check if the current position is less than the total allowed
        // bytes + original offset
        $remaining = $this->offset + $this->limit - $this->stream->tell();
        if ($remaining > 0) {
            // Only return the amount of requested data, ensuring that the byte
            // limit is not exceeded
            return $this->stream->read(min($remaining, $length));
        }
        return '';
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