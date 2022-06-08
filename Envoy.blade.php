@servers(['localhost' => '127.0.0.1'])

@setup
    $now = new DateTime;
    $inferredOntology="InferredOntology_".date(DateTimeInterface::W3C,$now->getTimestamp()).".owl";
@endsetup

@task('updateRawOntology' ['on'=>'localhost'])
    
@endtask

@task('reasoning', ['on'=>"localhost"])
    export PATH=$PATH:/usr/local/bin/jre1.8.0_333/bin
    robot reason -i ontology/{{$rawOntology}} -r HermiT -n true -o ontology/{{$inferredOntology}}
@endtask
 
@task('test', ['on' => 'localhost'])
    ping -c 4 google.com
@endtask