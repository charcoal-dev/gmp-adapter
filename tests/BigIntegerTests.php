<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\Gmp\Tests;

use Charcoal\Adapters\Gmp\BigInteger;

/**
 * Class BigIntegerTests
 */
class BigIntegerTests extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing behavior when an object of BigNumber is directly treated as a string value
     * @return void
     */
    public function testAsString(): void
    {
        $bN = new BigInteger(0xfffe);
        $this->assertEquals("65534", strval($bN));
        $this->assertEquals(5, strlen((string)$bN));
    }

    /**
     * @return void
     */
    public function testValueAsInt(): void
    {
        $this->assertEquals(254, (new BigInteger(0xfe))->toInt());
        $this->assertEquals(-1, (new BigInteger(0))->sub(1)->toInt());
        $this->assertEquals(9223372036854775807, (new BigInteger(PHP_INT_MAX))->toInt());
        $this->assertEquals(-9223372036854774808, (new BigInteger(PHP_INT_MIN))->add(1000)->toInt());
        $this->assertEquals(0, (new BigInteger(100))->div(2)->sub(50)->toInt());
    }

    /**
     * Test overflow when used "toInt" method
     * @return void
     */
    public function testOverflowAsInt(): void
    {
        $bN = (new BigInteger(PHP_INT_MAX))->add(1); // Simply add 1
        $this->assertEquals("9223372036854775808", $bN->toString());
        $this->expectException('OverflowException');
        $bN->toInt();
    }

    /**
     * @return void
     */
    public function testUnderflowAsInt(): void
    {
        $bN = (new BigInteger(PHP_INT_MIN))->sub(1); // Simply subtract 1
        $this->assertEquals("-9223372036854775809", $bN->toString());
        $this->expectException('UnderflowException');
        $bN->toInt();
    }

    /**
     * @return void
     */
    public function testSerialization(): void
    {
        $bN = new BigInteger(PHP_INT_MAX); // 9223372036854775807
        $bN = $bN->add(PHP_INT_MAX); // 18446744073709551614

        // string(99) "O:32:"Charcoal\Adapters\Gmp\BigInteger":1:{s:6:" * int";O:3:"GMP":1:{i:0;s:16:"fffffffffffffffe";}}"
        $ser = serialize($bN);

        /** @var BigInteger $bN2 */
        $bN2 = unserialize($ser);
        $this->assertEquals("18446744073709551614", $bN2->toString());
    }

    /**
     * @return void
     */
    public function testCompares(): void
    {
        $bN = new BigInteger(1000);
        $this->assertTrue($bN->lessThan(1001));
        $this->assertTrue($bN->lessThanOrEquals(1000));
        $this->assertTrue($bN->greaterThanOrEquals(1000));
        $this->assertTrue($bN->greaterThan(999));
        $this->assertTrue($bN->equals(1000));

        $this->assertFalse($bN->equals(999));
        $this->assertFalse($bN->equals(-1000));
        $this->assertFalse($bN->greaterThan(1000));
        $this->assertFalse($bN->greaterThan(1001));
        $this->assertFalse($bN->lessThan(1000));
        $this->assertFalse($bN->lessThan(999));
    }

    /**
     * @return void
     */
    public function testStates(): void
    {
        $this->assertTrue((new BigInteger(PHP_INT_MIN))->sub(1)->isSigned());
        $this->assertTrue((new BigInteger(1))->sub(2)->isSigned());
        $this->assertTrue((new BigInteger(0xff))->add(0xfffe)->isUnsigned());
    }

    /**
     * @return void
     */
    public function testNullPaddings(): void
    {
        // string(64) "000046757271616e000000000000000000000000000000000000000000000000"
        $bytes = bin2hex(str_pad("\0\0Furqan", 32, "\0", STR_PAD_RIGHT));

        $bN = BigInteger::fromBase16($bytes);
        // string(60) "46757271616e000000000000000000000000000000000000000000000000"
        $bN16 = $bN->toBase16();
        $this->assertEquals("46757271616e000000000000000000000000000000000000000000000000", $bN16);

        // Initial 2 bytes (0x00 and 0x00) are dropped when instantiated as BigInteger
    }
}