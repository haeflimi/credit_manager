<?php
namespace Concrete\Package\CreditManager;

use Concrete\Core\View\View;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\Category as PermissionCategory;
use PermissionAccess;
use Concrete\Core\Permission\Access\Entity\GroupEntity as PermissionAccessEntity;
use CreditManager\PaymentMethods\Paypal;
use Package,
    Route,
    BlockType,
    Page,
    Events,
    AssetList,
    Group;

class Controller extends Package
{
    protected $pkgHandle = 'credit_manager';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '0.955';
    protected $pkgAutoloaderRegistries = array(
        'src/PaymentMethods' => '\CreditManager\PaymentMethods'
    );

    public function getPackageName()
    {
        return t('Credit Manager');
    }

    public function getPackageDescription()
    {
        return t('Adds a Credit Account to every User that tracks transactions and supplyes ways to transfer
        founds to the account.');
    }

    public function on_start()
    {
        $pkg = Package::getByHandle($this->pkgHandle);

        // we need this Asset in order to be able to use Pnotify.
        // @todo mabe in the future there will be a sepparate asset for the notification system!? A lot overhead like this.
        $view = new View();
        $view->requireAsset('core/app');

        // hook into the delete_user event in order to remove deleted users from any ongoing Newsletter mailings
        Events::addListener('on_user_delete', function ($event) {
            // @todo implement some kind of secure resitting for user founds
        });

        // @todo add routs for datatables to get the Data, paypal and stripe to report payments, etc
        // register routes for payment method callbacks
        Route::registerMultiple(array(
            '/ccm/credit_manager/callback/paypal' => array('\CreditManager\PaymentMethods\Paypal::callback'),
            '/ccm/credit_manager/callback/verify' => array('\CreditManager\PaymentMethods\Paypal::verify'),
        ));

        $al = AssetList::getInstance();
        $al->register('javascript', 'datatables', 'js/datatables.js',
            array('version' => '1.10.18', 'minify' => true, 'combine' => false), $pkg
        );
        $al->register('css', 'datatables', 'css/datatables.css',
            array('version' => '1.10.18', 'minify' => true, 'combine' => false), $pkg
        );
        $al->registerGroup('datatables', array(
            array('javascript', 'datatables'),
            array('css', 'datatables')
        ));
    }

    public function install()
    {
        $pkg = \Concrete\Core\Package\Package::install();
        $this->setUp($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->setUp($pkg);
    }

    public function uninstall()
    {
        // @todo Remove Single Pages, Permissions, Groups, Etc. maybe make sure to NOT to delete the transaction/ account data at any point when uninstalling the package
        parent::uninstall();
    }

    private function setUp($pkg)
    {
        // Install Database Entities
        $this->installEntitiesDatabase();

        // Add Blocks
        BlockType::installBlockType('credit_balance', $pkg);

        // Add Single Pages
        $sp  = SinglePage::add('/dashboard/credit_manager', $pkg);
        $sp->update(array('cName' => t('Credit Manager'), 'cDescription' => 'Manage Credit Manager Balances and Transactions.'));
        $sp  = SinglePage::add('/user_balance', $pkg);
        $sp->update(array('cName' => t('User Balance'), 'cDescription' => 'Show User\'s Balance and allow for evening out the balance and transfer founds.'));

        // Set Groups and Permissions
        //$this->setGroupsAndPermissions($pkg);
    }

    private function setGroupsAndPermissions($pkg)
    {
        $adminGroup = Group::getByID(ADMIN_GROUP_ID);
        $adminGroupEntity = PermissionAccessEntity::getOrCreate($adminGroup);
        $cmGroup = Group::getByName('Credit Manager');
        if (!$cmGroup) {
            $cmGroup = Group::add('Credit Manager', t('The default Group for Credit Manager Administration'));
        }
        $cmGroupEntity = PermissionAccessEntity::getOrCreate($cmGroup);

        /*// Assign Dashboard Login Permissions
        $dashboard = Page::getByPath('/dashboard');
        $dashboard->assignPermissions($cmGroupEntity, array('view_page'));

        // Assign Credit Manager Section Permissions
        $creditManager = Page::getByPath('/dashboard/credit_manager');
        $creditManager->assignPermissions($cmGroup, array('view_page'));
        $creditManager->assignPermissions($adminGroup, array('view_page'));*/

        /* @todo finish implementation of permissions
         * $pkcHandle = 'credit_manager';
        if (!PermissionCategory::getByHandle($pkcHandle)) {
            PermissionCategory::add($pkcHandle, $pkg);
        }
        $pkHandle = 'manage_credits';
        if (!PermissionKey::getByHandle($pkHandle)) {
            $pk = PermissionKey::add($pkcHandle, $pkHandle, t('Manage Credits'), t('Can Manage and view all Credit Manager Balances and Transactions.'), false, false, $pkg);
            $pa = PermissionAccess::create($pk);
            $pa->addListItem($cmGroupEntity);
            $pao = $pk->getPermissionAssignmentObject();
            $pao->assignPermissionAccess($pa);
        }*/
    }
}