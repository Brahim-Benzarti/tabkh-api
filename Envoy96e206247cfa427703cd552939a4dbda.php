<?php $rawOntology = isset($rawOntology) ? $rawOntology : null; ?>
<?php $PATH = isset($PATH) ? $PATH : null; ?>
<?php $inferredOntology = isset($inferredOntology) ? $inferredOntology : null; ?>
<?php $now = isset($now) ? $now : null; ?>
<?php $__container->servers(['localhost' => '127.0.0.1']); ?>

<?php
    $now = new DateTime;
    $inferredOntology="InferredOntology".date(DateTimeInterface::W3C,$now->getTimestamp() );
?>

<?php $_vars = get_defined_vars(); $__container->before(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
}); ?>

<?php $__container->startTask('reasoning', ['on'=>"localhost"]); ?>
    pwd
    whoami
    echo <?php echo $now->getTimestamp(); ?>

    robot reason -h true -i <?php echo $rawOntology; ?> -r HermiT -o <?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>
 
<?php $__container->startTask('test', ['on' => 'localhost']); ?>
    ping -c 4 google.com
<?php $__container->endTask(); ?>