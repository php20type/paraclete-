<?php $__env->startSection('content'); ?>
	<form id="openai-form" action="" method="GET" enctype="multipart/form-data">		
		<?php echo csrf_field(); ?>
		<button class="btn btn-primary special-btn" id="chat-button">
												submit
											</button>
									
										</form>

										<div id="response-container"></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>

<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const mainForm = document.getElementById("openai-form");
            const responseContainer = document.getElementById("response-container");

            mainForm.addEventListener("submit", function (event) {
                event.preventDefault();
                fetch('/agentAiCreate', {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    // Display the response in the response container
                    responseContainer.innerHTML = data;
                })
                .catch(error => {
                    console.error(error);
                    // Handle errors if needed
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>	

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/staging.paraclete.ai/public_html/resources/views/user/video/agentAi.blade.php ENDPATH**/ ?>