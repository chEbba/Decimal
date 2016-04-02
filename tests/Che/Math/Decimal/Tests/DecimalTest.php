<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\Math\Decimal\Tests;

use Che\Math\Decimal\Decimal;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Test for Decimal
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DecimalTest extends TestCase
{
    /**
     * @test constructWithCorrectFormat
     * @dataProvider correctFormat
     *
     * @param string $string
     * @param int    $scale
     * @param string $value
     * @param string $case
     */
    public function constructWithCorrectFormat($string, $scale, $value, $case)
    {
        $decimal = new Decimal($string, $scale);
        $this->assertSame($value, $decimal->value(), $case);
    }

    /**
     * @test constructWithWrongFormat
     * @dataProvider wrongFormat
     *
     * @param string $string
     * @param string $case
     */
    public function constructWithWrongFormat($string, $case)
    {
        try {
            new Decimal($string);
        } catch (\InvalidArgumentException $e) {
            // Correct
            return;
        }

        $this->fail($case);
    }

    /**
     * @test constructNonScalar
     * @dataProvider nonScalar
     *
     * @param mixed $value
     * @param string $case
     */
    public function constructNonScalar($value, $case)
    {
        try {
            new Decimal($value);
        } catch (\InvalidArgumentException $e) {
            // Correct
            return;
        }

        $this->fail($case);
    }

    /**
     * @test constructNullScale
     * @dataProvider scaleDetection
     *
     * @param string $string
     * @param int    $expectedScale
     * @param string $case
     */
    public function constructDetectScale($string, $expectedScale, $case)
    {
        $decimal = new Decimal($string);
        $this->assertSame($expectedScale, $decimal->scale(), $case);
    }

    /**
     * @test precisionCalculation
     * @dataProvider decimalPrecisions
     *
     * @param string $value
     * @param string $precision
     */
    public function precisionCalculation($value, $precision)
    {
        $decimal = new Decimal($value);

        $this->assertSame($precision, $decimal->precision());
    }

    /**
     * @test add should correctly sum decimals
     */
    public function addGeneric()
    {
        $value1 = new Decimal('192341864273423843765928364.12345', 5);
        $value2 = new Decimal('1476127319823712827462.6789', 4);
        $sum = $value1->add($value2);

        $this->assertSame('192343340400743667478755826.80235', $sum->value());
    }

    /**
     * @test sub should correctly subtract
     */
    public function subGeneric()
    {
        $value1 = new Decimal('192341864273423843765928364.12345', 5);
        $value2 = new Decimal('1476127319823712827462.6789', 4);
        $result = $value1->sub($value2);

        $this->assertSame('192340388146104020053100901.44455', $result->value());
    }

    /**
     * @test mul should correctly multiply
     */
    public function mulGeneric()
    {
        $value1 = new Decimal('192341864273423843765928364.12345', 5);
        $value2 = new Decimal('1476127319823712827462.6789', 4);
        $result = $value1->mul($value2);

        $this->assertSame('283921080599825482308979477183220255889553969484.587310205', $result->value());
    }

    /**
     * @test div should correctly divide
     */
    public function divGeneric()
    {
        $value1 = new Decimal('192341864273423843765928364.12345', 5);
        $value2 = new Decimal('1476127319823712827462.6789', 4);
        $result = $value1->div($value2);

        $this->assertSame('130301.676346180', $result->value());
    }

    /**
     * @test divide with 0 throws exception
     * @expectedException InvalidArgumentException
     */
    public function divZero()
    {
        $value1 = new Decimal('123.45', 2);
        $value1->div(new Decimal('0.0000'));
    }

    /**
     * @test powGeneric
     */
    public function powGeneric()
    {
        $value1 = new Decimal('192341.12345', 5);
        $result = $value1->pow(7);

        $this->assertSame('9738790844484549401155595521762619025.20542465353794878176298358858515625', $result->value());
    }

    /**
     * @test powZero
     */
    public function powZero()
    {
        $value1 = new Decimal('192341.12345', 5);
        $result = $value1->pow(0);

        $this->assertSame('1', $result->value());
    }

    /**
     * @test powZero
     */
    public function powNegative()
    {
        try {
            $value1 = new Decimal('192341.12345', 5);
            $result = $value1->pow(-3);
        } catch (\InvalidArgumentException $e) {
            // pass
            return;
        }

        $this->fail('Exception was not raised for negative power');
    }

    /**
     * @test signumCorrectSign
     * @dataProvider decimalSigns
     *
     * @param string $string
     * @param int    $sign
     */
    public function signumCorrectSign($string, $sign)
    {
        $decimal = new Decimal($string);

        $this->assertSame($sign, $decimal->signum());
    }

    /**
     * @test negateCorrectConversion
     * @dataProvider negateDecimals
     *
     * @param string $value
     * @param int    $negative
     */
    public function negateCorrectConversion($value, $negative)
    {
        $decimal = new Decimal($value);

        $this->assertSame($negative, $decimal->negate()->value());
    }

    /**
     * @test absCorrectConversion
     * @dataProvider absDecimals
     *
     * @param string $value
     * @param string $abs
     */
    public function absCorrectConversion($value, $abs)
    {
        $decimal = new Decimal($value);

        $this->assertSame($abs, $decimal->abs()->value());
    }

    /**
     * @test roundModes
     * @dataProvider roundValues
     *
     * @param int    $mode
     * @param string $value
     * @param int    $scale
     * @param string $result
     */
    public function roundModes($mode, $value, $scale, $result)
    {
        $decimal = new Decimal($value);
        $rounded = $decimal->round($scale, $mode);

        $this->assertSame($result, $rounded->value(), sprintf('Round "%s" with mode "%d" and scale "%d"', $value, $mode, $scale));
    }

    public function correctFormat()
    {
        return array(
            array('123.45', 2, '123.45', 'Default positive conversion'),
            array('123.456', 2, '123.45', 'Fraction scale trim'),
            array('00123.45', 2, '123.45', 'Zero trim'),
            array('-123.45', 2, '-123.45', 'Default negative conversion'),
            array('-00123.45', 2, '-123.45', 'Negative zero trim'),
            array('123.45', 4, '123.4500', 'Fraction padding'),
            array('123', 4, '123.0000', 'Empty fraction padding'),
            array('+123.45', 2, '123.45', 'Plus sign'),
            array('123.10', 0, '123', 'Null fraction'),
            array('0.00', 2, '0.00', 'Zero'),
            array('0', 2, '0.00', 'Zero padding'),
            array('-0.00', 2, '0.00', 'Negative zero')
        );
    }

    public function wrongFormat()
    {
        return array(
            array('--123.45', 'Double sign'),
            array('*123.45', 'Wrong sign'),
            array('1a3.45', 'Wrong char in integer'),
            array('123.45a', 'Wrong char in fraction'),
            array('123.', 'Empty fraction'),
            array('.45', 'Empty integer'),
            array('-123,45', 'Wrong separator')
        );
    }

    public function nonScalar()
    {
        return array(
            array(new \DateTime(), 'Object'),
            array(array(), 'Array'),
            array(null, 'Null')
        );
    }

    public function decimalPrecisions()
    {
        return array(
            array('123.45', 3),
            array('-123.45', 3),
            array('0.00', 1),
            array('123.4500', 3),
        );
    }

    public function scaleDetection()
    {
        return array(
            array('123.45', 2, 'Simple value'),
            array('123.4500', 4, 'Trailing zeros'),
            array('123.00', 2, 'Zero fraction'),
            array('123', 0, 'Integer')
        );
    }

    public function decimalSigns()
    {
        return array(
            array('123.4500', 1),
            array('-123.4500', -1),
            array('0.0000', 0),
            array('0.91', 1),
            array('-0.91', -1)
        );
    }

    public function negateDecimals()
    {
        return array(
            array('-123.45', '123.45'),
            array('123.45', '-123.45'),
            array('0.00', '0.00'),
            array('0.91', '-0.91'),
            array('-0.91', '0.91')
        );
    }

    public function absDecimals()
    {
        return array(
            array('123.45', '123.45'),
            array('-123.45', '123.45'),
            array('0.00', '0.00'),
            array('0.91', '0.91'),
            array('-0.91', '0.91')
        );
    }

    public function roundValues()
    {
        return array(
            array(Decimal::ROUND_UP, '5.5', 0, '6'),
            array(Decimal::ROUND_UP, '2.5', 0, '3'),
            array(Decimal::ROUND_UP, '1.6', 0, '2'),
            array(Decimal::ROUND_UP, '1.1', 0, '2'),
            array(Decimal::ROUND_UP, '1.0', 0, '1'),
            array(Decimal::ROUND_UP, '-1.0', 0, '-1'),
            array(Decimal::ROUND_UP, '-1.1', 0, '-2'),
            array(Decimal::ROUND_UP, '-1.6', 0, '-2'),
            array(Decimal::ROUND_UP, '-2.5', 0, '-3'),
            array(Decimal::ROUND_UP, '-5.5', 0, '-6'),

            array(Decimal::ROUND_DOWN, '5.5', 0, '5'),
            array(Decimal::ROUND_DOWN, '2.5', 0, '2'),
            array(Decimal::ROUND_DOWN, '1.6', 0, '1'),
            array(Decimal::ROUND_DOWN, '1.1', 0, '1'),
            array(Decimal::ROUND_DOWN, '1.0', 0, '1'),
            array(Decimal::ROUND_DOWN, '-1.0', 0, '-1'),
            array(Decimal::ROUND_DOWN, '-1.1', 0, '-1'),
            array(Decimal::ROUND_DOWN, '-1.6', 0, '-1'),
            array(Decimal::ROUND_DOWN, '-2.5', 0, '-2'),
            array(Decimal::ROUND_DOWN, '-5.5', 0, '-5'),

            array(Decimal::ROUND_CEILING, '5.5', 0, '6'),
            array(Decimal::ROUND_CEILING, '2.5', 0, '3'),
            array(Decimal::ROUND_CEILING, '1.6', 0, '2'),
            array(Decimal::ROUND_CEILING, '1.1', 0, '2'),
            array(Decimal::ROUND_CEILING, '1.0', 0, '1'),
            array(Decimal::ROUND_CEILING, '-1.0', 0, '-1'),
            array(Decimal::ROUND_CEILING, '-1.1', 0, '-1'),
            array(Decimal::ROUND_CEILING, '-1.6', 0, '-1'),
            array(Decimal::ROUND_CEILING, '-2.5', 0, '-2'),
            array(Decimal::ROUND_CEILING, '-5.5', 0, '-5'),

            array(Decimal::ROUND_FLOOR, '5.5', 0, '5'),
            array(Decimal::ROUND_FLOOR, '2.5', 0, '2'),
            array(Decimal::ROUND_FLOOR, '1.6', 0, '1'),
            array(Decimal::ROUND_FLOOR, '1.1', 0, '1'),
            array(Decimal::ROUND_FLOOR, '1.0', 0, '1'),
            array(Decimal::ROUND_FLOOR, '-1.0', 0, '-1'),
            array(Decimal::ROUND_FLOOR, '-1.1', 0, '-2'),
            array(Decimal::ROUND_FLOOR, '-1.6', 0, '-2'),
            array(Decimal::ROUND_FLOOR, '-2.5', 0, '-3'),
            array(Decimal::ROUND_FLOOR, '-5.5', 0, '-6'),

            array(Decimal::ROUND_HALF_UP, '5.5', 0, '6'),
            array(Decimal::ROUND_HALF_UP, '2.5', 0, '3'),
            array(Decimal::ROUND_HALF_UP, '1.6', 0, '2'),
            array(Decimal::ROUND_HALF_UP, '1.1', 0, '1'),
            array(Decimal::ROUND_HALF_UP, '1.0', 0, '1'),
            array(Decimal::ROUND_HALF_UP, '-1.0', 0, '-1'),
            array(Decimal::ROUND_HALF_UP, '-1.1', 0, '-1'),
            array(Decimal::ROUND_HALF_UP, '-1.6', 0, '-2'),
            array(Decimal::ROUND_HALF_UP, '-2.5', 0, '-3'),
            array(Decimal::ROUND_HALF_UP, '-5.5', 0, '-6'),

            // Bug with zero at first truncated position.
            // It was been rounded to 6, because zero was ignored and the next digit was used
            array(Decimal::ROUND_HALF_UP, '5.06', 0, '5'),

            array(Decimal::ROUND_HALF_DOWN, '5.5', 0, '5'),
            array(Decimal::ROUND_HALF_DOWN, '2.5', 0, '2'),
            array(Decimal::ROUND_HALF_DOWN, '1.6', 0, '2'),
            array(Decimal::ROUND_HALF_DOWN, '1.1', 0, '1'),
            array(Decimal::ROUND_HALF_DOWN, '1.0', 0, '1'),
            array(Decimal::ROUND_HALF_DOWN, '-1.0', 0, '-1'),
            array(Decimal::ROUND_HALF_DOWN, '-1.1', 0, '-1'),
            array(Decimal::ROUND_HALF_DOWN, '-1.6', 0, '-2'),
            array(Decimal::ROUND_HALF_DOWN, '-2.5', 0, '-2'),
            array(Decimal::ROUND_HALF_DOWN, '-5.5', 0, '-5'),

            array(Decimal::ROUND_HALF_EVEN, '5.5', 0, '6'),
            array(Decimal::ROUND_HALF_EVEN, '2.5', 0, '2'),
            array(Decimal::ROUND_HALF_EVEN, '1.6', 0, '2'),
            array(Decimal::ROUND_HALF_EVEN, '1.1', 0, '1'),
            array(Decimal::ROUND_HALF_EVEN, '1.0', 0, '1'),
            array(Decimal::ROUND_HALF_EVEN, '-1.0', 0, '-1'),
            array(Decimal::ROUND_HALF_EVEN, '-1.1', 0, '-1'),
            array(Decimal::ROUND_HALF_EVEN, '-1.6', 0, '-2'),
            array(Decimal::ROUND_HALF_EVEN, '-2.5', 0, '-2'),
            array(Decimal::ROUND_HALF_EVEN, '-5.5', 0, '-6'),

            array(Decimal::ROUND_HALF_ODD, '5.5', 0, '5'),
            array(Decimal::ROUND_HALF_ODD, '2.5', 0, '3'),
            array(Decimal::ROUND_HALF_ODD, '1.6', 0, '2'),
            array(Decimal::ROUND_HALF_ODD, '1.1', 0, '1'),
            array(Decimal::ROUND_HALF_ODD, '1.0', 0, '1'),
            array(Decimal::ROUND_HALF_ODD, '-1.0', 0, '-1'),
            array(Decimal::ROUND_HALF_ODD, '-1.1', 0, '-1'),
            array(Decimal::ROUND_HALF_ODD, '-1.6', 0, '-2'),
            array(Decimal::ROUND_HALF_ODD, '-2.5', 0, '-3'),
            array(Decimal::ROUND_HALF_ODD, '-5.5', 0, '-5'),
        );
    }
}
