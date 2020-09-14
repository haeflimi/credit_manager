<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Type\Topic as TopicTree;
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
use Config;

class CreditManager extends DashboardPageController
{
    public function __construct(Page $c)
    {
        parent::__construct($c);

    }

    public function view()
    {
        $ul = new CmUserList();

        $relevant_groups = Config::get('credit_manager.relevant_groups');
        $relevant_groups['all'] = 'Alle';
        $this->set('relevant_groups', $relevant_groups);

        // only apply default filtering when not looking for someone specific
        if($keywords = $this->get('keywords')){
            $ul->filterByKeywords($keywords);
        }
        if(is_numeric($selectedGroup = $this->get('selectedGroup'))){
            $ul->filterByGroupID((integer)$selectedGroup);
        } elseif (empty($selectedGroup)) {
            $ul->filterByGroupID(key($relevant_groups));
        }
        $ul->sortByUserName();
        $this->set('userList', $ul->getResults());
        $this->set('ul', $ul);

        $this->requireAsset('core/topics');
        $tt = new TopicTree();
        $defaultTree = $tt->getDefault();
        $tree = $tt->getByID(Core::make('helper/security')->sanitizeInt(Config::get('credit_manager.categories_topic')));
        $this->set('categoryTree',$tree);

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
