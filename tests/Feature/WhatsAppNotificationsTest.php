<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use App\Services\PaystackService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected $student;

    protected $hostel;

    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.base_url' => 'http://openwa.test/api',
            'services.whatsapp.api_key' => 'test-key',
            'services.whatsapp.session_name' => 'main',
            'services.whatsapp.admin_alert_number' => '0240000001',
        ]);

        Http::fake([
            'openwa.test/*' => Http::response(['status' => true], 200),
        ]);

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'phone' => '0241000000']);

        $this->hostel = Hostel::create([
            'name' => 'WA Test Hostel',
            'slug' => 'wa-test-hostel',
            'institution' => 'WA Test University',
            'institution_name' => 'WA Test University, Accra',
            'location' => 'Accra',
            'distance' => 'Near campus',
            'rating' => 4.5,
            'description' => 'Hostel used by the whatsapp notification test.',
            'starting_price' => 800,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'hostel_id' => $this->hostel->id,
            'name' => 'Two in a room',
            'room_type' => 'Two in a room',
            'gender_category' => 'Unisex',
            'price' => 800,
            'total_rooms' => 6,
        ]);
    }

    protected function waEndpoint(): string
    {
        return 'http://openwa.test/api/sessions/main/messages/send-text';
    }

    protected function sentMessages(): array
    {
        Http::assertSent(function ($request) {
            return $request->url() === $this->waEndpoint()
                && $request->hasHeader('X-API-Key', 'test-key');
        });

        $messages = [];
        foreach (Http::recorded() as $pair) {
            $request = $pair[0];
            if ($request->url() === $this->waEndpoint()) {
                $messages[] = $request->data();
            }
        }

        return $messages;
    }

    protected function activeTokenForStudent(): BookingToken
    {
        return BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-WA-ACTIVE',
            'paid_at' => now(),
        ]);
    }

    protected function mockVerify(bool $success = true)
    {
        $this->mock(PaystackService::class, function ($mock) use ($success) {
            $mock->shouldReceive('verify')->once()->andReturn([
                'ok' => $success,
                'status' => $success ? 'success' : 'failed',
                'transaction_ref' => 'PSK-WA-TXN-0001',
                'paid_at' => now()->toIso8601String(),
                'amount' => 800.0,
                'message' => $success ? 'Transaction verified.' : 'Transaction failed.',
            ]);
        });
    }

    public function test_phone_normalization()
    {
        $service = app(WhatsAppService::class);

        $this->assertSame('233240000000', $service->normalizePhone('0240000000'));
        $this->assertSame('233240000000', $service->normalizePhone('233240000000'));
        $this->assertSame('233240000000', $service->normalizePhone('+233 240 000 000'));
        $this->assertSame('233240000000', $service->normalizePhone('(024) 000-0000'));
        $this->assertSame('441234567890', $service->normalizePhone('+44 1234 567890'));
        $this->assertNull($service->normalizePhone('12345'));
        $this->assertNull($service->normalizePhone('not-a-number'));
    }

    public function test_booking_creation_notifies_student_and_hostel_manager()
    {
        $this->activeTokenForStudent();

        $manager = User::factory()->create(['role' => User::ROLE_MANAGER, 'phone' => '0242555555']);
        $this->hostel->managers()->attach($manager);

        $this->actingAs($this->student)
            ->post(route('booking.store', $this->hostel), [
                'room' => $this->room->id,
                'student_name' => 'WA Student',
                'email' => 'wa@student.test',
                'phone' => '0240000000',
                'institution' => 'WA Test University',
                'gender' => 'Female',
                'academic_year' => '2026/2027',
            ])
            ->assertRedirect();

        $messages = $this->sentMessages();
        $this->assertCount(2, $messages);

        $studentMsg = collect($messages)->firstWhere('chatId', '233240000000@c.us');
        $this->assertNotNull($studentMsg);
        $this->assertStringContainsString('booking has been received', strtolower($studentMsg['text']));

        $managerMsg = collect($messages)->firstWhere('chatId', '233242555555@c.us');
        $this->assertNotNull($managerMsg);
        $this->assertStringContainsString('New booking received', $managerMsg['text']);
    }

    public function test_booking_creation_sends_nothing_when_gateway_disabled()
    {
        config(['services.whatsapp.enabled' => false]);
        $this->activeTokenForStudent();

        $this->actingAs($this->student)
            ->post(route('booking.store', $this->hostel), [
                'room' => $this->room->id,
                'student_name' => 'WA Student',
                'email' => 'wa@student.test',
                'phone' => '0240000000',
                'institution' => 'WA Test University',
            ])
            ->assertRedirect();

        Http::assertNothingSent();
    }

    public function test_token_callback_notifies_user_when_token_activated()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-WA-TOKEN-1',
        ]);

        $this->mockVerify();

        $this->get(route('token.callback', ['reference' => 'PSK-WA-TOKEN-1']))
            ->assertRedirect(route('token.success'));

        $messages = $this->sentMessages();
        $this->assertCount(1, $messages);
        $this->assertSame('233241000000@c.us', $messages[0]['chatId']);
        $this->assertStringContainsString('booking access token', strtolower($messages[0]['text']));
        $this->assertStringContainsString($token->token_ref, $messages[0]['text']);
    }

    public function test_payment_callback_notifies_student_and_manager_on_success()
    {
        $booking = $this->createBooking();
        $booking->paystack_reference = 'PSKB-WA-1';
        $booking->save();

        $manager = User::factory()->create(['role' => User::ROLE_MANAGER, 'phone' => '0242555555']);
        $this->hostel->managers()->attach($manager);

        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')->once()->with(
                \Mockery::on(fn ($ref) => $ref === 'PSKB-WA-1'),
                null
            )->andReturn([
                'ok' => true,
                'status' => 'success',
                'transaction_ref' => 'PSK-WA-TXN-0001',
                'paid_at' => now()->toIso8601String(),
                'amount' => 800.0,
                'message' => 'Transaction verified.',
            ]);
        });

        $this->actingAs($this->student)
            ->get(route('booking.payment.callback', ['reference' => 'PSKB-WA-1']))
            ->assertRedirect(route('booking.confirmation', $booking->booking_ref));

        $messages = $this->sentMessages();
        $this->assertCount(2, $messages);

        $studentMsg = collect($messages)->firstWhere('chatId', '233240000000@c.us');
        $this->assertNotNull($studentMsg);
        $this->assertStringContainsString('CONFIRMED', $studentMsg['text']);
        $this->assertStringContainsString('PSK-WA-TXN-0001', $studentMsg['text']);

        $managerMsg = collect($messages)->firstWhere('chatId', '233242555555@c.us');
        $this->assertNotNull($managerMsg);
        $this->assertStringContainsString('Payment received', $managerMsg['text']);
    }

    public function test_status_change_notifies_student()
    {
        $booking = $this->createBooking();

        $manager = User::factory()->create(['role' => User::ROLE_MANAGER, 'phone' => '0242555555']);
        $this->hostel->managers()->attach($manager);

        $this->actingAs($manager)
            ->post(route('manager.bookings.status', $booking), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertEquals('confirmed', $booking->fresh()->status);

        $messages = $this->sentMessages();
        $this->assertCount(1, $messages);
        $this->assertSame('233240000000@c.us', $messages[0]['chatId']);
        $this->assertStringContainsString('CONFIRMED', $messages[0]['text']);
    }

    public function test_contact_form_notifies_admin_number()
    {
        $this->post(route('contact.submit'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Question about a room',
            'message' => 'Do you have rooms near KNUST?',
        ])->assertRedirect();

        $messages = $this->sentMessages();
        $this->assertCount(1, $messages);
        $this->assertSame('233240000001@c.us', $messages[0]['chatId']);
        $this->assertStringContainsString('Question about a room', $messages[0]['text']);
    }

    protected function createBooking(): Booking
    {
        return Booking::create([
            'booking_ref' => 'SRB-WATEST01',
            'user_id' => $this->student->id,
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->room->id,
            'student_name' => 'WA Student',
            'email' => 'wa@student.test',
            'phone' => '0240000000',
            'institution' => 'WA Test University',
            'gender' => 'Female',
            'room_type' => 'Two in a room',
            'academic_year' => '2026/2027',
            'amount' => 800,
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);
    }
}
