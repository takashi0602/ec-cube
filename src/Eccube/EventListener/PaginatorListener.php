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

namespace Eccube\EventListener;

use Doctrine\ORM\Query;
use Eccube\Doctrine\ORM\Tools\Pagination\CountWalker;
use Eccube\Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QuerySubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaginatorListener implements EventSubscriberInterface
{
    const HINT_FETCH_JOIN_COLLECTION = 'knp_paginator.fetch_join_collection';

    public function items(ItemsEvent $event)
    {
        if (!class_exists('Eccube\Doctrine\ORM\Tools\Pagination\Paginator')) {
            return;
        }
        if (!$event->target instanceof Query) {
            return;
        }
        $event->stopPropagation();

        $useOutputWalkers = false;
        if (isset($event->options['wrap-queries'])) {
            $useOutputWalkers = $event->options['wrap-queries'];
        }

        $event->target
            ->setFirstResult($event->getOffset())
            ->setMaxResults($event->getLimit())
            ->setHint(CountWalker::HINT_DISTINCT, $event->options['distinct'])
        ;

        $fetchJoinCollection = true;
        if ($event->target->hasHint(self::HINT_FETCH_JOIN_COLLECTION)) {
            $fetchJoinCollection = $event->target->getHint(self::HINT_FETCH_JOIN_COLLECTION);
        }

        $paginator = new Paginator($event->target, $fetchJoinCollection);
        $paginator->setUseOutputWalkers($useOutputWalkers);
        if (($count = $event->target->getHint(QuerySubscriber::HINT_COUNT)) !== false) {
            $event->count = intval($count);
        } else {
            $event->count = count($paginator);
        }
        $event->items = iterator_to_array($paginator);
    }

    public static function getSubscribedEvents()
    {
        return [
            /*
             * Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QuerySubscriber\UsesPaginator
             * よりも先に実行させるため, 優先度を1に設定.
             *
             * 通常では
             * - Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QueryBuilderSubscriber
             * - Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QuerySubscriber\UsesPaginator
             * の順に実行されるが,
             * 優先度を1に設定し, UsesPaginatorの代わりにPaginatorListenerが動作するようにする
             */
            'knp_pager.items' => ['items', 1],
        ];
    }
}
