<div class="flex flex-col flex-1">
    <span class="text-3xl font-extrabold hover:underline cursor-pointer">
        {{ $getRecord()->title }}
    </span>
    <span class="text-2xl text-blue-400 font-bold hover:underline cursor-pointer">
        https://{{ config('ziplink.short_url_domain') }}/{{ $getRecord()->custom_url ? $getRecord()->custom_url : $getRecord()->short_url }}
    </span>
    <span class="flex items-center gap-1 hover:underline cursor-pointer">
        <x-filament::icon icon="heroicon-o-link" class="h-6 w-6 mr-2" />
        {{ $getRecord()->original_url }}
    </span>
    <span class="flex items-end text-gray-400 font-extralight text-sm flex-1 mt-3">
        {{ $getRecord()->created_at->format('d/m/Y H:i:s')}}
    </span>
</div>