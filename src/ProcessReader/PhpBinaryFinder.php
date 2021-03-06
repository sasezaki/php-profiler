<?php

/**
 * This file is part of the sj-i/php-profiler package.
 *
 * (c) sji <sji@sj-i.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PhpProfiler\ProcessReader;

/**
 * Class PhpBinaryFinder
 * @package PhpProfiler\ProcessReader
 */
final class PhpBinaryFinder
{
    /**
     * @param int $pid
     * @return string
     */
    public function findByProcessId(int $pid): string
    {
        return readlink("/proc/{$pid}/exe");
    }
}
