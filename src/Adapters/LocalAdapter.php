<?php

declare(strict_types=1);

namespace Fi1a\Filesystem\Adapters;

use Fi1a\Filesystem\Utils\LocalUtil;
use FilesystemIterator;
use InvalidArgumentException;
use OutOfBoundsException;

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

    /**
     * @var string
     */
    private $directory;

    /**
     * Конструктор
     */
    public function __construct(string $directory, int $rights = 0775)
    {
        if (!$directory) {
            throw new InvalidArgumentException(sprintf('Путь не может быть пустой'));
        }
        $this->directory = realpath($directory);
        if (!$this->directory) {
            throw new InvalidArgumentException(sprintf('Путь "%s" не существует', $directory));
        }
        if ($this->directory !== '/') {
            $this->directory = rtrim($this->directory, '/\\');
        }

        $this->rights = $rights;
    }

    /**
     * @inheritDoc
     */
    public function getName(string $path): string
    {
        $path = $this->normalizePath($path);
        $info = pathinfo($path);

        return $info['basename'];
    }

    /**
     * @inheritDoc
     */
    public function getExtension(string $path): ?string
    {
        $path = $this->normalizePath($path);
        $info = pathinfo($path);

        return array_key_exists('extension', $info) ? $info['extension'] : null;
    }

    /**
     * @inheritDoc
     */
    public function getBaseName(string $path): string
    {
        $path = $this->normalizePath($path);
        $info = pathinfo($path);

        return array_key_exists('filename', $info) ? $info['filename'] : '';
    }

    /**
     * @inheritDoc
     */
    public function peekParentPath(string $path)
    {
        $path = $this->normalizePath($path);
        $info = pathinfo($path);

        return $info['dirname'] && $info['dirname'] !== $path ? $info['dirname'] : false;
    }

    /**
     * @inheritDoc
     */
    public function canRead(string $path): bool
    {
        $path = $this->normalizePath($path);

        return is_readable($path);
    }

    /**
     * @inheritDoc
     */
    public function canWrite(string $path): bool
    {
        $path = $this->normalizePath($path);

        return is_writable($path);
    }

    /**
     * @inheritDoc
     */
    public function canExecute(string $path): bool
    {
        $path = $this->normalizePath($path);

        return is_executable($path);
    }

    /**
     * @inheritDoc
     */
    public function isFolderExist(string $path): bool
    {
        $path = $this->normalizePath($path);

        return is_dir($path);
    }

    /**
     * @inheritDoc
     */
    public function isFileExist(string $path): bool
    {
        $path = $this->normalizePath($path);

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
    public function makeFolder(string $path): bool
    {
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);

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
        $path = $this->normalizePath($path);
        $destination = $this->normalizePath($destination);

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
        $path = $this->normalizePath($path);
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
        $path = $this->normalizePath($path);
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
     * Возвращает путь
     */
    private function normalizePath(string $path): string
    {
        if (!LocalUtil::isAbsolutePath($path)) {
            $path = $this->directory . DIRECTORY_SEPARATOR . $path;
        }

        $path = is_file($path) || is_dir($path) || is_link($path) ? realpath($path) : $path;

        if (mb_strpos($path, $this->directory) !== 0) {
            throw new OutOfBoundsException(
                sprintf('Путь "%s" выходит за рамки определенного в адаптере.', $path)
            );
        }

        return $path;
    }
}
