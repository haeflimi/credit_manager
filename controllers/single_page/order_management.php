<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use Application\Turicane\CurrentLan;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\User\UserList;
use CreditManager\CreditManager;
use Concrete\Core\User\User;
use Package;
use Core;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderManagement extends PageController
{
    public function view()
    {
        $this->requireAsset('javascript', 'vue');
        $this->requireAsset('javascript', 'slimScroll');
        $em = Database::connection()->getEntityManager();
        $this->set('ccm_token', json_encode(Core::make('token')->generate('order_management')));
        $this->set('orderGetAction', $this->action('getOrders'));
        $this->set('orderSetOrderedAction', $this->action('setOrdered'));
        $this->set('orderSetDeliveredAction', $this->action('setDelivered'));
        $this->set('orderSetClosedAction', $this->action('setClosed'));
        $this->setThemeViewTemplate('blank.php');
    }

    public function getOrders(){
        $token = \Core::make("token");
        if (!$token->validate('order_management')) {
            return new Response('Invalid Request Token.', 401);
        }
        $em = Database::connection()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('op')->from('CreditManager\Entity\OrderPosition', 'op')
            ->where('op.status IN (:states)')
            ->orderBy('op.Id', 'ASC')
            ->setParameter('states', ['ordered', 'open', 'delivered']);
        $orderObjects = $qb->getQuery()->getResult();
        $orders = [];
        foreach($orderObjects as $oo){
            $orders[] = [
                'id' => $oo->getId(),
                'product_name' => $oo->getProduct()->getName(),
                'product_id' => $oo->getProduct()->getId(),
                'value' => $oo->getQuantity() * $oo->getProduct()->getPrice(),
                'user_name' => $oo->getUser()->getUserName(),
                'status' => $oo->getAllStates()[$oo->getStatus()],
                'status_handle' => $oo->getAllStates()
            ];
        }
        return new JsonResponse($orders);
    }

    public function setOrdered(){
        $em = Database::connection()->getEntityManager();
        $selected_orders = $this->post('selected_orders');
        $token = \Core::make("token");
        if (!$token->validate('order_management')) {
            return new Response('Invalid Request Token.', 401);
        }
        $n = 0;
        foreach($selected_orders as $soId){
            $order = $em->find('CreditManager\Entity\OrderPosition', $soId);
            if($order->getStatus() == 'open'){
                $order->setStatus('ordered');
                $em->persist($order);
                $em->flush();
                $n++;
            }
        }
        return new Response($n.' Bestellungen auf "Bestellt" gesetzt');
    }

    public function setDelivered(){
        $em = Database::connection()->getEntityManager();
        $selected_orders = $this->post('selected_orders');
        $token = \Core::make("token");
        if (!$token->validate('order_management')) {
            return new Response('Invalid Request Token.', 401);
        }
        $n = 0;
        foreach($selected_orders as $soId){
            $order = $em->find('CreditManager\Entity\OrderPosition', $soId);
            if($order->getStatus() == 'ordered'){
                $order->setStatus('delivered');
                $em->persist($order);
                $em->flush();
                $n++;
            }
        }
        return new Response($n.' Bestellungen auf "Ausgeliefert" gesetzt');
    }

    public function setClosed(){
        $em = Database::connection()->getEntityManager();
        $selected_orders = $this->post('selected_orders');
        $token = \Core::make("token");
        if (!$token->validate('order_management')) {
            return new Response('Invalid Request Token.', 401);
        }
        $n = 0;
        foreach($selected_orders as $soId){
            $order = $em->find('CreditManager\Entity\OrderPosition', $soId);
            if($order->getStatus() == 'ordered' || $order->getStatus() == 'delivered'){
                $msg = 'Catering Auslieferung für '.$order->getProduct()->getName().' abgeschlossen';
                CreditManager::addRecord($order->getUser(), -$order->getProduct()->getPrice(),$msg,['Catering Order',CurrentLan::getLANTitle()]);
                $order->setStatus('closed');
                $em->persist($order);
                $em->flush();
                $n++;
            }
        }
        return new Response($n.' Bestellungen auf "Abgeschlossen" gesetzt und verrechnet.');
    }

    public function processOrder()
    {
        $order = $this->post('order');
        $token = \Core::make("token");
        if (!$token->validate('order_management')) {
            return new Response('Invalid Request Token.', 401);
        }
        $badgeId = $order['badge_id'];
        if(empty($badgeId)) {
            return new Response('No Badge Id transmitted', 401);
        }
        $ul = new UserList();
        $ul->filterByAttribute('badge_id', $badgeId);
        $user = $ul->getResults()[0];
        if(!is_object($user)) {
            return new Response('No User associated to this Badge ID: '.$badgeId, 401);
        }
        $items = $order['items'];
        if(empty($items)){
            return new Response('No Items selected', 500);
        }

        $totalPrice = 0;
        $itemCount = 0;
        $itemNames = [];
        foreach($items as $i){
            $product = Express::getEntry($i['id']);
            $totalPrice += ($i['quantity'] * $product->getProductPrice());
            $itemCount += $i['quantity'];
            $itemNames[] = $product->getProductName();
        }
        $message = $itemCount.' Produkte gekauft: ('.implode(' ,', $itemNames).')';
        $lanName = CurrentLan::getLANTitle();
        try {
            $cr = CreditManager::addRecord($user, -$totalPrice, $message, [$this->getCmCategory(),$lanName]);
        } catch (Exception $e) {
            return new Response("Failed: " . $e->getMessage(), 500);
        }

        return new Response('Transaktion Erfolgreich');
    }
}
