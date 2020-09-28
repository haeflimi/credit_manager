<?php

namespace Concrete\Package\CreditManager\Controller\Dialog;

use Concrete\Core\Controller\Controller;
use CreditManager\Repository\CmUserList;
use CreditManager\CreditManager;

class History extends Controller
{
    protected $viewPath = 'dialogs/history';

    public function view($uId)
    {
        $history = CreditManager::getUserHistory($uId);
        $this->set('history', $history);
        $this->set('uId', $uId);
    }
}