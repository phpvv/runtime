<?php declare(strict_types=1);

/*
 * This file is part of the VV package.
 *
 * (c) Volodymyr Sarnytskyi <v00v4n@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace {

    /**
     * Shortcut returns
     *
     * @return \VV\VV
     */
    function vv(): \VV\VV {
        static $vv;
        if (!$vv) $vv = new VV\VV;

        return $vv;
    }
}

namespace VV {
    use JetBrains\PhpStorm\Pure;

    /**
     * Checks whether (string)data is empty string. If array was passed, retuns true if all emlements is emt()
     *
     * @param mixed $data
     *
     * @return bool
     */
    function emt(mixed $data): bool {
        if (is_array($data)) {
            return !array_filter($data, function ($d) {
                return !emt($d);
            });
        }
        if (is_object($data)) return false;

        return (string)$data == '';
    }

    /**
     * Convert scalar value to array
     *
     * @param mixed $data      Scalar value
     * @param bool  $keepEmpty If this parameter is true and $data is ampty function returns empty array
     *
     * @return array
     */
    #[Pure]
    function toa(mixed $data, bool $keepEmpty = true): array {
        if (is_array($data)) return $data;
        if (!is_object($data) && $keepEmpty && (string)$data == '') return [];

        return [$data];
    }

    /**
     * Get var(s) from array(s) by key(s)
     *
     * @param string[]        $keys
     * @param array[]|mixed[] $arrays
     *
     * @return mixed[]
     */
    function aget(array $keys, ...$arrays): array {
        $arrays[] = null; // add non-array value for default
        $total = [];
        foreach ($arrays as $arr) {
            if ($notarr = !is_array($arr)) {
                $arr = array_fill_keys($keys, $arr);
            }

            $total += $arr;
            if ($notarr) break;
        }

        $result = [];
        foreach ($keys as $k) $result[$k] = $total[$k];

        return $result;
    }

    /**
     * Returns an array containing all the entries of $arr which have keys that are passed in the rest arguments
     *
     * @param array      $arr
     * @param string|int ...$keys
     *
     * @return array
     */
    #[Pure]
    function intersectKey(array $arr, string|int ...$keys): array {
        return array_intersect_key($arr, array_flip($keys));
    }

    /**
     * Returns an array containing all the entries of $arr which not have keys that are passed in the rest arguments
     *
     * @param array      $arr
     * @param string|int ...$keys
     *
     * @return array
     */
    #[Pure]
    function diffKey(array $arr, string|int ...$keys): array {
        return array_diff_key($arr, array_flip($keys));
    }

    /**
     * Returns an array containing all the entries of $arr which not have keys that are passed in the rest arguments
     *
     * @param array      $arr
     * @param string|int ...$keys
     *
     * @return void
     */
    function unsetKey(array &$arr, string|int ...$keys) {
        foreach ($keys as $v) unset($arr[$v]);
    }

    /**
     * Returns value from array by key (or values by keys) and unsets key(s) from array
     *
     * @param array                     $arr
     * @param string|int|string[]|int[] $key
     *
     * @return mixed
     */
    function shiftKey(array &$arr, string|int|array $key): mixed {
        if (is_array($key)) {
            $vals = [];
            foreach ($key as $v) {
                $vals = shiftKey($arr, $v);
            }

            return $vals;
        }

        $val = $arr[$key] ?? null;
        unset($arr[$key]);

        return $val;
    }

    /**
     * @param int|null $time
     * @param bool     $append
     *
     * @return string
     */
    function dt(int $time = null, bool $append = true): string {
        if ($time === null) return date(\VV\DTF);
        if ($append) $time += time();

        return date(\VV\DTF, $time);
    }

    /**
     * Returns Uniq ID
     *
     * @return string 32 characters
     */
    #[Pure]
    function uid(): string {
        return md5(uniqid(more_entropy: true) . mt_rand());
    }

    #[Pure]
    function mbUcfirst(string $str, string $encoding = CHARSET): string {
        return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) . mb_substr($str, 1, mb_strlen($str, $encoding) - 1, $encoding);
    }

    #[Pure]
    function mbLcfirst(string $str, string $encoding = CHARSET): string {
        return mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding) . mb_substr($str, 1, mb_strlen($str, $encoding) - 1, $encoding);
    }

    function mbUcwords(string $str, string $encoding = CHARSET): string {
        return preg_replace_callback('/[^\s]+/', function ($m) use ($encoding) {
            return mbUcfirst($m[0], $encoding);
        }, $str);
    }

    function mbStrTr($str, $from, $to = null): string {
        if (is_array($from)) {
            $to = $from;
            $from = array_keys($from);
        } else {
            $r = '/.{1}|[^\x00]{1}$/us';
            preg_match_all($r, $from, $m);
            $from = $m[0];
            preg_match_all($r, $to, $m);
            $to = $m[0];
        }


        return str_replace($from, $to, $str);
    }

    function mbStrSplit($str, $len = 1): array {
        $arr = [];
        $i = 0;
        while (($c = mb_substr($str, $i * $len, $len)) !== '') {
            $arr[] = $c;
            $i += $len;
        }

        return $arr;
    }

    /**
     * Converts under_score style string to camelCase
     *
     * @param string $under_scored
     *
     * @return string
     */
    function camelCase(string $under_scored): string {
        return lcfirst(StudlyCaps($under_scored));
    }

    /**
     * Converts under_score style string to StudlyCaps (camelCase with upper first letter)
     *
     * @param string $under_scored
     *
     * @return string
     */
    function StudlyCaps(string $under_scored): string {
        return preg_replace_callback('/(?:^|[-_]+)(\w)/', fn($m) => strtoupper($m[1]), $under_scored);
    }

    /**
     * Converts camelCase style string to under_scored style
     *
     * @param string $camelCase
     * @param bool   $incDashes If true - will replace dashes with underscores
     *
     * @return string
     */
    function under_score(string $camelCase, $incDashes = false): string {
        if ($incDashes) $camelCase = str_replace('-', '_', $camelCase);

        return strtolower(preg_replace('/([a-z0-9])([A-Z])/', '\1_\2', $camelCase));
    }

    function translit($str, $map = []): string {
        static $called = false;
        static $cyr = 'абвгдезийклмнопрстуфхъыэі';
        static $lat = 'abvgdeziyklmnoprstufh\'iei';
        static $smap = [
            'ж' => 'zh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ь' => '', 'ю' => 'yu', 'я' => 'ya',
            'ї' => 'yi', 'є' => 'ie', 'ё' => 'yo', 'х' => 'kh',
        ];

        if (!$called) {
            $cyr = $cyr . mb_strtoupper($cyr);
            $lat = $lat . strtoupper($lat);
            $called = true;
        }

        if ($map == 'ua') $map = ['и' => 'y'];

        $map = $map2 = array_merge($smap, $map);
        foreach ($map2 as $k => $v) $map[mb_strtoupper($k)] = ucfirst($v);

        $str = mbStrTr($str, $map);
        $str = mbStrTr($str, $cyr, $lat);

        return $str;
    }

    function switchKeyboard($str): array {
        static $tr = [
            'en' => 'abcdefghijklmnopqrstuvwxyz[];\',.ABCDEFGHIJKLMNOPQRSTUVWXYZ{}:"<>`~@#$^&',
            'ru' => 'фисвуапршолдьтщзйкыегмцчняхъжэбюФИСВУАПРШОЛДЬТЩЗЙКЫЕГМЦЧНЯХЪЖЭБЮёЁ"№;:?',
            'uk' => 'фисвуапршолдьтщзйкіегмцчняхїжєбюФИСВУАПРШОЛДЬТЩЗЙКІЕГМЦЧНЯХЇЖЄБЮ\'₴"№;:?',
        ];

        $max = 0;
        $curlang = 'en';

        foreach ($tr as $lang => $trs) {
            for ($i = $cnt = 0; $i < mb_strlen($str); $i++) {
                if (strpos($trs, mb_substr($str, $i, 1))) $cnt++;
            }
            if ($cnt > $max) {
                $curlang = $lang;
                $max = $cnt;
            }
        }

        $res = [];
        foreach ($tr as $lang => $trs) {
            if ($curlang == $lang) continue;

            $tmp = \VV\mbStrTr($str, $tr[$curlang], $trs);
            if ($tmp == $str || in_array($tmp, $res)) {
                continue;
            }

            $res[] = $tmp;
        }

        return $res;
    }



    /**
     * @param string   $str
     * @param string   $startStr
     * @param string   $endStr
     * @param int|null $limit
     * @param bool     $trim
     *
     * @return string[]|string|null limit == 1 ? string|null : string[]
     */
    function strSearch(string $str, string $startStr, string $endStr, int $limit = null, bool $trim = true): array|string|null {
        $startLen = strlen($startStr);
        $endLen = strlen($endStr);

        $arr = [];
        $cnt = 0;
        while (($startPos = strpos($str, $startStr)) !== false) {
            $startPos += $startLen;
            $str = substr($str, $startPos);
            $endPos = strpos($str, $endStr);

            $value = $endPos !== false ? substr($str, 0, $endPos) : $str;
            $arr[] = $trim ? trim($value) : $value;

            $str = substr($str, $endPos + $endLen);

            if ($limit && (++$cnt) >= $limit) break;
        }

        return $limit === 1 ? ($arr[0] ?? null) : $arr;
    }

    function makeTree(array &$rows, $parentIdField = 'parent_id'): array {
        $unset = [];
        foreach ($rows as $k => &$v) {
            if ($v[$parentIdField]) {
                if (isset($rows[$v[$parentIdField]])) {
                    $nv = &$rows[$v[$parentIdField]];
                    if (!isset($nv['_children'])) $nv['_children'] = [];
                    $nv['_children'][$k] = &$v;
                    $unset[] = $k;
                } else {
                    unset($rows[$k]);
                }
            }
        }

        unset($v);
        foreach ($unset as $v) {
            unset($rows[$v]);
        }

        return $rows;
    }

    /**
     * make array with elemnts having key '_level'
     *
     * @param array    $tree - array maked by makeTree()
     * @param callable $decorator
     * @param int      $level
     *
     * @return array
     */
    function makeTreeLeveled(array $tree, $decorator = null, $level = 0): array {
        $res = [];
        if (!$decorator) {
            $decorator = function (&$row, $level) {
                $row['_level'] = $level;
            };
        }
        foreach ($tree as $k => $v) {
            if (isset($v['_child'])) {
                $ch = &$v['_child'];
            } else $ch = null;
            unset($v['_child']);

            if ($decorator($v, $level) !== false) $res[$k] = $v;

            if ($ch) $res = $res + makeTreeLeveled($ch, $decorator, $level + 1);
        }

        return $res;
    }

    function backtrace($name, $n = 0, $assoc = false) {
        $trace = debug_backtrace();
        if (!isset($trace[$n + 1])) return false;
        $trace = $trace[$n + 1];

        if (!$isarr = is_array($name)) $name = [$name];

        $res = \VV\aget($name, $trace);
        if (!$assoc) $res = array_values($res);

        return (!$isarr ? reset($res) : $res);
    }

    function caller($n = 0) {
        return backtrace('function', $n + 1);
    }

    function relativePath($from, $to): string {
        $from = str_replace('\\', '/', realpath($from));
        $to = str_replace('\\', '/', realpath($to));

        $arFrom = explode('/', rtrim($from, '/'));
        $arTo = explode('/', rtrim($to, '/'));
        while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
            array_shift($arFrom);
            array_shift($arTo);
        }

        return str_pad('', count($arFrom) * 3, '../') . implode('/', $arTo);
    }

    function ideUrl($file, $line = 1, $inhtml = null): string {
        $rfile = relativePath(vv()->appPath(), $file);
        $urlfile = rawurlencode($rfile);
        $name = vv()->appName();
        $url = "phpide://$name/$urlfile?$line";
        if ($inhtml) {
            if ($inhtml === true) $inhtml = "<b>$file</b> line <b>$line</b>";

            return '<a href="' . $url . '">' . $inhtml . '</a>';
        }

        return $url;
    }

    function toUtf8(string $str, string $incoding = null): array|string {
        if (!$str) return $str;
        if (!$incoding) $incoding = 'cp1251';
        if (is_array($str)) {
            array_walk_recursive($str, function ($v) use ($incoding) {
                return \VV\toUtf8($v, $incoding);
            });

            return $str;
        }

        if (mb_check_encoding($str, 'UTF-8')) return $str;

//        mb_detect_encoding($v, "CP1251");
        return iconv($incoding, 'utf-8//IGNORE', $str);
    }

    /**
     * Convert number from any to any base
     *
     * @param string|int      $number
     * @param string|int      $to   The base to convert number to or string with row of digits
     * @param string|int|null $from The base number is in or string with row of digits
     * @param int|null        $length
     *
     * @return string
     */
    function baseConvert(string|int $number, string|int $to, string|int $from = null, int $length = null): string {
        static $def = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (!$to) throw new \InvalidArgumentException('Argument $to is empty');
        if (!$from) $from = 10;

        if (is_string($to)) {
            $to = mb_strlen($trow = $to);
        } else {
            $trow = substr($def, 0, $to);
        }

        if (is_string($from)) {
            $from = mb_strlen($frow = $from);
        } else {
            $frow = substr($def, 0, $from);
        }

        // convert number to dec (as base) if it is not yet
        if (!($from == 10 && $frow == '0123456789')) {
            for ($i = $dec = 0, $l = mb_strlen($number); $i < $l; $i++) {
                $c = mb_substr($number, $i, 1);
                $p = mb_strpos($frow, $c);
                if ($p === false) {
                    throw new \InvalidArgumentException(
                        'Symbol `' . $c . '` does not exists in diapason `' . $frow . '`'
                    );
                }
                $dec = bcadd($dec, bcmul($p, bcpow($from, $l - $i - 1)));
                // $dec += $p * pow($from, $l - $i - 1);
            }
        } elseif (preg_match('/[^\d]/', ($dec = $number))) {
            throw new \InvalidArgumentException('Wrong number');
        }

        if ($to == 10 && $trow == '0123456789') {
            $res = (string)$dec;
        } else {
            $t = 0;
            if (!$dec) {
                $t = 1;
            } else {
                while ((int)bcdiv($dec, bcpow($to, $t), 0)) $t++;
            }
            for ($res = ''; $t--; $dec = bcmod($dec, $d)) {
                // $p = (int)($dec / ($d = pow($to, $t)));
                $p = (int)bcdiv($dec, ($d = bcpow($to, $t)));
                $res .= mb_substr($trow, $p, 1);
            }
        }

        if ($length && ($m = $length - mb_strlen($res)) > 0) {
            $res = str_repeat(mb_substr($trow, 0, 1), $m) . $res;
        }

        return $res;
    }

    function genLetterCode(int $length, string $advLetters = null, string $letters = null): string {
        static $def = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if (!$letters) $letters = $def . $advLetters;
        $lastIdx = strlen($letters) - 1;
        for ($i = 0, $code = ''; $i < $length; $i++) {
            $code .= $letters[mt_rand(0, $lastIdx)];
        }

        return $code;
    }

    /**
     * Converts shorthand notations of size to bytes
     *
     * @param string $val
     *
     * @return int
     */
    function size2bytes(string $val): int {
        if (!$val || !preg_match('/^(\d+)\s*([a-z])?/i', trim($val), $m)) return 0;

        $val = (int)$m[1];
        if (empty($m[2])) return $val;

        return $val * pow(1024, array_search(strtolower($m[2]), ['b', 'k', 'm', 'g']));
    }

    /**
     * Converts bytes to shorthand notations of size
     *
     * @param int   $val
     * @param int   $precision
     * @param float $k
     *
     * @return string
     */
    #[Pure]
    function bytes2size(int $val, int $precision = 2, float $k = 0.9): string {
        $s = null;
        foreach (['B', 'KB', 'MB', 'GB'] as $s) {
            if (($v = ($val / 1024)) < $k) break;
            $val = $v;
        }

        return round($val, $precision) . $s;
    }

    /**
     * @param mixed $data
     * @param int   $advopts
     *
     * @return string
     */
    function jsonEncode(mixed $data, int $advopts = 0): string {
        return @json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | $advopts);
    }

    function increasePhpTimeout($sec, &$left = null) {
        if (!$left) $left = \VV\START_TIME + (int)ini_get('max_execution_time') - time();
        set_time_limit($sec + $left);
    }

    /**
     * Transform seconds to string like h:mm:ss (or h:mm:ss.ms)
     *
     * @param float $seconds
     * @param bool  $incDays   True for division hours by incDays
     * @param int   $precision Count of digits for milliseconds (only if type of $seconds is float)
     *
     * @return string
     */
    #[Pure]
    function sec2hms(float $seconds, bool $incDays = false, int $precision = 3): string {
        if ($_ = $seconds < 0) $seconds = abs($seconds);
        if ($incDays) {
            $d = floor($seconds / 86400);
            $h = floor($seconds % 86400 / 3600);
        } else {
            $h = floor($seconds / 3600);
            $d = 0;
        }
        $m = sprintf('%02s', floor($seconds % 3600 / 60));
        $s = sprintf('%02s', floor($seconds % 3600 % 60));

        $res = ($_ ? '-' : '') . ($d ? $d . ' ' : '') . "$h:$m:$s";

        if (is_float($seconds) && $precision) {
            $dec = 10 ** $precision;
            $milli = round($seconds * $dec) % $dec;
            $res .= '.' . ($milli);
        }

        return $res;
    }

    function timeProfiler($name, $result = false): ?array {
        static $profiles = [];
        $p = &$profiles[$name];
        if (!$p) {
            $p = [0, 0, 0];
            $result = false;
        }
        if (!$result) {
            if (!$p[0]) {
                $p[0] = microtime(true);

                return null;
            }
            $p[1] += microtime(true) - $p[0];
            $p[2]++;
            $p[0] = 0;
        }

        return [$p[1], $p[2], $p[1] / $p[2]];
    }

    /**
     * @param string        $prompt
     * @param string|null   $dflt
     * @param string[]|null $allowed
     *
     * @return string|null
     */
    function readline(string $prompt, string $dflt = null, array $allowed = null): ?string {
        if ($allowed) $prompt .= ' (' . implode('|', $allowed) . ')';
        if ($r1 = $dflt !== null) $prompt .= " [$dflt]";

        do {
            echo $prompt . ': ';
            $h = fopen('php://stdin', 'r');
            $line = trim(fgets($h));
            if (!$line) $line = $dflt;

            if (!$allowed || in_array($line, $allowed)) {
                return $line;
            }

            echo "'$line' is not allowed. ";
        } while (true);
    }

    /**
     * Chunks string by $chunks
     *
     * strChunk('01234567898765', 4) -> 0123 4567898765
     * strChunk('01234567898765', [4, 8]) -> 0123 4567 898765
     * strChunk('01234567898765', [5, -3]) -> 01234 567898 765
     *
     * @param string $str
     * @param int[]  $chunks
     * @param string $separator
     *
     * @return string
     */
    function strChunks(string $str, array $chunks, string $separator = ' '): string {
        return implode($separator, splitChunks($str, $chunks));
    }

    /**
     * Splits string to array by specified positions
     *
     * @param string $str
     * @param int[]  $pos
     * @param bool   $skipLast If true, will skip last element
     *
     * @return string[]
     */
    function splitPos(string $str, array $pos, bool $skipLast = false): array {
        $parts = [];
        /** @noinspection PhpUnusedLocalVariableInspection */
        $p = $pp = 0;
        $len = strlen($str);
        foreach ($pos as $p) {
            if ($p < 0) $p = $len + $p;
            $l = abs($pp - $p);
            $parts[] = substr($str, $p = min($p, $pp), $l);
            $pp = $p + $l;
        }
        if (!$skipLast) $parts[] = substr($str, $pp);

        return $parts;
    }

    /**
     * Splits string to array by specified positions
     *
     * @param string $str
     * @param int[]  $chunks
     * @param bool   $skipLast If true, will skip last element
     *
     * @return string[]
     */
    function splitChunks(string $str, array $chunks, $skipLast = false): array {
        $pos = $chunks;
        $shift = 0;
        foreach ($pos as $k => $v) {
            $pos[$k] += $shift;
            $shift += $v;
        }

        return splitPos($str, $pos, $skipLast);
    }

    function splitNoEmpty(string $str, string $delimrx, bool $trim = true): array {
        $delimrx = str_replace('!', '\!', $delimrx);
        if ($trim) {
            $delimrx = '\s*' . $delimrx . '\s*';
            $str = trim($str);
        }

        return preg_split("!$delimrx!", $str, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /**
     * Returns true if $object is instance of at least one of $classes
     *
     * @param mixed  $object
     * @param string ...$classes Use only this syntax: Foo\Bar::class
     *
     * @return bool
     */
    #[Pure]
    function instOf(mixed $object, string ...$classes): bool {
        if (!is_object($object)) return false;

        foreach ($classes as $classs) {
            if (is_a($object, $classs)) return true;
        }

        return false;
    }

    /**
     * Cleans the output buffer of all levels
     */
    function obClean(): void {
        while (ob_get_level() > 0) ob_end_clean();
    }

    /**
     * Creates read string iterator
     *
     * @param string $data
     * @param int    $blockSize
     *
     * @return \Generator
     */
    function readStringIterator(string $data, int $blockSize = 64 * 1024): iterable {
        if (strlen($data) <= $blockSize) {
            yield $data;

            return;
        }

        $prevpos = 0;
        while ($part = substr($data, $prevpos, $prevpos += $blockSize)) {
            yield $part;
        }
    }


    /**
     * Creates read file iterator
     *
     * @param string|\SplFileObject $file
     * @param int                   $blockSize
     * @param bool                  $autoclose
     *
     * @return iterable
     */
    function readFileIterator(string|\SplFileObject $file, int $blockSize = 64 * 1024, bool $autoclose = true): iterable {
        if (is_string($file)) $file = fopen($file, 'r');

        if (\VV\isStream($file)) {
            try {
                while (!feof($file)) {
                    yield fread($file, $blockSize);
                }
            } finally {
                if ($autoclose) fclose($file);
            }

            return;
        }

        if ($file instanceof \SplFileObject) {
            try {
                while (!$file->eof()) {
                    yield $file->fread($blockSize);
                }
            } finally {
                if ($autoclose) $file = null; // just in case
            }

            return;
        }
        throw new \InvalidArgumentException('Wrong file type');
    }

    /**
     * Returns true if the $value is a stream resource
     *
     * @param mixed $value
     *
     * @return bool
     */
    function isStream(mixed $value): bool {
        return is_resource($value) && get_resource_type($value) == 'stream';
    }

    /**
     * Returns an array containing the results of applying the $callback to the corresponding index of $iterable used as arguments for the callback.
     *
     * Callback example:
     * <code>
     * $sqrEven = function (int $value, mixed &$key): mixed {
     *     if ($value % 2) return $key = false; // skip odd
     *     return $value ** 2; // sqr even
     * };
     * </code>
     *
     * @param iterable      $iterable The iterable being copied.
     * @param \Closure|null $callback Callback to apply to each element.
     *                                You can skip element by receiving &$key by reference and assign it to false.
     * @param bool          $useKeys  Whether to use the iterator element keys as index.
     *
     * @return array
     */
    function mapIterable(iterable $iterable, \Closure $callback = null, bool $useKeys = true): array {
        $array = [];
        foreach ($iterable as $key => $value) {
            $result = $callback ? $callback($value, $key) : $value;
            if ($key === false) continue; // `&$key = false` - inside callback

            if ($useKeys) {
                $array[$key] = $result;
            } else {
                $array[] = $result;
            }
        }

        return $array;
    }
}
