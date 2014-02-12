<?php
class PostsController extends ModController {
	
	function view($id) {
		$this->Post->recursive = 2;
		$this->set('post', $this->Post->findById($id));
	}
}
