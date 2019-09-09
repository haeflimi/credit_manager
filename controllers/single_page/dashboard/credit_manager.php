<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\UserList;
use Concrete\Core\User\EditResponse as UserEditResponse;
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
        $ul = new UserList();
        $ul->filterByGroup(Group::getByName('TGC Members'));
        $ul->sortByUserName();
        $this->set('userList', $ul->getResults());

        $this->requireAsset('selectize');
        $this->requireAsset('core/app/editable-fields');

        $site = Site::getSite();
        $balance = $site->getAttribute('balance');
        $this->set('balance', $balance);
    }

    public function update_attribute($uID = false)
    {
        $this->setupUser($uID);
        $sr = new UserEditResponse();
        if ($this->app->make('helper/validation/token')->validate()) {
            $ak = UserAttributeKey::getByID($this->app->make('helper/security')->sanitizeInt($this->request->request('name')));
            if (is_object($ak)) {
                if (!in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    throw new Exception(t('You do not have permission to modify this attribute.'));
                }

                $this->user->saveUserAttributesForm([$ak]);
                $val = $this->user->getAttributeValueObject($ak);
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
        }
        $sr->setUser($this->user);
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute saved successfully.'));
            $sr->setAdditionalDataAttribute('value', $val->getDisplayValue());
        }
        $this->user->reindex();
        $sr->outputJSON();
    }

    public function clear_attribute($uID = false)
    {
        $this->setupUser($uID);
        $sr = new UserEditResponse();
        if ($this->app->make('helper/validation/token')->validate()) {
            $ak = UserAttributeKey::getByID($this->app->make('helper/security')->sanitizeInt($this->request->request('akID')));
            if (is_object($ak)) {
                if (!in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    throw new Exception(t('You do not have permission to modify this attribute.'));
                }
                $this->user->clearAttribute($ak);
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
        }
        $sr->setUser($this->user);
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute cleared successfully.'));
        }
        $sr->outputJSON();
    }

    protected function setupUser($uID)
    {
        $me = new User();
        $ui = UserInfo::getByID($this->app->make('helper/security')->sanitizeInt($uID));
        if (is_object($ui)) {
            $up = new Permissions($ui);
            if (!$up->canViewUser()) {
                throw new Exception(t('Access Denied.'));
            }
            $tp = new Permissions();
            $pke = PermissionKey::getByHandle('edit_user_properties');
            $this->user = $ui;
            $this->assignment = $pke->getMyAssignment();
            $this->canEdit = $up->canEditUser();
            $this->canActivateUser = $this->canEdit && $tp->canActivateUser() && $me->getUserID() != $ui->getUserID();
            $this->canEditAvatar = $this->canEdit && $this->assignment->allowEditAvatar();
            $this->canEditUserName = $this->canEdit && $this->assignment->allowEditUserName();
            $this->canEditLanguage = $this->canEdit && $this->assignment->allowEditDefaultLanguage();
            $this->canEditTimezone = $this->canEdit && $this->assignment->allowEditTimezone();
            $this->canEditEmail = $this->canEdit && $this->assignment->allowEditEmail();
            $this->canEditPassword = $this->canEdit && $this->assignment->allowEditPassword();
            $this->canSignInAsUser = $this->canEdit && $tp->canSudo() && $me->getUserID() != $ui->getUserID();
            $this->canDeleteUser = $this->canEdit && $tp->canDeleteUser() && $me->getUserID() != $ui->getUserID();
            $this->canAddGroup = $this->canEdit && $tp->canAccessGroupSearch();
            $this->allowedEditAttributes = [];
            if ($this->canEdit) {
                $this->allowedEditAttributes = $this->assignment->getAttributesAllowedArray();
            }
            $this->set('user', $ui);
            $this->set('canEditAvatar', $this->canEditAvatar);
            $this->set('canEditUserName', $this->canEditUserName);
            $this->set('canEditEmail', $this->canEditEmail);
            $this->set('canEditPassword', $this->canEditPassword);
            $this->set('canEditTimezone', $this->canEditTimezone);
            $this->set('canEditLanguage', $this->canEditLanguage);
            $this->set('canActivateUser', $this->canActivateUser);
            $this->set('canSignInAsUser', $this->canSignInAsUser);
            $this->set('canDeleteUser', $this->canDeleteUser);
            $this->set('allowedEditAttributes', $this->allowedEditAttributes);
            $this->set('canAddGroup', $this->canAddGroup);
        }
    }
}
