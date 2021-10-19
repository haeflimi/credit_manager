<?php

namespace Concrete\Package\CreditManager\Block\CreditHistory;

use \Concrete\Core\Block\BlockController;
use \Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\User\User;
use CreditManager\CreditManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected $btCacheBlockRecord = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $pkgHandle = 'credit_manager';

    protected $limit = 10;

    public function __construct($obj = null)
    {

    }

    public function getBlockTypeDescription()
    {
        return t("Display the Currently singned in User's Transaction History.");
    }

    public function getBlockTypeName()
    {
        return t("Credit Manager User History");
    }

    public function add()
    {

    }

    public function edit()
    {

    }

    public function view()
    {
        $user = new User();
        $history = CreditManager::getUserHistory($user, $this->limit);
        $this->set('count', CreditManager::getRecordCount($user));
        $this->set('limit', $this->limit);
        $this->set('history', $history);
    }

    public function action_history()
    {
        $user = new User();
        $history = CreditManager::getUserHistory($user, CreditManager::getRecordCount($user));
        $this->set('history', $history);
        $this->set('uId', $user->getUserID());
        $this->render('complete');
    }
}