<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Iterator;

use ArrayIterator;
use ArrayObject;
use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Iterator\HydratingIteratorIterator;
use PHPUnit\Framework\TestCase;

/**
 * @covers Laminas\Hydrator\Iterator\HydratingIteratorIterator<extended>
 */
class HydratingIteratorIteratorTest extends TestCase
{
    public function testHydratesObjectAndClonesOnCurrent()
    {
        $data = [
            ['foo' => 'bar'],
            ['baz' => 'bat'],
        ];

        $iterator = new ArrayIterator($data);
        $object   = new ArrayObject();

        $hydratingIterator = new HydratingIteratorIterator(new ArraySerializable(), $iterator, $object);

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
        $this->assertNotSame(
            $object,
            $hydratingIterator->current(),
            'Hydrating Iterator did not clone the object'
        );

        $hydratingIterator->next();
        $this->assertEquals(new ArrayObject($data[1]), $hydratingIterator->current());
    }

    public function testUsingStringForObjectName()
    {
        $data = [
            ['foo' => 'bar'],
        ];

        $iterator = new ArrayIterator($data);

        $hydratingIterator = new HydratingIteratorIterator(new ArraySerializable(), $iterator, '\ArrayObject');

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
    }

    public function testThrowingInvalidArgumentExceptionWhenSettingPrototypeToInvalidClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $hydratingIterator = new HydratingIteratorIterator(
            new ArraySerializable(),
            new ArrayIterator(),
            'not a real class'
        );
    }
}
