@extends('admin_sekolah.layouts.admin_layout')

@section('content')
    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">Ringkasan Pendaftaran Calon Siswa</h3>
                </div>

                {{-- SUMMARY CARDS ROW 1: TOTAL PENDAFTAR PER JALUR --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    {{-- Total Siswa --}}
                    <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_siswa }}</p>
                        <p class="mt-2 text-sm">Total Pendaftar</p>
                    </div>

                    {{-- Total Prestasi (Asumsi ID 1) --}}
                    <div class="bg-green-500 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_prestasi }}</p>
                        <p class="mt-2 text-sm">Jalur Prestasi</p>
                    </div>
                    
                    {{-- Total Afirmasi (Asumsi ID 2) --}}
                    <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_afirmasi }}</p>
                        <p class="mt-2 text-sm">Jalur Afirmasi</p>
                    </div>
                    
                    {{-- Total Zonasi (Asumsi ID 3) --}}
                    <div class="bg-red-500 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_zonasi }}</p>
                        <p class="mt-2 text-sm">Jalur Zonasi</p>
                    </div>
                </div>

                <h4 class="text-lg font-semibold mb-4">Ringkasan Verifikasi Dokumen</h4>

                {{-- SUMMARY CARDS ROW 2: STATUS VERIFIKASI DITOLAK --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    
                    {{-- DITOLAK SERTIFIKAT/PRESTASI --}}
                    <div class="bg-gray-400 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_sertifikat }}</p>
                        <p class="mt-2 text-sm">Sertifikat/Berkas Prestasi Ditolak</p>
                    </div>

                    {{-- DITOLAK AFIRMASI --}}
                    <div class="bg-gray-700 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_afirmasi }}</p>
                        <p class="mt-2 text-sm">Dokumen Afirmasi Ditolak</p>
                    </div>

                    <div class="bg-indigo-500 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_pending_verifikasi }}</p>
                        <p class="mt-2 text-sm">Siswa Menunggu Verifikasi</p>
                    </div>
                    
                    {{-- Tambahkan card lain di sini jika diperlukan (misal: Total Siswa Menunggu Verifikasi) --}}
                </div>
                
                {{-- <h4 class="text-lg font-semibold mb-4">Ringkasan Dokumen Wajib</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <div class="bg-red-700 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_akta }}</p>
                        <p class="mt-2 text-sm">Berkas Akta Ditolak</p>
                    </div>

                    <div class="bg-red-700 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_rapor }}</p>
                        <p class="mt-2 text-sm">Berkas Rapor Ditolak</p>
                    </div>

                    <div class="bg-red-700 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_pernyataan }}</p>
                        <p class="mt-2 text-sm">Berkas Surat Pernyataan Ditolak</p>
                    </div>

                    <div class="bg-red-700 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_skl }}</p>
                        <p class="mt-2 text-sm">Berkas Surat Keterangan Lulus Ditolak</p>
                    </div>

                    <div class="bg-red-700 text-white p-6 rounded-lg shadow-md">
                        <p class="text-3xl font-bold">{{ $total_ditolak_ijazah }}</p>
                        <p class="mt-2 text-sm">Berkas Ijazah Ditolak</p>
                    </div>
                </div> --}}
            </div>
            
        </div>
    </div>
@endsection