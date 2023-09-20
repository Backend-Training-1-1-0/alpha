<?php

namespace Alpha\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /** @var resource|null A resource reference */
    private $stream;
    private bool $seekable = false;
    private bool $readable = false;
    private bool $writable = false;
    private mixed $uri;
    private int|null $size = null;
    private static array $readWriteHash = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

    public function __construct()
    {
    }

    public static function create(mixed $body = ''): self
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (\is_string($body) === true) {
            $resource = \fopen('php://temp', 'rw+');
            \fwrite($resource, $body);
            $body = $resource;
        }

        if (\is_resource($body) === true) {
            $new = new self();
            $new->stream = $body;
            $meta = \stream_get_meta_data($new->stream);
            $new->seekable = $meta['seekable'] && 0 === \fseek($new->stream, 0, \SEEK_CUR);
            $new->readable = isset(self::$readWriteHash['read'][$meta['mode']]);
            $new->writable = isset(self::$readWriteHash['write'][$meta['mode']]);

            return $new;
        }

        throw new \InvalidArgumentException('First argument to Stream::create() must be a string, resource or StreamInterface.');
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __toString()
    {
        if ($this->isSeekable()) {
            $this->seek(0);
        }

        return $this->getContents();
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            if (\is_resource($this->stream)) {
                \fclose($this->stream);
            }
            $this->detach();
        }
    }

    public function detach()
    {
        if (isset($this->stream) === false) {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    private function getUri(): mixed
    {
        if (false !== $this->uri) {
            $uri = $this->getMetadata('uri');
            $this->uri = $uri ?? false;
        }

        return $this->uri;
    }

    public function getSize(): ?int
    {
        if (null !== $this->size) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        if ($uri = $this->getUri()) {
            \clearstatcache(true, $uri);
        }

        $stats = \fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    public function tell(): int
    {
        if (isset($this->stream) === false) {
            throw new \RuntimeException('Stream is detached');
        }

        if (false === $result = @\ftell($this->stream)) {
            throw new \RuntimeException('Unable to determine stream position: ' . (isset(\error_get_last()['message']) ? \error_get_last()['message'] : ''));
        }

        return $result;
    }

    public function eof(): bool
    {
        return !isset($this->stream) || \feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        if (isset($this->stream) === false) {
            throw new \RuntimeException('Stream is detached');
        }

        if ($this->seekable === false) {
            throw new \RuntimeException('Stream is not seekable');
        }

        if (-1 === \fseek($this->stream, $offset, $whence)) {
            throw new \RuntimeException('Unable to seek to stream position "' . $offset . '" with whence ' . \var_export($whence, true));
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        if (isset($this->stream) === false) {
            throw new \RuntimeException('Stream is detached');
        }

        if ($this->writable === false) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        $this->size = null;

        if (false === $result = @\fwrite($this->stream, $string)) {
            throw new \RuntimeException('Unable to write to stream: ' . (isset(\error_get_last()['message']) ? \error_get_last()['message'] : ''));
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        if (isset($this->stream) === false) {
            throw new \RuntimeException('Stream is detached');
        }

        if ($this->readable === false) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        if (false === $result = @\fread($this->stream, $length)) {
            throw new \RuntimeException('Unable to read from stream: ' . (isset(\error_get_last()['message']) ? \error_get_last()['message'] : ''));
        }

        return $result;
    }

    public function getContents(): string
    {
        if (isset($this->stream) === false) {
            throw new \RuntimeException('Stream is detached');
        }

        if (false === $contents = @\stream_get_contents($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents: ' . (isset(\error_get_last()['message']) ? \error_get_last()['message'] : ''));
        }

        return $contents;
    }

    public function getMetadata(?string $key = null): mixed
    {
        if (isset($this->stream) === false) {
            return $key ? null : [];
        }

        $meta = \stream_get_meta_data($this->stream);

        if (null === $key) {
            return $meta;
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }
}