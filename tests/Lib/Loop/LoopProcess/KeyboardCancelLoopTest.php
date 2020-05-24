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

namespace PhpProfiler\Lib\Loop\LoopProcess;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class KeyboardCancelLoopTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testReturnFalseIfCancelKeyPressed(): void
    {
        $reflection = new ReflectionClass(KeyboardCancelLoop::class);
        $keyboard_cancel_loop = $reflection->newInstanceWithoutConstructor();
        $keyboard_input_stream = fopen('php://memory', 'rw');
        (function () use ($keyboard_input_stream) {
            /** @var KeyboardCancelLoop $this */
            $this->chain = new CallableLoop(fn () => true);
            $this->cancel_key = 'q';
            $this->keyboard_input = $keyboard_input_stream;
        })->bindTo($keyboard_cancel_loop, $keyboard_cancel_loop)();
        $this->assertSame(true, $keyboard_cancel_loop->invoke());
        fwrite($keyboard_input_stream, 'q');
        rewind($keyboard_input_stream);
        $this->assertSame(false, $keyboard_cancel_loop->invoke());
    }
}
