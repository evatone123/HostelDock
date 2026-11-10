<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use App\Models\User;
use App\Services\MTNMoMoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HostelGatewayManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $manager;

    protected $hostel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->hostel = Hostel::create([
            'name' => 'Gateway Test Hostel',
            'slug' => 'gateway-test-hostel',
            'institution' => 'Gateway Test University',
            'institution_name' => 'Gateway Test University, Accra',
            'location' => 'Accra',
            'distance' => 'Near campus',
            'rating' => 4.5,
            'description' => 'Hostel used by the gateway management test.',
            'starting_price' => 850,
            'is_active' => true,
        ]);

        $this->hostel->managers()->attach($this->manager);
    }

    protected function validPayload(): array
    {
        return [
            'is_active' => '1',
            'paystack_enabled' => '1',
            'paystack_public_key' => 'pk_test_hostel',
            'paystack_secret_key' => 'sk_test_hostel_secret',
            'momo_api_enabled' => '1',
            'momo_api_user' => 'api-user-01',
            'momo_api_key' => 'api-key-01',
            'momo_subscription_key' => 'sub-key-01',
            'momo_target_environment' => MTNMoMoService::TARGET_SANDBOX,
            'momo_provider' => 'MTN',
            'momo_number' => '0241234567',
            'momo_account_name' => 'Hostel MoMo Account',
            'bank_name' => 'GCB Bank',
            'bank_account_name' => 'Hostel Bank Account',
            'bank_account_number' => '1234567890',
            'instructions' => 'Please include the booking reference.',
        ];
    }

    public function test_admin_updates_hostel_payment_gateways()
    {
        $this->actingAs($this->admin)
            ->put(route('admin.hostels.payments.update', $this->hostel), $this->validPayload())
            ->assertRedirect(route('admin.hostels.payments.edit', $this->hostel))
            ->assertSessionHas('success');

        $detail = $this->hostel->fresh()->paymentDetail;

        $this->assertNotNull($detail);
        $this->assertTrue($detail->is_active);
        $this->assertTrue($detail->paystack_enabled);
        $this->assertTrue($detail->momo_api_enabled);
        $this->assertTrue($detail->hasPaystack());
        $this->assertTrue($detail->hasMtnMoMoApi());
        $this->assertSame('0241234567', $detail->momo_number);
        $this->assertSame('1234567890', $detail->bank_account_number);
    }

    public function test_secret_fields_are_encrypted_at_rest()
    {
        $this->actingAs($this->admin)
            ->put(route('admin.hostels.payments.update', $this->hostel), $this->validPayload());

        $raw = DB::table('hostel_payment_details')->where('hostel_id', $this->hostel->id)->first();

        $this->assertSame(
            'sk_test_hostel_secret',
            Crypt::decryptString($raw->paystack_secret_key)
        );
        $this->assertSame('api-key-01', Crypt::decryptString($raw->momo_api_key));
        $this->assertSame('sub-key-01', Crypt::decryptString($raw->momo_subscription_key));

        $this->assertNotSame('sk_test_hostel_secret', $raw->paystack_secret_key);
    }

    public function test_blank_secret_fields_preserve_existing_secrets()
    {
        $this->actingAs($this->admin)
            ->put(route('admin.hostels.payments.update', $this->hostel), $this->validPayload());

        $payload = $this->validPayload();
        $payload['paystack_secret_key'] = '';
        $payload['momo_api_key'] = '';
        $payload['momo_subscription_key'] = '';

        $this->actingAs($this->admin)
            ->put(route('admin.hostels.payments.update', $this->hostel), $payload)
            ->assertRedirect(route('admin.hostels.payments.edit', $this->hostel));

        $detail = $this->hostel->fresh()->paymentDetail;
        $this->assertTrue($detail->hasPaystack());
        $this->assertTrue($detail->hasMtnMoMoApi());
        $this->assertSame(
            'sk_test_hostel_secret',
            Crypt::decryptString(DB::table('hostel_payment_details')->where('hostel_id', $this->hostel->id)->value('paystack_secret_key'))
        );
    }

    public function test_manager_can_configure_gateways_for_managed_hostel()
    {
        $this->actingAs($this->manager)
            ->put(route('manager.hostels.payments.update', $this->hostel), $this->validPayload())
            ->assertRedirect(route('manager.hostels.payments.edit', $this->hostel));

        $detail = $this->hostel->fresh()->paymentDetail;
        $this->assertTrue($detail->hasPaystack());
        $this->assertTrue($detail->hasMtnMoMoApi());
    }

    public function test_manager_is_forbidden_for_unmanaged_hostel()
    {
        $other = Hostel::create([
            'name' => 'Other Hostel',
            'slug' => 'other-hostel',
            'institution' => 'Other University',
            'institution_name' => 'Other University, Accra',
            'location' => 'Kumasi',
            'distance' => '2 km',
            'rating' => 4.0,
            'description' => 'Hostel not managed by this manager.',
            'starting_price' => 700,
            'is_active' => true,
        ]);

        $this->actingAs($this->manager)
            ->put(route('manager.hostels.payments.update', $other), $this->validPayload())
            ->assertForbidden();
    }

    public function test_student_cannot_access_admin_gateway_configuration()
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($student)
            ->get(route('admin.hostels.payments.edit', $this->hostel))
            ->assertForbidden();
    }

    public function test_disabling_gateway_flags_removes_online_pay_options()
    {
        $this->actingAs($this->admin)
            ->put(route('admin.hostels.payments.update', $this->hostel), $this->validPayload());

        $payload = $this->validPayload();
        $payload['paystack_enabled'] = '0';
        $payload['momo_api_enabled'] = '0';

        $this->actingAs($this->admin)
            ->put(route('admin.hostels.payments.update', $this->hostel), $payload);

        $detail = $this->hostel->fresh()->paymentDetail;
        $this->assertFalse($detail->hasPaystack());
        $this->assertFalse($detail->hasMtnMoMoApi());
    }
}