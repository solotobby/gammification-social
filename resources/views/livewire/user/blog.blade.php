<div>
    {{-- Be like water. --}}

    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('src/assets/media/photos/photo9@2x.jpg') }}');">
        <div class="bg-black-50">
            <div class="content content-top content-full text-center">
                <h1 class="fw-bold text-white mt-5 mb-2">
                    Check out our latest stories
                </h1>
                <h3 class="fw-normal text-white-75 mb-5">Follow our Progress and be Inspired!</h3>
            </div>
        </div>
    </div>
    <!-- END Hero -->


    
        <div class="row items-push mt-2">
            @foreach ($blogs as $blog)
                 <!-- Story -->
            <div class="col-lg-4">
                <a class="block block-rounded block-link-pop h-100 mb-0" href="{{url('blog/'.$blog->slug)}}" target="_blank">
                    <img class="img-fluid" src="{{ $blog->cover_image }}" alt="">
                    <div class="block-content">
                        <h4 class="mb-1">{{ $blog->title }}</h4>
                        <p class="fs-sm">
                            <span class="text-primary">Payhankey</span> on {{ \Carbon\Carbon::parse($blog->created_at)->format('F d, Y') }} 
                             {{-- · <em class="text-muted">9
                                min</em> --}}
                        </p>
                        <p>
                             {!!$blog->excerpt!!}
                        </p>
                    </div>
                </a>
            </div>
            <!-- END Story -->
            @endforeach
           

     
    </div>
</div>
