@php
    /**
     * @var \Illuminate\Database\Eloquent\Collection $rooms
     * @var \Illuminate\Database\Eloquent\Collection $days
     * @var \Illuminate\Database\Eloquent\Collection $timeSlots
     * @var array $constraints
     * @var int|null $selectedRoomId
     * @var int|null $highlightedDayId
     * @var int|null $highlightedTimeSlotId
     */
@endphp

<div>
    <x-mary-toast />
    <x-mary-card>
        <x-slot:title>
            <h1 class="text-2xl font-semibold">Manajemen Batasan Waktu Ruangan</h1>
        </x-slot:title>

        <p class="mt-2 text-base-content/70">Pilih ruangan, lalu klik pada slot waktu untuk menandainya sebagai 'tidak tersedia' (merah).</p>

        <div class="my-4">
            <x-mary-select
                label="Pilih Ruangan"
                wire:model.live="selectedRoomId"
                :options="$rooms"
                option-value="id"
                option-label="nama_ruangan"
                placeholder="-- Pilih Ruangan --"
                icon="o-building-office-2" />
        </div>

        {{-- Panel Aksi untuk Kolom HARI yang Disorot --}}
        @if($highlightedDayId && $selectedRoomId)
            <div class="p-3 mb-4 rounded-lg bg-blue-500/10 flex items-center justify-between gap-4">
                <span class="text-sm font-semibold text-blue-500">
                    Kolom hari '{{ $days->find($highlightedDayId)->name }}' sedang disorot.
                </span>
                <div class="flex gap-2">
                    <x-mary-button label="Tandai Semua Tidak Tersedia" wire:click="setHighlightedDayUnavailable" class="btn-sm btn-warning" spinner />
                    <x-mary-button label="Kosongkan Batasan" wire:click="setHighlightedDayAvailable" class="btn-sm btn-ghost" spinner />
                    <x-mary-button wire:click="resetHighlight" icon="o-x-mark" class="btn-sm btn-ghost" />
                </div>
            </div>
        @endif

        {{-- Panel Aksi untuk Baris WAKTU yang Disorot --}}
        @if($highlightedTimeSlotId && $selectedRoomId)
            <div class="p-3 mb-4 rounded-lg bg-blue-500/10 flex items-center justify-between gap-4">
                <span class="text-sm font-semibold text-blue-500">
                    @php
                        $slot = $timeSlots->find($highlightedTimeSlotId);
                        $waktu = date('H:i', strtotime($slot->start_time)) . ' - ' . date('H:i', strtotime($slot->end_time));
                    @endphp
                    Baris waktu '{{ $waktu }}' sedang disorot.
                </span>
                <div class="flex gap-2">
                    <x-mary-button label="Tandai Semua Tidak Tersedia" wire:click="setHighlightedTimeSlotUnavailable" class="btn-sm btn-warning" spinner />
                    <x-mary-button label="Kosongkan Batasan" wire:click="setHighlightedTimeSlotAvailable" class="btn-sm btn-ghost" spinner />
                    <x-mary-button wire:click="resetHighlight" icon="o-x-mark" class="btn-sm btn-ghost" />
                </div>
            </div>
        @endif

        <div wire:loading wire:target="selectedRoomId" class="w-full text-center text-base-content/50 my-4">
            <p>Memuat data batasan...</p>
        </div>

        @if($selectedRoomId)
            <div class="overflow-x-auto" wire:loading.remove wire:target="selectedRoomId">
                <table class="min-w-full border-collapse table-fixed">
                    <thead>
                    <tr>
                        <th class="p-2 border border-base-300 bg-base-200 w-32">Waktu</th>
                        @foreach($days as $day)
                            <th wire:click="highlightDay({{ $day->id }})"
                                class="p-2 border border-base-300 bg-base-200 cursor-pointer hover:bg-base-300
                                {{ $highlightedDayId == $day->id ? '!bg-blue-500/30' : '' }}">
                                {{ $day->name }}
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($timeSlots as $slot)
                        <tr class="text-center">
                            <td wire:click="highlightTimeSlot({{ $slot->id }})"
                                class="p-2 border border-base-300 bg-base-100 text-xs cursor-pointer hover:bg-base-300
                                {{ $highlightedTimeSlotId == $slot->id ? '!bg-blue-500/30' : '' }}">
                                {{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }}
                            </td>
                            @foreach($days as $day)
                                @php
                                    $isConstrained = isset($constraints[$day->id . '-' . $slot->id]);
                                    $isHighlighted = ($highlightedDayId == $day->id || $highlightedTimeSlotId == $slot->id);
                                @endphp

                                <td wire:click="toggleConstraint({{ $day->id }}, {{ $slot->id }})"
                                    class="p-2 border border-base-300 cursor-pointer transition-colors
                                    @if($isHighlighted)
                                        {{ $isConstrained ? 'bg-red-500/30' : 'bg-blue-500/30' }}
                                    @else
                                        {{ $isConstrained ? 'bg-red-500/20' : 'bg-green-500/10' }}
                                    @endif
                                    hover:opacity-75">

                                    @if($isConstrained)
                                        <span class="font-bold text-lg text-red-500">X</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-4 p-4 border-2 border-dashed border-base-300 rounded-lg text-center">
                <p class="text-base-content/70">Pilih sebuah ruangan untuk melihat dan mengatur batasan waktunya.</p>
            </div>
        @endif
    </x-mary-card>
</div>
