<?php

namespace app\models;

class ArchivesHistories extends \app\models\Archives {

	public $belongsTo = array("Archives", "Users");

	public $hasOne = array(
		'WorksHistories' => array (
			'to' => 'app\models\WorksHistories',
			'key' => array(
				'start_date' => 'start_date',
				'archive_id' => 'work_id'
		)),
		'ArchitecturesHistories' => array (
			'to' => 'app\models\ArchitecturesHistories',
			'key' => array(
				'start_date' => 'start_date',
				'archive_id' => 'architecture_id'
		)),
		'AlbumsHistories' => array (
			'to' => 'app\models\AlbumsHistories',
			'key' => array(
				'start_date' => 'start_date',
				'archive_id' => 'album_id'
		)),
		'PublicationsHistories' => array (
			'to' => 'app\models\PublicationsHistories',
			'key' => array(
				'start_date' => 'start_date',
				'archive_id' => 'publication_id'
		)),
	);

	public $validates = array();
}

?>
