<?php

namespace app\controllers;

use app\models\Archives;
use app\models\ArchivesHistories;
use app\models\Exhibitions;
use app\models\ExhibitionsHistories;
use app\models\Works;
use app\models\Components;
use app\models\ExhibitionsLinks;
use app\models\ArchivesDocuments;

use app\models\Users;
use app\models\Roles;

use lithium\action\DispatchException;
use lithium\security\Auth;

use lithium\core\Libraries;

class ExhibitionsController extends \lithium\action\Controller {

	public function index() {
    
    	// Check authorization
	    $check = (Auth::check('default')) ?: null;
	
		// If the user is not authorized, redirect to the login screen
        if (!$check) {
            return $this->redirect('Sessions::add');
        }
        
        // Look up the current user with his or her role
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));
		
		$limit = isset($this->request->query['limit']) ? $this->request->query['limit'] : 40;
		$page = isset($this->request->params['page']) ? $this->request->params['page'] : 1;
		$order = array('Archives.earliest_date' => 'DESC');
		$total = Exhibitions::count();

		$limit = ($limit == 'all') ? $total : $limit;
		
		$exhibitions = Exhibitions::find('all', array(
			'with' => array('Archives', 'Components'),
			'order' => $order,
			'limit' => $limit,
			'page' => $page,
		));
		
		$li3_pdf = Libraries::get("li3_pdf");
		$pdf = "Exhibitions-" . date('Y-m-d') . ".pdf";
		$content = compact('pdf');

		return compact('exhibitions', 'total', 'page', 'limit', 'auth', 'pdf', 'li3_pdf', 'content');
	}

	public function search() {

		// Check authorization
		$check = (Auth::check('default')) ?: null;

		// If the user is not authorized, redirect to the login screen
		if (!$check) {
			return $this->redirect('Sessions::add');
		}

		// Look up the current user with his or her role
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));

		$exhibitions = array();

		$order = array('Archives.earliest_date' => 'DESC');

		$condition = '';
		$query = '';
		$type = 'All';

		$limit = isset($this->request->query['limit']) ? $this->request->query['limit'] : 40;
		$page = isset($this->request->params['page']) ? $this->request->params['page'] : 1;
		$total = 0;

		$data = $this->request->data ?: $this->request->query;

		if (isset($data['query']) && $data['query']) {
			$condition = isset($data['condition']) ? $data['condition'] : '';
			$type = isset($data['type']) ? $data['type'] : 'All';

			$query = trim($data['query']);

			if ($condition) {
				$conditions = array("$condition" => array('LIKE' => "%$query%"));
			} else {

				$exhibition_ids = array();

				$fields = array('title', 'venue', 'curator', 'earliest_date', 'city', 'country', 'remarks');

				foreach ($fields as $field) {
					$matching_exhibits = Exhibitions::find('all', array(
						'with' => 'Archives',
						'fields' => 'Exhibitions.id',
						'conditions' => array("$field" => array('LIKE' => "%$query%")),
					));

					if ($matching_exhibits) {
						$matching_ids = $matching_exhibits->map(function($me) {
							return $me->id;
						}, array('collect' => false));

						$exhibition_ids = array_unique(array_merge($exhibition_ids, $matching_ids));
					}
				}

				$conditions = $exhibition_ids ? array('Exhibitions.id' => $exhibition_ids) : array('title' => $query);
			}

			if ($type != 'All') {
				$conditions['Archives.type'] = $type;
			}

			$exhibitions = Exhibitions::find('all', array(
				'with' => array('Archives', 'Components'),
				'order' => $order,
				'conditions' => $conditions,
				'limit' => $limit,
				'page' => $page
			));

			$total = Exhibitions::count('all', array(
				'with' => array('Archives'),
				'order' => $order,
				'conditions' => $conditions,
			));

		}

		return compact('exhibitions', 'condition', 'type', 'query', 'total', 'page', 'limit', 'auth');
	}

	public function histories() {

		$check = (Auth::check('default')) ?: null;
	
		if (!$check) {
			return $this->redirect('Sessions::add');
		}
		
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));

		$limit = 50;
		$page = isset($this->request->params['page']) ? $this->request->params['page'] : 1;
		$order = array('start_date' => 'DESC');
		$total = ExhibitionsHistories::count();
		$archives_histories = ArchivesHistories::find('all', array(
			'with' => array('Users', 'Archives'),
			'conditions' => array('ArchivesHistories.controller' => 'exhibitions'),
			'limit' => $limit,
			'order' => $order,
			'page' => $page
		));
		
		return compact('auth', 'archives_histories', 'total', 'page', 'limit', 'auth');
	}

	public function venues() {

		// Check authorization
		$check = (Auth::check('default')) ?: null;

		// If the user is not authorized, redirect to the login screen
		if (!$check) {
			return $this->redirect('Sessions::add');
		}

		// Look up the current user with his or her role
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));

		$exhibitions_venues = Exhibitions::find('all', array(
			'fields' => array('venue', 'count(venue) as exhibits'),
			'group' => 'venue',
			'conditions' => array('venue' => array('!=' => '')),
			'order' => array('venue' => 'ASC')
		));

		$venues = $exhibitions_venues->map(function($ev) {
			return array('name' => $ev->venue, 'count' => $ev->exhibits);
		}, array('collect' => false));

		$exhibitions_cities = Exhibitions::find('all', array(
			'fields' => array('city', 'count(city) as cities'),
			'group' => 'city',
			'conditions' => array('city' => array('!=' => '')),
			'order' => array('city' => 'ASC')
		));

		$cities = $exhibitions_cities->map(function($ec) {
			return array('name' => $ec->city, 'count' => $ec->cities);
		}, array('collect' => false));

		$exhibitions_countries = Exhibitions::find('all', array(
			'fields' => array('country', 'count(country) as countries'),
			'group' => 'country',
			'conditions' => array('country' => array('!=' => '')),
			'order' => array('country' => 'ASC')
		));

		$countries = $exhibitions_countries->map(function($ec) {
			return array('name' => $ec->country, 'count' => $ec->countries);
		}, array('collect' => false));

		return compact('venues', 'cities', 'countries', 'auth');

	}

	public function view() {
    
	    $check = (Auth::check('default')) ?: null;
	
        if (!$check) {
            return $this->redirect('Sessions::add');
        }
        
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));
	
		//Don't run the query if no slug is provided
		if(isset($this->request->params['slug'])) {
		
			//Get single record from the database where the slug matches the URL
			$exhibition = Exhibitions::find('first', array(
				'with' => 'Archives',
				'conditions' => array(
				'slug' => $this->request->params['slug'],
			)));
		
			$exhibitions_works = Components::find('all', array(
				'fields' => 'archive_id2',
				'conditions' => array(
					'archive_id1' => $exhibition->id,
					'type' => 'exhibitions_works'
				),
			));

			$works = array();

			if ($exhibitions_works->count()) {

				//Get all the work IDs in a plain array
				$work_ids = $exhibitions_works->map(function($ew) {
					return $ew->archive_id2;
				}, array('collect' => false));

				$works = Works::find('all', array(
					'with' => 'Archives',
					'conditions' => array('Works.id' => $work_ids),
					'order' => 'earliest_date DESC'
				));

			}

			$total = $works ? $works->count() : 0;

			$exhibitions_links = ExhibitionsLinks::find('all', array(
				'with' => 'Links',
				'conditions' => array('exhibition_id' => $exhibition->id),
				'order' => array('date_modified' =>  'DESC')
			));

			$archives_documents = ArchivesDocuments::find('all', array(
				'with' => array(
					'Documents',
					'Formats'
				),
				'conditions' => array('archive_id' => $exhibition->id),
				'order' => array('slug' => 'ASC')
			));
			
			//Send the retrieved data to the view
			return compact('exhibition', 'works', 'total', 'archives_documents', 'exhibitions_links', 'auth');
		}
		
		//since no record was specified, redirect to the index page
		$this->redirect(array('Exhibitions::index'));
	}

	public function add() {
    
	    $check = (Auth::check('default')) ?: null;
	
        if (!$check) {
            return $this->redirect('Sessions::add');
        }
        
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));
        
        // If the user is not an Admin or Editor, redirect to the index
        if($auth->role->name != 'Admin' && $auth->role->name != 'Editor') {
        	return $this->redirect('Exhibitions::index');
        }
        
		$exhibition = Exhibitions::create();

		if (($this->request->data) && $exhibition->save($this->request->data)) {
			//The slug has been saved with the Archive object, so let's look it up
			$archive = Archives::find('first', array(
				'conditions' => array('id' => $exhibition->id)
			));
		
			return $this->redirect(array('Exhibitions::view', 'args' => array($archive->slug)));
		}

		$exhibition_titles = Exhibitions::find('all', array(
			'fields' => 'title',
			'group' => 'title',
			'conditions' => array('title' => array('!=' => '')),
			'order' => array('title' => 'ASC'),
		));

		$titles = $exhibition_titles->map(function($tit) {
			return $tit->title;
		}, array('collect' => false));

		$exhibition_venues = Exhibitions::find('all', array(
			'fields' => 'venue',
			'group' => 'venue',
			'conditions' => array('venue' => array('!=' => '')),
			'order' => array('venue' => 'ASC'),
		));

		$venues = $exhibition_venues->map(function($ven) {
			return $ven->venue;
		}, array('collect' => false));

		$exhibition_cities = Exhibitions::find('all', array(
			'fields' => 'city',
			'group' => 'city',
			'conditions' => array('city' => array('!=' => '')),
			'order' => array('city' => 'ASC'),
		));

		$cities = $exhibition_cities->map(function($cit) {
			return $cit->city;
		}, array('collect' => false));

		$exhibition_countries = Exhibitions::find('all', array(
			'fields' => 'country',
			'group' => 'country',
			'conditions' => array('country' => array('!=' => '')),
			'order' => array('country' => 'ASC'),
		));

		$countries = $exhibition_countries->map(function($con) {
			return $con->country;
		}, array('collect' => false));

		$documents = array();

		if (isset($this->request->data['documents']) && $this->request->data['documents']) {
			$document_ids = $this->request->data['documents'];

			$documents = Documents::find('all', array(
				'with' => 'Formats',
				'conditions' => array('Documents.id' => $document_ids),
			));

		}

		return compact('exhibition', 'titles', 'venues', 'cities', 'countries');
	}

	public function edit() {
    
	    $check = (Auth::check('default')) ?: null;
	
        if (!$check) {
            return $this->redirect('Sessions::add');
        }
        
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));
		
		$exhibition = Exhibitions::find('first', array(
			'with' => 'Archives',
			'conditions' => array(
				'slug' => $this->request->params['slug'],
		)));

		if (!$exhibition) {
			return $this->redirect('Exhibitions::index');
		}

		$exhibition_links = ExhibitionsLinks::find('all', array(
			'with' => 'Links',
			'conditions' => array('exhibition_id' => $exhibition->id),
			'order' => array('date_modified' =>  'DESC')
		));

		$archives_documents = ArchivesDocuments::find('all', array(
			'with' => array(
				'Documents',
				'Formats'
			),
			'conditions' => array('archive_id' => $exhibition->id),
			'order' => array('slug' => 'ASC')
		));

		if (($this->request->data) && $exhibition->save($this->request->data)) {
		
			return $this->redirect(array('Exhibitions::view', 'args' => array($exhibition->archive->slug)));
		}

		$exhibition_titles = Exhibitions::find('all', array(
			'fields' => 'title',
			'group' => 'title',
			'conditions' => array('title' => array('!=' => '')),
			'order' => array('title' => 'ASC'),
		));

		$titles = $exhibition_titles->map(function($tit) {
			return $tit->title;
		}, array('collect' => false));

		$exhibition_venues = Exhibitions::find('all', array(
			'fields' => 'venue',
			'group' => 'venue',
			'conditions' => array('venue' => array('!=' => '')),
			'order' => array('venue' => 'ASC'),
		));

		$venues = $exhibition_venues->map(function($ven) {
			return $ven->venue;
		}, array('collect' => false));

		$exhibition_cities = Exhibitions::find('all', array(
			'fields' => 'city',
			'group' => 'city',
			'conditions' => array('city' => array('!=' => '')),
			'order' => array('city' => 'ASC'),
		));

		$cities = $exhibition_cities->map(function($cit) {
			return $cit->city;
		}, array('collect' => false));

		$exhibition_countries = Exhibitions::find('all', array(
			'fields' => 'country',
			'group' => 'country',
			'conditions' => array('country' => array('!=' => '')),
			'order' => array('country' => 'ASC'),
		));

		$countries = $exhibition_countries->map(function($con) {
			return $con->country;
		}, array('collect' => false));
		
		return compact('exhibition', 'archives_documents', 'exhibition_links', 'titles', 'venues', 'cities', 'countries');
	}

	public function attachments() {

		$check = (Auth::check('default')) ?: null;
	
		if (!$check) {
			return $this->redirect('Sessions::add');
		}
		
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));

		// If the user is not an Admin or Editor, redirect to the index
		if($auth->role->name != 'Admin' && $auth->role->name != 'Editor') {
			return $this->redirect('Exhibitions::index');
		}

		$exhibition = Exhibitions::find('first', array(
			'with' => 'Archives',
			'conditions' => array(
				'slug' => $this->request->params['slug'],
		)));

		if (!$exhibition) {
			return $this->redirect('Exhibitions::index');
		}

		$exhibition_links = ExhibitionsLinks::find('all', array(
			'with' => 'Links',
			'conditions' => array('exhibition_id' => $exhibition->id),
			'order' => array('date_modified' =>  'DESC')
		));

		$archives_documents = ArchivesDocuments::find('all', array(
			'with' => array(
				'Documents',
				'Formats'
			),
			'conditions' => array('archive_id' => $exhibition->id),
			'order' => array('slug' => 'ASC')
		));

		return compact('exhibition', 'exhibition_links', 'archives_documents', 'auth');

	}

	public function history() {
	
		$check = (Auth::check('default')) ?: null;
	
		if (!$check) {
			return $this->redirect('Sessions::add');
		}
		
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));
	
		//Don't run the query if no slug is provided
		if(isset($this->request->params['slug'])) {
		
			//Get single record from the database where the slug matches the URL
			$exhibition = Exhibitions::first(array(
				'with' => 'Archives',
				'conditions' => array('slug' => $this->request->params['slug']),
			));
			
			if($exhibition) {

				$archives_histories = ArchivesHistories::find('all', array(
					'conditions' => array('ArchivesHistories.archive_id' => $exhibition->id),
					'order' => 'ArchivesHistories.start_date DESC',
					'with' => array('Users', 'ExhibitionsHistories'),
				));

				//FIXME We can't actually guarantee that the start_date can be used as a foreign key for the histories,
				//so for now let's grab the subclass history table, then iterate through it as well
				$exhibitions_histories = ExhibitionsHistories::find('all', array(
					'conditions' => array('exhibition_id' => $exhibition->id),
					'order' => array('start_date' => 'DESC')
				));
		
		
				//Send the retrieved data to the view
				return compact('auth', 'exhibition', 'archives_histories', 'exhibitions_histories', 'auth');
			}
		}
		
		//since no record was specified, redirect to the index page
		$this->redirect(array('Works::index'));
	}

	public function delete() {
    
	    $check = (Auth::check('default')) ?: null;
	
        if (!$check) {
            return $this->redirect('Sessions::add');
        }
        
		$auth = Users::first(array(
			'conditions' => array('username' => $check['username']),
			'with' => array('Roles')
		));
		
		$exhibition = Exhibitions::find('first', array(
			'with' => 'Archives',
			'conditions' => array(
			'slug' => $this->request->params['slug'],
		)));
        
        // If the user is not an Admin or Editor, redirect to the record view
        if($auth->role->name != 'Admin' && $auth->role->name != 'Editor') {
        	return $this->redirect(array(
        		'Exhibitions::view', 'args' => array($this->request->params['slug']))
        	);
        }
        
        // For the following to work, the delete form must have an explicit 'method' => 'post'
        // since the default method is PUT
		if (!$this->request->is('post') && !$this->request->is('delete')) {
			$msg = "Exhibitions::delete can only be called with http:post or http:delete.";
			throw new DispatchException($msg);
		}
		
		$exhibition->delete();
		return $this->redirect('Exhibitions::index');
	}
}

?>
