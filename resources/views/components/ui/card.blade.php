@props(['h2' => null, 'h3' => null, '$description' => null, 'href' => null, 'image' => null ])



<div {{ $attributes->merge(['']) }}>
    <div class="w-full mx-auto group sm:max-w-sm">
        <a href="{{ $href }}">
            @isset($image)
                <img src="{{$image}}" loading="lazy" class="w-full hover:scale-105" />
            @endisset

            <div class="mt-3 space-y-2">

            </div>
        </a>
    </div>

    <div class="space-y-5 ">
        @isset($h2)
            <h2>{{ $h2 }}</h2>
        @endisset
        @isset($h3)
            <h3>{{ $h3 }}</h3>
        @endisset
        @isset($description)
            <p class="">{{ $description }}</p>
        @endisset
    </div>


</div>
