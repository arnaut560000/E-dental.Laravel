<?php

namespace Tests\Feature;

use App\Models\ClientRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaffAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_open_login_but_not_the_queue(): void
    {
        $this->get('/')->assertOk()->assertSee('Staff login');
        $this->get('/staff/login')->assertOk()->assertSee('name="password"', false);
        $this->get('/staff/requests')->assertRedirect(route('login'));
    }

    public static function allowedRoles(): array
    {
        return [['admin'], ['scheduler']];
    }

    #[DataProvider('allowedRoles')]
    public function test_active_staff_can_log_in_and_open_queue(string $role): void
    {
        $user = $this->staff(['role' => $role]);

        $this->post('/staff/login', [
            'username' => ' STAFF ',
            'password' => 'test-password-long',
        ])->assertRedirect(route('staff.requests.index'));

        $this->assertAuthenticatedAs($user);
        $this->get('/staff/requests')->assertOk()->assertSee('No requests are waiting');
    }

    public static function deniedAccounts(): array
    {
        return [
            'inactive' => [['is_active' => false]],
            'password change required' => [['must_change_password' => true]],
            'service desk' => [['role' => 'service_desk']],
            'unknown role' => [['role' => 'other']],
        ];
    }

    #[DataProvider('deniedAccounts')]
    public function test_ineligible_accounts_cannot_log_in(array $attributes): void
    {
        $this->staff($attributes);

        $this->from('/staff/login')->post('/staff/login', [
            'username' => 'staff',
            'password' => 'test-password-long',
        ])->assertRedirect('/staff/login')->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected_and_attempts_are_throttled(): void
    {
        $this->staff();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/staff/login', [
                'username' => 'staff',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('username');
        }

        $this->post('/staff/login', [
            'username' => 'staff',
            'password' => 'test-password-long',
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_logged_in_staff_loses_access_when_disabled(): void
    {
        $user = $this->staff();
        $this->actingAs($user)->get('/staff/requests')->assertOk();

        $user->is_active = false;
        $user->save();

        $this->get('/staff/requests')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_change_flag_revokes_queue_access(): void
    {
        $user = $this->staff(['must_change_password' => true]);

        $this->actingAs($user)->get('/staff/requests')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_service_desk_cannot_access_queue_even_with_a_session(): void
    {
        $this->actingAs($this->staff(['role' => 'service_desk']))
            ->get('/staff/requests')->assertForbidden();
    }

    public function test_logout_removes_access_and_cannot_be_done_with_get(): void
    {
        $this->actingAs($this->staff());
        $this->get('/staff/logout')->assertStatus(405);
        $this->post('/staff/logout')->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get('/staff/requests')->assertRedirect(route('login'));
    }

    public function test_queue_is_filtered_ordered_paginated_and_escaped(): void
    {
        // Deliberately insert a newer request before the older ones.
        $this->client('NEWER', now());
        $first = $this->client('FIRST', now()->subDay());
        $this->client('SECOND', $first->created_at);
        $this->client('REJECTED-REQUEST', now()->subDays(2), 'Rejected');
        $first->first_name = '<script>alert(1)</script>';
        $first->save();

        for ($i = 0; $i < 19; $i++) {
            $this->client('LATER-'.$i, now()->addDays(2));
        }

        $response = $this->actingAs($this->staff())->get('/staff/requests');
        $response->assertOk()
            ->assertSeeInOrder(['FIRST', 'SECOND', 'NEWER'])
            ->assertDontSee('REJECTED-REQUEST')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame(22, $response->viewData('requests')->total());
        $this->assertCount(20, $response->viewData('requests')->items());
        $this->get('/staff/requests?page=2')->assertOk()->assertViewHas(
            'requests', fn ($requests) => $requests->count() === 2
        );
    }

    public function test_admin_command_creates_a_hashed_permanent_account(): void
    {
        $this->artisan('dental:create-admin')
            ->expectsQuestion('Username', ' Clinic.Admin ')
            ->expectsQuestion('Full name', 'Clinic Administrator')
            ->expectsQuestion('Password: 12-72 ASCII characters', 'test-password-long')
            ->expectsQuestion('Confirm password', 'test-password-long')
            ->expectsOutput('Administrator created successfully.')
            ->assertSuccessful();

        $user = User::where('username', 'clinic.admin')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('test-password-long', $user->password));
    }

    public function test_admin_command_does_not_overwrite_existing_staff(): void
    {
        $user = $this->staff();

        $this->artisan('dental:create-admin')
            ->expectsQuestion('Username', 'staff')
            ->expectsQuestion('Full name', 'Changed Name')
            ->expectsQuestion('Password: 12-72 ASCII characters', 'different-password')
            ->expectsQuestion('Confirm password', 'different-password')
            ->assertFailed();

        $this->assertSame(1, User::count());
        $this->assertSame($user->name, $user->fresh()->name);
        $this->assertTrue(Hash::check('test-password-long', $user->fresh()->password));
    }

    private function staff(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'username' => 'staff',
            'role' => 'admin',
            'password' => 'test-password-long',
            'is_active' => true,
            'must_change_password' => false,
        ], $attributes));
    }

    private function client(string $code, $createdAt, string $status = 'Waiting for schedule'): ClientRequest
    {
        return ClientRequest::create([
            'request_code' => $code,
            'last_name' => 'Example',
            'first_name' => 'Client',
            'birth_date' => '1990-01-01',
            'gender' => 'Female',
            'barangay' => 'Sample Barangay',
            'category' => 'General Public',
            'contact_number' => '09123456789',
            'contact_key' => '09123456789',
            'status' => $status,
            'created_at' => $createdAt,
        ]);
    }
}
