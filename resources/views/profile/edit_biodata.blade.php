<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Biodata Siswa - SPMB</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link
    href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
    rel="stylesheet"
  />

  {{-- Leaflet CSS --}}
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  />
</head>

<body class="bg-gradient-to-b from-gray-100 to-gray-200 min-h-screen flex flex-col">
  <div class="max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    {{-- Tombol Kembali --}}
    <div class="mb-6">
      <a
        href="{{ route('profile.settings') }}"
        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 font-semibold text-sm shadow hover:bg-gray-50 hover:text-gray-900 transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          ></path>
        </svg>
        <span>Kembali ke Pengaturan</span>
      </a>
    </div>

    {{-- Card Form --}}
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Edit Biodata & Lokasi
        </h1>
      </div>

      <form method="POST" action="{{ route('profile.update.biodata') }}" enctype="multipart/form-data" class="px-6 space-y-8">
        @csrf
        @method('POST')

        {{-- Konten Form Biodata --}}
        <div class="space-y-6">
          @include('account.timeline_form.biodata', ['siswa' => $siswa, 'ortu' => $ortu, 'user' => $user])
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex justify-end pt-4 border-t border-gray-200">
          <button
            type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 active:scale-95 transition-transform duration-150"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Leaflet JS --}}
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  {{-- Script Map --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const defaultLat = parseFloat(document.getElementById('latitude')?.value) || -7.3690;
      const defaultLng = parseFloat(document.getElementById('longitude')?.value) || 109.3496;

      const map = L.map('mapid', { scrollWheelZoom: true }).setView([defaultLat, defaultLng], 13);

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(map);

      const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

      function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
      }

      marker.on('dragend', function (e) {
        const coords = e.target.getLatLng();
        updateCoordinates(coords.lat, coords.lng);
      });

      map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
      });

      updateCoordinates(defaultLat, defaultLng);
      setTimeout(() => map.invalidateSize(), 300);
    });
  </script>
</body>
</html>
