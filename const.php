<?php declare(strict_types=1);

/*
 * This file is part of the VV package.
 *
 * (c) Volodymyr Sarnytskyi <v00v4n@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VV {
    define('VV\START_TIME', time());
    define('VV\OS\WIN', str_starts_with(PHP_OS, 'WIN'));
    define('VV\OS\NIX', !OS\WIN);

    const ISCLI = PHP_SAPI === 'cli';
    const ISHTTP = !ISCLI;

    const CHARSET = 'utf-8';
    const DS = DIRECTORY_SEPARATOR;
    const DF = 'Y-m-d';
    const DTF = DF . ' H:i:s';
}
