<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmaController;
use App\Http\Middleware\IsAdminSekolah;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AplicationController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProgressBarController;
use App\Http\Controllers\AdminSekolahController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/welcome', function () {
    return view('welcome');
});
Route::get('/', function () {
    return view('landing_page');
})->name('landing_page');

Route::get('/', [HomeController::class, 'index'])->name('landing_page');

// Rute untuk super admin
Route::get('/superadmin/login', [SuperAdminController::class, 'showLoginForm'])->name('superadmin.login.form');
Route::post('/superadmin/login', [SuperAdminController::class, 'login'])->name('superadmin.login');
Route::middleware(['is_super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
    Route::get('/data-sma', [SuperAdminController::class, 'dataSma'])->name('super_admin.data_sma');
    Route::get('/data-sma/tambah', [SuperAdminController::class, 'createSma'])->name('super_admin.sma.create');
    Route::get('/data-sma/{sma}/edit', [SuperAdminController::class, 'editSma'])->name('super_admin.sma.edit');
    Route::put('/data-sma/{sma}', [SuperAdminController::class, 'updateSma'])->name('super_admin.sma.update');
    Route::post('/data-sekolah', [SuperAdminController::class, 'storeSma'])->name('super_admin.sma.store');
    Route::delete('/data-sma/{sma}', [SuperAdminController::class, 'destroySma'])->name('super_admin.sma.destroy');

    Route::get('/data-admin-sekolah', [SuperAdminController::class, 'dataAdminSekolah'])->name('super_admin.data_admin_sekolah');
    Route::get('/data-admin-sekolah/tambah', [SuperAdminController::class, 'createAdminForm'])->name('super_admin.admin.create');
    Route::post('/data-admin-sekolah', [SuperAdminController::class, 'storeAdmin'])->name('super_admin.admin.store');
    Route::get('/data-admin-sekolah/{admin}/edit', [SuperAdminController::class, 'editAdminForm'])->name('super_admin.admin.edit');
    Route::put('/data-admin-sekolah/{admin}', [SuperAdminController::class, 'updateAdmin'])->name('super_admin.admin.update');
    Route::delete('/data-admin-sekolah/{admin}', [SuperAdminController::class, 'destroyAdmin'])->name('super_admin.admin.destroy');

    Route::get('/start-stop', [SuperAdminController::class, 'stopStart'])->name('super_admin.stop.index');
    Route::post('/ppdb/set-closing-time', [SuperAdminController::class, 'setSchedule'])->name('super_admin.ppdb.set_schedule');
    Route::post('/ppdb/hentikan-dan-tentukan', [SuperAdminController::class, 'stopSpmbAndDetermineAcceptance'])->name('super_admin.ppdb.stop');
    Route::get('/data-diterima', [SuperAdminController::class, 'showAcceptedStudents'])->name('super_admin.data_diterima');
    Route::post('/spmb/reset-status', [SuperAdminController::class, 'resetSpmbStatus'])->name('super_admin.spmb.reset');

    Route::post('/banner/add', [SuperAdminController::class, 'storeBanner'])->name('banner.add');
    Route::get('/banner/form', [SuperAdminController::class, 'addBanner'])->name('banner.form');
    Route::get('/banner', [SuperAdminController::class, 'indexBanner'])->name('banner.index');
    Route::delete('/banner/{banner}', [SuperAdminController::class, 'deleteBanner'])->name('banner.delete');

    Route::get('/batas-usia', [SuperAdminController::class, 'indexUsia'])->name('usia.siswa.index');
    Route::post('/batas-usia', [SuperAdminController::class, 'updateUsia'])->name('usia.siswa.update');

    Route::get('/informasi', [SuperAdminController::class, 'showInformasi'])->name('super_admin.informasi.index');
    Route::post('/informasi', [SuperAdminController::class, 'updateInformasi'])->name('super_admin.informasi.update');
});

// Grup rute untuk Admin Sekolah
Route::get('/admin/login', [AdminSekolahController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminSekolahController::class, 'login']);
Route::middleware([IsAdminSekolah::class])->group(function () {
    Route::get('/admin/dashboard', [AdminSekolahController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/verifikasi_sertifikat/{siswa}', [AdminSekolahController::class, 'verifikasiSertifikat'])->name('admin.verifikasi_sertifikat');
    Route::get('/admin/jalur-pendaftaran', [AdminSekolahController::class, 'showJalurIndex'])->name('admin.jalur_pendaftaran.index');

    Route::get('/admin/jalur-pendaftaran/{jalur_id}', [AdminSekolahController::class, 'showStudentsByJalur'])->name('admin.jalur_pendaftaran.show');
    Route::get('/siswa/{siswa}/detail', [AdminSekolahController::class, 'showSiswaDetail'])->name('siswa.detail');
    Route::post('/siswa/{siswa}/verifikasi-dokumen/{dokumen}', [AdminSekolahController::class, 'verifikasiDokumen'])->name('admin.siswa.verifikasi_dokumen');

    Route::post('/admin/verifikasi-afirmasi/{siswa}', [AdminSekolahController::class, 'verifikasiAfirmasi'])->name('admin.verifikasi_afirmasi');
    Route::get('/admin/peringkat-murid', [AdminSekolahController::class, 'indexPeringkatMurid'])->name('admin.show_peringkat_murid');
    Route::get('/admin/peringkat-murid/{jalur_id}', [AdminSekolahController::class, 'showPeringkatMurid'])->name('admin.peringkat_murid.show');
});

// Route::get('/dashboard/test', [SmaController::class, 'testField'])->name('test_field');
// Route::get('/dashboard/progress_bar', [ProgressBarController::class, 'index'])->name('progress_bar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('logout');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::get('/dashboard', [PendaftaranController::class, 'showTimeline'])->name('dashboard');
    Route::post('/simpan-data', [PendaftaranController::class, 'saveRegistration'])->name('save.registration');
    Route::get('/surat_pernyataan', [DocumentController::class, 'showSuratPernyataan'])->name('surat_pernyataan');
    Route::get('/download_surat', [DocumentController::class, 'downloadPdf'])->name('download.surat.pernyataan');
    
    Route::get('/pendaftaran_sma', [SmaController::class, 'index'])->name('pendaftaran_sma');
    Route::get('/pendaftaran_sma/jalur', [SmaController::class, 'showJalurPendaftaran'])->name('jalur_pendaftaran');
    Route::post('/pendaftaran_sma/simpan-jalur', [SmaController::class, 'saveJalurPendaftaran'])->name('pendaftaran.sma.saveJalur');
    Route::get('/pendaftaran_sma/timeline', [SmaController::class, 'showTimeline'])->name('pendaftaran.sma.timeline');

    Route::get('/pendaftaran_sma/resume', [SmaController::class, 'showResume'])->name('resume');
    Route::post('/pendaftaran_sma/save-step', [SmaController::class, 'savePendaftaran'])->name('pendaftaran.sma.save_step');
    Route::get('/pendaftaran/peringkat', [SmaController::class, 'showPeringkatSiswa'])->name('siswa.peringkat');
    Route::post('/pendaftaran/tarik-berkas', [SmaController::class, 'tarikBerkas'])->name('siswa.tarik_berkas');

    Route::get('/dashboard-siswa', [PendaftaranController::class, 'showSetelahDashboard'])->name('setelah.dashboard.show');

    Route::delete('/notifications/clear', [AplicationController::class, 'clearAll'])->name('notification.clear_all');
    Route::delete('/notifications/{notification}', [AplicationController::class, 'destroy'])->name('notification.destroy');
    Route::get('/notification', [AplicationController::class, 'index'])->name('notification.index');
    Route::post('/notifications/mark-read', [AplicationController::class, 'markAllAsRead'])->name('notification.mark_read'); 

    // Rute untuk menampilkan halaman profil utama siswa
    Route::get('/profile/settings', [ProfileController::class, 'showSettings'])->name('profile.settings');
    Route::get('/profile/edit/biodata', [ProfileController::class, 'editBiodata'])->name('profile.edit.biodata');
    Route::post('/profile/update/biodata', [ProfileController::class, 'updateBiodata'])->name('profile.update.biodata');
    Route::get('/profile/edit/nilai', [ProfileController::class, 'editNilai'])->name('profile.edit.nilai');
    Route::post('/profile/update/nilai', [ProfileController::class, 'updateNilai'])->name('profile.update.nilai');
    Route::get('/profile/edit/dokumen', [ProfileController::class, 'editDokumen'])->name('profile.edit.dokumen');
    Route::post('/profile/update/dokumen', [ProfileController::class, 'updateDokumen'])->name('profile.update.dokumen');
    Route::post('/profile/reset/biodata', [ProfileController::class, 'resetBiodata'])->name('profile.reset.biodata');
    Route::post('/profile/reset/nilai', [ProfileController::class, 'resetNilai'])->name('profile.reset.nilai');
    Route::post('/profile/reset/dokumen', [ProfileController::class, 'resetDokumen'])->name('profile.reset.dokumen');

    Route::get('/juknis', [PendaftaranController::class, 'juknisPendaftaran'])->name('juknis.index');
    Route::get('/alur-spmb', [PendaftaranController::class, 'alurSpmb'])->name('alur.index');
});

require __DIR__.'/auth.php';
