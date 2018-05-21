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

namespace Eccube\Repository;

use Eccube\Entity\Delivery;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Payment;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * DelivRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeliveryRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    /**
     * @deprecated 呼び出し元で制御する
     *
     * @param $id
     *
     * @return Delivery|null|object
     */
    public function findOrCreate($id)
    {
        if ($id == 0) {
            $em = $this->getEntityManager();

            $SaleType = $em
                ->getRepository(SaleType::class)
                ->findOneBy([], ['sort_no' => 'DESC']);

            $Delivery = $this->findOneBy([], ['sort_no' => 'DESC']);

            $sortNo = 1;
            if ($Delivery) {
                $sortNo = $Delivery->getSortNo() + 1;
            }

            $Delivery = new Delivery();
            $Delivery
                ->setSortNo($sortNo)
                ->setVisible(true)
                ->setSaleType($SaleType);
        } else {
            $Delivery = $this->find($id);
        }

        return $Delivery;
    }

    /**
     * 複数の販売種別から配送業者を取得
     *
     * @param $saleTypes
     *
     * @return array
     */
    public function getDeliveries($saleTypes)
    {
        $deliveries = $this->createQueryBuilder('d')
            ->where('d.SaleType in (:saleTypes)')
            ->andWhere('d.visible = :visible')
            ->setParameter('saleTypes', $saleTypes)
            ->setParameter('visible', true)
            ->getQuery()
            ->getResult();

        return $deliveries;
    }

    /**
     * 選択可能な配送業者を取得
     *
     * @param $saleTypes
     * @param $payments
     *
     * @return array
     */
    public function findAllowedDeliveries($saleTypes, $payments)
    {
        $d = $this->getDeliveries($saleTypes);
        $arr = [];

        foreach ($d as $Delivery) {
            $paymentOptions = $Delivery->getPaymentOptions();

            foreach ($paymentOptions as $PaymentOption) {
                foreach ($payments as $Payment) {
                    if ($PaymentOption->getPayment() instanceof Payment) {
                        if ($PaymentOption->getPayment()->getId() == $Payment['id']) {
                            $arr[$Delivery->getId()] = $Delivery;
                            break;
                        }
                    }
                }
            }
        }

        return array_values($arr);
    }
}
