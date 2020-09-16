<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use Concrete\Core\Database\CharacterSetCollation\Exception;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\User\UserList;
use Concrete\Core\Page\Page;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Support\Facade\Express;
use CreditManager\CreditManager;
use CreditManager\Entity\CreditRecord;
use Concrete\Core\User\User;

class Pos extends PageController
{
    public function __construct(Page $c)
    {
        parent::__construct($c);
    }

    public function view()
    {
        $ul = new UserList();
        $ul->filterByGroup(Group::getByName('Turicane 22'));
        $this->set('users', $ul->getResults());

        $entity = Express::getObjectByHandle('product');
        $list = new EntryList($entity);
        $products = $list->getResults();
        $this->set('products', $products);
    }

    public function badgeScan()
    {
        if(!$this->validateRequestToken($this->post(), 'scanBadge')) {
            return new Response('Invalid Request Token.', 401);
        }
        $badgeId = $this->post('badgeInput');
        if(empty($badgeId)) {
            return new Response('No Badge Id transmitted', 401);
        }
        $ul = new UserList();
        $ul->filterByAttribute('badge_id', $badgeId);
        $user = $ul->getResults()[0];
        if(!is_object($user)) {
            return new Response('No User associated to this Badge ID: '.$badgeId, 401);
        } else {
            return new Response('Hello '.$user->getUserName());
        }
    }

    public function checkout()
    {
        if($this->validateRequestToken($this->post(), 'confirmOrder')){
            $data = unserialize($this->post('data'));
            $user = User::getByUserID($this->post('orderUser'));
            $quantity = $this->post('orderQuantity');
            $entity = Express::getObjectByHandle('product');
            $product = Express::getEntry($this->post('orderProduct'));
            $cr = CreditRecord::addRecord($user, -((float)$product->getProductPrice() * (int)$quantity),
                'purchased '.$quantity.'x '.$product->getProductName().' using the Self Service Checout');
            if(is_object($cr)){
                return new Response('Success');
            } else {
                return new Response('Unknown Error', 500);
            }
        } else {
            return new Response('Invalid Request Token.', 500);
        }
    }

    public function validateRequestToken($data, $action = false) {
        $errors = new \Concrete\Core\Error\Error();
        // we want to use a token to validate each call in order to protect from xss and request forgery
        $token = \Core::make("token");
        if ($action && !$token->validate($action)) {
            $errors->add('Invalid Request, token must be valid.');
        }
        if ($errors->has()) {
            return $errors;
        }
        return true;
    }
}
