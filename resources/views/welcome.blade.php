<x-guest-layout>
    <div class="flex justify-center items-center" style="height:100vh;width:100vw;">
        <img src="{{asset('svg/logo.svg')}}" alt="Logo" style="width:250px;height:250px;">
    </div>
        <style>
            .mytext{
                margin-top: 15vh;
                font-size: 90px;
                font-family:'Berlin Sans FB';
                font-weight: 500;
                /* font-weight: bold; */
                background:linear-gradient(rgba(50,117,227) 80%, white 100%);
                -webkit-background-clip:text;
                color: transparent;
            }
            .secondary{
                font-size: 40px;
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script>
            $(()=>{
                // $('nav').addClass('bg-transparent text-light').removeClass('bg-white');
                let start=1;
                let end=6;
                let path="https://"+window.location.host;
                $(".main").css({
                    "transition": "background-image 2s",
                    "background-image":"url("+path+"/images/bgs/"+start+".jpeg)",
                    "background-size":"cover",
                    "background-repeat":"no-repeat"
                });
                start++;
                setInterval(() => {
                    if(start==end+1){start=0};
                    $(".main").css({
                        "transition": "background-image 2s",
                        "background-image":"url("+path+"/images/bgs/"+start+".jpeg)",
                        "background-size":"cover",
                        "background-repeat":"no-repeat"
                    });
                    start++;
                }, 3000);
            })
        </script>
</x-guest-layout>