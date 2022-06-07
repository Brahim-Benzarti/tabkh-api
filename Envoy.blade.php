@servers(['server' => ['127.0.0.1']])

@setup
    
@endsetup
 
@task('test', ['on' => 'server'])
    robot reason -i {{$source_file}} -r HermiT -o {{$destination_file}} -m true
@endtask