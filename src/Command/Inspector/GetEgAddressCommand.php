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

namespace PhpProfiler\Command\Inspector;

use PhpProfiler\Lib\Elf\Parser\ElfParserException;
use PhpProfiler\Lib\Elf\Tls\TlsFinderException;
use PhpProfiler\Lib\Process\MemoryReader\MemoryReader;
use PhpProfiler\Lib\Process\MemoryReader\MemoryReaderException;
use PhpProfiler\ProcessReader\PhpGlobalsFinder;
use PhpProfiler\ProcessReader\PhpSymbolReaderCreator;
use PhpProfiler\Lib\Elf\Process\ProcessSymbolReaderException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetEgAddressCommand
 * @package PhpProfiler\Command\Inspector
 */
final class GetEgAddressCommand extends Command
{
    public function configure(): void
    {
        $this->setName('inspector:eg_address')
            ->setDescription('get EG address from an outer process or thread')
            ->addOption('pid', 'p', InputOption::VALUE_REQUIRED, 'process id');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MemoryReaderException
     * @throws ProcessSymbolReaderException
     * @throws ElfParserException
     * @throws TlsFinderException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $pid = $input->getOption('pid');
        if (is_null($pid)) {
            $error_output = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $error_output->writeln('pid is not specified');
            return 1;
        }
        $pid = filter_var($pid, FILTER_VALIDATE_INT);
        if ($pid === false) {
            $error_output = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $error_output->writeln('pid is not integer');
            return 2;
        }

        $memory_reader = new MemoryReader();
        $php_globals_finder = new PhpGlobalsFinder(
            (new PhpSymbolReaderCreator($memory_reader))->create($pid)
        );

        $output->writeln('0x' . dechex($php_globals_finder->findExecutorGlobals()));

        return 0;
    }
}
