{{-- Data Siswa --}}
<div class="border border-gray-400 p-3 md:p-6 rounded-lg">
    <div>
        Data <span class="font-bold">Calon Murid Baru</span>
    </div>

    <div class="space-y-10">
        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
            
            <div class="sm:col-span-3 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="name" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-3 relative px-3 pt-3 border border-gray-400 rounded-lg flex">
                @if (isset($siswa->foto) && $siswa->foto)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Profile" class="max-w-40 max-h-10 rounded-full shadow-md">
                    </div>
                @endif
                <div class="ps-2">
                    <label for="foto" class="absolute px-1 text-xs bg-white text-gray-700">Upload Foto</label>
                    <input type="file" name="foto" id="foto" 
                        @if (!isset($siswa->foto) || is_null($siswa->foto)) required @endif 
                        class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
                </div>
            </div>
            
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg bg-gray-300">
                <label for="nisn" class="absolute left-3 px-1 text-xs bg-gray text-gray-500">NISN</label>
                <input type="text" name="nisn" id="nisn" value="{{ Auth::user()->siswa->nisn }}" readonly
                class="bg-gray-300 block w-full px-1 mt-3 text-base text-gray-500 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="jenis_kelamin" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" value="{{ $siswa->jenis_kelamin ?? '' }}" required
                    class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-Laki" {{ (isset($siswa->jenis_kelamin) && $siswa->jenis_kelamin == 'Laki-Laki') ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="Perempuan" {{ (isset($siswa->jenis_kelamin) && $siswa->jenis_kelamin == 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="tanggal_lahir" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Tanggal Lahir</label>
                <input type="text" name="tanggal_lahir" id="tanggal_lahir" value="{{ Auth::user()->siswa->tanggal_lahir }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            

            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="kabupaten" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Kabupaten/Kota Asal</label>
                <input type="text" name="kabupaten" id="kabupaten" placeholder="Asal Kabupaten/Kota" value="{{ $siswa->kabupaten ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="kecamatan" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Kecamatan Asal</label>
                <input type="text" name="kecamatan" id="kecamatan" placeholder="Asal Kecamatan" value="{{ $siswa->kecamatan ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="desa" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Kelurahan/Desa Asal</label>
                <input type="text" name="desa" id="desa" placeholder="Asal Desa/Kelurahan" value="{{ $siswa->desa ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            
            <div class="sm:col-span-6 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="alamat" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Alamat Lengkap</label>
                <input type="text" name="alamat" id="alamat" placeholder="Alamat Rumah" value="{{ $siswa->alamat ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>

            <div class="sm:col-span-6">
                <div class="p-3 bg-gray-50 border border-gray-300 rounded-lg">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tentukan Titik Lokasi Domisili Rumah</label>
                    <p class="text-xs text-red-500 mb-2">Klik atau seret penanda pada peta untuk menentukan koordinat rumah Anda.</p>
                    
                    <div id="mapid" style="height: 300px;" class="rounded-lg border border-gray-400"></div>

                    <div class="mt-3 grid grid-cols-2 gap-4">
                        <div class="relative px-3 pt-3 border border-gray-400 rounded-lg">
                            <label for="latitude" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ $siswa->latitude_siswa ?? '-7.3690' }}" required
                            class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none" readonly>
                        </div>
                        <div class="relative px-3 pt-3 border border-gray-400 rounded-lg">
                            <label for="longitude" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ $siswa->longitude_siswa ?? '109.3496' }}" required
                            class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="no_kk" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nomor KK</label>
                <input type="text" name="no_kk" id="no_kk" placeholder="No KK" value="{{ $siswa->no_kk ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="nik" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nomor Induk Kependudukan</label>
                <input type="text" name="nik" id="nik" placeholder="NIK" value="{{ $siswa->nik ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="no_hp" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nomor Hp/Telephone</label>
                <input type="text" name="no_hp" id="no_hp" placeholder="No HP" value="{{ $siswa->no_hp ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>

            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="nama_ayah" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nama Lengkap Ayah</label>
                <input type="text" name="nama_ayah" id="nama_ayah" placeholder="Nama Ayah" value="{{ $siswa->nama_ayah ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="nama_ibu" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nama Lengkap Ibu</label>
                <input type="text" name="nama_ibu" id="nama_ibu" placeholder="Nama Ibu" value="{{ $siswa->nama_ibu ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="email" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Email Pribadi</label>
                <input type="text" name="email" id="email" placeholder="E-mail" value="{{ Auth::user()->email }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>

            <div class="sm:col-span-3 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="agama" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Agama</label>
                <select name="agama" id="agama" value="{{ $siswa->agama ?? '' }}" required
                    class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
                        <option value="">Pilih Agama</option>
                        <option value="Islam" {{ (isset($siswa->agama) && $siswa->agama == 'Islam') ? 'selected' : '' }}>Islam</option>
                        <option value="Katolik" {{ (isset($siswa->agama) && $siswa->agama == 'Katolik') ? 'selected' : '' }}>Katolik</option>
                        <option value="Kristen Protestan" {{ (isset($siswa->agama) && $siswa->agama == 'Kristen Protestan') ? 'selected' : '' }}>Kristen Protestan</option>
                        <option value="Hindu" {{ (isset($siswa->agama) && $siswa->agama == 'Hindu') ? 'selected' : '' }}>Hindu</option>
                        <option value="Budha" {{ (isset($siswa->agama) && $siswa->agama == 'Budha') ? 'selected' : '' }}>Budha</option>
                        <option value="Konghucu" {{ (isset($siswa->agama) && $siswa->agama == 'Konghucu') ? 'selected' : '' }}>Konghucu</option>
                        
                </select>
            </div>
            <div class="sm:col-span-3 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="kebutuhan_k" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Kebutuhan Khusus</label>
                <input type="text" name="kebutuhan_k" id="kebutuhan_k" placeholder="(Kosongkan Jika Tidak Punya)" value="{{ $siswa->kebutuhan_k ?? '' }}"
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>

            <div class="sm:col-span-3 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="sekolah_asal_id" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Sekolah Asal</label>
                <select name="sekolah_asal_id" id="sekolah_asal_id" required
                    class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
                    <option value="">Pilih Asal Sekolah</option>
                    @foreach ($sekolahAsals as $sekolah)
                        <option value="{{ $sekolah->id }}" {{ (isset($siswa->sekolah_asal_id) && $siswa->sekolah_asal_id == $sekolah->id) ? 'selected' : '' }}>
                            {{ $sekolah->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="akta_file" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Akta Kelahiran</label>
                <input type="file" name="akta_file" id="akta_file" value="" 
                    @if (!isset($siswa->akta_file) || is_null($siswa->akta_file)) required @endif
                    class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
                <div class="bg-green-500 rounded-full w-5 absolute right-3 top-5">
                    @if (isset($siswa->akta_file) && $siswa->akta_file)
                        <svg class="check-akta" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="size-4">
                            <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Data Wali --}}
<div class="border border-gray-400 p-6 rounded-lg mt-6">
    <div>
    Data <span class="font-bold">Orang Tua / Wali Murid</span>
    </div>

    <div class="space-y-10">
        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
            
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="nama_wali" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Nama Orang Tua/Wali</label>
                <input type="text" name="nama_wali" id="nama_wali" placeholder="Nama Lengkap" value="{{ $siswa->ortu->nama_wali ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="tempat_lahir_wali" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Tempat Lahir Orang Tua/Wali</label>
                <input type="text" name="tempat_lahir_wali" id="tempat_lahir_wali" placeholder="Tempat Lahir" value="{{ $siswa->ortu->tempat_lahir_wali ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="tanggal_lahir_wali" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Tanggal Lahir Orang Tua/Wali</label>
                <input type="date" name="tanggal_lahir_wali" id="tanggal_lahir_wali" placeholder="Tanggal Lahir" value="{{ $siswa->ortu->tanggal_lahir_wali ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>

            <div class="sm:col-span-2 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="pekerjaan_wali" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Pekerjaan Orang Tua/Wali</label>
                <input type="text" name="pekerjaan_wali" id="pekerjaan_wali" placeholder="Pekerjaan" value="{{ $siswa->ortu->pekerjaan_wali ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>
            <div class="sm:col-span-4 relative px-3 pt-3 border border-gray-400 rounded-lg">
                <label for="alamat_wali" class="absolute left-3 px-1 text-xs bg-white text-gray-700">Alamat Orang Tua/Wali</label>
                <input type="text" name="alamat_wali" id="alamat_wali" placeholder="Alamat Lengkap" value="{{ $siswa->ortu->alamat_wali ?? '' }}" required
                class="block w-full px-1 mt-3 text-base text-gray-800 border-0 focus:ring-0 focus:outline-none">
            </div>

        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Nilai awal dari input (jika sudah ada data) atau default ke Purbalingga
            var defaultLat = parseFloat(document.getElementById('latitude').value) || -7.3690; 
            var defaultLng = parseFloat(document.getElementById('longitude').value) || 109.3496;

            // Inisialisasi Peta
            var map = L.map('mapid').setView([defaultLat, defaultLng], 13);

            // Tile Layer (Map Tampilan)
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Tambahkan Penanda (Marker)
            var marker = L.marker([defaultLat, defaultLng], {
                draggable: true // Membuat penanda bisa diseret
            }).addTo(map);

            // Fungsi Update Koordinat
            function updateCoordinates(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
            }

            // Event saat Penanda diseret
            marker.on('dragend', function (e) {
                var coords = e.target.getLatLng();
                updateCoordinates(coords.lat, coords.lng);
            });

            // Event saat Peta diklik (opsional, untuk penempatan penanda baru)
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });
            
            // Inisialisasi awal nilai input
            updateCoordinates(defaultLat, defaultLng);

            // 🌟 FIX UTAMA: Perintah ini memastikan peta menyesuaikan diri
            // setelah container div selesai di-render dan memiliki ukuran definitif.
            setTimeout(function () {
                map.invalidateSize();
            }, 300); // Penundaan 300ms memberi waktu lebih untuk rendering DOM.
        });
    </script>
@endpush