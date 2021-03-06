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

namespace PhpProfiler\Lib\Elf\Process;

use FFI\CData;
use PhpProfiler\Lib\Elf\SymbolResolver\Elf64SymbolResolver;
use PhpProfiler\Lib\Process\MemoryMap\ProcessMemoryArea;
use PhpProfiler\Lib\Process\MemoryReader\MemoryReaderInterface;
use PhpProfiler\Lib\Process\MemoryReader\MemoryReaderException;

/**
 * Class ProcessModuleSymbolReader
 * @package PhpProfiler\ProcessReader
 */
final class ProcessModuleSymbolReader implements ProcessSymbolReaderInterface
{
    private Elf64SymbolResolver $symbol_resolver;
    /** @var ProcessMemoryArea[] */
    private array $memory_areas;
    private int $base_address;
    private MemoryReaderInterface $memory_reader;
    private ?int $tls_block_address;
    private int $pid;

    /**
     * ProcessModuleSymbolResolver constructor.
     * @param int $pid
     * @param Elf64SymbolResolver $symbol_resolver
     * @param ProcessMemoryArea[] $memory_areas
     * @param MemoryReaderInterface $memory_reader
     * @param int|null $tls_block_address
     */
    public function __construct(
        int $pid,
        Elf64SymbolResolver $symbol_resolver,
        array $memory_areas,
        MemoryReaderInterface $memory_reader,
        ?int $tls_block_address
    ) {
        $this->pid = $pid;
        $this->symbol_resolver = $symbol_resolver;
        $this->memory_areas = $memory_areas;
        $this->base_address = hexdec(current($memory_areas)->begin);
        $this->memory_reader = $memory_reader;
        $this->tls_block_address = $tls_block_address;
    }

    /**
     * @param string $symbol_name
     * @return \FFI\CArray|null
     * @throws MemoryReaderException
     * @throws ProcessSymbolReaderException
     */
    public function read(string $symbol_name): ?CData
    {
        $symbol = $this->symbol_resolver->resolve($symbol_name);
        if ($symbol->isUndefined()) {
            return null;
        }
        $base_address = $this->base_address;
        if ($symbol->isTls()) {
            if (is_null($this->tls_block_address)) {
                throw new ProcessSymbolReaderException(
                    'trying to resolve TLS symbol but cannot find TLS block address'
                );
            }
            $base_address = $this->tls_block_address;
        }
        $address = $base_address + $symbol->st_value->toInt();
        return $this->memory_reader->read($this->pid, $address, $symbol->st_size->toInt());
    }

    /**
     * @param string $symbol_name
     * @return int|null
     * @throws ProcessSymbolReaderException
     */
    public function resolveAddress(string $symbol_name): ?int
    {
        $symbol = $this->symbol_resolver->resolve($symbol_name);
        if ($symbol->isUndefined()) {
            return null;
        }
        $base_address = $this->base_address;

        if ($symbol->isTls()) {
            if (is_null($this->tls_block_address)) {
                throw new ProcessSymbolReaderException(
                    'trying to resolve TLS symbol but cannot find TLS block address'
                );
            }
            $base_address = $this->tls_block_address;
        }
        return $base_address + $symbol->st_value->toInt();
    }
}
