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
            ->andWhere('pc.nodeId IN (:categoryNodeIds)')
            ->orderBy('p.name')
            ->setParameter('categoryNodeIds', [1977,1974]);
        $query = $qb->getQuery();
        $productObjects = $query->getResult();
        $products = [];
        foreach($productObjects as $po){
            if(empty($po))continue;
            $products[] = [
                'id' => $po->getId(),
                'name' => $po->getName(),
                'price' => $po->getPrice()
            ];
        }
        return $products;
    }
}
