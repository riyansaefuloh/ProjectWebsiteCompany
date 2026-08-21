<?php

/*
|--------------------------------------------------------------------------
| Pesan Validasi — Bahasa Indonesia
|--------------------------------------------------------------------------
|
| Seluruh panel dan situs berbahasa Indonesia, tapi pesan galat validasi
| sebelumnya masih berbunyi "The company name field is required." — bahasa
| kerangka kerjanya, bukan bahasa pemakainya.
|
| Kunci yang tidak ada di berkas ini otomatis jatuh ke lang/en/validation.php
| karena fallback_locale bernilai 'en'. Karena itu daftarnya dilengkapi, bukan
| disaring: satu kunci yang terlewat berarti satu kalimat Inggris yang muncul
| tiba-tiba di tengah halaman Indonesia.
|
| Bagian 'attributes' di bawah menerjemahkan NAMA ISIANNYA. Tanpa itu pesannya
| berbunyi "Kolom company name wajib diisi" — nama kolom basis data, bukan
| nama yang tertulis di layar.
|
*/

return [

    'accepted'             => 'Kolom :attribute harus disetujui.',
    'accepted_if'          => 'Kolom :attribute harus disetujui bila :other bernilai :value.',
    'active_url'           => 'Kolom :attribute bukan URL yang sah.',
    'after'                => 'Kolom :attribute harus berisi tanggal setelah :date.',
    'after_or_equal'       => 'Kolom :attribute harus berisi tanggal setelah atau sama dengan :date.',
    'alpha'                => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'           => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'any_of'               => 'Kolom :attribute tidak sah.',
    'array'                => 'Kolom :attribute harus berupa larik.',
    'ascii'                => 'Kolom :attribute hanya boleh berisi karakter dan simbol alfanumerik satu bita.',

    'before'               => 'Kolom :attribute harus berisi tanggal sebelum :date.',
    'before_or_equal'      => 'Kolom :attribute harus berisi tanggal sebelum atau sama dengan :date.',

    'between' => [
        'array'   => 'Kolom :attribute harus berisi antara :min dan :max butir.',
        'file'    => 'Berkas di kolom :attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai antara :min dan :max.',
        'string'  => 'Kolom :attribute harus terdiri dari :min sampai :max karakter.',
    ],

    'boolean'              => 'Kolom :attribute harus bernilai benar atau salah.',
    'can'                  => 'Kolom :attribute berisi nilai yang tidak diizinkan.',
    'confirmed'            => 'Konfirmasi kolom :attribute tidak cocok.',
    'contains'             => 'Kolom :attribute tidak memuat nilai yang diwajibkan.',
    'current_password'     => 'Kata sandi yang dimasukkan salah.',
    'date'                 => 'Kolom :attribute bukan tanggal yang sah.',
    'date_equals'          => 'Kolom :attribute harus berisi tanggal yang sama dengan :date.',
    'date_format'          => 'Kolom :attribute tidak cocok dengan format :format.',
    'decimal'              => 'Kolom :attribute harus memiliki :decimal angka di belakang koma.',
    'declined'             => 'Kolom :attribute harus ditolak.',
    'declined_if'          => 'Kolom :attribute harus ditolak bila :other bernilai :value.',
    'different'            => 'Kolom :attribute dan :other harus berbeda.',
    'digits'               => 'Kolom :attribute harus terdiri dari :digits angka.',
    'digits_between'       => 'Kolom :attribute harus terdiri dari :min sampai :max angka.',
    'dimensions'           => 'Kolom :attribute berisi gambar dengan dimensi yang tidak sah.',
    'distinct'             => 'Kolom :attribute berisi nilai yang kembar.',
    'doesnt_contain'       => 'Kolom :attribute tidak boleh memuat satu pun dari nilai berikut.',
    'doesnt_end_with'      => 'Kolom :attribute tidak boleh diakhiri salah satu dari: :values.',
    'doesnt_start_with'    => 'Kolom :attribute tidak boleh diawali salah satu dari: :values.',
    'email'                => 'Kolom :attribute harus berisi alamat surel yang sah.',
    'encoding'             => 'Kolom :attribute harus memakai penyandian :encoding.',
    'ends_with'            => 'Kolom :attribute harus diakhiri salah satu dari: :values.',
    'enum'                 => 'Pilihan pada kolom :attribute tidak sah.',
    'exists'               => 'Pilihan pada kolom :attribute tidak sah.',
    'extensions'           => 'Kolom :attribute harus berupa berkas berekstensi: :values.',
    'file'                 => 'Kolom :attribute harus berupa berkas.',
    'filled'               => 'Kolom :attribute wajib diisi.',

    'gt' => [
        'array'   => 'Kolom :attribute harus berisi lebih dari :value butir.',
        'file'    => 'Berkas di kolom :attribute harus lebih besar dari :value kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai lebih dari :value.',
        'string'  => 'Kolom :attribute harus terdiri lebih dari :value karakter.',
    ],

    'gte' => [
        'array'   => 'Kolom :attribute harus berisi :value butir atau lebih.',
        'file'    => 'Berkas di kolom :attribute harus berukuran :value kilobita atau lebih.',
        'numeric' => 'Kolom :attribute harus bernilai :value atau lebih.',
        'string'  => 'Kolom :attribute harus terdiri dari :value karakter atau lebih.',
    ],

    'hex_color'            => 'Kolom :attribute harus berisi warna heksadesimal yang sah.',
    'image'                => 'Kolom :attribute harus berupa gambar.',
    'in'                   => 'Pilihan pada kolom :attribute tidak sah.',
    'in_array'             => 'Kolom :attribute tidak ada di dalam :other.',
    'in_array_keys'        => 'Kolom :attribute harus memuat sedikitnya satu dari kunci berikut: :values.',
    'integer'              => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip'                   => 'Kolom :attribute harus berisi alamat IP yang sah.',
    'ipv4'                 => 'Kolom :attribute harus berisi alamat IPv4 yang sah.',
    'ipv6'                 => 'Kolom :attribute harus berisi alamat IPv6 yang sah.',
    'json'                 => 'Kolom :attribute harus berisi teks JSON yang sah.',
    'list'                 => 'Kolom :attribute harus berupa daftar.',
    'lowercase'            => 'Kolom :attribute harus ditulis dengan huruf kecil.',

    'lt' => [
        'array'   => 'Kolom :attribute harus berisi kurang dari :value butir.',
        'file'    => 'Berkas di kolom :attribute harus lebih kecil dari :value kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai kurang dari :value.',
        'string'  => 'Kolom :attribute harus terdiri kurang dari :value karakter.',
    ],

    'lte' => [
        'array'   => 'Kolom :attribute tidak boleh berisi lebih dari :value butir.',
        'file'    => 'Berkas di kolom :attribute harus berukuran :value kilobita atau kurang.',
        'numeric' => 'Kolom :attribute harus bernilai :value atau kurang.',
        'string'  => 'Kolom :attribute harus terdiri dari :value karakter atau kurang.',
    ],

    'mac_address'          => 'Kolom :attribute harus berisi alamat MAC yang sah.',

    'max' => [
        'array'   => 'Kolom :attribute tidak boleh berisi lebih dari :max butir.',
        'file'    => 'Berkas di kolom :attribute tidak boleh lebih besar dari :max kilobita.',
        'numeric' => 'Kolom :attribute tidak boleh bernilai lebih dari :max.',
        'string'  => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],

    'max_digits'           => 'Kolom :attribute tidak boleh terdiri lebih dari :max angka.',
    'mimes'                => 'Kolom :attribute harus berupa berkas bertipe: :values.',
    'mimetypes'            => 'Kolom :attribute harus berupa berkas bertipe: :values.',

    'min' => [
        'array'   => 'Kolom :attribute harus berisi sedikitnya :min butir.',
        'file'    => 'Berkas di kolom :attribute harus berukuran sedikitnya :min kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai sedikitnya :min.',
        'string'  => 'Kolom :attribute harus terdiri sedikitnya :min karakter.',
    ],

    'min_digits'           => 'Kolom :attribute harus terdiri sedikitnya :min angka.',
    'missing'              => 'Kolom :attribute tidak boleh ada.',
    'missing_if'           => 'Kolom :attribute tidak boleh ada bila :other bernilai :value.',
    'missing_unless'       => 'Kolom :attribute tidak boleh ada kecuali :other bernilai :value.',
    'missing_with'         => 'Kolom :attribute tidak boleh ada bila :values ada.',
    'missing_with_all'     => 'Kolom :attribute tidak boleh ada bila seluruh :values ada.',
    'multiple_of'          => 'Kolom :attribute harus merupakan kelipatan dari :value.',
    'not_in'               => 'Pilihan pada kolom :attribute tidak sah.',
    'not_regex'            => 'Format kolom :attribute tidak sah.',
    'numeric'              => 'Kolom :attribute harus berupa angka.',

    'password' => [
        'letters'       => 'Kolom :attribute harus memuat sedikitnya satu huruf.',
        'mixed'         => 'Kolom :attribute harus memuat sedikitnya satu huruf besar dan satu huruf kecil.',
        'numbers'       => 'Kolom :attribute harus memuat sedikitnya satu angka.',
        'symbols'       => 'Kolom :attribute harus memuat sedikitnya satu simbol.',
        'uncompromised' => 'Kata sandi ini pernah bocor dalam kebocoran data. Pilih kata sandi lain.',
    ],

    'present'              => 'Kolom :attribute harus ada.',
    'present_if'           => 'Kolom :attribute harus ada bila :other bernilai :value.',
    'present_unless'       => 'Kolom :attribute harus ada kecuali :other bernilai :value.',
    'present_with'         => 'Kolom :attribute harus ada bila :values ada.',
    'present_with_all'     => 'Kolom :attribute harus ada bila seluruh :values ada.',
    'prohibited'           => 'Kolom :attribute tidak diizinkan.',
    'prohibited_if'        => 'Kolom :attribute tidak diizinkan bila :other bernilai :value.',
    'prohibited_if_accepted' => 'Kolom :attribute tidak diizinkan bila :other disetujui.',
    'prohibited_if_declined' => 'Kolom :attribute tidak diizinkan bila :other ditolak.',
    'prohibited_unless'    => 'Kolom :attribute tidak diizinkan kecuali :other termasuk :values.',
    'prohibits'            => 'Kolom :attribute membuat :other tidak diizinkan ada.',
    'regex'                => 'Format kolom :attribute tidak sah.',
    'required'             => 'Kolom :attribute wajib diisi.',
    'required_array_keys'  => 'Kolom :attribute harus memuat kunci: :values.',
    'required_if'          => 'Kolom :attribute wajib diisi bila :other bernilai :value.',
    'required_if_accepted' => 'Kolom :attribute wajib diisi bila :other disetujui.',
    'required_if_declined' => 'Kolom :attribute wajib diisi bila :other ditolak.',
    'required_unless'      => 'Kolom :attribute wajib diisi kecuali :other termasuk :values.',
    'required_with'        => 'Kolom :attribute wajib diisi bila :values ada.',
    'required_with_all'    => 'Kolom :attribute wajib diisi bila seluruh :values ada.',
    'required_without'     => 'Kolom :attribute wajib diisi bila :values tidak ada.',
    'required_without_all' => 'Kolom :attribute wajib diisi bila seluruh :values tidak ada.',
    'same'                 => 'Kolom :attribute dan :other harus sama.',

    'size' => [
        'array'   => 'Kolom :attribute harus berisi tepat :size butir.',
        'file'    => 'Berkas di kolom :attribute harus berukuran :size kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'string'  => 'Kolom :attribute harus terdiri dari :size karakter.',
    ],

    'starts_with'          => 'Kolom :attribute harus diawali salah satu dari: :values.',
    'string'               => 'Kolom :attribute harus berupa teks.',
    'timezone'             => 'Kolom :attribute harus berisi zona waktu yang sah.',
    'ulid'                 => 'Kolom :attribute harus berisi ULID yang sah.',
    'unique'               => ':attribute itu sudah dipakai.',
    'uploaded'             => 'Kolom :attribute gagal diunggah.',
    'uppercase'            => 'Kolom :attribute harus ditulis dengan huruf besar.',
    'url'                  => 'Kolom :attribute harus berisi URL yang sah.',
    'uuid'                 => 'Kolom :attribute harus berisi UUID yang sah.',

    /*
    |--------------------------------------------------------------------------
    | Pesan Khusus per Isian
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'pdfFile' => [
            'required' => 'Berkas PDF wajib dipilih untuk unduhan baru.',
            'mimes'    => 'Berkasnya harus PDF.',
        ],
        'logo' => [
            'image' => 'Logo harus berupa berkas gambar.',
        ],
        'favicon' => [
            'image' => 'Favicon harus berupa berkas gambar.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama Isian
    |--------------------------------------------------------------------------
    |
    | Menggantikan nama kolom basis data dengan nama yang tertulis di layar.
    | Tanpa ini pesannya berbunyi "Kolom company name wajib diisi".
    |
    */

    'attributes' => [
        // Pengaturan
        'company_name'        => 'nama perusahaan',
        'company_address'     => 'alamat',
        'company_phone'       => 'nomor telepon',
        'contact_email'       => 'email kontak',
        'whatsapp_number'     => 'nomor WhatsApp',
        'google_map_url'      => 'tautan sematan Google Maps',
        'google_analytics_id' => 'ID Google Analytics',
        'timezone'            => 'zona waktu',
        'facebook_url'        => 'Facebook',
        'instagram_url'       => 'Instagram',
        'linkedin_url'        => 'LinkedIn',
        'hours_weekday'       => 'jam Senin–Jumat',
        'hours_saturday'      => 'jam Sabtu',
        'hours_sunday'        => 'jam Minggu',
        'established_year'    => 'tahun berdiri',
        'logo'                => 'logo',
        'favicon'             => 'favicon',
        'hero_image'          => 'gambar hero',
        'about_image'         => 'gambar Tentang Kami',
        'cta_image'           => 'gambar ajakan',

        // Produk
        'name_en'             => 'nama (Inggris)',
        'name_id'             => 'nama (Indonesia)',
        'description_en'      => 'deskripsi (Inggris)',
        'description_id'      => 'deskripsi (Indonesia)',
        'slug'                => 'slug',
        'category_id'         => 'kategori',
        'hs_code'             => 'HS Code',
        'moq'                 => 'MOQ',
        'supply_capacity'     => 'kapasitas suplai',
        'packaging'           => 'kemasan',
        'origin'              => 'asal',
        'indicative_price'    => 'harga indikatif',
        'currency'            => 'mata uang',
        'incoterms'           => 'Incoterms',
        'is_featured'         => 'produk unggulan',
        'status'              => 'status',

        // Berita & halaman
        'title_en'            => 'judul (Inggris)',
        'title_id'            => 'judul (Indonesia)',
        'content_en'          => 'konten (Inggris)',
        'content_id'          => 'konten (Indonesia)',
        'excerpt_en'          => 'ringkasan (Inggris)',
        'excerpt_id'          => 'ringkasan (Indonesia)',
        'news_category_id'    => 'kategori berita',
        'selectedTags'        => 'tag',
        'published_at'        => 'tanggal terbit',

        // Sertifikasi
        'issuer'              => 'penerbit',
        'certificate_number'  => 'nomor sertifikat',
        'issued_at'           => 'tanggal terbit',
        'expires_at'          => 'masa berlaku',

        // Pasar ekspor
        'country_code'        => 'kode negara',
        'region'              => 'wilayah',
        'notes_en'            => 'catatan pasar (Inggris)',
        'notes_id'            => 'catatan pasar (Indonesia)',

        // Unduhan
        'pdfFile'             => 'berkas PDF',
        'require_email'       => 'gerbang email',

        // Pengguna
        'name'                => 'nama',
        'email'               => 'email',
        'password'            => 'kata sandi',
        'role'                => 'peran',

        // Inquiry
        'company'             => 'perusahaan',
        'message'             => 'pesan',
        'volume'              => 'perkiraan volume',
        'incoterm'            => 'Incoterms',
        'product_id'          => 'produk yang diminati',
    ],

];
