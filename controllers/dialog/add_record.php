<?php

namespace Concrete\Package\CreditManager\Controller\Dialog;

use Concrete\Core\Controller\Controller;
use CreditManager\CreditManager;
use CreditManager\Entity\CreditRecord;
use URL;

class AddRecord extends Controller
{
    protected $viewPath = 'dialogs/add_record';

    public function view($uId)
    {
        $this->set('uId', $uId);
    }

    public function confirm() {
        $e = $this->validate($this->post(), 'addRecord');
        if($e === true){
            $value = $this->post('recordValue');
            $comment = $this->post('recordComment');
            $user = $this->post('recordUid');
            CreditRecord::addRecord($user, $value,$comment);
            $this->flash('success', t('Record Added'));
        } else {
            $this->flash('error', $e);
        }
        $this->redirect(URL::to('/dashboard/credit_manager'));
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
            if(!is_numeric($data['recordValue'])){
                $errors->add('No valid Record Value set.');
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