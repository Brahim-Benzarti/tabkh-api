@servers(['localhost' => '127.0.0.1'])

@setup
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
@endsetup

@story('handleRecipeCreation')
    registerJava
    generateInsertQuery
    updateRawOntology
    reason
@endstory

@task('generateInsertQuery', ['on'=>'localhost'])
    echo "PREFIX : <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> \
    PREFIX owl: <http://www.w3.org/2002/07/owl#> \
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> \
    PREFIX xml: <http://www.w3.org/XML/1998/namespace> \
    PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> \
    BASE <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> \
    INSERT DATA { \
        :{{$recipeName}} rdf:type owl:Class ; \
                rdfs:subClassOf :NamedRecipe , \
                                @for ($i = 0; $i < count(explode(",",$ingredientClasses)); $i++)
                                    [ rdf:type owl:Restriction ; \
                                        owl:onProperty :hasIngredient ; \
                                        owl:someValuesFrom :{{explode(",",$ingredientClasses)[$i]}} \
                                    ] , \
                                @if($i==(count(explode(",",$ingredientClasses))-1))
                                    [ rdf:type owl:Restriction ; \
                                        owl:onProperty :hasTopping ; \
                                        owl:allValuesFrom [ rdf:type owl:Class ; \
                                                            owl:unionOf ( \
                                                                            @foreach (explode(",",$ingredientClasses) as $ingredientClass)
                                                                                :{{$ingredientClass}} \
                                                                            @endforeach
                                                                        ) \
                                                          ] \
                                    ] . \
                                @endif
                                @endfor
    } \
    " > ontology/insert_query
@endtask

@task('updateRawOntology', ['on'=>'localhost'])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot query --input ontology/dbara.owl \
        --update ontology/insert_query \
        --output ontology/dbara_updated.owl
@endtask

@task('reason', ['on'=>"localhost"])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot reason -i ontology/dbara_updated.owl -r HermiT -n true -o ontology/{{$inferredOntology}}
@endtask

@task('selectInferredData', ['on'=>'localhost'])

@endtask

@task('registerJava', ['on'=>'localhost'])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
@endtask
 
@task('test', ['on' => 'localhost'])
    ping -c 4 google.com
@endtask