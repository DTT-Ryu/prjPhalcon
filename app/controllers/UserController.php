<?php
declare(strict_types=1);


class UserController extends ControllerBase
{
    public function indexAction(){
        $role = $this->session->get('role');
        $this->view->setVar('role', $role);
    }
}