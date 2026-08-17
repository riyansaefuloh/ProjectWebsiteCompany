<?php

namespace Tests\Feature;

use App\Livewire\Public\DownloadIndex;
use App\Models\Download;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Gerbang unduhan pada halaman Downloads — PRD Bab 7.9 "Lead Capture Gate".
 *
 * MEMAKAI DatabaseTransactions, BUKAN RefreshDatabase.
 * ────────────────────────────────────────────────────
 * Proyek ini belum punya .env.testing, dan DB_CONNECTION di phpunit.xml masih
 * dikomentari — artinya test berjalan di atas basis data PENGEMBANGAN yang
 * sama. RefreshDatabase akan menjalankan migrate:fresh di sana dan menghapus
 * seluruh data yang sudah di-seed. DatabaseTransactions hanya membungkus tiap
 * test dalam transaksi lalu mengembalikannya, jadi tidak ada yang hilang.
 */
class DownloadLeadCaptureTest extends TestCase
{
    use DatabaseTransactions;

    private function gatedFile(): Download
    {
        return Download::where('require_email', true)->orderBy('sort_order')->firstOrFail();
    }

    #[Test]
    public function ketukan_pertama_membuka_kolom_email_dan_belum_mencatat_lead(): void
    {
        $file = $this->gatedFile();
        $before = Inquiry::count();

        Livewire::test(DownloadIndex::class)
            ->call('download', $file->id)
            ->assertSet('selectedDownloadId', $file->id)
            ->assertHasErrors('email');

        $this->assertSame($before, Inquiry::count(), 'Lead tidak boleh tercatat sebelum email diisi.');
    }

    #[Test]
    public function email_yang_tidak_sah_ditolak_dan_belum_mencatat_lead(): void
    {
        $file = $this->gatedFile();
        $before = Inquiry::count();

        Livewire::test(DownloadIndex::class)
            ->set('email', 'bukan-email')
            ->call('download', $file->id)
            ->assertHasErrors(['email' => 'email']);

        $this->assertSame($before, Inquiry::count(), 'Alamat tak sah tidak boleh jadi lead.');
    }

    #[Test]
    public function email_sah_mencatat_lead_dan_menaikkan_penghitung(): void
    {
        $file = $this->gatedFile();
        $countBefore = $file->download_count;

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

        $this->assertSame($countBefore + 1, $file->fresh()->download_count);
    }

    #[Test]
    public function unduhan_berulang_tidak_menghasilkan_lead_ganda_tapi_penghitung_tetap_naik(): void
    {
        $file = $this->gatedFile();
        $countBefore = $file->download_count;

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
            $countBefore + 3,
            $file->fresh()->download_count,
            'Statistik unduhan tetap menghitung setiap unduhan.'
        );
    }

    #[Test]
    public function berkas_tanpa_gerbang_email_tidak_mencatat_lead(): void
    {
        $free = Download::where('require_email', false)->orderBy('sort_order')->firstOrFail();
        $before = Inquiry::count();

        Livewire::test(DownloadIndex::class)
            ->call('download', $free->id)
            ->assertHasNoErrors();

        $this->assertSame($before, Inquiry::count(), 'Berkas bebas email tidak boleh membuat lead.');
    }
}
