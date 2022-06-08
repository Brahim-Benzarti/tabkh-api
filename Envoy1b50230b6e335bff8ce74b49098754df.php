<?php $rawOntology = isset($rawOntology) ? $rawOntology : null; ?>
<?php $inferredOntology = isset($inferredOntology) ? $inferredOntology : null; ?>
<?php $now = isset($now) ? $now : null; ?>
<?php $__container->servers(['localhost' => '127.0.0.1']); ?>

<?php
    $now = new DateTime;
    $inferredOntology="InferredOntology"+$now;
?>

<?php $__container->startTask('reasoning', ['on'=>"localhost"]); ?>
    pwd
    echo <?php echo $now; ?>

    robot reason -h true -i <?php echo $rawOntology; ?> -r HermiT -o <?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>
 
<?php $__container->startTask('test', ['on' => 'localhost']); ?>
    ping -c 4 google.com
<?php $__container->endTask(); ?>