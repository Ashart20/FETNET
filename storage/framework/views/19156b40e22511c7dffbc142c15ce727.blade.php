<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
@props(['class','xBind:class'])
<x-tallstack-ui::icon.heroicons.solid.x-mark :class="$class" :x-bind:class="$xBindClass" >

{{ $slot ?? "" }}
</x-tallstack-ui::icon.heroicons.solid.x-mark>