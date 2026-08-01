<div style="font-family: sans-serif; padding: 20px;">
    <h2>Received Inquiries (RFQ Management)</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <input type="text" wire:model.live="search" placeholder="Search buyer name, company, email, country..." style="padding: 8px; width: 300px;">
            <select wire:model.live="selectedStatus" style="padding: 8px;">
                <option value="">All Statuses</option>
                <option value="new">NEW</option>
                <option value="processing">PROCESSING</option>
                <option value="quoted">QUOTED</option>
                <option value="closed">CLOSED</option>
                <option value="rejected">REJECTED</option>
            </select>
        </div>
        
        <a href="{{ route('admin.inquiries.export') }}" target="_blank" style="padding: 8px 16px; background: #10b981; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
            📥 Export to CSV
        </a>
    </div>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>Date</th>
                <th>Buyer Name</th>
                <th>Company</th>
                <th>Country</th>
                <th>Product Inquired</th>
                <th>Status</th>
                <th>Assigned Sales</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inquiries as $inq)
                <tr>
                    <td>{{ $inq->created_at->format('d M Y H:i') }}</td>
                    <td><strong>{{ $inq->name }}</strong><br><small>{{ $inq->email }}</small></td>
                    <td>{{ $inq->company }}</td>
                    <td><code>{{ $inq->country_code }}</code></td>
                    <td>{{ $inq->product ? $inq->product->translated_name : 'General Inquiry' }}</td>
                    <td>
                        <span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;
                            background: {{ $inq->status === 'new' ? '#fee2e2' : ($inq->status === 'quoted' ? '#dbeafe' : '#f3f4f6') }};
                            color: {{ $inq->status === 'new' ? '#991b1b' : ($inq->status === 'quoted' ? '#1e40af' : '#374151') }};">
                            {{ strtoupper($inq->status) }}
                        </span>
                    </td>
                    <td>{{ $inq->assignedSales ? $inq->assignedSales->name : 'Unassigned' }}</td>
                    <td>
                        <button wire:click="viewDetails('{{ $inq->id }}')" style="padding: 4px 8px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Manage
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No inquiries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $inquiries->links() }}
    </div>

    <!-- Modal Manage Inquiry -->
    @if($showModal && $selectedInquiry)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 600px; max-height: 90vh; overflow-y: auto;">
                <h3>Inquiry Details & Follow-up</h3>
                
                <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                    <p><strong>Buyer Name:</strong> {{ $selectedInquiry->name }} ({{ $selectedInquiry->company }})</p>
                    <p><strong>Email:</strong> {{ $selectedInquiry->email }} | <strong>Phone/WA:</strong> {{ $selectedInquiry->phone ?? '-' }}</p>
                    <p><strong>Country:</strong> {{ $selectedInquiry->country_code }} | <strong>IP Address:</strong> {{ $selectedInquiry->ip_address }}</p>
                    <p><strong>Product:</strong> {{ $selectedInquiry->product ? $selectedInquiry->product->translated_name : 'General' }}</p>
                    <p><strong>Volume:</strong> {{ $selectedInquiry->volume ?? '-' }} | <strong>Incoterms:</strong> {{ $selectedInquiry->incoterms ?? '-' }}</p>
                    <hr>
                    <p><strong>Buyer Message:</strong></p>
                    <p><em>"{{ $selectedInquiry->message }}"</em></p>
                </div>

                <form wire:submit.prevent="updateStatus">
                    <div style="margin-bottom: 10px;">
                        <label><strong>Inquiry Status</strong></label>
                        <select wire:model="status" style="width: 100%; padding: 6px;">
                            <option value="new">NEW (Unprocessed)</option>
                            <option value="processing">PROCESSING (Contacted)</option>
                            <option value="quoted">QUOTED (Offer Sent)</option>
                            <option value="closed">CLOSED (Deal Won)</option>
                            <option value="rejected">REJECTED (Lost)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label><strong>Assign to Sales Staff</strong></label>
                        <select wire:model="assigned_to" style="width: 100%; padding: 6px;">
                            <option value="">Unassigned</option>
                            @foreach($salesUsers as $sUser)
                                <option value="{{ $sUser->id }}">{{ $sUser->name }} ({{ $sUser->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label><strong>Internal Sales Note</strong></label>
                        <textarea wire:model="internal_note" rows="3" placeholder="Notes for internal team only..." style="width: 100%; padding: 6px;"></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Close</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
