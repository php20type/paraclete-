<?php $__env->startComponent('mail::message'); ?>
# You have been invited to join <?php echo e(config('app.name')); ?> by <?php echo e(ucfirst(auth()->user()->name)); ?>


Join <?php echo e(config('app.name')); ?> to start generating your content by AI.

<!-- <a href="<?php echo e(config('app.url')); ?>/?ref=<?php echo e(auth()->user()->referral_id); ?>">Register Now</a> -->
<a href="<?php echo e(config('frontend.custom_url.link')); ?>/?ref=<?php echo e(auth()->user()->referral_id); ?>">Register Now</a>
Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH /home/customer/www/paraclete.ai/public_html/resources/views/emails/referral.blade.php ENDPATH**/ ?>