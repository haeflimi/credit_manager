<?php
namespace CreditManager\Repository;

use Concrete\Core\User\UserList;

/**
 *
 * Extend the User List to create our own Credit Manager User List
 *
 */
class CmUserList extends UserList
{

    /** @var  \Closure | integer | null */
    protected $permissionsChecker;
    /**
     * Columns in this array can be sorted via the request.
     * @var array
     */
    protected $autoSortColumns = array(
        'balance'
    );

    public function createQuery()
    {
        parent::createQuery();
        return $this->query->leftJoin('u', 'cmCreditRecord', 'cmu', 'u.uID = cmu.uId')
            ->addSelect('SUM(cmu.value) as balance');
    }

    public function filterByBalance()
    {
        return $this->query->having("SUM(cmu.value) <> 0");
    }
}
