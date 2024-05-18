<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

class UsersController {

    public function index() {
        return View::render('/Users/index');
    }

    public function login() {
        if (!empty($_POST)) {
            // Do user authentication


        }
        return View::render('/Users/login');
    }

    public function view(int $id) {
        $user = UserModel::load($id);
        return View::render('/Users/view', ['user' => $user]);
    }

}
