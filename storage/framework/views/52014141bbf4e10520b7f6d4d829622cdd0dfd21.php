<?php $__env->startSection('css'); ?>
	<!-- Sweet Alert CSS -->
	<link href="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.min.css')); ?>" rel="stylesheet" />
	<style>
	.chat-message-container .d-flex .form-switch{
		max-height: 1rem;
	}
	.audio_search a{
		padding:0px 2px;
		
	}
    #audioPlayer{
        display: block;
    }
	.audio_search .fa{
		font-size:1.3rem;
		cursor:pointer;
	}

	.chats-input-b .form-group {
		position: relative;
		display: flex;
		flex-wrap: inherit;
		align-items: center;
		flex-direction: row;
		width: 100%;
     }
	 .chats-input-b .input-group-btn {
   		 position: relative;
	 }
	 .chats-input-b .microphone-voice {
	    margin: 0px 10px !IMPORTANT;
	}
	.chats-input-b .microphone-voice i {
		width: 40px;
		height: 40px;
		line-height: 40px;
		background: #7111ef;
		color: #ffffff;
		font-size: 18px;
		text-align: center;
		border-radius: 100%;
	}
	.chats-input-b .microphone-voice .active:after {
		content: '';
		width: 12px;
		height: 12px;
		background: red;
		position: absolute;
		border-radius: 100%;
		top: 2px;
	}
	.audio_search a i {
	    width: 24px;
		height: 24px;
		line-height: 24px;
		text-align: center;
		background: #520cae;
		color: #fff;
		border-radius: 100%;
		font-size: 12px;
	}

  .card-footer .dropdown-selected {
    margin: 15px 0;
  }
  .card-footer .dropdown-selected .btn:focus {
	  box-shadow: none;
  }
  .card-footer .dropdown-selected .btn {
        min-width: 100%;
       border: 1px solid #ddd;
       padding: 8px 40px;
	    background: #4987f8c2;
        color: #ffffff;
  }
.chat-main-container .card-footer {
    min-height: auto !important;
    height: auto !important;
}
.input-box.chats-input-b .form-control {
	border-color: #007BFF;
}

.card-footer .dropdown-selected .s-dropdown-menu {
    width: calc(100% - 24px); 
    padding: 10px 30px 10px;
	overflow-y: auto;
    max-height: 340px;
    height: max-content;

}
 .card-footer .dropdown-selected .s-dropdown-menu li {
    font-size: 14px;
    line-height: 24px;
    font-weight: 400;
    list-style: number;
} 
.card.balance-view {
    background: #f5f9fc;
}

.chage-x {
	    background: #f4f4f6;
    padding: 5px;
    border-radius: 4px;
}
.chage-x span.bold-f {
	font-weight: 600;
    color: #222222;
}
.card.balance-view p {
	font-size: 14px;
}
.card.balance-view p span {
    background: #75a4fa;
    color: #ffffff;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 14px;
}

	</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-header'); ?>
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0"><?php echo e(__($chat->name)); ?></h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="<?php echo e(route('user.dashboard')); ?>"><i class="fa-solid fa-messages-question mr-2 fs-12"></i><?php echo e(__('User')); ?></a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('user.chat')); ?>"> <?php echo e(__('AI Chat Assistants')); ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(url('#')); ?>"> <?php echo e(__($chat->name)); ?></a></li>
			</ol>
		</div>
		<div class="page-rightheader">
			<div id="balance-status">
				<span class="fs-11 text-muted pl-3"><i class="fa-sharp fa-solid fa-bolt-lightning mr-2 text-primary"></i><?php echo e(__('Your Balance is')); ?> <span class="font-weight-semibold" id="balance-number"><?php echo e(number_format(auth()->user()->available_words + auth()->user()->available_words_prepaid)); ?></span> <?php echo e(__('Words')); ?></span>
			</div>
		</div>
	</div>
	<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<form id="openai-form" action="" method="GET" enctype="multipart/form-data">		
		<?php echo csrf_field(); ?>
		<div class="row justify-content-md-center">	
			
			<div class="chat-main-container">
				<div class="chat-sidebar-container">
					<div class="chat-sidebar-messages">
						<?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<div class="chat-sidebar-message <?php if($loop->first): ?> selected-message <?php endif; ?>" id="<?php echo e($message->message_code); ?>">
								<div class="chat-title" id="title-<?php echo e($message->message_code); ?>">
									<?php echo e(__($message->title)); ?>

								</div>
								<div class="chat-info">
									<div class="chat-count"><span><?php echo e($message->messages); ?></span> <?php echo e(__('messages')); ?></div>
									<div class="chat-date"><?php echo e(\Carbon\Carbon::parse($message->updated_at)->diffForhumans()); ?></div>
								</div>
								<div class="chat-actions d-flex">
									<a href="#" class="chat-edit fs-12" id="<?php echo e($message->message_code); ?>"><i class="fa-sharp fa-solid fa-pen-to-square" data-tippy-content="<?php echo e(__('Edit Name')); ?>"></i></a>
									<a href="#" class="chat-delete fs-12 ml-2" id="<?php echo e($message->message_code); ?>"><i class="fa-sharp fa-solid fa-trash" data-tippy-content="<?php echo e(__('Delete Chat')); ?>"></i></a>
								</div>
							</div>
						
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>						
					</div>
                    <div class="card balance-view mb-0"> 
					   <div class="card-body">
                        <p>Available words : <span id="available_words"> <?php echo e(number_format(auth()->user()->available_words)); ?> </span> </p>
                        <p>Available words prepaid : <span id="available_words_prepaid"> <?php echo e(number_format(auth()->user()->available_words_prepaid)); ?> </span></p>
                        <p>Total words : <span id="balance"> <?php echo e(number_format(auth()->user()->available_words + auth()->user()->available_words_prepaid)); ?> </span> <p>
                   </div>
				    </div>
					<div class="card-footer">
						<div class="row text-center">						
							<div class="col-sm-12">									
								<a class="btn btn-primary pl-5 pr-5 mt-1" id="new-chat-button"><?php echo e(__('New Chat')); ?></a>
							</div>
						</div>
					</div>
				</div>

				<div class="chat-message-container" id="chat-system">
					<div class="card-header">
						<div class="w-100 pt-2 pb-2">
							<div class="d-flex">
								<div class="overflow-hidden mr-4"><img alt="Avatar" class="chat-avatar" src="<?php echo e(URL::asset($chat->logo)); ?>"></div>
								<div class="widget-user-name"><span class="font-weight-bold"><?php echo e(__($chat->name)); ?></span><br><span class="text-muted"><?php echo e(__($chat->sub_name)); ?></span></div>
							</div>
						</div>
						<div class="w-50 text-right pt-2 pb-2">				
							<a id="expand" class="template-button" href="#"><i class="fa-solid fa-bars table-action-buttons table-action-buttons-big edit-action-button" data-tippy-content="<?php echo e(__('Show Chat Conversations')); ?>"></i></a>
							<a id="export-word" class="template-button mr-2" onclick="exportWord();" href="#"><i class="fa-solid fa-file-word table-action-buttons table-action-buttons-big edit-action-button" data-tippy-content="<?php echo e(__('Export Chat Conversation as Word File')); ?>"></i></a>
							<a id="export-pdf" class="template-button mr-2" onclick="exportPDF();" href="#"><i class="fa-solid fa-file-pdf table-action-buttons table-action-buttons-big edit-action-button" data-tippy-content="<?php echo e(__('Export Chat Conversation as PDF File')); ?>"></i></a>
							<a id="export-txt" class="template-button mr-2" onclick="exportTXT();" href="#"><i class="fa-solid fa-file-lines table-action-buttons table-action-buttons-big edit-action-button" data-tippy-content="<?php echo e(__('Export Chat Conversation Text File')); ?>"></i></a>
							
						</div>
					</div>
					<div class="card-body pl-0 pr-0">
						<div class="row">						
							<div class="col-md-12 col-sm-12" >
								
								<div id="chat-container"></div>
							</div>
						</div>
					</div>
					<div class="card-footer mb-8">
						<div class="row">						
							<div class="col-sm-12">	
								<div class='d-flex justify-content-between'>
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" id="web_access_button">
										<label class="form-check-label" for="web-access-button">Web access</label>
                                        <!-- <label class="form-check-label" >GPT 4</label>
										<img src="<?php echo e(URL::asset("/img/svgs/chatgpt-icon.svg")); ?>" alt="ChatGPT-4 Icon" width="20px" height="20px"> -->
				
									</div>
									<div class="text-black-3 font-medium bg-gray-3 px-1.5 rounded-lg">
										<div class="flex gap-0.5 items-center text-xs whitespace-nowrap">
											<!-- <span style="box-sizing: border-box; display: inline-block; overflow: hidden; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px; position: relative; max-width: 100%;">
												<span style="box-sizing: border-box; display: block; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px; max-width: 100%;">
													<img alt="" aria-hidden="true" src="data:image/svg+xml,%3csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20version=%271.1%27%20width=%2717%27%20height=%2716%27/%3e" style="display: block; max-width: 100%; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px;">
												</span>
												<img aria-label="You'll be charged 2x of the number of words in the output" src="/_next/static/media/info-small-icon.3780da37.svg" decoding="async" data-nimg="intrinsic" class="" style="position: absolute; inset: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%;" srcset="/_next/static/media/info-small-icon.3780da37.svg 1x, /_next/static/media/info-small-icon.3780da37.svg 2x">
											</span> -->
											<p class="chage-x"><svg data-toggle="tooltip" data-placement="top" title="When Web access is highlighted, you will be deducted double the amount of words from your account. Note: Web access is for research purposes only" class="me-1" xmlns="http://www.w3.org/2000/svg" width="17" height="16" fill="none"><path stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M8.5 14a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/><path stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M8 7.5h.5V11H9"/><path fill="#6B7280" stroke="#6B7280" stroke-width="125" d="M9.063 5.25a.687.687 0 1 1-1.375 0 .687.687 0 0 1 1.375 0Z"/></svg><span class="bold-f">Charge: <span class="charge-count">1</span> * output words</span></p>
										</div>
									</div>
									<div class="form-check form-switch d-flex">
										<div class="audio_search">
										<audio id="audioPlayer" controls style="visibility:hidden"></audio>
										<input type="hidden" id="isAudioSearch" value="0">
										<a id="pauseAudio" style="display:none"><i class="fa-regular fa-pause"></i></a>
										<a id="playAudio" style="display:none"><i class="fa-solid fa-play"></i></a>
										<a id="replayAudio" style="display:none"><i class="fa-solid fa-arrows-rotate"></i></a>
										<p id="status"></p>
										</div>
										
									</div>
                                </div>
                                <?php if(isset($template)): ?>
								<div class="dropdown-selected">
										<button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
											Select Template
										</button>
										<ul class="dropdown-menu s-dropdown-menu">
                                            <?php $__currentLoopData = $template; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<li> <?php echo e($t->template); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
									</div>
								<?php endif; ?>	
								<div class="input-box chats-input-b mb-0">								
									<div class="form-group file-browser">							    
										<input type="message" class="form-control <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="message" name="message" placeholder="<?php echo e(__('Enter your question here...')); ?>">
										<div class="microphone-voice"> <a id="record-button"><i class="fa-regular fa-microphone"></i></a>
										</div>
										<label class="input-group-btn">
											<button class="btn btn-primary special-btn" id="chat-button">
												<?php echo e(__('Send')); ?>

											</button>
										</label>
										
									</div> 
									<?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
										<p class="text-danger"><?php echo e($errors->first('message')); ?></p>
									<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
								</div> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.all.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('plugins/pdf/html2canvas.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('plugins/pdf/jspdf.umd.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/export-chat.js')); ?>"></script>
<script type="text/javascript">
	const main_form = get("#openai-form");
	const input_text = get("#message");
	const msgerChat = get("#chat-container");
	const msgerSendBtn = get("#chat-button");
	const bot_avatar = "<?php echo e($chat->logo); ?>";
	const user_avatar = "<?php echo e(URL::asset(auth()->user()->profile_photo_path)); ?>";	
	let chat_code = "<?php echo e($chat->chat_code); ?>";	
	let active_id;
	let default_message;
    const domainUrl = window.location.origin;

	// Process deault chat message	
	$(document).ready(function() {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#audio-player').hide();
		$(".chat-sidebar-message").first().focus().trigger('click');

		let check_messages = document.querySelectorAll('.chat-sidebar-message').length;
		if (check_messages == 0) {
			let id = makeid(10);
			$('#chat-container').html('');
			
			$('.chat-sidebar-messages').prepend(`<div class="chat-sidebar-message selected-message" id=${id}>
					<div class="chat-title" id="title-${id}">
						<?php echo e(__('New Chat')); ?>

					</div>
					<div class="chat-info">
						<div class="chat-count"><span>0</span> <?php echo e(__('messages')); ?></div>
						<div class="chat-date"><?php echo e(__('Now')); ?></div>
					</div>
					<div class="chat-actions d-flex">
						<a href="#" class="chat-edit id=${id} fs-12"><i class="fa-sharp fa-solid fa-pen-to-square" data-tippy-content="<?php echo e(__('Edit Name')); ?>"></i></a>
						<a href="#" class="chat-delete  id=${id} fs-12 ml-2"><i class="fa-sharp fa-solid fa-trash" data-tippy-content="<?php echo e(__('Delete Chat')); ?>"></i></a>
					</div>
				</div>`);
			active_id = id;
		}
		$("#web_access_button").click(function() {
			if ($(this).is(":checked")) {
				$(".charge-count").text("2");
			} else {
				$(".charge-count").text("1");
			}
		});
	});
	

	// Change message box styles
	$(document).on('click', ".chat-sidebar-message", function (e) { 

		$('.chat-sidebar-message').removeClass('selected-message');
		$(this).addClass('selected-message');
		active_id = this.id;

		$('.chat-sidebar-container').removeClass('extend');

		$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'POST',
				url: '/user/chat/messages',
				data: { 'code': active_id,},
				success: function (data) {
					$('#chat-container').html('');

					let messages = document.querySelectorAll('.chat-sidebar-message').length;
					if (messages >= 1) {
						let json = isJson(data)
						if (json) {
							let result = JSON.parse(data);
							if (result['chat']) {
								let chat = result['chat'];

								for(const key in chat) {
									if (chat[key]['role'] == 'user') {
										appendMessage(user_avatar, "right", chat[key]['content']);
									} else if (chat[key]['role'] == 'assistant') {
										appendMessage(bot_avatar, "left", chat[key]['content']);
									}
								}
							}
						}
					} else {
						let id = makeid(10);
						$('#chat-container').html('');

						$('.chat-sidebar-messages').prepend(`<div class="chat-sidebar-message selected-message" id=${id}>
								<div class="chat-title" id="title-${id}">
									<?php echo e(__('New Chat')); ?>

								</div>
								<div class="chat-info">
									<div class="chat-count"><span>0</span> <?php echo e(__('messages')); ?></div>
									<div class="chat-date"><?php echo e(__('Now')); ?></div>
								</div>
								<div class="chat-actions d-flex">
									<a href="#" class="chat-edit id=${id} fs-12"><i class="fa-sharp fa-solid fa-pen-to-square" data-tippy-content="<?php echo e(__('Edit Name')); ?>"></i></a>
									<a href="#" class="chat-delete  id=${id} fs-12 ml-2"><i class="fa-sharp fa-solid fa-trash" data-tippy-content="<?php echo e(__('Delete Chat')); ?>"></i></a>
								</div>
							</div>`);
						active_id = id;
					}
								
				},
				error: function(data) {
					toastr.warning('<?php echo e(__('There was an issue while retrieving chat history')); ?>');
				}
			});
	});

	// Create new chat message box
	$("#new-chat-button").on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
		let id = makeid(10);
		var element = document.getElementById(active_id);
		if (element) {
			element.classList.remove("selected-message");
		}
  		
		$('#chat-container').html('');

        $('.chat-sidebar-messages').prepend(`<div class="chat-sidebar-message selected-message" id=${id}>
				<div class="chat-title" id="title-${id}">
					<?php echo e(__('New Chat')); ?>

				</div>
				<div class="chat-info">
					<div class="chat-count"><span>0</span> <?php echo e(__('messages')); ?></div>
					<div class="chat-date"><?php echo e(__('Now')); ?></div>
				</div>
				<div class="chat-actions d-flex">
					<a href="#" class="chat-edit id=${id} fs-12"><i class="fa-sharp fa-solid fa-pen-to-square" data-tippy-content="<?php echo e(__('Edit Name')); ?>"></i></a>
					<a href="#" class="chat-delete id=${id} fs-12 ml-2"><i class="fa-sharp fa-solid fa-trash" data-tippy-content="<?php echo e(__('Delete Chat')); ?>"></i></a>
				</div>
			</div>`);
		active_id = id;
    });

	$(function () {
		
		main_form.addEventListener("submit", event => {
			event.preventDefault();
			var webAccessBtn = $("#web_access_button").prop('checked') ? 1 : 0;
			$('#audioPlayer').css('visibility','hidden');
			
			const message = input_text.value;
			if (!message) return;

			appendMessage(user_avatar, "right", message);
			input_text.value = "";
			process(message , webAccessBtn)
		});

	});


	// Send chat message
	function process(message , webAccessBtn){
		msgerSendBtn.disabled = true
		let formData = new FormData();
		formData.append('message', message);
		formData.append('chat_code', chat_code);
		formData.append('message_code', active_id);
		formData.append('webAccessBtn', webAccessBtn);
		
		let code = makeid(10);
		appendMessage(bot_avatar, "left", "", code);
		fetch('/user/chat/process', {
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				method: 'POST', 
				body: formData
			})		
			.then(response => response.json())
			.then(function(result){
				
				if (result['old'] && result['current']) {
					animateValue("balance-number", result['old'], result['current'], 300);
				}
		
				if (result['status'] == 'error') {
					Swal.fire('<?php echo e(__('Chat Notification')); ?>', result['message'], 'warning');
					clearConversationInvalid();
				}
			})	
			.then(data => {
				
				const eventSource = new EventSource("/user/chat/generate?message_code=" + active_id);				
				const response = document.getElementById(code);
				const chatbubble = document.getElementById('chat-bubble-' + code);
				
				eventSource.onopen = function(e) {
					response.innerHTML = '';
				};

				eventSource.onmessage = function (e) {

					if (e.data == "[DONE]") {
                        loadChat(active_id);
						msgerSendBtn.disabled = false
						eventSource.close();
                        if( $('#isAudioSearch').val() == '1'){
							fetch("/user/chat/audio-convert", { 
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								method: 'post',
								 body: formData
							 })
                            .then(function(response){
								return response.text();
							})
							.then(function(result){
								const parsedResult = JSON.parse(result);
								convertTextToSpeech(parsedResult.data, parsedResult.voice_code);
							})
                        }
                        fetch("/user/chat/update-words", { 
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								method: 'post',
								 body: formData
							 })
                            .then(function(response){
								return response.text();
							})
							.then(function(result){
								const parsedResult = JSON.parse(result);
                                $("#balance").text(parsedResult.balance);
                                $("#available_words").text(parsedResult.available_words);
                                $("#available_words_prepaid").text(parsedResult.available_words_prepaid);
						    })
                    } else {
						let txt = JSON.parse(e.data).choices[0].delta.content
						if (txt !== undefined) {
							response.innerHTML += txt.replace(/(?:\r\n|\r|\n)/g, '<br>');
						}
						msgerChat.scrollTop += 100;
					}
				};
				eventSource.onerror = function (e) {
					msgerSendBtn.disabled = false
					console.log(e);
					eventSource.close();
				};
				
			})
			.catch(function (error) {
				console.log(error);
				msgerSendBtn.disabled = false
			});

	}

    function loadChat(active_id)
    {
        $.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'POST',
				url: '/user/chat/messages',
				data: { 'code': active_id,},
				success: function (data) {
					$('#chat-container').html('');

					let messages = document.querySelectorAll('.chat-sidebar-message').length;
					if (messages >= 1) {
						let json = isJson(data)
						if (json) {
							let result = JSON.parse(data);
							if (result['chat']) {
								let chat = result['chat'];

								for(const key in chat) {
									if (chat[key]['role'] == 'user') {
										appendMessage(user_avatar, "right", chat[key]['content']);
									} else if (chat[key]['role'] == 'assistant') {
										appendMessage(bot_avatar, "left", chat[key]['content']);
									}
								}
							}
						}
					} else {
						let id = makeid(10);
						$('#chat-container').html('');

						$('.chat-sidebar-messages').prepend(`<div class="chat-sidebar-message selected-message" id=${id}>
								<div class="chat-title" id="title-${id}">
									<?php echo e(__('New Chat')); ?>

								</div>
								<div class="chat-info">
									<div class="chat-count"><span>0</span> <?php echo e(__('messages')); ?></div>
									<div class="chat-date"><?php echo e(__('Now')); ?></div>
								</div>
								<div class="chat-actions d-flex">
									<a href="#" class="chat-edit id=${id} fs-12"><i class="fa-sharp fa-solid fa-pen-to-square" data-tippy-content="<?php echo e(__('Edit Name')); ?>"></i></a>
									<a href="#" class="chat-delete  id=${id} fs-12 ml-2"><i class="fa-sharp fa-solid fa-trash" data-tippy-content="<?php echo e(__('Delete Chat')); ?>"></i></a>
								</div>
							</div>`);
						active_id = id;
					}
								
				},
				error: function(data) {
					toastr.warning('<?php echo e(__('There was an issue while retrieving chat history')); ?>');
				}
			});
    }

	function clearConversation() {
		document.getElementById("chat-container").innerHTML = "";

		fetch('/user/chat/clear', {
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				method: 'POST', 
			})		
			.then(response => response.json())
			.then(function(result){

				if (result.status == 'success') {
					toastr.success('<?php echo e(__('Chat conversation has been cleared successfully')); ?>');
				}

			})	
			.catch(function (error) {
				console.log(error);
				msgerSendBtn.disabled = false
			});
	}

	function clearConversationInvalid() {
		document.getElementById("chat-container").innerHTML = "";

		fetch('/user/chat/clear', {
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				method: 'POST', 
			})		
			.then(response => response.json())
			.then(function(result){})	
			.catch(function (error) {
				console.log(error);
				msgerSendBtn.disabled = false
			});
	}

	// RENAME TITLE
	$(document).on('click', '.chat-edit', function(e) {

		e.preventDefault();

		Swal.fire({
			title: '<?php echo e(__('Rename Chat Title')); ?>',
			showCancelButton: true,
			confirmButtonText: '<?php echo e(__('Rename')); ?>',
			reverseButtons: true,
			input: 'text',
		}).then((result) => {
			if (result.value) {
				var formData = new FormData();
				formData.append("name", result.value);
				formData.append("code", $(this).attr('id'));
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'post',
					url: '/user/chat/rename',
					data: formData,
					processData: false,
					contentType: false,
					success: function (data) {
						if (data['status'] == 'success') {
							Swal.fire('<?php echo e(__('Title Updated')); ?>', '<?php echo e(__('Chat title has been updated successfully')); ?>', 'success');
							document.getElementById("title-"+data['code']).innerHTML =  result.value;
						} else {
							Swal.fire('<?php echo e(__('Update Error')); ?>', '<?php echo e(__('Chat title was not updated correctly')); ?>', 'error');
						}      
					},
					error: function(data) {
						Swal.fire('Update Error', data.responseJSON['error'], 'error');
					}
				})
			} else if (result.dismiss !== Swal.DismissReason.cancel) {
				Swal.fire('<?php echo e(__('No Title Entered')); ?>', '<?php echo e(__('Make sure to provide a new chat title before updating')); ?>', 'error')
			}
		})
	});

	// DELETE PLAN
	$(document).on('click', '.chat-delete', function(e) {

		e.preventDefault();

		Swal.fire({
			title: '<?php echo e(__('Confirm Chat Deletion')); ?>',
			text: '<?php echo e(__('It will permanently delete this chat history')); ?>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: '<?php echo e(__('Delete')); ?>',
			reverseButtons: true,
		}).then((result) => {
			if (result.isConfirmed) {
				var formData = new FormData();
				formData.append("code", $(this).attr('id'));
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'post',
					url: '/user/chat/delete',
					data: formData,
					processData: false,
					contentType: false,
					success: function (data) {
						console.log(data)
						if (data['status'] == 'success') {
							Swal.fire('<?php echo e(__('Chat Deleted')); ?>', '<?php echo e(__('Chat history has been successfully deleted')); ?>', 'success');	
							$("#" + active_id).remove();	
							$('#chat-container').html('');	
							$(".chat-sidebar-message").first().focus().trigger('click');
							let check_messages = document.querySelectorAll('.chat-sidebar-message').length;
							if (check_messages == 0) {
								let id = makeid(10);
								$('#chat-container').html('');
								
								$('.chat-sidebar-messages').prepend(`<div class="chat-sidebar-message selected-message" id=${id}>
										<div class="chat-title" id="title-${id}">
											<?php echo e(__('New Chat')); ?>

										</div>
										<div class="chat-info">
											<div class="chat-count"><span>0</span> <?php echo e(__('messages')); ?></div>
											<div class="chat-date"><?php echo e(__('Now')); ?></div>
										</div>
										<div class="chat-actions d-flex">
											<a href="#" class="chat-edit id=${id} fs-12"><i class="fa-sharp fa-solid fa-pen-to-square" data-tippy-content="<?php echo e(__('Edit Name')); ?>"></i></a>
											<a href="#" class="chat-delete  id=${id} fs-12 ml-2"><i class="fa-sharp fa-solid fa-trash" data-tippy-content="<?php echo e(__('Delete Chat')); ?>"></i></a>
										</div>
									</div>`);
								active_id = id;
							}						
						} else if (data['status'] == 'empty') { 
							$('#chat-container').html('');	
								
						}else {
							Swal.fire('<?php echo e(__('Delete Failed')); ?>', '<?php echo e(__('There was an error while deleting this chat history')); ?>', 'error');
						}      
					},
					error: function(data) {
						Swal.fire('Oops...','Something went wrong!', 'error')
					}
				})
			} 
		})
	});

	// Counter for words
	function animateValue(id, start, end, duration) {
		if (start === end) return;
		var range = end - start;
		var current = start;
		var increment = end > start? 1 : -1;
		var stepTime = Math.abs(Math.floor(duration / range));
		var obj = document.getElementById(id);
		var timer = setInterval(function() {
			current += increment;
			if (current > 0) {
				obj.innerHTML = current;
			} else {
				obj.innerHTML = 0;
			}
			
			if (current == end) {
				clearInterval(timer);
			}
		}, stepTime);
	}

	// Display chat messages (bot and user)
	function appendMessage(img, side, text, code) {
		let msgHTML;
		text = nl2br(text);

		if (side == 'left' && text == '') {
			msgHTML = `
			<div class="msg ${side}-msg">
			<div class="message-img" style="background-image: url(${img})"></div>
			<div class="message-bubble" id="chat-bubble-${code}">
				<div class="msg-text" id="${code}"><img src='<?php echo e(URL::asset("/img/svgs/chat.svg")); ?>'></div>
			</div>
			</div>`;
		} else {
			msgHTML = `
			<div class="msg ${side}-msg">
			<div class="message-img" style="background-image: url(${img})"></div>
			<div class="message-bubble" id="chat-bubble-${code}">
		

				<div class="msg-text" id="${code}">${text}</div>
			</div>
			</div>`;
		}

		msgerChat.insertAdjacentHTML("beforeend", msgHTML);
		msgerChat.scrollTop += 500;
	}

	function get(selector, root = document) {
		return root.querySelector(selector);
	}

	// Generate a random value
	function makeid(length) {
		let result = '';
		const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		const charactersLength = characters.length;
		let counter = 0;
		while (counter < length) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
		counter += 1;
		}
		return result;
	}

	function nl2br (str, is_xhtml) {
     	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
     	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
  	} 

	$("#expand").on('click', function (e) {
        $('.chat-sidebar-container').toggleClass('extend');
    });

	// Check if it is a json
	function isJson(str) {
		try {
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}

	const recordButton = document.getElementById('record-button');
	const statusElement = document.getElementById('status');
	let mediaRecorder;
	let audioChunks = [];
	recordButton.addEventListener('click', toggleRecording);
	function toggleRecording() {
	if (mediaRecorder && mediaRecorder.state === 'recording') {
		stopRecording();
	} else {
		$('#isAudioSearch').val(1);
		startRecording();
	}
	}
	function startRecording() {
	navigator.mediaDevices.getUserMedia({ audio: true })
		.then(function (stream) {
		mediaRecorder = new MediaRecorder(stream);
		mediaRecorder.addEventListener('dataavailable', function (event) {
			audioChunks.push(event.data);
		});
		mediaRecorder.addEventListener('stop', function () {
			const audioBlob = new Blob(audioChunks);
			const formData = new FormData();
			formData.append('audio', audioBlob, 'recorded_audio.wav');
			fetch('/user/chat/save-audio', {
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.response) {
				$('#message').val(data.response.text);
				$('#chat-button').click();
				} else {
				console.log('Error saving audio');
				}
			})
			.catch(error => {
				console.error('Error:', error);
			});
			audioChunks = [];
		});
		mediaRecorder.start();
		recordButton.innerHTML = '<i class="fa-solid fa-stop active"></i>';
		})
		.catch(function (error) {
		console.error('Error:', error);
		});
	}
	function stopRecording() {
		if (mediaRecorder) {
			mediaRecorder.stop();
			recordButton.innerHTML = '<i class="fa-regular fa-microphone"></i>';
		}
	}
		
	function convertTextToSpeech(text, code){
		$.get('<?php echo e(route("convert-text-to-audio")); ?>', { text: text, voiceCode: code })
		.done(function (voices) {
			console.log(voices);
			$('#audioPlayer').css('visibility','inherit');  
			const audioUrl = domainUrl + voices.result_url;
			const audioPlayer = document.getElementById('audioPlayer');
			audioPlayer.src = audioUrl;
			audioPlayer.play();
            $('#isAudioSearch').val(0);
		})
		.fail(function (error) {
			console.error('Error fetching voices:', error);
		});
	}

    $('.s-dropdown-menu li').on('click', function () {
           var selectedTemplateText = $(this).text();
           $('#message').val(selectedTemplateText);
           $('#message').text(selectedTemplateText);

     });       

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/staging.paraclete.ai/public_html/resources/views/user/chat/view.blade.php ENDPATH**/ ?>