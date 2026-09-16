<?php

namespace Tests\Feature;

use App\Livewire\Public\DownloadIndex;
use App\Models\Download;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Gerbang unduhan pada halaman Downloads — PRD Bab 7.9 "Lead Capture Gate".
 *
 * MEMBUAT DATANYA SENDIRI.
 * ────────────────────────
 * Versi sebelumnya memakai DatabaseTransactions dan mencari berkas hasil
 * seeder di basis data pengembangan. Itu masuk akal ketika uji memang berjalan
 * di atas basis data kerja — tapi phpunit.xml kini mengarah ke sqlite
 * :memory:, penjaga yang dipasang sesudah seluruh isi basis data kerja pernah
 * terhapus oleh migrate:fresh milik RefreshDatabase.
 *
 * Akibatnya kelima uji di berkas ini berhenti berjalan sama sekali: tabelnya
 * tidak ada, dan firstOrFail() gagal sebelum apa pun sempat diperiksa. Uji
 * yang selalu gagal sama tidak bergunanya dengan uji yang tidak ada.
 *
 * Sekarang tiap uji membuat berkas unduhannya sendiri, jadi ia tidak lagi
 * bergantung pada isi basis data mana pun.
 */
class DownloadLeadCaptureTest extends TestCase
{
    use RefreshDatabase;

    /*
     * Berkasnya benar-benar ditaruh di disk palsu.
     *
     * Komponennya memeriksa keberadaan berkas sebelum mencatat lead, jadi
     * baris basis data saja tidak cukup — tanpa berkasnya, yang teruji cuma
     * jalur "berkas hilang", bukan gerbang emailnya.
     */
    private function berkasBergerbang(): Download
    {
        Storage::fake('public');
        Storage::disk('public')->put('downloads/katalog-2026.pdf', '%PDF-1.4 uji');

        return Download::create([
            'title'          => 'Katalog Produk 2026',
            'file_path'      => 'downloads/katalog-2026.pdf',
            'require_email'  => true,
            'download_count' => 0,
            'sort_order'     => 1,
        ]);
    }

    private function berkasBebas(): Download
    {
        Storage::fake('public');
        Storage::disk('public')->put('downloads/profil.pdf', '%PDF-1.4 uji');

        return Download::create([
            'title'          => 'Profil Perusahaan',
            'file_path'      => 'downloads/profil.pdf',
            'require_email'  => false,
            'download_count' => 0,
            'sort_order'     => 2,
        ]);
    }

    #[Test]
    public function ketukan_pertama_membuka_kolom_email_dan_belum_mencatat_lead(): void
    {
        $file = $this->berkasBergerbang();

        Livewire::test(DownloadIndex::class)
            ->call('download', $file->id)
            ->assertSet('selectedDownloadId', $file->id)
            ->assertHasErrors('email');

        $this->assertSame(0, Inquiry::count(), 'Lead tidak boleh tercatat sebelum email diisi.');
    }

    #[Test]
    public function email_yang_tidak_sah_ditolak_dan_belum_mencatat_lead(): void
    {
        $file = $this->berkasBergerbang();

        Livewire::test(DownloadIndex::class)
            ->set('email', 'bukan-email')
            ->call('download', $file->id)
            ->assertHasErrors(['email' => 'email']);

        $this->assertSame(0, Inquiry::count(), 'Alamat tak sah tidak boleh jadi lead.');
    }

    #[Test]
    public function email_sah_mencatat_lead_dan_menaikkan_penghitung(): void
    {
        $file = $this->berkasBergerbang();

        Livewire::test(DownloadIndex::class)
            ->set('email', 'buyer@hamburg-gmbh.de')
            ->call('download', $file->id)
            ->assertHasNoErrors();

        $lead = Inquiry::where('email', 'buyer@hamburg-gmbh.de')->first();

        $this->assertNotNull($lead, 'Lead harus tercatat di tabel inquiries.');
        $this->assertSame('Download Lead Gate', $lead->company);
        $this->assertSame('ZZ', $lead->country_code);
        $this->assertSame('new', $lead->status);
        $this->assertStringContainsString($file->title, $lead->message);

        $this->assertSame(1, $file->fresh()->download_count);
    }

    #[Test]
    public function unduhan_berulang_tidak_menghasilkan_lead_ganda_tapi_penghitung_tetap_naik(): void
    {
        $file = $this->berkasBergerbang();

        foreach (range(1, 3) as $ignored) {
            Livewire::test(DownloadIndex::class)
                ->set('email', 'repeat@buyer.test')
                ->call('download', $file->id);
        }

        $this->assertSame(
            1,
            Inquiry::where('email', 'repeat@buyer.test')->count(),
            'Tiga unduhan berkas yang sama hanya boleh menghasilkan satu lead.'
        );

        $this->assertSame(
            3,
            $file->fresh()->download_count,
            'Statistik unduhan tetap menghitung setiap unduhan.'
        );
    }

    #[Test]
    public function berkas_tanpa_gerbang_email_tidak_mencatat_lead(): void
    {
        $free = $this->berkasBebas();

        Livewire::test(DownloadIndex::class)
            ->call('download', $free->id)
            ->assertHasNoErrors();

        $this->assertSame(0, Inquiry::count(), 'Berkas bebas email tidak boleh membuat lead.');
    }
}
