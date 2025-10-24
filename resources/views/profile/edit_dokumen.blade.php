<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Dokumen Siswa - SPMB</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link
    href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
    rel="stylesheet"
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

    {{-- Card Utama --}}
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-red-50 to-red-100 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 11V7m0 8h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
        </svg>
        <h1 class="text-2xl font-bold text-gray-800">Edit Dokumen Penting</h1>
      </div>

      {{-- Form --}}
      <form method="POST" action="{{ route('profile.update.dokumen') }}" enctype="multipart/form-data" class="px-6 space-y-8">
        @csrf

        {{-- Pesan Error --}}
        @if ($errors->any())
          <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4m0 4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z" />
            </svg>
            <p><strong class="font-semibold">Gagal menyimpan!</strong> Periksa kolom yang ditandai merah.</p>
          </div>
        @endif

        {{-- FORM SURAT PERNYATAAN --}}
        @include('account.timeline_form.surat_pernyataan', ['siswa' => $siswa])

        {{-- FORM SURAT KETERANGAN LULUS & IJAZAH --}}
        @include('account.timeline_form.surat_keterangan_lulus', ['siswa' => $siswa])

        {{-- Tombol Simpan --}}
        <div class="flex justify-end pt-4 border-t border-gray-200">
          <button
            type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700 active:scale-95 transition-transform duration-150"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Perubahan Dokumen
          </button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
