<?php
namespace Concrete\Package\CreditManager;

use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\View\View;
use Concrete\Core\Page\Single as SinglePage;
use PermissionAccess;
use Concrete\Core\Permission\Access\Entity\GroupEntity as PermissionAccessEntity;
use Package,
    Route,
    BlockType,
    Page,
    Events,
    AssetList,
    Group;

class Controller extends Package implements ProviderAggregateInterface
{
    protected $pkgHandle = 'credit_manager';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '1.4';
    protected $pkgAutoloaderRegistries = array(
        'src/PaymentMethods' => '\CreditManager\PaymentMethods',
        'src/Entity' => '\CreditManager\Entity',
        'src/Repository' => '\CreditManager\Repository',
        'src/CreditManager' => '\CreditManager'
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

    public function getEntityManagerProvider()
    {
        $provider = new StandardPackageProvider($this->app, $this, [
            'src/Entity' => 'CreditManager\Entity'
        ]);
        return $provider;
    }

    public function on_start()
    {
        $pkg = Package::getByHandle($this->pkgHandle);

        // register routes for payment method callbacks and modal dialogs
        Route::registerMultiple(array(
            '/ccm/credit_manager/callback/paypal' => array('\CreditManager\PaymentMethods\Paypal::callback'),
            '/ccm/credit_manager/callback/verify' => array('\CreditManager\PaymentMethods\Paypal::verify'),
            '/ccm/credit_manager/add_record/{uId}' => array('\Concrete\Package\CreditManager\Controller\Dialog\AddRecord::view'),
            '/ccm/credit_manager/add_record/{uId}/confirm/' => array('\Concrete\Package\CreditManager\Controller\Dialog\AddRecord::confirm'),
            '/ccm/credit_manager/history/{uId}' => array('\Concrete\Package\CreditManager\Controller\Dialog\History::view'),
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
        BlockType::installBlockType('credit_history', $pkg);

        // Add Single Pages
        $sp  = SinglePage::add('/dashboard/credit_manager', $pkg);
        $sp->update(array('cName' => t('Credit Manager'), 'cDescription' => 'Manage Credit Manager Balances and Transactions.'));
        $sp  = SinglePage::add('/user_balance', $pkg);
        $sp->update(array('cName' => t('User Balance'), 'cDescription' => 'Show User\'s Balance and allow for evening out the balance and transfer founds.'));

        // Set Groups and Permissions
        $this->setGroupsAndPermissions($pkg);
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

        // Assign Dashboard Login Permissions
        $dashboard = Page::getByPath('/dashboard');
        $dashboard->assignPermissions($cmGroupEntity, array('view_page'));

        // Assign Credit Manager Section Permissions
        $creditManager = Page::getByPath('/dashboard/credit_manager');
        $creditManager->assignPermissions($cmGroup, array('view_page'));
        $creditManager->assignPermissions($adminGroup, array('view_page'));
    }
}