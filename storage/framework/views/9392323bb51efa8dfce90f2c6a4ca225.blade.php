    <div wire:key="{{ $uuid }}">
        <div
            {{ $attributes->class([
                    "flex justify-start items-center gap-4 px-3",
                    "hover:bg-base-200" => !$noHover,
                    "cursor-pointer" => $link
                ])
            }}
        >

            @if($link && (data_get($item, $avatar) || !is_string($avatar)))
                <div>
                    <a href="{{ $link }}" wire:navigate>
            @endif

            <!-- AVATAR -->
            @if(data_get($item, $avatar))
                <div class="py-3">
                    <div class="avatar">
                        <div class="w-11 rounded-full">
                            <img src="{{ data_get($item, $avatar) }}" />
                        </div>
                    </div>
                </div>
            @endif

            @if(!is_string($avatar))
                <div {{ $avatar->attributes->class(["py-3"]) }}>
                    {{ $avatar }}
                </div>
            @endif


            @if($link && (data_get($item, $avatar) || !is_string($avatar)))
                    </a>
                </div>
            @endif

            <!-- CONTENT -->
            <div class="flex-1 overflow-hidden whitespace-nowrap text-ellipsis truncate w-0 mary-hideable">
                @if($link)
                    <a href="{{ $link }}" wire:navigate>
                @endif

                <div class="py-3">
                    <div @if(!is_string($value)) {{ $value->attributes->class(["font-semibold truncate"]) }} @else class="font-semibold truncate" @endif>
                        {{ is_string($value) ? data_get($item, $value) : $value }}
                    </div>

                    <div @if(!is_string($subValue))  {{ $subValue->attributes->class(["text-base-content/50 text-sm truncate"]) }} @else class="text-base-content/50 text-sm truncate" @endif>
                        {{ is_string($subValue) ? data_get($item, $subValue) : $subValue }}
                    </div>
                </div>

                @if($link)
                    </a>
                @endif
            </div>

            <!-- ACTION -->
            @if($actions)
                @if($link && !Str::of($actions)->contains([':click', '@click' , 'href']))
                    <a href="{{ $link }}" wire:navigate>
                @endif
                    <div {{ $actions->attributes->class(["py-3 flex items-center gap-3 mary-hideable"]) }}>
                            {{ $actions }}
                    </div>

                @if($link && !Str::of($actions)->contains([':click', '@click' , 'href']))
                    </a>
                @endif
            @endif
        </div>

        @if(!$noSeparator)
            <hr class="border-t-[length:var(--border)] border-base-content/10"/>
        @endif
    </div>