@servers(['localhost' => '127.0.0.1'])

@setup
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
@endsetup

@story('handleRecipeCreation')
    updateRawOntology
    reason
    selectInferredData
@endstory

@task('updateRawOntology', ['on'=>'localhost'])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot query --input ontology/dbara.owl \
        --update public/insert_query.rq \
        --output ontology/dbara_updated.owl
@endtask

@task('reason', ['on'=>"localhost"])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot reason -i ontology/dbara_updated.owl -r HermiT -n true -o ontology/{{$inferredOntology}}
@endtask

@task('selectInferredData', ['on'=>'localhost'])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot query --input ontology/{{$inferredOntology}} \
        --query public/select_query.rq public/inferred_categories.csv
@endtask

@task('registerJava', ['on'=>'localhost'])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
@endtask
 
@task('test', ['on' => 'localhost'])
    ping -c 4 google.com
@endtask