<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

use const DIRECTORY_SEPARATOR;

/**
 * Папка
 */
class Folder extends Node implements FolderInterface
{
    /**
     * @inheritDoc
     */
    public function isExist(): bool
    {
        return $this->filesystem->isFolderExist($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function isFile(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isFolder(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isLink(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        $nodes = $this->filesystem->all($this->getPath());
        if ($nodes === false) {
            return false;
        }

        return new NodeCollection($this->filesystem, $nodes);
    }

    /**
     * @inheritDoc
     */
    public function allFiles()
    {
        $nodes = $this->filesystem->allFiles($this->getPath());
        if ($nodes === false) {
            return false;
        }

        return new NodeCollection($this->filesystem, $nodes);
    }

    /**
     * @inheritDoc
     */
    public function allFolders()
    {
        $nodes = $this->filesystem->allFolders($this->getPath());
        if ($nodes === false) {
            return false;
        }

        return new NodeCollection($this->filesystem, $nodes);
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if (!$this->isExist()) {
            return false;
        }
        /**
         * @var int[] $sizes
         */
        $sizes = [0];
        $lists = $this->all();
        if ($lists !== false) {
            /**
             * @var int[] $sizes
             */
            $sizes = $lists->getSize();
        }

        return array_sum($sizes);
    }

    /**
     * @inheritDoc
     */
    public function make(): bool
    {
        if ($this->isExist() || !$this->canWrite()) {
            return false;
        }

        return $this->filesystem->makeFolder($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function delete(): bool
    {
        if (!$this->isExist() || !$this->canWrite()) {
            return false;
        }
        $lists = $this->all();
        if ($lists !== false) {
            $lists->__call('delete', []);
        }

        return $this->filesystem->deleteFolder($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function copy(string $path): bool
    {
        $folder = $this->filesystem->factoryFolder($path);
        if (
            !$this->canRead()
            || !$folder->canWrite()
            || (!$folder->isExist() && !$folder->make())
        ) {
            return false;
        }
        /**
         * @var NodeInterface[]|false $lists
         */
        $lists = $this->all();
        if ($lists !== false) {
            foreach ($lists as $node) {
                if (!$node->copy($folder->getPath() . DIRECTORY_SEPARATOR . $node->getName())) {
                    return false;
                }
            }
        }

        return true;
    }
}
