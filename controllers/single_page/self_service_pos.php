<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use CreditManager\PageControllers\PosPageController;

class SelfServicePos extends PosPageController
{
    public function getCmCategory(){
        return 'Slef Service POS';
    }
}
