<div id="_AZBlMwVTKQl7JvRX">
    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rssapp-item">
            <div class="rssapp-item-content">
                <h2 class="rssapp-item-title"><?php echo e($result->title); ?></h2>
                <p class="rssapp-item-description"><?php echo e($result->description); ?></p>
                <a class="rssapp-item-link" href="<?php echo e($result->link); ?>" target="_blank">Read more</a>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH /home/customer/www/staging.paraclete.ai/public_html/resources/views/user/video/search_results.blade.php ENDPATH**/ ?>