<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use Concrete\Core\Page\Controller\PageController;
use CreditManager\CreditManager;
use Concrete\Core\User\User;
use Package;
use Core;

class Balance extends PageController
{
    public function view()
    {
        $user = new User();
        $ui = $user->getUserInfoObject();
        $history = CreditManager::getUserHistory($user);
        $balance = CreditManager::getUserBalance($user);
        $this->set('balance', $balance);
        $this->set('history', $history);
    }
}
