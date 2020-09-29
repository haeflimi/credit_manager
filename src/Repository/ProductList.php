<?php
namespace CreditManager\Repository;

use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Support\Facade\Database;
use Package;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

/**
 *
 * An object that allows a filtered list of pages to be returned.
 *
 */
class ProductList extends DatabaseItemList
{

    /** @var  \Closure | integer | null */
    protected $permissionsChecker;
    /**
     * Columns in this array can be sorted via the request.
     * @var array
     */
    protected $autoSortColumns = array(
        'p.name'
    );

    public function createQuery()
    {
        return $this->query->select('p.Id, p.name, p.price');
    }

    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        $query->from('cmProduct', 'p');
        return $query;
    }

    /**
     * @param $queryRow
     * @return \Concrete\Core\File\File
     */
    public function getResult($queryRow)
    {
        $em = Database::connection()->getEntityManager();
        return $em->getRepository('CreditManager\Entity\Product')->findOneBy( ['Id' => $queryRow['Id']] );
    }

    public function getTotalResults()
    {
        $em = Database::connection()->getEntityManager();
        return $em->getRepository('CreditManager\Entity\Product')->count();
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\CollectionKey';
    }
}
