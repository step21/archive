<?php

$this->title($work->title);

$this->form->config(
    array( 
        'templates' => array( 
            'error' => '<div class="help-inline">{:content}</div>' 
        )
    )
); 

$artist_names = json_encode($artists);

$classification_names = json_encode($classifications);


?>

<div id="location" class="row-fluid">

    
	<ul class="breadcrumb">

	<li>
	<?=$this->html->link('Artwork','/works'); ?>
	<span class="divider">/</span>
	</li>

	<li>
	<?=$this->html->link($work->title,'/works/view/'.$work->archive->slug); ?>
	<span class="divider">/</span>
	</li>
	
	<li class="active">
		Edit
	</li>

	</ul>

</div>

<ul class="nav nav-tabs">
	<li><?=$this->html->link('View','/works/view/'.$work->archive->slug); ?></li>
	<li class="active">
		<a href="#">
			Edit
		</a>
	</li>
	<li><?=$this->html->link('History','/works/history/'.$work->archive->slug); ?></li>
</ul>



<div class="row">

	<div class="span5">
		<div class="well">
		<?=$this->form->create($work); ?>
			<legend>Info</legend>
    		<?=$this->form->field('artist', array('autocomplete' => 'off', 'data-provide' => 'typeahead', 'data-source' => $artist_names));?>
			<?=$this->form->field('title');?>
    		<?=$this->form->field('classification', array('autocomplete' => 'off', 'data-provide' => 'typeahead', 'data-source' => $classification_names));?>
			<?=$this->form->field('earliest_date', array('value' => $work->archive->start_date_formatted()));?>
			<?=$this->form->field('latest_date', array('value' => $work->archive->end_date_formatted()));?>
			<?=$this->form->field('creation_number', array('label' => 'Artwork ID'));?>
			<?=$this->form->field('materials', array('type' => 'textarea'));?>
			<?=$this->form->field('quantity');?>
			<?=$this->form->field('remarks', array('type' => 'textarea'));?>
			<?=$this->form->field('height', array(
				'label' => "Height (cm)"
			));?>
			<?=$this->form->field('width', array(
				'label' => "Width (cm)"
			));?>
			<?=$this->form->field('depth', array(
				'label' => "Depth (cm)"
			));?>
			<?=$this->form->field('diameter', array(
				'label' => "Diameter (cm)"
			));?>
			<?=$this->form->field('weight', array(
				'label' => "Weight (kg)"
			));?>
			<?=$this->form->field('running_time');?>
			<?=$this->form->field('measurement_remarks', array('type' => 'textarea'));?>
			<?=$this->form->submit('Save', array('class' => 'btn btn-inverse')); ?>
			<?=$this->html->link('Cancel','/works/view/'.$work->archive->slug, array('class' => 'btn')); ?>
		<?=$this->form->end(); ?>
		</div>
		
		<div class="well">
		
			<legend>Edit</legend>
		
			<a class="btn btn-danger" data-toggle="modal" href="#deleteModal">
				<i class="icon-white icon-trash"></i> Delete Artwork
			</a>
		
		</div>
		
		
	</div>
	
	<div class="span5">
	
	<div class="well">
		<legend>Annotation</legend>
		<?=$this->form->create($work); ?>
			<?=$this->form->field('annotation', array(
				'type' => 'textarea', 
				'rows' => '10', 
				'style' => 'width:90%;',
				'label' => ''
			));?>

			<?=$this->form->hidden('title'); ?>
		
			<?=$this->form->submit('Save', array('class' => 'btn btn-inverse')); ?>
			<?=$this->html->link('Cancel','/works/view/'.$work->archive->slug, array('class' => 'btn')); ?>
		<?=$this->form->end(); ?>
		
	</div>

	<?=$this->partial->archives_links_edit(array(
		'model' => $work,
		'junctions' => $work_links,
	)); ?>		

	<div class="well">
		<legend>Albums</legend>
		<table class="table">
		
			<?php foreach($albums as $album): ?>
			<?php $cw = $album->albums_works[0]; ?> 
				<tr>
					<td>
						<?=$this->html->link($album->title, $this->url(array('Albums::view', 'slug' => $album->archive->slug))); ?>
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($cw, array('url' => $this->url(array('AlbumsWorks::delete', 'id' => $cw->id)), 'method' => 'post')); ?>
			<input type="hidden" name="work_slug" value="<?=$work->archive->slug ?>" />
			<?=$this->form->submit('Remove', array('class' => 'btn btn-mini btn-danger')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			
			<?php endforeach; ?>
			
			<?php if(sizeof($other_albums) > 0): ?>
			
			<tr>
				<td></td>
				<td align="right" style="text-align:right">
					<a data-toggle="modal" href="#albumModal" class="btn btn-mini btn-inverse">Add an Album</a>
				</td>
			</tr>
			
			<?php endif; ?>
			
			</table>
		
	</div>
	
	<div class="well">
		<legend>Exhibitions</legend>
		<table class="table">
		
			<?php foreach($exhibitions as $exhibition): ?>
			<?php $ew = $exhibition->exhibitions_works[0]; ?>
				<tr>
					<td>
						<a href="/exhibitions/view/<?=$exhibition->slug ?>"><?=$exhibition->title ?></a> <strong></strong> 
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($ew, array('url' => "/exhibitions_works/delete/$ew->id", 'method' => 'post')); ?>
			<input type="hidden" name="work_slug" value="<?=$work->archive->slug ?>" />
			<?=$this->form->submit('Remove', array('class' => 'btn btn-mini btn-danger')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			
			<?php endforeach; ?>
			
			<?php if(sizeof($other_exhibitions) > 0): ?>			
			
			<tr>
				<td></td>
				<td align="right" style="text-align:right">
					<a data-toggle="modal" href="#exhibitionModal" class="btn btn-mini btn-inverse">Add an Exhibition</a>
				</td>
			</tr>
			
			<?php endif; ?>
			
			</table>
		
	</div>
	
<div class="modal fade hide" id="albumModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
			<h3>Add this Artwork to an Album</h3>
		</div>
		<div class="modal-body">
			<table class="table"><tbody>
			<?php foreach($other_albums as $oc): ?>
				<tr>
					<td>
						<strong>
							<?=$this->html->link($oc->title, $this->url(array('Albums::view', 'slug' => $oc->slug))); ?>
						</strong><br/>
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($oc, array('url' => $this->url(array('AlbumsWorks::add')), 'method' => 'post')); ?>
			<input type="hidden" name="album_id" value="<?=$oc->id ?>" />
			<input type="hidden" name="work_id" value="<?=$work->id ?>" />
			<input type="hidden" name="work_slug" value="<?=$work->archive->slug ?>" />
			<?=$this->form->submit('Add', array('class' => 'btn btn-mini btn-success')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>
			</div>
			<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Cancel</a>
	</div>
</div>

	<?=$this->partial->archives_documents_edit(array(
		'model' => $work,
		'junctions' => $work_documents,
	)); ?>		

<div class="modal fade hide" id="exhibitionModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
			<h3>Add this Artwork to an Exhibition</h3>
		</div>
		<div class="modal-body">
			<table class="table"><tbody>
			<?php foreach($other_exhibitions as $oe): ?>
				<tr>
					<td>
						<a href="/exhibitions/view/<?=$oe->slug ?>">
							<strong><?=$oe->title ?></a></strong><br/>
							<?=$oe->venue ?><br/>
							<?=$oe->dates() ?>
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($oe, array('url' => "/exhibitions_works/add", 'method' => 'post')); ?>
			<input type="hidden" name="exhibition_id" value="<?=$oe->id ?>" />
			<input type="hidden" name="work_id" value="<?=$work->id ?>" />
			<input type="hidden" name="work_slug" value="<?=$work->archive->slug ?>" />
			<?=$this->form->submit('Add', array('class' => 'btn btn-mini btn-success')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>
			</div>
			<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Cancel</a>
	</div>
</div>

<div class="modal fade hide" id="deleteModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
			<h3>Delete Artwork</h3>
		</div>
		<div class="modal-body">
			<p>Are you sure you want to permanently delete <strong><?=$work->title; ?></strong>?</p>
			
			<p>By selecting <code>Delete</code>, you will remove this Artwork from the listings. Are you sure you want to continue?</p>
			</div>
			<div class="modal-footer">
			<?=$this->form->create($work, array('url' => "/works/delete/$work->id", 'method' => 'post')); ?>
			<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			<?=$this->form->submit('Delete', array('class' => 'btn btn-danger')); ?>
			<?=$this->form->end(); ?>
	</div>
</div>
