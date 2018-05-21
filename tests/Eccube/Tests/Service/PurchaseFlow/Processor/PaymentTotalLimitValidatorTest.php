<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalLimitValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentTotalLimitValidatorTest extends EccubeTestCase
{
    public function testCartValidate()
    {
        $validator = $this->newValidator(1000);

        $cart = new Cart();
        $cart->setTotal(100);

        $result = $validator->process($cart, new PurchaseContext());
        self::assertFalse($result->isError());
    }

    public function testCartValidateFail()
    {
        $validator = $this->newValidator(1000);

        $cart = new Cart();
        $cart->setTotal(1001);

        $result = $validator->process($cart, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    public function testOrderValidate()
    {
        $validator = $this->newValidator(1000);

        $order = new Order();
        $order->setTotal(100);

        $result = $validator->process($order, new PurchaseContext());
        self::assertFalse($result->isError());
    }

    public function testOrderValidateFail()
    {
        $validator = $this->newValidator(1000);

        $order = new Order();
        $order->setTotal(1001);

        $result = $validator->process($order, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    private function newValidator($maxTotalFee)
    {
        $result = $this->container->get(PaymentTotalLimitValidator::class);
        $rc = new \ReflectionClass(PaymentTotalLimitValidator::class);
        $prop = $rc->getProperty('maxTotalFee');
        $prop->setAccessible(true);
        $prop->setValue($result, $maxTotalFee);

        return $result;
    }
}
