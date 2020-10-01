<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use Concrete\Core\Support\Facade\Database;
use CreditManager\PageControllers\PosPageController;

class Pos extends PosPageController
{
    public function getCmCategory(){
        return 'Catering POS';
    }

    public function getVisibleProducts(){
        $em = Database::connection()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('CreditManager\Entity\Product', 'p')
            ->innerJoin('CreditManager\Entity\ProductCategory', 'pc','WITH', 'p.Id = pc.pId')
            ->where('p.isOrder = :isOrder')
            ->andWhere('pc.nodeId IN (:categoryNodeIds)')
            ->orderBy('p.name')
            ->setParameter('isOrder', 1)
            ->setParameter('categoryNodeIds', [1977,1974]);
        $query = $qb->getQuery();
        $productObjects = $query->getResult();
        return $products;
    }
}
