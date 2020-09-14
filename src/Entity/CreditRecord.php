<?php
namespace CreditManager\Entity;

use Concrete\Core\Support\Facade\Database;
use Doctrine\ORM\Mapping as ORM;
use CreditManager\Repository\CreditRecordList;
use User;
use Page;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cmCreditRecord")
 *
 */
class CreditRecord
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $Id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $uId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="datetime", name="timestamp", nullable=false)
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $value;

    /**
     * Any Credit Record can have any Topic Tag
     * @ORM\OneToMany(targetEntity="CreditManager\Entity\CreditRecordCategory", mappedBy="record")
     */
    protected $categorie_tags;

    public function __construct($user, $value, $comment, $categories = []) {
        $em = Database::get()->getEntityManager();
        foreach($categories as $c){
            $crc = new CreditRecordCategory($this->id, $categories);
            $em->persist($crc);
            $em->flush();
        }
        $this->setUser($user);
        $this->setValue($value);
        $this->setComment($comment);
        $this->setTimestamp();
        return $this;
    }

    public function getId()
    {
        return $this->Id;
    }

    public function getUser(){
        return User::getByID($this->uId);
    }

    public function getComment(){
        return $this->comment;
    }

    public function getTimestamp(){
        return $this->timestamp;
    }

    public function getValue(){
        return $this->value;
    }

    private function setUser($user){
        if(is_object($user)){
            $this->uId = $user->getUserID();
        } elseif(is_numeric($user)){
            $this->uId = $user;
        }
        return $this;
    }

    private function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    private function setTimestamp()
    {
        $this->timestamp = new \DateTime('now');
        return $this;
    }

    private function setValue($value){
        return $this->value = $value;
    }

    public static function addRecord($user, $value, $comment, $categories = []){
        $db = Database::connection();
        $em = $db->getEntityManager();
        $cr = new CreditRecord($user, $value, $comment, $categories = []);
        $em->persist($cr);
        $em->flush();
        return $cr;
    }

    public function getCategories(){
        return $this->categorie_tags;
    }
}