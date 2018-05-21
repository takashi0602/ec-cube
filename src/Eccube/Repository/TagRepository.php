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

use Eccube\Annotation\Repository;
use Eccube\Entity\Tag;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @Repository
 */
class TagRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * タグを保存する.
     *
     * @param  Tag $tag タグ
     */
    public function save($tag)
    {
        if (!$tag->getId()) {
            $sortNoTop = $this->findOneBy([], ['sort_no' => 'DESC']);
            $sort_no = 0;
            if (!is_null($sortNoTop)) {
                $sort_no = $sortNoTop->getSortNo();
            }

            $tag->setSortNo($sort_no + 1);
        }

        $em = $this->getEntityManager();
        $em->persist($tag);
        $em->flush($tag);
    }

    /**
     * タグ一覧を取得する.
     *
     * @return Tag[] タグの配列
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('t')->orderBy('t.sort_no', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * タグを削除する.
     *
     * @param  Tag $Tag 削除対象のタグ
     */
    public function delete($Tag)
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        $em->createQuery("DELETE \Eccube\Entity\ProductTag pt WHERE pt.Tag = :tag")->execute(['tag' => $Tag]);

        $this
            ->createQueryBuilder('t')
            ->update()
            ->set('t.sort_no', 't.sort_no - 1')
            ->where('t.sort_no > :sort_no')
            ->setParameter('sort_no', $Tag->getSortNo())
            ->getQuery()
            ->execute();

        $em->remove($Tag);
        $em->flush($Tag);

        $em->commit();
    }
}
