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
}
