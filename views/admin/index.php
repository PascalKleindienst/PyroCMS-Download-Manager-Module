<section class="title"> 
<h4><?php echo lang('dm_admin.title'); ?> </h4>
</section>
<section class="item">
	<?php if($downloads): ?>
		<table class="table-list">
			<thead>
				<tr>
					<th><?php echo lang('dm_admin.name_label');?></th>
					<th><?php echo lang('dm_admin.slug_label');?></th>
					<th><?php echo lang('dm_admin.downloads_label');?></th>
					<th><?php echo lang('dm_admin.status_label');?></th>
					<th><?php echo lang('dm_admin.login_label');?></th>
					<th><?php echo lang('dm_admin.file_label');?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($downloads as $file): ?>
				<tr>
					<td><?php echo $file->name; ?></td>
					<td><?php echo $file->slug; ?></td>
					<td><?php echo $file->downloads; ?></td>
					<td><?php echo $file->status; ?></td>
					<td><?php echo $file->login; ?></td>
					<td>
					<?php if ($file->type): ?> 
						<?php $f = Files::get_file($file->file);?>
						<a href="<?php echo $f['data']->path;?>" target="_blank"><?php echo $f['data']->name; ?></a>
					<?php else: ?>
						<a href="<?php echo $file->file;?>" target="_blank"><?php echo $file->file; ?></a>
					<?php endif;?>
					</td>
					<td class="actions">
					<?php echo anchor('admin/download_manager/edit/'.$file->id.'/', lang('global:edit'), 'class="button"'); ?>
					<?php echo anchor('admin/download_manager/delete/'.$file->id.'/', lang('global:delete'), 'class="confirm button"'); ?></td>
				</tr>	
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="no_data"><?php echo lang('dm_admin.no_downloads'); ?></div>
	<?php endif; ?>
</section>