<?php $PATH = isset($PATH) ? $PATH : null; ?>
<?php $rawOntology = isset($rawOntology) ? $rawOntology : null; ?>
<?php $ingredientClasses = isset($ingredientClasses) ? $ingredientClasses : null; ?>
<?php $i = isset($i) ? $i : null; ?>
<?php $RecipeName = isset($RecipeName) ? $RecipeName : null; ?>
<?php $inferredOntology = isset($inferredOntology) ? $inferredOntology : null; ?>
<?php $now = isset($now) ? $now : null; ?>
<?php $__container->servers(['localhost' => '127.0.0.1']); ?>

<?php
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
?>

<?php $__container->startMacro('handleRecipeCreation'); ?>
    generateInsertQuery --RecipeName=FirstApi --ingredientClasses=['PepperIngredient']
<?php $__container->endMacro(); ?>

<?php $__container->startTask('generateInsertQuery', ['on'=>'localhost']); ?>
    echo "PREFIX : <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> \
    PREFIX owl: <http://www.w3.org/2002/07/owl#> \
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> \
    PREFIX xml: <http://www.w3.org/XML/1998/namespace> \
    PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> \
    BASE <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> \
    INSERT DATA { \
        :<?php echo $RecipeName; ?> rdf:type owl:Class ; \
                rdfs:subClassOf :NamedRecipe , \
                                <?php for ($i = 0; $i < count($ingredientClasses); $i++): ?>
                                    [ rdf:type owl:Restriction ; \
                                        owl:onProperty :hasIngredient ; \
                                        owl:someValuesFrom :<?php echo $ingredientClasses[$i]; ?> \
                                    ] <?php if($i=(count($ingredientClasses)-1)): ?> . <?php else: ?> , <?php endif; ?> \
                                <?php endfor; ?>
    } \
    " > ontology/insert_query
<?php $__container->endTask(); ?>

<?php $__container->startTask('updateRawOntology', ['on'=>'localhost']); ?>
    robot query --input ontology/<?php echo $rawOntology; ?> \
        --update ontology/insert_query \
        --output ontology/<?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>

<?php $__container->startTask('reasoning', ['on'=>"localhost"]); ?>
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot reason -i ontology/<?php echo $rawOntology; ?> -r HermiT -n true -o ontology/<?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>

<?php $__container->startTask('selectInferredData', ['on'=>'localhost']); ?>

<?php $__container->endTask(); ?>
 
<?php $__container->startTask('test', ['on' => 'localhost']); ?>
    ping -c 4 google.com
<?php $__container->endTask(); ?>