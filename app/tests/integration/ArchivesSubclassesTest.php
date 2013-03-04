<?php

namespace app\tests\integration;

use app\models\Archives;
use app\models\ArchivesHistories;

use app\models\Works;
use app\models\WorksHistories;

use app\models\Albums;
use app\models\AlbumsHistories;


class ArchivesSubclassesTest extends \lithium\test\Integration {

	public function setUp() {

		Works::find("all")->delete();
		WorksHistories::find("all")->delete();

		Albums::find("all")->delete();
		AlbumsHistories::find("all")->delete();

		Archives::find("all")->delete();
		ArchivesHistories::find("all")->delete();

	}

	public function tearDown() {

		Works::find("all")->delete();
		WorksHistories::find("all")->delete();

		Albums::find("all")->delete();
		AlbumsHistories::find("all")->delete();

		Archives::find("all")->delete();
		ArchivesHistories::find("all")->delete();

	}

	public function testWorksSubclass() {

		$work = Works::create();
		$data = array(
			'title' => 'The Title',
			'artist' => 'The Artist',
			'classification' => 'Painting',
			'catalog_level' => 'series',
			'earliest_date' => 'February 2012'
		);

		$slug = 'The-Title';

		$this->assertTrue($work->save($data));

		$archive = Archives::first();

		$this->assertEqual($work->id, $archive->id);

		$this->assertEqual($data['classification'], $archive->classification);
		$this->assertEqual('works', $archive->controller);

		$archive = Archives::find('first', array(
			'with' => 'Works'	
		));

		$this->assertEqual($archive->id, $archive->work->id);
		$this->assertEqual($slug, $archive->slug);

		$work = Works::find('first', array(
			'with' => 'Archives'
		));

		$this->assertEqual($work->id, $work->archive->id);
		$this->assertEqual($slug, $work->archive->slug);

		$work = Works::find('first', array(
			'with' => 'Archives',
			'conditions' => array('slug' => $slug)
		));

		$this->assertTrue($work);

		$this->assertEqual('series', $work->archive->catalog_level);
		$this->assertEqual('2012', $work->archive->years());

		$data['catalog_level'] = 'item';

		$work->save($data);

		$work = Works::find('first', array(
			'with' => 'Archives'
		));

		$this->assertEqual('item', $work->archive->catalog_level);

		$count = Works::count();

		$this->assertEqual('1', $count);

		$count = Archives::count();

		$this->assertEqual('1', $count);

		$work->delete();

		$count = Archives::count();

		$this->assertEqual('0', $count);

	}

	public function testAlbumsSubclass() {

		$album = Albums::create();
		$data = array(
			'title' => 'The Title',
			'remarks' => 'Some Description',
			'classification' => 'Personal',
			'catalog_level' => 'album',
			'earliest_date' => 'February 2012'
		);

		$slug = 'The-Title';

		$this->assertTrue($album->save($data));

		$archive = Archives::first();

		$this->assertEqual($album->id, $archive->id);

		$this->assertEqual($data['classification'], $archive->classification);
		$this->assertEqual('albums', $archive->controller);

		$archive = Archives::find('first', array(
			'with' => 'Albums'	
		));

		$this->assertEqual($archive->id, $archive->album->id);
		$this->assertEqual($slug, $archive->slug);

		$album = Albums::find('first', array(
			'with' => 'Archives'
		));

		$this->assertEqual($album->id, $album->archive->id);
		$this->assertEqual($slug, $album->archive->slug);

		$album = Albums::find('first', array(
			'with' => 'Archives',
			'conditions' => array('slug' => $slug)
		));

		$this->assertTrue($album);

		$this->assertEqual('album', $album->archive->catalog_level);
		$this->assertEqual('2012', $album->archive->years());

		$data['catalog_level'] = 'item';

		$album->save($data);

		$album = Albums::find('first', array(
			'with' => 'Archives'
		));

		$this->assertEqual('item', $album->archive->catalog_level);

		$count = Albums::count();

		$this->assertEqual('1', $count);

		$count = Archives::count();

		$this->assertEqual('1', $count);

		$album->delete();

		$count = Archives::count();

		$this->assertEqual('0', $count);

	}
}

?>
