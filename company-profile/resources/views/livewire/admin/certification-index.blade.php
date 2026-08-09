<div style="font-family: sans-serif; padding: 20px;">
    <h2>Manage Certifications & Legalities</h2>

    <!-- Alert Warning Sertifikat Mendekati Kedaluwarsa (PRD Bab 8.4 & 15) -->
    @if($expiringCertifications->count() > 0)
        <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            ⚠️ <strong>EXPIRATION WARNING:</strong> There are {{ $expiringCertifications->count() }} certificate(s) expiring within 30 days!
            <ul>
                @foreach($expiringCertifications as $expCert)
                    <li>
                        <strong>{{ $expCert->translated_name }}</strong> (Issuer: {{ $expCert->issuer }}) — Expires on: <u>{{ $expCert->expires_at->format('d M Y') }}</u>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Bar & Search -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search by name or issuer..." style="padding: 8px; width: 300px;">
        <button wire:click="create" style="padding: 8px 16px; background-color: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Add New Certificate
        </button>
    </div>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <!-- Table Certifications -->
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>Name (EN)</th>
                <th>Logo/PDF</th>
                <th>Issuer</th>
                <th>Cert No</th>
                <th>Issued At</th>
                <th>Expires At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($certifications as $cert)
                <tr>
                    <td><strong>{{ $cert->translated_name }}</strong></td>
                    <td>
                        @if($cert->getFirstMediaUrl('logos'))
                            <img src="{{ $cert->getFirstMediaUrl('logos') }}" style="height: 30px; object-fit: contain; margin-bottom: 4px; border: 1px solid #ccc; border-radius: 2px;"><br>
                        @endif
                        @if($cert->getFirstMediaUrl('pdfs'))
                            <a href="{{ $cert->getFirstMediaUrl('pdfs') }}" target="_blank" style="font-size: 11px; background: #e11d48; color: white; padding: 2px 4px; border-radius: 2px; text-decoration: none;">View PDF</a>
                        @endif
                    </td>
                    <td>{{ $cert->issuer }}</td>
                    <td>{{ $cert->certificate_number ?? '-' }}</td>
                    <td>{{ $cert->issued_at ? $cert->issued_at->format('d M Y') : '-' }}</td>
                    <td>
                        @if($cert->expires_at && $cert->expires_at->isPast())
                            <span style="color: red; font-weight: bold;">EXPIRED ({{ $cert->expires_at->format('d M Y') }})</span>
                        @elseif($cert->expires_at)
                            {{ $cert->expires_at->format('d M Y') }}
                        @else
                            No Expiry
                        @endif
                    </td>
                    <td>
                        <button wire:click="edit('{{ $cert->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $cert->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No certifications found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $certifications->links() }}
    </div>

    <!-- Modal Form (Inline Simpel) -->
    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; overflow-y: auto; padding: 20px;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 500px; max-height: 90vh; overflow-y: auto;">
                <h3>{{ $editingId ? 'Edit Certificate' : 'Add New Certificate' }}</h3>
                <form wire:submit.prevent="save">
                    <div style="margin-bottom: 10px;">
                        <label>Certificate Name (EN) *</label>
                        <input type="text" wire:model="name_en" style="width: 100%; padding: 6px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Nama Sertifikat (ID) *</label>
                        <input type="text" wire:model="name_id" style="width: 100%; padding: 6px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Issuer / Lembaga Penerbit *</label>
                        <input type="text" wire:model="issuer" style="width: 100%; padding: 6px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Certificate Number</label>
                        <input type="text" wire:model="certificate_number" style="width: 100%; padding: 6px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Issued Date</label>
                        <input type="date" wire:model="issued_at" style="width: 100%; padding: 6px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Expires Date</label>
                        <input type="date" wire:model="expires_at" style="width: 100%; padding: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 15px; border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px;">
                        <label><strong>Certification Logo (Auto WebP)</strong></label>
                        @if($editingId && $existingLogoUrl)
                            <div style="margin-top: 5px; margin-bottom: 10px;">
                                <img src="{{ $existingLogoUrl }}" style="height: 60px; object-fit: contain; border: 1px solid #ccc;"><br>
                                <button type="button" wire:click="deleteLogo" wire:confirm="Delete this logo?" style="font-size: 10px; background: #ef4444; color: white; border: none; padding: 4px; cursor: pointer; border-radius: 2px;">Delete Logo</button>
                            </div>
                        @endif
                        <input type="file" wire:model="logoFile" accept="image/*" style="width: 100%; padding: 6px; margin-top: 5px;">
                        <div wire:loading wire:target="logoFile" style="font-size: 12px; color: #2563eb;">Uploading...</div>
                        @error('logoFile') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-bottom: 15px; border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px;">
                        <label><strong>Official PDF Document</strong></label>
                        @if($editingId && $existingPdfUrl)
                            <div style="margin-top: 5px; margin-bottom: 10px;">
                                <a href="{{ $existingPdfUrl }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-size: 12px;">📄 View Current PDF</a><br>
                                <button type="button" wire:click="deletePdf" wire:confirm="Delete this PDF?" style="font-size: 10px; background: #ef4444; color: white; border: none; padding: 4px; margin-top: 5px; cursor: pointer; border-radius: 2px;">Delete PDF</button>
                            </div>
                        @endif
                        <input type="file" wire:model="pdfFile" accept="application/pdf" style="width: 100%; padding: 6px; margin-top: 5px;">
                        <div wire:loading wire:target="pdfFile" style="font-size: 12px; color: #2563eb;">Uploading...</div>
                        @error('pdfFile') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Certificate</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
