<?php

namespace app\tests\cases\controllers;

use app\controllers\WorksController;

use app\models\Users;

use lithium\security\Auth;
use lithium\storage\Session;
use lithium\action\Request;

class WorksControllerTest extends \lithium\test\Unit {

	public function setUp() {
	
		Session::config(array(
			'default' => array('adapter' => 'Php', 'session.name' => 'app')
		));
	
		Auth::clear('default');
		
	}

	public function tearDown() {}

	public function testIndex() {}
	public function testView() {}
	public function testAdd() {}
	public function testEdit() {}
	public function testDelete() {}
	
	public function testUnauthorizedAccess() {
	
		$this->request = new Request();
		$this->request->params = array(
			'controller' => 'works'
		);

		$works = new WorksController(array('request' => $this->request));
		
		$response = $works->index();
		$this->assertEqual($response->headers["Location"], "/login");
		
		$response = $works->search();
		$this->assertEqual($response->headers["Location"], "/login");
		
		$response = $works->view();
		$this->assertEqual($response->headers["Location"], "/login");
		
		$response = $works->add();
		$this->assertEqual($response->headers["Location"], "/login");
		
		$response = $works->edit();
		$this->assertEqual($response->headers["Location"], "/login");
		
		$response = $works->history();
		$this->assertEqual($response->headers["Location"], "/login");

		$response = $works->histories();
		$this->assertEqual($response->headers["Location"], "/login");

		$response = $works->delete();
		$this->assertEqual($response->headers["Location"], "/login");
	
	}
}

?>
