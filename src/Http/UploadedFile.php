<?php

namespace Alpha\Http;

use Psr\Http\Message\{
    StreamInterface,
    UploadedFileInterface,
};

class UploadedFile implements UploadedFileInterface
{
    /** @var array */
    private const ERRORS = [
        \UPLOAD_ERR_OK => 1,
        \UPLOAD_ERR_INI_SIZE => 1,
        \UPLOAD_ERR_FORM_SIZE => 1,
        \UPLOAD_ERR_PARTIAL => 1,
        \UPLOAD_ERR_NO_FILE => 1,
        \UPLOAD_ERR_NO_TMP_DIR => 1,
        \UPLOAD_ERR_CANT_WRITE => 1,
        \UPLOAD_ERR_EXTENSION => 1,
    ];

    private string $clientFilename = '';
    private string $clientMediaType = '';
    private int $error;
    private string|null $file = null;
    private bool $moved = false;
    private int $size;
    private StreamInterface|null $stream = null;

    public function __construct(
        StreamInterface|string $streamOrFile,
        int                    $size,
        int                    $errorStatus,
        string|null            $clientFilename = null,
        string|null            $clientMediaType = null,
    )
    {
        if (isset(self::ERRORS[$errorStatus]) === false) {
            throw new \InvalidArgumentException('Upload file error status must be an integer value and one of the "UPLOAD_ERR_*" constants');
        }

        $this->error = $errorStatus;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;

        if (\UPLOAD_ERR_OK === $this->error) {
            if (\is_string($streamOrFile) === true && '' !== $streamOrFile) {
                $this->file = $streamOrFile;
            }

            if (\is_resource($streamOrFile) === true) {
                $this->stream = Stream::create($streamOrFile);
            }

            if ($streamOrFile instanceof StreamInterface) {
                $this->stream = $streamOrFile;
            }

            throw new \InvalidArgumentException('Invalid stream or file provided for UploadedFile');
        }
    }

    /**
     * @throws \RuntimeException if is moved or not ok
     */
    private function validateActive(): void
    {
        if (\UPLOAD_ERR_OK !== $this->error) {
            throw new \RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new \RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }

    public function getStream(): StreamInterface
    {
        $this->validateActive();

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        if (false === $resource = @\fopen($this->file, 'r')) {
            throw new \RuntimeException(\sprintf('The file "%s" cannot be opened: %s', $this->file, \error_get_last()['message'] ?? ''));
        }

        return Stream::create($resource);
    }

    public function moveTo($targetPath): void
    {
        $this->validateActive();

        if (!\is_string($targetPath) || '' === $targetPath) {
            throw new \InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }

        if (null !== $this->file) {
            $this->moved = 'cli' === \PHP_SAPI ? @\rename($this->file, $targetPath) : @\move_uploaded_file($this->file, $targetPath);

            if (false === $this->moved) {
                throw new \RuntimeException(\sprintf('Uploaded file could not be moved to "%s": %s', $targetPath, \error_get_last()['message'] ?? ''));
            }

            return;
        }

        $stream = $this->getStream();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        if (false === $resource = @\fopen($targetPath, 'w')) {
            throw new \RuntimeException(\sprintf('The file "%s" cannot be opened: %s', $targetPath, \error_get_last()['message'] ?? ''));
        }

        $dest = Stream::create($resource);

        while (!$stream->eof()) {
            if (!$dest->write($stream->read(1048576))) {
                break;
            }
        }

        $this->moved = true;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}