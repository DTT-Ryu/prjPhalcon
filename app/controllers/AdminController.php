<?php
declare(strict_types=1);


class AdminController extends ControllerBase
{
    public function indexAction(){
         $role = $this->session->get('role');
            if (!$role) {
                // Nếu không có role, chuyển về trang login hoặc báo lỗi
                return $this->response->redirect('/');
            }
        $this->view->setVar('role', $role);
    }
}