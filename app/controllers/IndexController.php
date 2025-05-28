<?php
declare(strict_types=1);
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

class IndexController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function loginAction(){
        if($this->request->isPost()){
        $email = $this->request->getPost('loginEmail');
        $password = $this->request->getPost('loginPass');

        $user = Users::findFirstByEmail($email);
        if($user && $password === $user->password){
            $this->session->set('role', $user->role === 0 ? 'admin' : 'user');
            if($user->role == 0){
                return $this->response->redirect('/admin');
            }else{
                return $this->response->redirect('/user');
            }
        }else{
            $this->flashSession->error("Email or password is wrong!");
            // return $this->dispatcher->forward([
            //     'controller' => 'index',
            //     'action' => 'wrong'
            // ]);
            return $this->response->redirect('/');
        }
    }
    }

    public function wrongAction(){}

    public function logoutAction(){
        $this->session->destroy();
        return $this->response->redirect('/');
    }
}

