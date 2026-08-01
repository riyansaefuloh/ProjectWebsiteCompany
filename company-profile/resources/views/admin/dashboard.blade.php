<div style="font-family: sans-serif; padding: 30px; max-width: 800px; margin: 0 auto;">
    <h2>Welcome to CMS Admin Dashboard</h2>
    <p>Logged in as: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->roles->pluck('name')->implode(', ') }})</p>
    <hr style="margin: 20px 0;">
    
    <h3>Your Accessible Admin Menus:</h3>
    <ul style="line-height: 2.2; font-size: 16px;">
        @can('manage products')
            <li><a href="{{ route('admin.products.index') }}">📦 Export Products Management</a></li>
            <li><a href="{{ route('admin.categories.index') }}">🏷️ Product Categories Management</a></li>
        @endcan

        @can('manage certifications')
            <li><a href="{{ route('admin.certifications.index') }}">📜 Certifications & Expiry Alert</a></li>
        @endcan

        @can('manage export markets')
            <li><a href="{{ route('admin.export-markets.index') }}">🌍 Export Target Countries</a></li>
        @endcan

        @can('manage news')
            <li><a href="{{ route('admin.news.index') }}">📰 News & Articles Management</a></li>
        @endcan

        @can('view inquiries')
            <li><a href="{{ route('admin.inquiries.index') }}">📥 Buyer Inquiries (RFQ) Management</a></li>
        @endcan

        @can('manage downloads')
            <li><a href="{{ route('admin.downloads.index') }}">📂 PDF Brochures & Downloads Management</a></li>
        @endcan

        @can('manage users')
            <li><a href="{{ route('admin.users.index') }}">👥 Admin Users & Role Permissions</a></li>
        @endcan

        @can('manage global settings')
            <li><a href="{{ route('admin.settings.index') }}">⚙️ Global Website Settings</a></li>
        @endcan
    </ul>

    <br>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Logout</button>
    </form>
</div>
