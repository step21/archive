<?php 

$this->title('Add Artwork');

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
	
	<li class="active">
		Add
	</li>

	</ul>

</div>

<div class="well">
<?=$this->form->create($work); ?>
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
    <?=$this->html->link('Cancel','/works', array('class' => 'btn')); ?>
<?=$this->form->end(); ?>
</div>