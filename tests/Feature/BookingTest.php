<?php

namespace Tests\Feature;

use App\Models\AvailabilityRule;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $this->admin->assignRole($superAdminRole);
        }
    }

    // ─── Booking Services CRUD ─────────────────────────────────

    public function test_admin_can_create_booking_service()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/booking-services', [
                'name' => ['fr' => 'Coupe homme', 'en' => 'Haircut', 'ar' => 'قص الشعر'],
                'duration_minutes' => 30,
                'price' => 25.00,
                'is_active' => true,
                'order' => 1,
            ])->assertCreated()
            ->assertJsonPath('data.name_translations.en', 'Haircut');
    }

    public function test_booking_service_requires_name_and_duration()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/booking-services', [])
            ->assertJsonValidationErrors(['name', 'duration_minutes']);
    }

    public function test_admin_can_list_booking_services()
    {
        BookingService::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/booking-services')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_update_booking_service()
    {
        $service = BookingService::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/booking-services/{$service->id}", [
                'duration_minutes' => 60,
                'price' => 50.00,
            ])->assertOk()
            ->assertJsonPath('data.duration_minutes', 60);
    }

    public function test_admin_can_delete_booking_service()
    {
        $service = BookingService::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/booking-services/{$service->id}")
            ->assertOk();

        $this->assertSoftDeleted($service);
    }

    public function test_admin_can_toggle_booking_service_status()
    {
        $service = BookingService::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/booking-services/{$service->id}/toggle-status")
            ->assertOk();

        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_unauthorized_user_cannot_manage_booking_services()
    {
        $service = BookingService::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/booking-services', [
                'name' => ['fr' => 'Test', 'en' => 'Test', 'ar' => 'Test'],
                'duration_minutes' => 30,
            ])->assertForbidden();

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/booking-services/{$service->id}")
            ->assertForbidden();
    }

    // ─── Public Booking ───────────────────────────────────────

    public function test_guest_can_create_booking_with_valid_data()
    {
        $service = BookingService::factory()->create(['duration_minutes' => 30]);
        $date = Carbon::now()->addDay()->format('Y-m-d');
        AvailabilityRule::create([
            'booking_service_id' => $service->id,
            'day_of_week' => Carbon::parse($date)->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => $date,
            'start_time' => '10:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertCreated()
            ->assertJsonPath('data.customer_name', 'John Doe');
    }

    public function test_public_booking_requires_all_fields()
    {
        $this->postJson('/bookings', [])
            ->assertJsonValidationErrors([
                'booking_service_id', 'date', 'start_time',
                'customer_name', 'customer_phone',
            ]);
    }

    public function test_public_booking_rejects_past_date()
    {
        $service = BookingService::factory()->create();

        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => Carbon::now()->subDay()->format('Y-m-d'),
            'start_time' => '10:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertJsonValidationErrors(['date']);
    }

    public function test_public_booking_rejects_unavailable_slot()
    {
        $service = BookingService::factory()->create(['duration_minutes' => 30]);
        // No availability rule created → no slots available

        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => Carbon::now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertStatus(422);
    }

    public function test_public_booking_rejects_duplicate_phone_by_default()
    {
        $service = BookingService::factory()->create(['duration_minutes' => 30]);
        $date = Carbon::now()->addDay()->format('Y-m-d');

        AvailabilityRule::create([
            'booking_service_id' => $service->id,
            'day_of_week' => Carbon::parse($date)->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        // Create first booking
        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => $date,
            'start_time' => '10:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertCreated();

        // Duplicate phone on same date should be rejected
        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => $date,
            'start_time' => '11:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertStatus(422);
    }

    public function test_public_booking_allows_duplicate_phone_when_enabled()
    {
        Setting::set('booking.allow_duplicate_phone', true, ['name' => 'Allow duplicate phone', 'group' => 'booking']);

        $service = BookingService::factory()->create(['duration_minutes' => 30]);
        $date = Carbon::now()->addDay()->format('Y-m-d');

        AvailabilityRule::create([
            'booking_service_id' => $service->id,
            'day_of_week' => Carbon::parse($date)->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => $date,
            'start_time' => '10:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertCreated();

        $this->postJson('/bookings', [
            'booking_service_id' => $service->id,
            'date' => $date,
            'start_time' => '11:00',
            'customer_name' => 'John Doe',
            'customer_phone' => '+212600000000',
        ])->assertCreated();
    }

    // ─── Admin Booking Operations ─────────────────────────────

    public function test_admin_can_list_bookings()
    {
        Booking::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/bookings')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_filter_bookings_by_status()
    {
        Booking::factory()->pending()->create();
        Booking::factory()->confirmed()->count(2)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/bookings?status=pending')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_filter_bookings_by_date()
    {
        $date = Carbon::now()->addDay()->format('Y-m-d');
        Booking::factory()->create(['date' => $date]);
        Booking::factory()->count(2)->create(['date' => Carbon::now()->addDays(2)->format('Y-m-d')]);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/bookings?date={$date}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_search_bookings_by_name()
    {
        Booking::factory()->create(['customer_name' => 'Alice Wonderland']);
        Booking::factory()->create(['customer_name' => 'Bob Marley']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/bookings?search=Alice')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_view_booking()
    {
        $booking = Booking::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/bookings/{$booking->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $booking->id);
    }

    public function test_admin_can_confirm_booking()
    {
        $booking = Booking::factory()->pending()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/confirm")
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed');
    }

    public function test_admin_can_cancel_booking()
    {
        $booking = Booking::factory()->pending()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_admin_can_complete_booking()
    {
        $booking = Booking::factory()->confirmed()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/complete")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }

    public function test_admin_can_reschedule_booking_to_any_date()
    {
        $service = BookingService::factory()->create(['duration_minutes' => 30]);
        $booking = Booking::factory()->confirmed()->create([
            'booking_service_id' => $service->id,
        ]);

        $futureDate = Carbon::now()->addDays(5)->format('Y-m-d');
        AvailabilityRule::create([
            'booking_service_id' => $service->id,
            'day_of_week' => Carbon::parse($futureDate)->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        // Reschedule to a future date
        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/reschedule", [
                'date' => $futureDate,
                'start_time' => '10:00',
                'reason' => 'Client request',
            ])->assertOk()
            ->assertJsonPath('data.status', 'rescheduled')
            ->assertJsonPath('data.date', $futureDate);
    }

    public function test_admin_can_reschedule_booking_to_past_date()
    {
        $service = BookingService::factory()->create(['duration_minutes' => 30]);
        $booking = Booking::factory()->confirmed()->create([
            'booking_service_id' => $service->id,
        ]);

        $pastDate = Carbon::now()->subDays(5)->format('Y-m-d');
        AvailabilityRule::create([
            'booking_service_id' => $service->id,
            'day_of_week' => Carbon::parse($pastDate)->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/reschedule", [
                'date' => $pastDate,
                'start_time' => '10:00',
            ])->assertOk()
            ->assertJsonPath('data.date', $pastDate);
    }

    public function test_admin_can_delete_booking()
    {
        $booking = Booking::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/bookings/{$booking->id}")
            ->assertOk();

        $this->assertSoftDeleted($booking);
    }

    public function test_admin_can_bulk_delete_bookings()
    {
        $bookings = Booking::factory()->count(3)->create();
        $ids = $bookings->pluck('id')->toArray();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/bookings/bulk-delete', ['ids' => $ids])
            ->assertOk();

        foreach ($ids as $id) {
            $this->assertSoftDeleted(Booking::class, ['id' => $id]);
        }
    }

    public function test_unauthorized_user_cannot_confirm_booking()
    {
        $booking = Booking::factory()->pending()->create();

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/confirm")
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_delete_booking()
    {
        $booking = Booking::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/bookings/{$booking->id}")
            ->assertForbidden();
    }

    // ─── Booking Settings ──────────────────────────────────────

    public function test_admin_can_get_booking_settings()
    {
        Setting::set('booking.time_slot_style', 'wheel', ['name' => 'Time slot style', 'group' => 'booking']);
        Setting::set('booking.allow_duplicate_phone', false, ['name' => 'Allow duplicate phone', 'group' => 'booking']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/booking-settings')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'booking.time_slot_style' => 'wheel',
                    'booking.allow_duplicate_phone' => false,
                ],
            ]);
    }

    public function test_admin_can_update_booking_settings()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/booking-settings', [
                'time_slot_style' => 'list',
                'allow_duplicate_phone' => true,
            ])->assertOk();

        $this->assertEquals('list', setting('booking.time_slot_style'));
        $this->assertTrue((bool) setting('booking.allow_duplicate_phone'));
    }

    public function test_booking_settings_validates_style()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/booking-settings', [
                'time_slot_style' => 'invalid',
            ])->assertStatus(422);
    }

}
