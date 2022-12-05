<?php

declare(strict_types=1);

namespace Fi1a\Filesystem\Adapters;

use FilesystemIterator;
use InvalidArgumentException;

use const DIRECTORY_SEPARATOR;
use const LOCK_EX;
use const LOCK_SH;
use const LOCK_UN;

/**
 * Адаптер файловой системы
 */
class LocalAdapter implements FilesystemAdapterInterface
{
    /**
     * Файлы
     */
    private const MODE_FILE = 8;

    /**
     * Папки
     */
    private const MODE_FOLDER = 16;

    /**
     * @var int
     */
    private $rights;

    public function __construct(int $rights = 0775)
    {
        $this->rights = $rights;
    }

    /**
     * @inheritDoc
     */
    public function getName(string $path): string
    {
        $this->checkPath($path);
        $info = pathinfo($path);

        return $info['basename'];
    }

    /**
     * @inheritDoc
     */
    public function getExtension(string $path): ?string
    {
        $this->checkPath($path);
        $info = pathinfo($path);

        return array_key_exists('extension', $info) ? $info['extension'] : null;
    }

    /**
     * @inheritDoc
     */
    public function getBaseName(string $path): string
    {
        $this->checkPath($path);
        $info = pathinfo($path);

        return array_key_exists('filename', $info) ? $info['filename'] : '';
    }

    /**
     * @inheritDoc
     */
    public function peekParentPath(string $path)
    {
        $this->checkPath($path);
        $info = pathinfo($path);

        return $info['dirname'] && $info['dirname'] !== $path ? $info['dirname'] : false;
    }

    /**
     * @inheritDoc
     */
    public function canRead(string $path): bool
    {
        $this->checkPath($path);

        return is_readable($path);
    }

    /**
     * @inheritDoc
     */
    public function canWrite(string $path): bool
    {
        $this->checkPath($path);

        return is_writable($path);
    }

    /**
     * @inheritDoc
     */
    public function canExecute(string $path): bool
    {
        $this->checkPath($path);

        return is_executable($path);
    }

    /**
     * @inheritDoc
     */
    public function isFolderExist(string $path): bool
    {
        $this->checkPath($path);

        return is_dir($path);
    }

    /**
     * @inheritDoc
     */
    public function isFileExist(string $path): bool
    {
        $this->checkPath($path);

        return is_file($path);
    }

    /**
     * @inheritDoc
     */
    public function isFolder(string $path): bool
    {
        return $this->isFolderExist($path);
    }

    /**
     * @inheritDoc
     */
    public function isFile(string $path): bool
    {
        return $this->isFileExist($path);
    }

    /**
     * @inheritDoc
     */
    public function isLink(string $path): bool
    {
        $this->checkPath($path);

        return is_link($path);
    }

    /**
     * @inheritDoc
     */
    public function makeFolder(string $path): bool
    {
        $this->checkPath($path);
        if ($this->isFolderExist($path)) {
            return false;
        }

        return mkdir($path, $this->rights, true);
    }

    /**
     * @inheritDoc
     */
    public function all(string $path)
    {
        $this->checkPath($path);
        if (!$this->isFolder($path) || !$this->canRead($path)) {
            return false;
        }

        return $this->doAll($path, self::MODE_FILE | self::MODE_FOLDER);
    }

    /**
     * @inheritDoc
     */
    public function allFiles(string $path)
    {
        $this->checkPath($path);
        if (!$this->isFolder($path)) {
            return false;
        }

        return $this->doAll($path, self::MODE_FILE);
    }

    /**
     * @inheritDoc
     */
    public function allFolders(string $path)
    {
        $this->checkPath($path);
        if (!$this->isFolder($path)) {
            return false;
        }

        return $this->doAll($path, self::MODE_FOLDER);
    }

    /**
     * @inheritDoc
     */
    public function filesize(string $path)
    {
        $this->checkPath($path);
        if (!$this->isFileExist($path)) {
            return false;
        }

        return filesize($path);
    }

    /**
     * @inheritDoc
     */
    public function deleteFile(string $path): bool
    {
        $this->checkPath($path);
        if (!$this->canWrite($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     * @inheritDoc
     */
    public function deleteFolder(string $path): bool
    {
        $this->checkPath($path);
        if (!$this->canWrite($path)) {
            return false;
        }

        return @rmdir($path);
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, $content)
    {
        $this->checkPath($path);
        if ($this->isFileExist($path) && !$this->canWrite($path)) {
            return false;
        }
        $return = @file_put_contents($path, (string) $content, LOCK_EX);
        @chmod($path, $this->rights);

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function copyFile(string $path, string $destination): bool
    {
        $this->checkPath($path);
        $this->checkPath($destination);

        $return = @copy($path, $destination);
        if ($return) {
            @chmod($destination, $this->rights);
        }

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function readFile(string $path)
    {
        $this->checkPath($path);
        if (
            !$this->isFileExist($path)
            || !$this->canRead($path)
            || !($file = @fopen($path, 'r'))
        ) {
            return false;
        }

        flock($file, LOCK_SH);
        $content = stream_get_contents($file);
        flock($file, LOCK_UN);
        fclose($file);

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function getMTime(string $path)
    {
        $this->checkPath($path);
        if (!$this->isFileExist($path)) {
            return false;
        }

        return @filemtime($path);
    }

    /**
     * Возвращает дочерние элементы
     *
     * @return string[]
     */
    private function doAll(string $path, int $flags)
    {
        $iterator = new FilesystemIterator(
            $path,
            FilesystemIterator::KEY_AS_PATHNAME
            | FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::SKIP_DOTS
            | FilesystemIterator::FOLLOW_SYMLINKS
        );
        $nodes = [];
        while ($iterator->valid()) {
            if (
                ($flags === self::MODE_FILE && !$iterator->isFile())
                || ($flags === self::MODE_FOLDER && !$iterator->isDir())
                || $iterator->isDot()
            ) {
                $iterator->next();

                continue;
            }
            $nodes[] = $iterator->getPath() . DIRECTORY_SEPARATOR . $iterator->getFilename();
            $iterator->next();
        }

        return $nodes;
    }

    /**
     * Выбрасывает исключение при пустом пути
     */
    private function checkPath(string $path): void
    {
        if (!$path) {
            throw new InvalidArgumentException('Путь не может быть пустым');
        }
    }
}
