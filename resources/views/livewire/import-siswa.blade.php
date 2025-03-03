<div>
    <div class="container mx-auto">
        <div class="bg-white p-6 rounded-lg mt-3 shadow-lg">
            <h2 class="text-2xl font-bold mb-2">Import Data Siswa</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Form Upload -->
                <div class="col-span-1">
                    <div class="bg-gray-100 p-6 rounded-lg border border-gray-300">
                        <p class="mb-2 text-lg font-semibold">Pilih file Excel yang ingin diimport:</p>

                        @if (session('success'))
                            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('import_siswa') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Drag & Drop Area -->
                            <div id="drop-area"
                                class="w-full p-6 border-2 border-dashed border-gray-400 rounded-lg bg-white text-center cursor-pointer hover:border-blue-500 transition">
                                <p class="text-gray-700">Seret & Letakkan file di sini atau</p>
                                <label for="file"
                                    class="text-blue-600 font-semibold cursor-pointer underline">Browse</label>
                                <input type="file" id="file" name="file" class="hidden">
                            </div>

                            <!-- Menampilkan Nama File -->
                            <p id="file-name" class="mt-2 text-gray-600 text-sm text-center"></p>

                            <button type="submit"
                                class="w-full bg-blue-400 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg shadow-md transition duration-300 flex items-center justify-center gap-2">
                                <span class="w-5 h-5">@svg('heroicon-c-document-arrow-up')</span>
                                Import Data
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Keterangan & Template -->
                <div class="col-span-1">
                    <div class="bg-gray-100 p-6 rounded-lg border border-gray-300">
                        <h3 class="text-lg font-semibold mb-2">Keterangan & Template</h3>
                        <ul class="list-disc pl-5 text-gray-700">
                            <li>Pastikan format file dalam bentuk <strong>.xlsx</strong> atau <strong>.xls</strong>.
                            </li>
                            <li>Kolom yang diperlukan: <strong>NISN, Nama, Kelas, NIK</strong>, dan lainnya sesuai
                                kebutuhan.</li>
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
            @if (session('import_result'))
                <div class="mt-6 p-4 bg-gray-100 border rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Hasil Import</h3>
                    <p class="text-gray-700">Total Baris: <strong>{{ session('import_result')['total'] }}</strong></p>
                    <p class="text-green-700">Berhasil: <strong>{{ session('import_result')['sukses'] }}</strong></p>
                    <p class="text-red-700">Gagal: <strong>{{ session('import_result')['gagal'] }}</strong></p>
                </div>
            @endif

            <!-- Hasil Upload -->
            @if (isset($data_siswa) && count($data_siswa) > 0)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Data Berhasil Diimport</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="py-2 px-4 border">No</th>
                                    <th class="py-2 px-4 border">NISN</th>
                                    <th class="py-2 px-4 border">NIPD</th>
                                    <th class="py-2 px-4 border">NIK</th>
                                    <th class="py-2 px-4 border">Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_siswa as $index => $siswa)
                                    <tr class="border">
                                        <td class="py-2 px-4 border">{{ $index + 1 }}</td>
                                        <td class="py-2 px-4 border">{{ $siswa->nisn }}</td>
                                        <td class="py-2 px-4 border">{{ $siswa->nipd }}</td>
                                        <td class="py-2 px-4 border">{{ $siswa->nik }}</td>
                                        <td class="py-2 px-4 border">{{ $siswa->nama }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
