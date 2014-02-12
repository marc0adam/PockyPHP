<?php
class UsersController extends ModController {
	
	function index() {
		$this->set('message', 'Hello, world!');
		
		$this->data = $this->User->findById(2);
		
	}
}
