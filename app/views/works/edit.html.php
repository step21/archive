<?php

$this->title($work->title);

$this->form->config(
    array( 
        'templates' => array( 
            'error' => '<div class="help-inline">{:content}</div>' 
        )
    )
); 

?>

<div id="location" class="row-fluid">

    
	<ul class="breadcrumb">

	<li>
	<?=$this->html->link('Artwork','/works'); ?>
	<span class="divider">/</span>
	</li>

	<li>
	<?=$this->html->link($work->title,'/works/view/'.$work->slug); ?>
	<span class="divider">/</span>
	</li>
	
	<li class="active">
		Edit
	</li>

	</ul>

</div>


<div class="row">

	<div class="span5">
		<div class="well">
		<?=$this->form->create($work); ?>
			<legend>Info</legend>
			<?=$this->form->field('artist');?>
			<?=$this->form->field('title');?>
			<?=$this->form->field('classification');?>
			<?=$this->form->field('earliest_date');?>
			<?=$this->form->field('latest_date');?>
			<?=$this->form->field('creation_number', array('label' => 'Artwork ID'));?>
			<?=$this->form->field('materials', array('type' => 'textarea'));?>
			<?=$this->form->field('quantity');?>
			<?=$this->form->field('location');?>
			<?=$this->form->field('lender');?>
			<?=$this->form->field('remarks', array('type' => 'textarea'));?>
			<?=$this->form->field('height');?>
			<?=$this->form->field('width');?>
			<?=$this->form->field('depth');?>
			<?=$this->form->field('diameter');?>
			<?=$this->form->field('weight');?>
			<?=$this->form->field('running_time');?>
			<?=$this->form->field('measurement_remarks', array('type' => 'textarea'));?>
			<?=$this->form->submit('Save', array('class' => 'btn btn-inverse')); ?>
			<?=$this->html->link('Cancel','/works/view/'.$work->slug, array('class' => 'btn')); ?>
		<?=$this->form->end(); ?>
		</div>
	</div>
	
	<div class="span5">
	<div class="well">
		<legend>Collections</legend>
		<table class="table">
		
			<?php foreach($collection_works as $cw): ?>
			
				<tr>
					<td>
						<a href="/collections/view/<?=$cw->collection->slug ?>"><?=$cw->collection->title ?></a>
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($cw, array('url' => "/collections_works/delete/$cw->id", 'method' => 'post')); ?>
			<input type="hidden" name="work_slug" value="<?=$work->slug ?>" />
			<?=$this->form->submit('Remove', array('class' => 'btn btn-mini btn-danger')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			
			<?php endforeach; ?>
			
			</table>
		
		<?php if(sizeof($other_collections) > 0): ?>
		
		<form action="/collections_works/add" method="post">
		<legend>Add this work to a Collection</legend>
		<select name="collection_id">
			<?php foreach($other_collections as $oc): ?>
			<option value="<?=$oc->id ?>"><?=$oc->title ?></option>
			<?php endforeach; ?>
		</select>
		
		<input type="hidden" name="work_id" value="<?=$work->id ?>" />
		<input type="hidden" name="work_slug" value="<?=$work->slug ?>" />
		
		<br/>
		
		<?=$this->form->submit('Add', array('class' => 'btn btn-inverse')); ?>
		
		</form>
		
		<?php endif; ?>
		
	</div>
	
	
		
	<div class="well">
		<legend>Exhibitions</legend>
		<table class="table">
		
			<?php foreach($exhibition_works as $ew): ?>
			
				<tr>
					<td>
						<a href="/exhibitions/view/<?=$ew->exhibition->slug ?>"><?=$ew->exhibition->title ?></a> <strong>(<?=$ew->exhibition->years(); ?>)</strong> 
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($ew, array('url' => "/exhibitions_works/delete/$ew->id", 'method' => 'post')); ?>
			<input type="hidden" name="work_slug" value="<?=$work->slug ?>" />
			<?=$this->form->submit('Remove', array('class' => 'btn btn-mini btn-danger')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			
			<?php endforeach; ?>
			
			</table>
		
		<?php if(sizeof($other_exhibitions) > 0): ?>
		
		<form action="/exhibitions_works/add" method="post">
		<legend>Add this work to an Exhibition</legend>
		<select name="exhibition_id">
			<?php foreach($other_exhibitions as $oe): ?>
				<option value="<?=$oe->id ?>"><?=$oe->selection(); ?></option>
			<?php endforeach; ?>
		</select>
		
		<input type="hidden" name="work_id" value="<?=$work->id ?>" />
		<input type="hidden" name="work_slug" value="<?=$work->slug ?>" />
		
		<br/>
		
		<?=$this->form->submit('Add', array('class' => 'btn btn-inverse')); ?>
		
		</form>
		
		<?php endif; ?>
		
	</div>
	
	<div class="well">
		<legend>Images</legend>
		<table class="table">
		
			<?php foreach($work_documents as $wd): ?>
			
				<tr>
					<td align="center" valign="center" style="text-align: center; vertical-align: center; width: 125px;">
						<?php $px = '260'; ?>
						<a href="/documents/view/<?=$wd->document->slug ?>" title="<?=$wd->document->title ?>">
						<img width="125" height="125" src="/files/thumb/<?=$wd->document->slug?>.jpeg" alt="<?=$wd->document->title ?>">
						</a>
					</td>
					<td align="right" style="text-align:right">
			<?=$this->form->create($wd, array('url' => "/works_documents/delete/$wd->id", 'method' => 'post')); ?>
			<input type="hidden" name="work_slug" value="<?=$work->slug ?>" />
			<?=$this->form->submit('Remove', array('class' => 'btn btn-mini btn-danger')); ?>
			<?=$this->form->end(); ?>
					</td>
				</tr>
			
			<?php endforeach; ?>
			
			</table>
		
		<?=$this->form->create(null, array('url' => "/works_documents/add/", 'method' => 'post')); ?>
			<legend>Add a Document</legend>
			<span class="help-block">Find the document you want to add, click the <code>Edit</code> button, copy the text in the <code>Permalink</code> field, and paste it here.</span>
			<?=$this->form->field('document_slug', array('label' => 'Document Permalink'));?>
			
			<input type="hidden" name="work_slug" value="<?=$work->slug ?>" />
			<input type="hidden" name="work_id" value="<?=$work->id ?>" />
		
		<?=$this->form->submit('Add Document', array('class' => 'btn btn-inverse')); ?>
		<?=$this->form->end(); ?>
		
	</div>

	</div>

</div>
