<?php $__env->startSection('title','تفاصيل الحدث'); ?>

<?php $__env->startSection('header'); ?>

  <style>
    th , td {
      text-align: center !important
    }

    .is_popularity i {
        color: blue
    }
  </style>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>




<!-- Zero configuration table -->
<section id="basic-datatable">

  <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            تفاصيل الحدث
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> الوصف  </th>
                  	<th> ملاحظات  </th>
                    <th> التاريخ  </th>
                </tr>
              </thead>

              <tbody>

                <?php $x = 1; ?>

                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                	<?php
                		$log = json_decode($value->log,true);
                	?>
                


                  <tr>
                      <td> <?php echo e($x); ?> </td>
                    
                      <td>
                    	<?php if( is_array($log) && array_key_exists("entry",$log) && array_key_exists("changes",$log['entry'][0]) && array_key_exists("value",$log['entry'][0]['changes'][0]) && array_key_exists("statuses",$log['entry'][0]['changes'][0]['value']) && array_key_exists("status",$log['entry'][0]['changes'][0]['value']['statuses'][0])  ): ?>
                         
                        	حالة الحدث <b>  <?php echo e($log['entry'][0]['changes'][0]['value']['statuses'][0]['status']); ?>  </b>
                                            	
                        <?php elseif( is_array($log) && array_key_exists("entry",$log) && array_key_exists("changes",$log['entry'][0]) && array_key_exists("value",$log['entry'][0]['changes'][0]) && array_key_exists("messages",$log['entry'][0]['changes'][0]['value']) && array_key_exists("button",$log['entry'][0]['changes'][0]['value']['messages'][0]) ): ?>

							 تم <b> ( <?php echo e($log['entry'][0]['changes'][0]['value']['messages'][0]['button']['text']); ?> ) </b>
                        
                        <?php else: ?>
							<?php echo e($value->log); ?>

                        <?php endif; ?>
                      </td>
                    
                      <?php

                          $error_title = $value->error_title;
                          $error_details = $value->error_details;
                    
                    	  if(is_array($log) && 
                    		array_key_exists("entry",$log) && array_key_exists("changes",$log['entry'][0]) && array_key_exists("value",$log['entry'][0]['changes'][0]) && array_key_exists("statuses",$log['entry'][0]['changes'][0]['value']) && array_key_exists("errors",$log['entry'][0]['changes'][0]['value']['statuses'][0]) && 
                            array_key_exists("title", $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) && array_key_exists("error_data", $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) ) {

                              $error_title = $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['title'];
                              $error_details = $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['error_data']['details'];

                          }
                      ?>
                    
                    
                      <td>
                        <b style="color:red"> <?php echo e($error_title != null ? $error_title . ' - ' : ''); ?>  </b> 
                        <span style="color:blue"> <?php echo e($error_details != null ? $error_details : ''); ?> </span>
                      </td>
                    
                      <td style="direction: ltr;"> <?php echo e(Carbon\Carbon::parse($value->created_at)->format('Y-m-d h:i A')); ?> </td>
         
                  </tr>

                  <?php $x = $x + 1; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--/ Zero configuration table -->



<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/events/event_user_history.blade.php ENDPATH**/ ?>