<section class="title">
<?php if ($this->method == 'create'): ?>
	<h4><?php echo lang('dm_admin.create_title'); ?></h4>
<?php else: ?>
	<h4><?php echo sprintf(lang('dm_admin.edit_title')); ?></h4>
<?php endif; ?>
</section>

<section class="item">
<?php echo form_open_multipart(uri_string(), 'class="download_manager"'); ?>
	<div class="form_inputs">
		<ul>
			<li class="odd">
				<label for="download_name"><?php echo lang('dm_admin.name_label'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('download_name', htmlspecialchars_decode($file->name), 'maxlength="50" id="download_name"'); ?></div>				
			</li>
			<li class="even">
				<label for="download_slug"><?php echo lang('dm_admin.slug_label'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('download_slug', htmlspecialchars_decode($file->slug), 'maxlength="50" id="title"'); ?></div>				
			</li>
			<li class="odd">
				<label for="download_downloads"><?php echo lang('dm_admin.downloads_label'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('download_downloads', $file->downloads, 'id="download_downloads"'); ?></div>				
			</li>
			<li>
				<label for="download_status"><?php echo lang('dm_admin.status_label'); ?> <span>*</span></label>
				<div class="input type-radio">
					<label class="inline">
						<?php echo form_radio('download_status', 1, $file->status, 'id="download_status_1"') ?>
						<?php echo lang('dm_admin.enabled'); ?>
					</label>
					<label class="inline">
						<?php echo form_radio('download_status', 0, !$file->status, 'id="download_status_0"') ?>
						<?php echo lang('dm_admin.disabled'); ?>
					</label>
				</div>
			</li>
			<li>
				<label for="download_login"><?php echo lang('dm_admin.login_label'); ?> <span>*</span></label>
				<div class="input type-radio">
					<label class="inline">
						<?php echo form_radio('download_login', 1, $file->login, 'id="download_login_1"') ?>
						<?php echo lang('dm_admin.login_all'); ?>
					</label>
					<label class="inline">
						<?php echo form_radio('download_login', 0, !$file->login, 'id="download_login_0"') ?>
						<?php echo lang('dm_admin.login_register'); ?>
					</label>
				</div>
			</li>
			<li>
				<label for="download_type"><?php echo lang('dm_admin.status_label'); ?> <span>*</span></label>
				<div class="input type-radio">
					<label class="inline">
						<?php echo form_radio('download_type', 1, ($file->type == Download_Manager_m::$TYPE['LOCAL']), 'id="download_type_1"') ?>
						<?php echo lang('dm_admin.file'); ?>
					</label>
					<label class="inline">
						<?php echo form_radio('download_type', 0, ($file->type != Download_Manager_m::$TYPE['LOCAL']), 'id="download_type_0"') ?>
						<?php echo lang('dm_admin.url'); ?>
					</label>
				</div>
			</li>
			<li class="filetype file <?php if($file->type == Download_Manager_m::$TYPE['EXTERNAL']) echo 'hidden';?>"">
				<label for="download_file_file"><?php echo lang('dm_admin.file_label'); ?> <span>*</span></label>
				<div class="input">
					<?php echo form_upload('download_file_file', '', 'id="download_file_file"'); ?>
					<?php if($file->type == Download_Manager_m::$TYPE['LOCAL']): ?>
						<?php echo lang('dm_admin.current_file');?>
						<a href="<?php echo $file->file->path;?>" target="_blank"><?php echo $file->file->name; ?></a>
					<?php endif; ?>
				</div>			
			</li>
			<li class="filetype url <?php if($file->type == Download_Manager_m::$TYPE['LOCAL']) echo 'hidden';?>">
				<label for="download_file_url"><?php echo lang('dm_admin.file_label'); ?> <span>*</span></label>
				<div class="input">
					<?php echo form_input('download_file_url', $file->type == Download_Manager_m::$TYPE['EXTERNAL'] ? $file->file : '', 'id="download_file_url"'); ?>
				</div>			
			</li>
			<?php if(isset($file->id)) : ?>
				<?php echo form_hidden('id', $file->id);?>
				<?php echo form_hidden('old_download_file', $file->type == Download_Manager_m::$TYPE['LOCAL'] ? $file->file->id : $file->file);?>
			<?php endif; ?>
			<div class="buttons float-right padding-top">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); ?>
			</div>
		</ul>
	</div>
<?php echo form_close(); ?>