<?php $rawOntology = isset($rawOntology) ? $rawOntology : null; ?>
<?php $PATH = isset($PATH) ? $PATH : null; ?>
<?php $inferredOntology = isset($inferredOntology) ? $inferredOntology : null; ?>
<?php $now = isset($now) ? $now : null; ?>
<?php $__container->servers(['localhost' => '127.0.0.1']); ?>

<?php
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
?>

<?php $__container->startTask('updateRawOntology' ['on'=>'localhost']); ?>
    
<?php $__container->endTask(); ?>

<?php $__container->startTask('reasoning', ['on'=>"localhost"]); ?>
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot reason -i ontology/<?php echo $rawOntology; ?> -r HermiT -n true -o ontology/<?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>
 
<?php $__container->startTask('test', ['on' => 'localhost']); ?>
    ping -c 4 google.com
<?php $__container->endTask(); ?>