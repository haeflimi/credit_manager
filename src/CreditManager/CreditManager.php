<?php
namespace CreditManager;

use Concrete\Core\Support\Facade\Database;
use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * This Class wraps the core Functionality of the Credit Manager
 *
 * Class CreditManager
 * @package CreditManager
 */
class CreditManager
{
    protected $qb; // Doctrine Query Builder
    protected $em; // Doctrine Entity Manager

    public function __construct($obj = null)
    {
        $this->db = Database::connection();
        $this->qb = $this->db->createQueryBuilder();
        $this->em = $this->db->getEntityManager();
    }

    public static function getUserBalance($user){
        if(is_object($user)) {
            $uId = $user->getUserID();
        } else {
            $uId = $user;
        }
        $db = Database::connection();
        $em = $db->getEntityManager();
        $userRecords = $em->getRepository('CreditManager\Entity\CreditRecord')
                ->findBy(array('uId' => $uId));
        $sum = 0;
        foreach($userRecords as $record){
            $sum += $record->getValue();
        }

        return $sum;
    }

    public static function getUserHistory($user){
        if(is_object($user)) {
            $uId = $user->getUserID();
        } else {
            $uId = $user;
        }
        $db = Database::connection();
        $em = $db->getEntityManager();
        $userRecords = $em->getRepository('CreditManager\Entity\CreditRecord')
            ->findBy(array('uId' => $uId), ['timestamp'=>'desc']);
        return $userRecords;
    }
}