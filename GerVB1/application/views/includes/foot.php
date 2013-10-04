		<!--
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="jquery/jquery-1.9.0.min.js"><\/script>')</script>
		-->
		<script src="assets/bootstrap-filestyle.js"></script>
		<!--<script src="assets/minoto/uploadform.js"></script>-->
		<!-- todo: make javascript file -->
		<script>
			function stopEvent(e)
			{
				if(e.preventDefault)
					e.preventDefault();
				else
					e.returnValue = false;
			}
			var now = new Date();
			$('*[data-toggle="tooltip"]').tooltip();
			$('a[data-toggle="popover"]').popover();
			$('.datetimepicker').datetimepicker({  });
			$('.timepicker').datetimepicker({ pickDate: false, pickSeconds : false });
			$('.datepicker').datetimepicker({ pickTime: false /*, startDate :  now */ });

			// TODO, using modernizr might be a better idea!
			// `tag` does not work in older versions of Internet Explorer,
			// so disable it for IE.
			if(navigator.appVersion.indexOf("MSIE") == -1){
				$('input[data-type="tag"]').tag({});
			}

			initSelectCustomFwd(); // see application.controls.custom.js;
			
			// Sortable tables
			$('.table-sortable').tablesorter({});
			$('.table-sortable').find('th').css('cursor', 'pointer');
			
			// handle confirmation modals for href links
			$('#confirmation-modal').modal({
				keyboard : true,
				show: false,
				backdrop: true // 'static' iff true modal
			});
			
			// for anchors
			$('a.confirmation-modal').click(function(e){
				// e.preventDefault();
				stopEvent(e);
				var primaryButton = $('#confirmation-modal a.btn-primary');
				primaryButton.off('click');
				primaryButton.attr('href', $(this).attr('href'));
				$('#confirmation-modal').modal('show');
			});
			
			// for forms
			$('button[type="submit"].confirmation-modal').click(function(e){
				//e.preventDefault();
				stopEvent(e);
				var primaryButton = $('#confirmation-modal a.btn-primary');
				var theForm = $(this).parents('form:first');
				// primaryButton.attr('href', theForm.attr('action'));
				primaryButton.off('click');
				primaryButton.on('click', function(event) {
					//event.preventDefault();
					stopEvent(event);
					theForm.submit();
				});
				$('#confirmation-modal').modal('show');
			});

			// Simple form use ajax... NOTE: This does NOT work in IE8 and lower.
			$('form.ajax-form-simple').each(function(){
				var myform    = $(this);
				var mybutton  = myform.find('button[type="submit"]');
				var mymessage = myform.find('div.my-message');

				myform.submit(function(event){
					stopEvent(event);
					
					myform.ajaxSubmit({
						// url: myurl,
						beforeSend: function(){
							mybutton.button('loading');
						},
						uploadProgress: function(event, position, total, percentComplete) {
							//
						},
						success: function(responseText, statusText, xhr, $form){
							// console.log(responseText);
							mymessage.html(responseText);
						},
						complete: function(){
							mybutton.button('reset');
						}
					});
					return false;
				});
			});
			
			// TABS <--> HASH
			// --------------
			// Javascript to enable link to tab
			var url = document.location.toString();
			if (url.match('#')) {
				$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
			} 
			// Change hash for page-reload
			$('.nav-tabs a').on('shown', function (e) {
				window.location.hash = e.target.hash;
			})
			
			
			// unable to track progress via cross-domain upload.
			/*
			$('form').ajaxForm({
				beforeSend: function() {
					var percentVal = '0%';
					$('#progress .bar').animate({ width: percentVal },1000);
					$('#progress .remaining').html(percentVal);
				},
				uploadProgress: function(event, position, total, percentComplete) {
					var percentVal = percentComplete + '%';
					$('#progress .bar').animate({ width: percentVal },1000);
					$('#progress .remaining').html(percentVal);
				},
				success: function() {
					var percentVal = '100%';
					$('#progress .bar').animate({ width: percentVal },1000);
					$('#progress .remaining').html(percentVal);
				},
				complete: function(xhr) {
					console.log('ok');
				}
			});
			*/
			
			/*
			$('#uploadButton').click(function(e){
				e.preventDefault();
				beginUpload();
				// $('form').submit();
				$.ajax({
					type: $('form').attr('method'),
					url: $('form').attr('action'),
					data: $("form").serialize(),
					success: function(data) {
						// alert(data); // show response from the php script.
					}
				});
			});
			*/
			// Upload

			function beginUpload() {
				$("#progress .bar").css('width', '0%');
				$("#progress").fadeIn();
				setTimeout("showUpload()", 3000);
			}
			
			function showUpload() {
				var url = '<?php echo $base_url; ?>dashboard/video/progress/' + $('#pid').val() +'/'+ $('#vid').val() +'/'+ $('#UPLOAD_IDENTIFIER').val();
				$.getJSON(url , function(response) {
					if (!response)
						return;
					var percentage = Math.floor(100 * parseInt(response.upload_bytes_uploaded) / parseInt(response.upload_bytes_total));
					$('#progress .bar').animate({ width: percentage+"%" },1000);
					
					var text = 'Approximately ' + formatRemaining(response.upload_time_remaining) + ' remaining (' + percentage + ' / 100%)';
					//console.log(text);
					$('#progress .remaining').html(text);
				});
				setTimeout("showUpload()", 3000);    
			}

			function formatRemaining(seconds) {
				if(seconds < 60) {
					return seconds + ' seconds';
				}
				var minutes = Math.floor(seconds / 60);
				seconds = seconds % 60;
				if(minutes < 60) {
					return minutes + ' minutes, ' + seconds + ' seconds';
				}
				var hours = Math.floor(minutes / 60);
				minutes = minutes % 60;
				return hours + ' hours, ' + minutes + ' minutes';
			}
		</script>
	</body>
</html>