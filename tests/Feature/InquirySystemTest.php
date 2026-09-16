<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Inquiry;
use App\Livewire\Public\InquiryForm;
use Livewire\Livewire;
use App\Models\Permission;
use App\Models\Role;
use PHPUnit\Framework\Attributes\Test;

class InquirySystemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_user_can_submit_inquiry_successfully()
    {
        Livewire::test(InquiryForm::class)
            ->set('name', 'John Doe')
            ->set('company', 'Global Import Co.')
            ->set('email', 'john@globalimport.com')
            ->set('country_code', 'US')
            ->set('volume', '2 x 40ft container')
            ->set('message', 'We would like to request a quote for green coffee beans.')
            // Token dikirim seperti yang dilakukan formulirnya. Sewaktu
            // RECAPTCHA_SECRET_KEY kosong, sisi peladen melewati verifikasinya.
            ->call('submit', 'token-uji')
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('inquiries', [
            'email'        => 'john@globalimport.com',
            'company'      => 'Global Import Co.',
            'country_code' => 'US',
            'status'       => 'new',
        ]);
    }

    #[Test]
    public function unauthorized_user_cannot_export_inquiries()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/inquiries/export');
        $response->assertStatus(403);
    }

        #[Test]
    public function authorized_sales_user_can_export_inquiries()
    {
        $permission = Permission::firstOrCreate(['name' => 'export inquiries']);
        $role = Role::firstOrCreate(['name' => 'Sales']);
        $role->givePermissionTo($permission);

        $salesUser = User::factory()->create();
        $salesUser->assignRole($role);

        $response = $this->actingAs($salesUser)->get('/admin/inquiries/export');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

}
