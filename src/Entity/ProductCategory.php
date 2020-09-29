<?php
namespace CreditManager\Entity;

use Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use Doctrine\ORM\Mapping as ORM;
use CreditManager\Repository\CreditRecordList;
use User;
use Page;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cmProductCategory")
 *
 */
class ProductCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $pId;

    /**
     * Node Id for the c5 Topics Attribute Entry
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $nodeId;

    public function __construct(Product $p, TopicTreeNode $t) {
        $this->pId = $p->getId();
        $this->nodeId = $t->getTreeNodeID();
        return $this;
    }

    public function getCreditRecordId(){
        return $this->crId;
    }

    public function getCreditRecord(){
        return CreditRecord::getById($this->crId);
    }

    public function getCategoryId(){
        return $this->nodeId;
    }

    public function getCategoryName(){
        $t = TopicTreeNode::getById($this->nodeId);
        if(is_object($t)){
            return $t->getTreeNodeDisplayName();
        }
        return '';
    }

    public function setRecord(CreditRecord $cr){
        $this->crId = $cr->getId();
    }

    public function setCategory($nodeId){
        $this->nodeId = $nodeId;
    }
}