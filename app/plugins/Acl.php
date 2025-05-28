<?php
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

class Acl{
    public static function getAcl(){
        $acl = new AclMemory();

        //Add role
        $acl->addRole(new Role('admin'));
        $acl->addRole(new Role('user'));

        //add component
        $acl->addComponent(new Component('admin'), ['index']);
        $acl->addComponent(new Component('user'), ['index']);
        $acl->addComponent(new Component('index'), ['index', 'login', 'logout', 'wrong']); // Thêm dòng này

        //set permissions
        $acl->allow('admin', 'admin', '*');
        $acl->allow('user', 'user', '*');
        $acl->allow('admin', 'index', '*'); // Thêm quyền cho admin truy cập index
        $acl->allow('user', 'index', '*');  // Thêm quyền cho user truy cập index

        return $acl;
    }
}