<?php $PATH = isset($PATH) ? $PATH : null; ?>
<?php $ingredientClass = isset($ingredientClass) ? $ingredientClass : null; ?>
<?php $recipeName = isset($recipeName) ? $recipeName : null; ?>
<?php $ingredientClasses = isset($ingredientClasses) ? $ingredientClasses : null; ?>
<?php $i = isset($i) ? $i : null; ?>
<?php $sup = isset($sup) ? $sup : null; ?>
<?php $inferredOntology = isset($inferredOntology) ? $inferredOntology : null; ?>
<?php $now = isset($now) ? $now : null; ?>
<?php $__container->servers(['localhost' => '127.0.0.1']); ?>

<?php
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
?>

<?php $__container->startMacro('handleRecipeCreation'); ?>
    registerJava
    generateInsertQuery
    updateRawOntology
    reason
<?php $__container->endMacro(); ?>

<?php $__container->startTask('generateInsertQuery', ['on'=>'localhost']); ?>
    <?php $_vars = get_defined_vars(); $__container->before(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; 
        $sup=""
        for($i = 0; $i < count(explode(",",$ingredientClasses)); $i++){
            $sup=$sup."[rdf:type owl:Restriction ; owl:onProperty :hasIngredient ; owl:someValuesFrom :".explode(",",$ingredientClasses)[$i]."] ,"
        }
    }); ?>
    echo <?php echo $sup; ?>> nice
    echo "PREFIX : <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> \
    PREFIX owl: <http://www.w3.org/2002/07/owl#> \
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> \
    PREFIX xml: <http://www.w3.org/XML/1998/namespace> \
    PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> \
    BASE <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> \
    INSERT DATA { \
        :<?php echo $recipeName; ?> rdf:type owl:Class ; \
                rdfs:subClassOf :NamedRecipe , \
                                <?php for ($i = 0; $i < count(explode(",",$ingredientClasses)); $i++): ?>
                                    [ rdf:type owl:Restriction ; \
                                        owl:onProperty :hasIngredient ; \
                                        owl:someValuesFrom :<?php echo explode(",",$ingredientClasses)[$i]; ?> \
                                    ] , \
                                <?php if($i==(count(explode(",",$ingredientClasses))-1)): ?>
                                    [ rdf:type owl:Restriction ; \
                                        owl:onProperty :hasTopping ; \
                                        owl:allValuesFrom [ rdf:type owl:Class ; \
                                                            owl:unionOf ( \
                                                                            <?php foreach (explode(",",$ingredientClasses) as $ingredientClass): ?>
                                                                                :<?php echo $ingredientClass; ?> \
                                                                            <?php endforeach; ?>
                                                                        ) \
                                                          ] \
                                    ] . \
                                <?php endif; ?>
                                <?php endfor; ?>
    } \
    " > ontology/insert_query
<?php $__container->endTask(); ?>

<?php $__container->startTask('updateRawOntology', ['on'=>'localhost']); ?>
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot query --input ontology/dbara.owl \
        --update ontology/insert_query \
        --output ontology/dbara_updated.owl
<?php $__container->endTask(); ?>

<?php $__container->startTask('reason', ['on'=>"localhost"]); ?>
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot reason -i ontology/dbara_updated.owl -r HermiT -n true -o ontology/<?php echo $inferredOntology; ?>

<?php $__container->endTask(); ?>

<?php $__container->startTask('selectInferredData', ['on'=>'localhost']); ?>

<?php $__container->endTask(); ?>

<?php $__container->startTask('registerJava', ['on'=>'localhost']); ?>
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
<?php $__container->endTask(); ?>
 
<?php $__container->startTask('test', ['on' => 'localhost']); ?>
    ping -c 4 google.com
<?php $__container->endTask(); ?>