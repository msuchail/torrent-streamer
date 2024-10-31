@props(['h2' => null, 'h3' => null, '$description' => null, 'href' => null, 'image' => null ])



<div {{ $attributes->merge(['']) }}>
    <div class="w-full group hover:scale-105">
        <div href="{{ $href }}">
            @isset($image)
                <img src="{{$image}}" loading="lazy" class="w-full aspect-video" />
            @endisset

        </div>
    </div>
    <div class="">
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
