<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\UserList;
use Concrete\Core\User\EditResponse as UserEditResponse;
use CreditManager\Repository\CmUserList;
use Exception;
use PermissionKey;
use Permissions;
use User;
use UserAttributeKey;
use UserInfo;
use Group;
use Page;
use Site;

class CreditManager extends DashboardPageController
{
    public function __construct(Page $c)
    {
        parent::__construct($c);

    }

    public function view()
    {
        $ul = new CmUserList();
        if($keywords = $this->get('keywords'))$ul->filterByKeywords($keywords);
        $ul->filterByBalance();
        $ul->sortByUserName();
        $this->set('userList', $ul->getResults());
        $this->set('ul', $ul);

        $site = Site::getSite();
        $balance = $site->getAttribute('balance');
        $this->set('balance', $balance);
    }

    public function viewHistory(){

    }

    public function action_addRecord()
    {
        $errors = $this->validate($this->post(),'addRecord');
    }

    public function validate($data, $action = false)
    {
        $errors = new \Concrete\Core\Error\Error();

        // we want to use a token to validate each call in order to protect from xss and request forgery
        $token = \Core::make("token");
        if($action && !$token->validate($action)){
            $errors->add('Invalid Request, token must be valid.');
        }

        // validate the action addPonts
        if($action == 'addRecord'){
            if(empty($data['recordValue'])){
                $errors->add('No Record Value set.');
            }
            if(empty($data['recordComment'])){
                $errors->add('You need to set a comment for the Record.');
            }
        }

        if ($errors->has()) {
            return $errors;
        }

        return true;
    }
}
