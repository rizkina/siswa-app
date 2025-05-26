<div>
    <div class="container mx-auto">
        <div class="bg-white p-6 rounded-lg mt-3 shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Import Data Siswa</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Form Upload -->
                <div class="col-span-1">
                    <div class="bg-gray-100 p-6 rounded-lg border border-gray-300">
                        <p class="mb-2 text-lg font-semibold">Pilih file Excel yang ingin diimport:</p>

                        <form wire:submit.prevent="import_excel" enctype="multipart/form-data">
                            @csrf
                            <div id="drop-area" class="w-full p-6 border-2 border-dashed border-gray-400 rounded-lg bg-white text-center cursor-pointer hover:border-blue-500 transition">
                                <p class="text-gray-700">Seret & Letakkan file di sini atau</p>
                                <label for="file" class="text-blue-600 font-semibold cursor-pointer underline">Browse</label>
                                <input type="file" wire:model="file" id="file" class="hidden" wire:change="$refresh" accept=".xlsx, .xls, .csv">
                                <p id="file-name" class="text-gray-700">
                                    @if ($file)
                                        File terpilih: {{ $file->getClientOriginalName() }}
                                    @endif
                                </p>
                            </div>

                            @error('file')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg shadow-md transition duration-300 flex items-center justify-center gap-2 mt-4" wire:loading.attr="disabled" wire:target="import_excel">
                                <span class="w-5 h-5">@svg('heroicon-c-document-arrow-up')</span>
                                <span wire:loading.remove wire:target="import_excel">Import Data</span>
                                <span wire:loading wire:target="import_excel">⏳ Mengimpor...</span>
                            </button>
                        </form>
                        <div class="mt-2 text-xs text-gray-500">
                            <strong>Catatan:</strong> Pastikan file Excel menggunakan template yang disediakan dan header sesuai urutan serta penamaan.
                        </div>
                    </div>
                </div>

                <!-- Keterangan & Template -->
                <div class="col-span-1">
                    <div class="bg-gray-100 p-6 rounded-lg border border-gray-300">
                        <h3 class="text-lg font-semibold mb-2">Keterangan & Template</h3>
                        <ul class="list-disc pl-5 text-gray-700">
                            <li>Pastikan format file dalam bentuk <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
                            <li>Kolom yang diperlukan: <strong>NISN, Nama, Kelas, NIK</strong>, dan lainnya sesuai kebutuhan.</li>
                            <li>Hindari penggunaan karakter khusus pada kolom Nama.</li>
                        </ul>
                        <a href="{{ asset('templates/siswa_template.xlsx') }}"
                            class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
                            📥 Download Template Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hasil Import -->
            @php
                $summary = $importSummary ?: session('importSummary');
                // $gagalData = $gagalImport ?: session('gagalImport');
            @endphp
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if (!empty($summary))
                <div class="mt-6 p-4 bg-gray-100 border rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Hasil Import</h3>
                    <p class="text-gray-700">Total Baris: <strong>{{ $summary['total'] ?? 0 }}</strong></p>
                    <p class="text-green-700">Berhasil: <strong>{{ $summary['sukses'] ?? 0 }}</strong></p>
                    <p class="text-red-700">Gagal: <strong>{{ $summary['gagal'] ?? 0 }}</strong></p>
                </div>

                @if (!empty($summary['gagal']) && !empty($summary['gagalData']))
                    <div class="mt-6 p-4 bg-gray-100 border rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Data Gagal Diimport</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 border">Baris ke</th>
                                        <th class="py-2 px-4 border">NISN</th>
                                        <th class="py-2 px-4 border">Nama</th>
                                        <th class="py-2 px-4 border">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary['gagalData'] as $siswa)
                                        <tr class="border">
                                            <td class="py-2 px-4 border">{{ $siswa['baris'] }}</td>
                                            <td class="py-2 px-4 border">{{ $siswa['nisn'] }}</td>
                                            <td class="py-2 px-4 border">{{ $siswa['nama'] }}</td>
                                            <td class="py-2 px-4 border text-red-600">{{ $siswa['error'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Tombol Kembali -->
            <div class="mt-6 p-4 bg-gray-100 border rounded-lg">
                <a href="{{ url('admin/siswas') }}">
                    <button class="inline-block bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
                        Kembali
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>
