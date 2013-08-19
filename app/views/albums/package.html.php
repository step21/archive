<?php 

$this->title($album->title);

if($this->authority->timezone()) {
	$tz = new DateTimeZone($this->authority->timezone());
}

?>

<div id="location" class="row-fluid">
    
	<ul class="breadcrumb">

	<li>
		<?=$this->html->link('Albums', $this->url(array('Albums::index'))); ?>
		<span class="divider">/</span>
	</li>

	<li>
		<?=$this->html->link($album->title, $this->url(array('Albums::view', 'slug' => $album->archive->slug))); ?>
		<span class="divider">/</span>
	</li>
	
	<li class="active">
		Package
	</li>

	</ul>

</div>

<div class="actions">

	<ul class="nav nav-tabs">
		<li><?=$this->html->link('View', $this->url(array('Albums::view', 'slug' => $album->archive->slug))); ?></li>

		<?php if($this->authority->canEdit()): ?>
		
			<li><?=$this->html->link('Edit', $this->url(array('Albums::edit', 'slug' => $album->archive->slug))); ?></li>
		
		<?php endif; ?>

		<li><?=$this->html->link('History', $this->url(array('Albums::history', 'slug' => $album->archive->slug))); ?></li>

		<li class="active"><?=$this->html->link('Packages', $this->url(array('Albums::package', 'slug' => $album->archive->slug))); ?></li>

	</ul>

</div>


<table class="table table-striped table-bordered">
<thead>
	<tr>
		<th style="width:14px"><i class="icon-eye-close"></i></th>
		<th>Package</th>
		<th>Date</th>
		<?php if($this->authority->canEdit()): ?>
		<th>Delete</th>
		<?php endif; ?>
	</tr>
</thead>

<?php foreach ($packages as $package): ?>

	<?php
		$package_date_created = new DateTime($package->date_created);

		if (isset($tz)) {
			$package_date_created->setTimeZone($tz);
		}
		$package_date_display = $package_date_created->format("Y-m-d H:i:s");
	?>

<tr>
	
	<td>
		<?php
			$filesystem = $package->filesystem;

			switch ($filesystem) {
				case "secure":
					echo '<i class="icon-lock">';
				break;
				case "packages":
					echo '<i class="icon-eye-open">';
				break;
			}

		?>
	</td>
	<td><?=$this->html->link($package->name, $package->url()); ?></td>
	<td><?=$package_date_display ?></td>
	<?php if($this->authority->canEdit()): ?>
	<td>
			<?=$this->form->create($package, array('url' => "/packages/delete/$package->id", 'method' => 'post', 'style' => 'margin-bottom:0;')); ?>
			<?=$this->form->submit('Delete', array('class' => 'btn btn-mini btn-danger')); ?>
			<?=$this->form->end(); ?>
	</td>
	<?php endif; ?>

</tr>

<?php endforeach; ?>

</table>

<?php if($this->authority->canEdit()): ?>

<div class="well">
<legend>Create Package</legend>
<?=$this->form->create(null, array('url' => "/packages/add/", 'method' => 'post')); ?>
	<?=$this->form->hidden('album_id', array('value' => $album->id)); ?>
	<?=$this->form->hidden('slug', array('value' => $album->archive->slug)); ?>
<fieldset>
	<label class="radio inline">
		<input type="radio" name="filesystem" id="secure" value="secure" checked>
		<i class="icon-lock"></i> Secure Package
	</label>
	<label class="radio inline">
		<input type="radio" name="filesystem" id="packages" value="packages">
		<i class="icon-eye-open"></i> Public Package
	</label>
</fieldset>
<br/>
<fieldset>
	<?=$this->form->submit('Create Package', array('class' => 'btn btn-primary')); ?> 
</fieldset>
<?=$this->form->end(); ?> 
</div>
<?php endif; ?>
