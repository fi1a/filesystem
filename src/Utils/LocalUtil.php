<?php

declare(strict_types=1);

namespace Fi1a\Filesystem\Utils;

/**
 * Вспомогательные методы для файловой системы
 */
class LocalUtil
{
    /**
     * Является ли путь абсолютным
     */
    public static function isAbsolutePath(string $path): bool
    {
        if (preg_match('/^([a-zA-Z]+)\:\\\*/mi', $path) > 0) {
            return true;
        }

        return mb_substr($path, 0, 1) === '/';
    }

    /**
     * Возвращает путь до родительской папки
     *
     * @return string|false
     */
    public static function peekParentPath(string $path)
    {
        if (!$path) {
            return false;
        }
        $info = pathinfo($path);

        return $info['dirname'] && $info['dirname'] !== $path ? $info['dirname'] : false;
    }

    /**
     * Проверяет существование папки
     */
    public static function isFolderExist(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Проверяет существование файла
     */
    public static function isFileExist(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Возвращает нормализованный путь
     */
    public static function normalizePath(string $path): string
    {
        $tokens = [];
        $path = str_replace('\\', '/', $path);
        preg_match('#^(?P<root>([a-zA-Z]+:)?//?)#', $path, $matches);
        $root = !isset($matches['root']) || !$matches['root'] ? '' : $matches['root'];
        $path = mb_substr($path, strlen($root));
        $parts = explode('/', $path);

        foreach ($parts as $part) {
            if (!$part) {
                continue;
            }
            if ($part === '.' || $part === '..') {
                if ($part === '..' && count($tokens) !== 0) {
                    array_pop($tokens);
                }

                continue;
            }

            $tokens[] = $part;
        }

        return $root . implode('/', $tokens);
    }
}
