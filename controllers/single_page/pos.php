<?php
namespace Concrete\Package\CreditManager\Controller\SinglePage;

use CreditManager\PageControllers\PosPageController;

class Pos extends PosPageController
{
    public function getCmCategory(){
        return 'Catering POS';
    }
}
