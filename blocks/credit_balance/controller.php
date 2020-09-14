<?php

namespace Concrete\Package\CreditManager\Block\CreditBalance;

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

    public function __construct($obj = null)
    {

    }

    public function getBlockTypeDescription()
    {
        return t("Display the Currently singned in User's Credit Manager Balance.");
    }

    public function getBlockTypeName()
    {
        return t("Credit Manager Balance");
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
        $ui = $user->getUserInfoObject();
        $balance = CreditManager::getUserBalance($user);
        $paymentHandle = 'tgc_balance';
        $this->set('balance', $balance);

        $this->set('paypalPrice', abs($balance));
        $this->set('paypalFee',$paypalFee = round((abs($balance) * 3.4 / 100) + 0.55),2);
        $this->set('paypalTotal', abs($balance));
        $this->set('paypalItemTitle', 'Ausgleich TGC Kontostand');
        $this->set('paypalItemDescription', 'Einzahlung um Schulden beim Verein Turicane Game Club zu Begleichen');
        $this->set('paypalItemID', $user->getUserID());
        $this->set('paypalPayload', base64_encode($paymentHandle.'-'.$user->getUserID()));
        $this->set('paypalNote','');
        $this->set('paypalCurrency',Config::get('credit_manager.pacment_methods.paypal.currency'));
        $this->set('paypalEnv', Config::get('credit_manager.pacment_methods.paypal.environment'));
        $this->set('paypalClientIDSandbox', Config::get('credit_manager.pacment_methods.paypal.sandbox_client_id'));
        $this->set('paypalClientIDProd', Config::get('credit_manager.pacment_methods.paypal.client_id'));

        if(Config::get('credit_manager.pacment_methods.paypal.environment') == 'sandbox'){
            $clientID = Config::get('credit_manager.pacment_methods.paypal.sandbox_client_id');
        } elseif(Config::get('credit_manager.pacment_methods.paypal.environment') == 'production') {
            $clientID = Config::get('credit_manager.pacment_methods.paypal.client_id');
        }
        $this->addHeaderItem('
            <script src="https://www.paypal.com/sdk/js?client-id='.$clientID.'&currency='.Config::get('credit_manager.pacment_methods.paypal.currency').'"></script>
        ');
    }
}