<?php
namespace Concrete\Package\CreditManager\Src\Entity;

use User;
use Page;

/**
 * @Entity(repositoryClass="Concrete\Package\CreditManager\Src\Repository\CreditRecordList")
 * @Table(name="CreditRecord")
 *
 */
class CreditRecord
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $Id;
    /**
     * @Column(type="integer")
     */
    protected $uId;
    /** @Column(type="text", nullable=true) */
    protected $comment;
    /** @Column(type="datetime", name="timestamp", nullable=false) */
    protected $timestamp;
    /** @Column(type="text", nullable=true) */
    protected $package;
    /** @Column(type="float", nullable=false) */
    protected $value;

    public function __construct() {

    }

    public function getId()
    {
        return $this->Id;
    }

    public function getUser(){
        return User::getByID($this->uId);
    }

    public function getPackage(){
        return $this->package;
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

    public function setUser($user){
        if(is_object($user)){
            $this->uId = $user->getUserID();
        } elseif(is_numeric($user)){
            $this->uId = $user;
        }
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function setTimestamp()
    {
        $this->sentAt = new \DateTime('now');
        return $this;
    }

    public function setValue($value){
        return $this->value = $value;
    }

    public function setPackage($pkg){
        if(is_object($pkg)){
            $this->package = $pkg->getPackageHandle();
        } elseif(is_string($pkg)){
            $this->package = $pkg;
        }
    }

}