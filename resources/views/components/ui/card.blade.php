@props([
    'h2' => null,
    'h3' => null,
    'description' => null,
    'href' => null,
    'image' => null,
    'masced' => false
])


<div {{ $attributes->merge(['']) }}>
    <div class="w-full group hover:scale-105">
        <div href="{{ $href }}" class="relative">
            <div>
                {{--                <x-heroicon-c-eye class="absolute top-2 right-2 h-7 z-[100] {{ $masced ? 'hover:visible invisible' : 'visible hover:invisible' }}" />--}}
                {{--            <x-heroicon-o-eye-slash class="absolute top-2 right-2 h-7 z-50 {{ $masced ? 'hidden hover:visible' : 'hover:hidden' }}" />--}}
            </div>
            @isset($image)
                <img src="{{$image}}" loading="lazy" class="w-full aspect-video"/>
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
