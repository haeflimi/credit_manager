<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use Concrete\Core\Support\Facade\Database;
use CreditManager\PageControllers\PosPageController;

class SelfServicePos extends PosPageController
{
    public function getCmCategory(){
        return 'Self Service POS';
    }

    public function getVisibleProducts(){
        $em = Database::connection()->getEntityManager();
        $productObjects = $em->getRepository('CreditManager\Entity\Product')->findBy(['isSelfService'=>1]);
        $products = [];
        foreach($productObjects as $pO){
            $product['id'] = $pO->getId();
            $product['name'] = $pO->getName();
            $product['price'] = $pO->getPrice();
            $product['image'] = is_object($pO->getImage())?$pO->getImage()->getRelativePath():'';
            $products[] = $product;
        }
        return $products;
    }
}
