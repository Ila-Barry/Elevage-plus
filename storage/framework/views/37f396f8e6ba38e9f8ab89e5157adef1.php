<?php $__env->startSection('title', 'Messages - Élevage+'); ?>

<?php $__env->startSection('content'); ?>

    <!-- style_css -->
    <link rel="stylesheet" href="<?php echo e(asset('css/eleveurCSS/messages.css')); ?>">

    <!-- contenue de la page messages -->
    <div class="row">
        <div class="col-md-4">
            <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Messages</h1>
            <p>Gérez vos messages ici !</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/messages.blade.php ENDPATH**/ ?>