

<div class="message-input">
  <div class="wrap">
    <form>
      <input type="text" name="msg" id="msg" value="" required placeholder="اكتب رسالتك الان" />
      <button type="submit" class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
    </form>
  </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>

    $(document).ready(function() {
        
        // Set CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // Handle form submission
        $('form').submit(function(e) {

          	e.preventDefault();
            
            var msg = $('#msg').val();

            //alert(msg);

            const data = {
                event_user_id: '<?php echo e($event_user_id); ?>',
                action: 'save_msg',
                msg: msg
            };                    

            $.ajax({
                url: '<?php echo e(route("website.chat.save_event_action")); ?>', // Laravel route
                method: 'POST',
                data: data, // Send key-value pairs
                // data: $(this).serialize(),
                success: function(response) {

                    $('form')[0].reset();
                  
                  	setTimeout(function() {
                      location.reload(); // Reload the page
                    }, 1500); // 2000 milliseconds = 2 seconds
                  
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });

        });
        
        
    });

</script><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/event/send_msg.blade.php ENDPATH**/ ?>