<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('home-component')->html();
} elseif ($_instance->childHasBeenRendered('Y4owE8j')) {
    $componentId = $_instance->getRenderedChildComponentId('Y4owE8j');
    $componentTag = $_instance->getRenderedChildComponentTagName('Y4owE8j');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('Y4owE8j');
} else {
    $response = \Livewire\Livewire::mount('home-component');
    $html = $response->html();
    $_instance->logRenderedChild('Y4owE8j', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/pages/home.blade.php ENDPATH**/ ?>