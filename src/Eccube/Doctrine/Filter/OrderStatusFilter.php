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

namespace Eccube\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class OrderStatusFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // 決済処理中/購入処理中を除く.
        if ($targetEntity->reflClass->getName() === 'Eccube\Entity\Order') {
            return $targetTableAlias.'.order_status_id <> 7 AND '.$targetTableAlias.'.order_status_id <> 8';
        }

        // 決済処理中/購入処理中を除く.
        if ($targetEntity->reflClass->getName() === 'Eccube\Entity\Master\OrderStatus') {
            return $targetTableAlias.'.id <> 7 AND '.$targetTableAlias.'.id <> 8';
        }

        return '';
    }
}
