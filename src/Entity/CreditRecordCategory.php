<?php
namespace CreditManager\Entity;

use Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use Doctrine\ORM\Mapping as ORM;
use CreditManager\Repository\CreditRecordList;
use User;
use Page;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cmCreditRecordCategory")
 *
 */
class CreditRecordCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $crId;

    /**
     * Node Id for the c5 Topics Attribute Entry
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $nodeId;

    /**
     * Any Credit Record can have any Topic Tag
     * @ORM\ManyToOne(targetEntity="CreditRecord")
     * @JoinColumn(name="crId", referencedColumnName="id")
     */
    protected $record;


    public function __construct() {
        return $this;
    }

    public function getRecord(){
        return $this->record;
    }

    public function getCategory(){
        $t = TopicTreeNode::getById($this->nodeId);
        return $t->getTreeNodeDisplayName();
    }
}