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

namespace PhpProfiler\Lib\Loop;

use Exception;
use LogicException;
use PhpProfiler\Lib\Loop\LoopProcess\CallableLoop;
use PhpProfiler\Lib\Loop\LoopProcess\RetryOnExceptionLoop;
use PHPUnit\Framework\TestCase;

class LoopBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $call_counter = 0;
        $execute_counter = 0;
        $builder = new LoopBuilder();
        $loop = $builder->addProcess(RetryOnExceptionLoop::class, [1, [Exception::class]])
            ->addProcess(
                CallableLoop::class,
                [
                    function () use (&$call_counter, &$execute_counter): bool {
                        if (++$call_counter === 1) {
                            throw new Exception();
                        }
                        if (++$execute_counter === 3) {
                            return false;
                        }
                        return true;
                    }
                ]
            )
            ->build();
        $loop->invoke();
        $this->assertSame(4, $call_counter);
        $this->assertSame(3, $execute_counter);
    }

    public function testThrowIfNotLoopProcess(): void
    {
        $builder = new LoopBuilder();
        $this->expectException(LogicException::class);
        $builder->addProcess('abcde', []);
    }

    public function testThrowIfNoLoopProcess(): void
    {
        $builder = new LoopBuilder();
        $this->expectException(LogicException::class);
        $builder->build();
    }
}
