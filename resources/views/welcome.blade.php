<x-guest-layout>
    <div class="main flex justify-center items-center" style="height:100vh;width:100vw;">
        {{-- <img src="{{asset('svg/logo.svg')}}" alt="Logo" style="width:250px;height:250px;"> --}}
    </div>
    <style>
        .main{
            background-image: url("{{asset('images/bg.png')}}");
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</x-guest-layout>