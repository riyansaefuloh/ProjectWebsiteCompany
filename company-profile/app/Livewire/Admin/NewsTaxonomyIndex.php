<?php

namespace App\Livewire\Admin;

use App\Models\NewsCategory;
use App\Models\NewsTag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Kategori & tag berita.
 *
 * Kedua tabelnya sudah ada sejak awal beserta relasinya ke berita, tapi tidak
 * pernah punya halaman pengelola — jadi keduanya selalu kosong, dan pemilih tag
 * di modal berita tidak pernah menampilkan apa pun untuk dicentang.
 *
 * Digabung dalam SATU halaman, bukan dua menu terpisah. Isinya cuma nama dan
 * slug; dua menu penuh untuk sepasang daftar sependek itu hanya menambah
 * tempat yang harus dicari, sementara keduanya selalu diurus bersamaan.
 *
 * Tidak memakai modal seperti halaman admin lain — barisnya disunting di
 * tempat. Untuk satu isian nama, membuka jendela terpisah lebih banyak
 * langkahnya daripada mengetiknya langsung.
 */
class NewsTaxonomyIndex extends Component
{
    /* ── Isian tambah ────────────────────────────────────────────────── */
    public string $kategoriBaru = '';
    public string $tagBaru = '';

    /* ── Isian ubah di tempat ────────────────────────────────────────── */
    public ?string $sedangUbah = null;
    public string $namaUbah = '';

    protected function messages(): array
    {
        return [
            'kategoriBaru.required' => 'Nama kategori wajib diisi.',
            'kategoriBaru.unique'   => 'Kategori dengan nama itu sudah ada.',
            'tagBaru.required'      => 'Nama tag wajib diisi.',
            'tagBaru.unique'        => 'Tag dengan nama itu sudah ada.',
            'namaUbah.required'     => 'Namanya tidak boleh kosong.',
        ];
    }

    public function tambahKategori(): void
    {
        $this->validate([
            'kategoriBaru' => 'required|string|max:80|unique:news_categories,name',
        ]);

        NewsCategory::create([
            'name' => trim($this->kategoriBaru),
            'slug' => $this->slugUnik(NewsCategory::class, $this->kategoriBaru),
        ]);

        $this->kategoriBaru = '';
        session()->flash('message', 'Kategori berita ditambahkan.');
    }

    public function tambahTag(): void
    {
        $this->validate([
            'tagBaru' => 'required|string|max:80|unique:news_tags,name',
        ]);

        NewsTag::create([
            'name' => trim($this->tagBaru),
            'slug' => $this->slugUnik(NewsTag::class, $this->tagBaru),
        ]);

        $this->tagBaru = '';
        session()->flash('message', 'Tag berita ditambahkan.');
    }

    /**
     * Membuka satu baris untuk disunting.
     *
     * Kuncinya digabung dengan jenisnya ("kategori:01H…") supaya satu properti
     * bisa melayani kedua daftar tanpa dua baris yang kebetulan ber-ULID sama
     * saling membuka.
     */
    public function ubah(string $jenis, string $id): void
    {
        $this->resetValidation();

        $baris = $this->model($jenis)::findOrFail($id);

        $this->sedangUbah = $jenis . ':' . $id;
        $this->namaUbah   = $baris->name;
    }

    public function batalUbah(): void
    {
        $this->resetValidation();
        $this->sedangUbah = null;
        $this->namaUbah   = '';
    }

    public function simpanUbah(): void
    {
        if (! $this->sedangUbah) {
            return;
        }

        [$jenis, $id] = explode(':', $this->sedangUbah, 2);
        $kelas = $this->model($jenis);

        $this->validate([
            'namaUbah' => 'required|string|max:80|unique:'
                . (new $kelas)->getTable() . ',name,' . $id,
        ]);

        $baris = $kelas::findOrFail($id);

        /*
         * Slug TIDAK ikut berubah.
         *
         * Slug lama sudah dipakai di alamat halaman publik dan bisa saja sudah
         * tersebar. Mengganti nama tampilan tidak layak mematikan tautan yang
         * sudah dibagikan orang.
         */
        $baris->update(['name' => trim($this->namaUbah)]);

        $this->batalUbah();
        session()->flash('message', 'Nama diperbarui.');
    }

    /**
     * Menghapus satu kategori atau tag.
     *
     * Kategori yang masih dipakai berita DITOLAK, bukan dihapus paksa: kolom
     * news_category_id akan menggantung menunjuk baris yang sudah tidak ada.
     * Tag boleh dihapus kapan saja — kaitannya hidup di tabel pivot, jadi
     * melepasnya tidak meninggalkan apa pun yang rusak.
     */
    public function hapus(string $jenis, string $id): void
    {
        $baris = $this->model($jenis)::withCount('news')->findOrFail($id);

        if ($jenis === 'kategori' && $baris->news_count > 0) {
            session()->flash('galat', 'Kategori "' . $baris->name . '" masih dipakai '
                . $baris->news_count . ' berita. Pindahkan berita itu dulu.');

            return;
        }

        if ($jenis === 'tag') {
            $baris->news()->detach();
        }

        $baris->delete();

        if ($this->sedangUbah === $jenis . ':' . $id) {
            $this->batalUbah();
        }

        session()->flash('message', 'Dihapus.');
    }

    /**
     * Slug yang dijamin belum terpakai.
     *
     * Dua nama berbeda bisa menghasilkan slug yang sama ("Ekspor & Impor" dan
     * "Ekspor Impor" sama-sama jadi "ekspor-impor"), dan kolom slug itu unik —
     * tanpa penomoran di belakangnya, penyimpanan kedua akan gagal dengan
     * galat basis data mentah, bukan pesan yang bisa dibaca.
     */
    private function slugUnik(string $kelas, string $nama): string
    {
        $dasar = Str::slug($nama) ?: 'item';
        $slug  = $dasar;
        $n     = 2;

        while ($kelas::where('slug', $slug)->exists()) {
            $slug = $dasar . '-' . $n++;
        }

        return $slug;
    }

    private function model(string $jenis): string
    {
        return match ($jenis) {
            'kategori' => NewsCategory::class,
            'tag'      => NewsTag::class,
            default    => abort(404),
        };
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.news-taxonomy-index', [
            'kategori' => NewsCategory::withCount('news')->orderBy('name')->get(),
            'tag'      => NewsTag::withCount('news')->orderBy('name')->get(),
        ]);
    }
}
