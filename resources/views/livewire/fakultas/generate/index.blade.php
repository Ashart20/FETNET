<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        {{-- Header Halaman --}}
        <x-mary-header title="Generate Jadwal Otomatis"
                       subtitle="Mulai proses pembuatan jadwal untuk semua prodi di bawah fakultas Anda." />

        <div class="mt-6">
            <x-mary-card title="Mulai Proses" icon="o-rocket-launch" shadow>
                <p class="text-sm text-gray-500 mb-4">
                    Sistem akan membuat jadwal untuk semua program studi yang ada.
                    Pastikan semua data di setiap prodi (dosen, matkul, aktivitas, dan batasan) sudah lengkap. Proses ini akan berjalan di latar belakang.
                </p>

                @if (session('status'))
                    <x-mary-alert :description="session('status')" icon="o-information-circle" class="alert-info mb-4" />
                @endif
                @if (session('error'))
                    <x-mary-alert :description="session('error')" icon="o-exclamation-triangle" class="alert-error mb-4" />
                @endif

                <x-slot:actions>
                    <form action="{{ route('fakultas.generate.store') }}" method="POST">
                        @csrf
                        <x-mary-button label="Mulai Generate untuk Semua Prodi" type="submit" class="btn-success" icon="o-bolt" spinner />
                    </form>
                </x-slot:actions>
            </x-mary-card>
        </div>
    </div>
</x-app-layout>
