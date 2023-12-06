
<?php $__env->startSection('css'); ?>
	<!-- Data Table CSS -->
	<link href="<?php echo e(URL::asset('plugins/datatable/datatables.min.css')); ?>" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.min.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-header'); ?>
<!-- PAGE HEADER -->
<div class="page-header mt-5-7">
	<div class="page-leftheader">
		<h4 class="page-title mb-0"><?php echo e(__('Edit Banner')); ?></h4>
		<ol class="breadcrumb mb-2">
			<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i><?php echo e(__('Admin')); ?></a></li>
			<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('admin.davinci.dashboard')); ?>"> <?php echo e(__('Davinci Management')); ?></a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="#"> <?php echo e(__('Edit Banner')); ?></a></li>
		</ol>
	</div>
	
</div>
<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>	
	<div class="row">
		<div class="col-sm-12">
			<div class="card border-0">	
				<div class="card-header">
					<h3 class="card-title"><i class="fa-solid fa-microchip-ai mr-2 text-primary"></i><?php echo e(__('Banner Editor')); ?></h3>
				</div>			
				<div class="card-body pt-5">
					<form class="w-100" action="<?php echo e(route('admin.davinci.banner.update', $id)); ?>" method="POST" enctype="multipart/form-data">
						<?php echo method_field('PUT'); ?>
						<?php echo csrf_field(); ?>

						<div class="row">	
							<div class="col-lg-6 col-md-12 col-sm-12">													
								<div class="input-box">								
									<h6><?php echo e(__('Image')); ?> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="file" class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="image" name="image">
										<?php if($id->image): ?>
											<img src="<?php echo e(asset('banner/'.$id->image)); ?>" height="80px" width="80px">
										<?php endif; ?>
									</div> 
								</div> 
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-12 col-sm-12 mt-2">
								<div class="form-group">
									<h6><?php echo e(__('Banner Type')); ?> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="d-flex">
										<div class="custom-control custom-radio mr-3">
											<input type="radio" id="bannerTypeWebsite" name="banner_type" class="custom-control-input" value="website" <?php if($id->type === 'website'): ?> checked <?php endif; ?>>
											<label class="custom-control-label" for="bannerTypeWebsite"><?php echo e(__('Website')); ?></label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" id="bannerTypeVideo" name="banner_type" class="custom-control-input" value="video" <?php if($id->type === 'video'): ?> checked <?php endif; ?>>
											<label class="custom-control-label" for="bannerTypeVideo"><?php echo e(__('Video')); ?></label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-12 col-sm-12 mt-2">
								<div class="input-box">
									<h6><?php echo e(__('URL')); ?> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">                                
										<input type="text" class="form-control <?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="url" name="url" value="<?php echo e($id->url); ?>">
										<?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
											<p class="text-danger"><?php echo e($message); ?></p>
										<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
									</div> 
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-12 col-sm-12 mt-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="status" class="custom-switch-input" <?php if($id->status): ?> checked <?php endif; ?>>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description"><?php echo e(__('Activate Banner')); ?></span>
									</label>
								</div>
							</div>

							
						</div>

						<div class="row">
							

							
				
							<div class="col-md-12 col-sm-12 text-center mb-2">
								<a href="<?php echo e(route('admin.davinci.banner')); ?>" class="btn btn-cancel mr-2"><?php echo e(__('Return')); ?></a>
								<button type="submit" class="btn btn-primary pl-5 pr-5"><?php echo e(__('Update')); ?></button>	
							</div>	
							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
	<script src="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.all.min.js')); ?>"></script>
	<!-- Data Tables JS -->
	<script src="<?php echo e(URL::asset('plugins/datatable/datatables.min.js')); ?>"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";

		});

		let i = 2;
		function addField(plusElement, k){

			if (k == 2) {
				i = 3;
			}

			let field_type = plusElement.previousElementSibling;
			let placeholder = field_type.previousElementSibling;	
			let name = placeholder.previousElementSibling;	
		
			// Stopping the function if the input field has no value.
			if(placeholder.previousElementSibling.value.trim() === ""){
				return false;
			}

			createButton(name.id);
			
			let new_field ='<div class="field input-group mb-4">' +
								'<input type="hidden" name="code[]" value="input-field-' + i + '">' +
								'<input type="text" class="form-control" name="names[]" id="input-field-' + i + '" placeholder="<?php echo e(__('Enter Input Field Title (Required)')); ?>">' +
								'<input type="text" class="form-control" placeholder="<?php echo e(__('Enter Input Field Description')); ?>" name="placeholders[]">' +								
								'<select class="form-select" name="input_field[]" >' +
									'<option value="input" selected><?php echo e(__('Input Field')); ?></option>' +
									'<option value="textarea"><?php echo e(__('Textarea Field')); ?></option>' +
									'</select>' +
								'<span onclick="addField(this)" class="btn btn-primary">' +
									'<i class="fa fa-btn fa-plus"></i>' +
								'</span>' +
								'<span onclick="removeField(this)" class="btn btn-primary">' +
									'<i class="fa fa-btn fa-minus"></i>' +
								'</span>' +
							'</div>';
			i++;
   			$("#field-container").append(new_field);

			// Un hiding the minus sign.
			plusElement.nextElementSibling.style.display = "block"; 
			// Hiding the plus sign.
			plusElement.style.display = "none"; 
		}

		function removeField(minusElement){
			let plusElement = minusElement.previousElementSibling;
			let field_type = plusElement.previousElementSibling;
			let placeholder = field_type.previousElementSibling;	
			let name = placeholder.previousElementSibling;	

			deleteButton(name.id);

			minusElement.parentElement.remove();
		}

		function createButton(id) {
			let new_button = '<span onclick="insertText(this)" id="' + id+'-button" class="btn btn-primary mr-4 mb-2">' + id + '</span>';
   			$("#field-buttons").append(new_button);
		}

		function deleteButton(id) {
			let button = document.getElementById(id + '-button');
			button.remove();
		}

		function insertText(value) {
			insertToPrompt(" ###" + value.innerHTML + "### ");
		}

		function insertToPrompt(text) {
			var curPos = document.getElementById("prompt").selectionStart;
			let x = $("#prompt").val();
			$("#prompt").val(x.slice(0, curPos) + text + x.slice(curPos));
		}
		
		$('input[name="banner_type"]').on('change', function () {
             $('#url').val('');
        });

	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/staging.paraclete.ai/public_html/resources/views/admin/davinci/banner/edit.blade.php ENDPATH**/ ?>