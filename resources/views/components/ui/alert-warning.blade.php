<div class="max-w-5xl mx-auto px-4 md:px-8">
    <div class="flex justify-between p-4 rounded-md bg-red-50 border border-red-300">
        <div class="flex gap-3">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="self-center">
                @isset($title)
                    <span class="text-red-600 font-medium">
                        {{ $title }}
                    </span>
                @endisset
                <div class="text-red-600">
                    <p class="mt-2 sm:text-sm">
                        {{ $slot }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
