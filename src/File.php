<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

/**
 * Файл
 */
class File extends Node implements FileInterface
{
    /**
     * @inheritDoc
     */
    public function getExtension()
    {
        return $this->filesystem->getExtension($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function read()
    {
        if (!$this->isExist() || !$this->canRead()) {
            return false;
        }

        return $this->filesystem->readFile($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function getBaseName(): string
    {
        return $this->filesystem->getBaseName($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function write(string $content)
    {
        return $this->filesystem->write($this->getPath(), $content);
    }

    /**
     * @inheritDoc
     */
    public function canExecute(): bool
    {
        return $this->filesystem->canExecute($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function getMTime()
    {
        if (!$this->isExist()) {
            return false;
        }

        return $this->filesystem->getMTime($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function isExist(): bool
    {
        return $this->filesystem->isFileExist($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function isFile(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isFolder(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if (!$this->isExist()) {
            return false;
        }

        return $this->filesystem->filesize($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function make(): bool
    {
        if ($this->isExist()) {
            return false;
        }

        return $this->write('') !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(): bool
    {
        if (!$this->canWrite() || !$this->isExist()) {
            return false;
        }

        return $this->filesystem->deleteFile($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function copy(string $path): bool
    {
        return $this->filesystem->copyFile($this->getPath(), $path);
    }
}
