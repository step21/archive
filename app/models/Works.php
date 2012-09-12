<?php

namespace app\models;

use lithium\util\Inflector;
use lithium\util\Validator;
use lithium\security\Auth;

class Works extends \app\models\Archives {

	public $hasMany = array('CollectionsWorks', 'WorksDocuments', 'WorksHistories');

	public $belongsTo = array('Users');

    public function dimensions($entity) {
    	$hwd = array_filter(array($entity->height, $entity->width, $entity->depth));
    	$measures = $hwd ? implode(' × ', $hwd) . ' cm' : '';
    	$diameter = $entity->diameter ? 'Ø ' . $entity->diameter . ' cm' : '';
    	$running_time = $entity->running_time ? $entity->running_time : '';
    	$dimensions = array_filter(array($measures, $diameter, $running_time));
    	return implode(', ', $dimensions);
    }
    
    public function caption($entity) {
    
    	$years = Works::years($entity);
    
    	$caption = array_filter(array(
    		$entity->artist,
    		'<em>'.$entity->title.'</em>',
    		$years,
    		$entity->dimensions()
    	));
    	
    	return implode(', ', $caption) . '.';
    
    }
    
    public function notes($entity) {
						
		$dimensions = $entity->dimensions();
		
		$materials = $entity->materials ? $entity->materials : '';
		$dimensions = $dimensions ? $dimensions : '';
		$measurement_remarks = $entity->measurement_remarks ? $entity->measurement_remarks : '';
		$quantity = $entity->quantity ? 'Quantity: ' . $entity->quantity : '';
		$lender = $entity->lender ? 'Lender: ' . $entity->lender : '';
		$remarks =  $entity->remarks ? $entity->remarks : '';
		
		$info = array_filter(array(
			$materials,
			$dimensions,
			$measurement_remarks,
			$quantity,
			$lender,
			$remarks,
		));
		
		return implode('<br/>', $info);
	}

	public function documents($entity,  $type = 'all', $conditions = null) {
		
		$conditions['work_id'] = $entity->id;

		$documents = Documents::find($type, array(
			'with' => array(
				'WorksDocuments',
				'Formats'
			),
			'conditions' => $conditions,
		));

		return $documents;
	}

}

Works::applyFilter('delete', function($self, $params, $chain) {

	$work_id = $params['entity']->id;
		
	//Delete any relationships
	CollectionsWorks::find('all', array(
		'conditions' => array('work_id' => $work_id)
	))->delete();
	
	WorksDocuments::find('all', array(
		'conditions' => array('work_id' => $work_id)
	))->delete();

	return $chain->next($self, $params, $chain);

});

Works::applyFilter('save', function($self, $params, $chain) {


	$check = (Auth::check('default')) ?: null;

	if($check) {

		$user_id = $check['id'];
		$params['data']['user_id'] = $user_id;
		
	}

	return $chain->next($self, $params, $chain);
});

?>
