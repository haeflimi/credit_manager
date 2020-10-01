<?php
namespace CreditManager\PageControllers;

use Application\Turicane\CurrentLan;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\User\UserList;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Express;
use CreditManager\CreditManager;
use CreditManager\Entity\CreditRecord;
use Concrete\Core\User\User;
use Package;
use Core;

class PosPageController extends PageController
{
    public function __construct(Page $c)
    {
        parent::__construct($c);
    }

    public function view()
    {
        $ul = new UserList();
        $ul->filterByGroup(CurrentLan::getParticipantGroup());
        $ul->sortBy('uName');
        $users = [];
        foreach($ul->getResults() as $key => $u){
            $user['id'] = $u->getUserId();
            $user['badge_id'] = $u->getAttribute('badge_id');
            $user['name'] = $u->getUserName();
            if ($u->hasAvatar()){
                $user['avatar'] = '/application/files/avatars/' . $u->getUserID() . '_sm.jpg';
            } else {
                $user['avatar'] = '';
            }
            $users[] = $user;
        }
        $this->set('users', json_encode($users));

        $this->set('products', json_encode($this->getVisibleProducts()));

        $this->requireAsset('javascript', 'vue');
        $this->requireAsset('javascript', 'slimScroll');

        $this->set('ccm_token', json_encode(Core::make('token')->generate('pos_order')));

        $this->set('orderAction', $this->action('processOrder'));

        $this->setThemeViewTemplate('blank.php');
    }

    public function processOrder()
    {
        $em = Database::connection()->getEntityManager();
        $order = $this->post('order');
        $token = \Core::make("token");
        if (!$token->validate('pos_order')) {
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
            $product = $em->find('CreditManager\Entity\Product',$i['id']);
            $totalPrice += ($i['quantity'] * $product->getPrice());
            $itemCount += $i['quantity'];
            $itemNames[] = $product->getName();
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

    public function getCmCategory(){
        return '';
    }
}
