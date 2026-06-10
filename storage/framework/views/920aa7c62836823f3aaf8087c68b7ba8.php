<?php $__env->startSection('title', 'Profile - Élevage+'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/authCSS/profile.css')); ?>">


    <div class="row">
        <div class="col-md-4">
            
            <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>

        <!-- espace de travail -->
        <div class="col-md-8">
            <h1>Profile</h1>
            <p>Gérez votre profile ici !</p>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/auth/profile.blade.php ENDPATH**/ ?>