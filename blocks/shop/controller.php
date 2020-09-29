<?php

namespace Concrete\Package\CreditManager\Block\Shop;

use Application\Turicane\CurrentLan;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Http\Response;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use Concrete\Core\Tree\Type\Topic as TopicTree;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Config;
use Core;
use CreditManager\CreditManager;
use CreditManager\Entity\OrderPosition;
use Express;
use Tfts\Tfts;

class Controller extends BlockController {

  public $collection;
  protected $btTable = 'btCmShop';
  protected $btInterfaceWidth = "800";
  protected $btInterfaceHeight = "600";
  protected $btCacheBlockRecord = true;
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;
  protected $btHandle = 'cm_shop';

  public function __construct($obj = null) {
    parent::__construct($obj);
  }

  public function getBlockTypeDescription() {
    return t("");
  }

  public function getBlockTypeName() {
    return t("Shop");
  }

  public function save($args) {
    parent::save($args);
  }

  public function edit()
  {
      $tt = new TopicTree();
      $tree = $tt->getByID(Core::make('helper/security')->sanitizeInt(Config::get('credit_manager.product_categories_topic')));
      $this->set('categoryTree',$tree);
      $nodeIds = $tree->getRootTreeNodeObject()->getAllChildNodeIDs();
      $nodes = [0=>'Keine'];
      foreach($nodeIds as $key => $nodeId){
          $node = TopicTreeNode::getByID($nodeId);
          $nodes[$nodeId] = $node->getTreeNodeDisplayName();
      }
      $this->set('categoryTreeNodes', $nodes);
      $this->set('active_category',$this->active_category);
      $this->set('run_time', $this->run_time);
  }

  public function view() {

      $this->requireAsset('javascript', 'vue');
      $this->requireAsset('javascript', 'slimScroll');

      $this->set('ccm_token', json_encode(Core::make('token')->generate('shop_block_order')));

      $em = Database::connection()->getEntityManager();
      $qb = $em->createQueryBuilder();
      $qb->select('p')
          ->from('CreditManager\Entity\Product', 'p')
          ->innerJoin('CreditManager\Entity\ProductCategory', 'pc','WITH', 'p.Id = pc.pId')
          ->where('p.isOrder = :isOrder')
          ->andWhere('pc.nodeId = :categoryNodeId')
          ->orderBy('p.name')
          ->setParameter('isOrder', 1)
          ->setParameter('categoryNodeId', $this->active_category);
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
      $this->set('products', json_encode($products));

      $u = new User();
      $orderObjects = $em->getRepository('CreditManager\Entity\OrderPosition')->findBy(['uId'=>$u->getUserID(),'status'=>['open','ordered']]);
      $orders = [];
      $allStates = OrderPosition::getAllStates();
      foreach($orderObjects as $oo)
      {
          if(!is_object($oo))continue;
          $orders[] = [
              'id' => $oo->getId(),
              'product' => $oo->getProduct()->getName(),
              'status' => $allStates[$oo->getStatus()],
          ];
      }
      $this->set('orders', json_encode($orders));
      $this->set('run_time', $this->run_time);
      $this->set('bId', $this->bID);
      $this->set('userId', $u->getUserID());
  }

  public function action_orderProduct() {
      $order = $this->post('order');
      $token = \Core::make("token");
      if (!$token->validate('shop_block_order')) {
          return new Response('Invalid Request Token.', 401);
      }
      $user = User::getByUserID($order['user_id']);
      if(!$user){
          return new Response('Invalid User.', 401);
      }
      $em = Database::connection()->getEntityManager();
      $product = $em->find('CreditManager\Entity\Product',$order['product_id']);
      if(!$product){
          return new Response('Invalid Product.', 401);
      }

      $orderPosition = new OrderPosition();
      $orderPosition->setQuantity(1);
      $orderPosition->setStatus('open');
      $orderPosition->setProduct($product);
      $orderPosition->setUserId($user->getUserID());
      $em->persist($orderPosition);
      $em->flush();
      return new Response('Bestellung Erfolgreich');
  }

  public function action_deleteOrder() {
      $order = $this->post('order');
      $opId = $order['order_id'];
      $em = Database::connection()->getEntityManager();
      $token = \Core::make("token");
      if (!$token->validate('shop_block_order')) {
          return new Response('Invalid Request Token.', 401);
      }
      $orderPosition = $em->find('CreditManager\Entity\OrderPosition',$opId);
      if($orderPosition->getStatus() != 'open'){
          return new Response('Cannot delete processed Order.', 401);
      } else {
          $em->remove($orderPosition);
          $em->flush();
          return new Response('Löschung Erfolgreich');
      }
  }
}
