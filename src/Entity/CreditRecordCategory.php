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

    public function __construct($crId, $nodeId) {
        $this->crId = $crId;
        $this->nodeId = $nodeId;
        return $this;
    }

    public function getCreditRecord(){
        return CreditRecord::getById($this->crId);
    }

    public function getCategory(){
        $t = TopicTreeNode::getById($this->nodeId);
        return $t->getTreeNodeDisplayName();
    }
}