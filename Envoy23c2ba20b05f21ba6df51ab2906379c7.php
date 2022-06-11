<?php $PATH = isset($PATH) ? $PATH : null; ?>
<?php $inferredOntology = isset($inferredOntology) ? $inferredOntology : null; ?>
<?php $now = isset($now) ? $now : null; ?>
<?php $__container->servers(['localhost' => '127.0.0.1']); ?>

<?php
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
?>

<?php $_vars = get_defined_vars(); $__container->before(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
}); ?>

<?php $__container->startMacro('handleRecipeCreation'); ?>
    registerJava
    updateRawOntology
    reason
    selectInferredData
<?php $__container->endMacro(); ?>

<?php $__container->startTask('updateRawOntology', ['on'=>'localhost']); ?>
    robot query --input ontology/dbara.owl \
        --update public/insert_query.rq \
        --output ontology/dbara_updated.owl
<?php $__container->endTask(); ?>

<?php $__container->startTask('reason', ['on'=>"localhost"]); ?>
    robot reason -i ontology/dbara_updated.owl -r HermiT -n true -o ontology/<?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>

<?php $__container->startTask('selectInferredData', ['on'=>'localhost']); ?>
    robot query --input nucleus.owl \
        --query public/select_query.rq public/inferred_categories.txt
<?php $__container->endTask(); ?>

<?php $__container->startTask('registerJava', ['on'=>'localhost']); ?>
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
<?php $__container->endTask(); ?>
 
<?php $__container->startTask('test', ['on' => 'localhost']); ?>
    ping -c 4 google.com
<?php $__container->endTask(); ?>