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

namespace PhpProfiler\Lib\Binary;

use PhpProfiler\Lib\UInt64;

/**
 * Class BinaryReader
 * @package PhpProfiler\Lib\Binary
 */
final class BinaryReader
{
    public function read8(ByteReaderInterface $data, int $offset): int
    {
        return $data[$offset];
    }

    public function read16(ByteReaderInterface $data, int $offset): int
    {
        return ($data[$offset + 1] << 8) | $data[$offset];
    }

    public function read32(ByteReaderInterface $data, int $offset): int
    {
        return ($data[$offset + 3] << 24)
            | ($data[$offset + 2] << 16)
            | ($data[$offset + 1] << 8)
            | $data[$offset];
    }

    public function read64(ByteReaderInterface $data, int $offset): UInt64
    {
        return new UInt64(
            $this->read32($data, $offset + 4),
            $this->read32($data, $offset),
        );
    }

    public function readString(ByteReaderInterface $data, int $offset, int $size): string
    {
        return $data->createSliceAsString($offset, $size);
    }
}
