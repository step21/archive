<?php

namespace app\tests\cases\controllers;

use app\controllers\UsersController;

use app\models\Users;

use lithium\security\Auth;
use lithium\storage\Session;
use lithium\action\Request;

class UsersControllerTest extends \lithium\test\Unit {

	public $admin;
	public $admin_data;

	public $editor;
	public $editor_data;

	public function setUp() {
	
		Session::config(array(
			'default' => array('adapter' => 'Php', 'session.name' => 'app')
		));
	
		Auth::clear('default');
	
		$this->admin = Users::create();
		$this->admin_data = array(
			"username" => "admin",
			"password" => "abcd",
			"name" => "Full Name",
			"email" => "admin@example.com",
			"role_id" => '1'
		);
		$this->admin->save($this->admin_data);
	
		$this->editor = Users::create();
		$this->editor_data = array(
			"username" => "editor",
			"password" => "abcd",
			"name" => "Full Name",
			"email" => "editor@example.com",
			"role_id" => '2'
		);
		$this->editor->save($this->editor_data);
	
		$viewer = Users::create();
		$viewer_data = array(
			"username" => "viewer",
			"password" => "abcd",
			"name" => "Full Name",
			"email" => "editor@example.com",
			"role_id" => '3'
		);
		$viewer->save($viewer_data);
	
	}

	public function tearDown() {
	
		Users::all()->delete();
		Auth::clear('default');
	
	}

	public function testIndex() {}
	public function testView() {}
	public function testAdd() {}
	public function testEdit() {} //TODO Test that non-Amins cannot change user role, and that usernames and ids can never be changed
	public function testDelete() {}
	
	public function testRegister() {
	
		Auth::set('default', $this->editor_data);
	
		$request = new Request();
		$request->params = array(
			'controller' => 'users'
		);

		$usersRegister = new UsersController(array('request' => $request));
		
		$response = $usersRegister->register();
		$this->assertEqual($response->headers["Location"], "/login");
		
		Auth::clear('default');
		Users::all()->delete();
		//FIXME the response now rendering into the test suite
		/*$usersRegister = new UsersController(array('request' => $request));
		
		$response = $usersRegister->register();
		
		$this->assertNull($response);*/
	
		$request = new Request();
		$request->params = array(
			'controller' => 'users'
		);
		$request->data = $this->admin_data;

		$usersRegister = new UsersController(array('request' => $request));
		
		$response = $usersRegister->register();
		$this->assertEqual($response->headers["Location"], "/home");
		
	
	}
}

?>
