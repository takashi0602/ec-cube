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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShippingStatus
 *
 * @ORM\Table(name="mtb_shipping_status")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\ShippingStatusRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class ShippingStatus extends AbstractMasterEntity
{
    /**
     * 出荷準備中.
     */
    const PREPARED = 1;

    /**
     * 出荷済み.
     */
    const SHIPPED = 2;

    /**
     * キャンセル.
     */
    const CANCELED = 3;
}
