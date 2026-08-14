<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MessengerInterfaceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $doctor;
    protected User $pharmacist;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor           = $this->createUserWithRole('doctor', 'Internal Medicine');
        $this->pharmacist       = $this->createUserWithRole('pharmacist', 'Pharmacy');
        $this->unauthorizedUser = $this->createUserWithRole('rad-tech', 'Radiology');
    }

    protected function createUserWithRole(string $roleSlug, string $department): User
    {
        $user = User::create([
            'name'        => 'Dr. ' . ucfirst($roleSlug) . ' ' . rand(100, 999),
            'email'       => "{$roleSlug}_" . uniqid() . "@hospital.test",
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => $department,
            'is_active'   => true,
        ]);

        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return $user;
    }

    public function test_authenticated_user_can_view_messages_index(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('messages.index'));

        $response->assertStatus(200);
        $response->assertViewIs('messages.index');
        $response->assertSee('Staff Messaging Hub');
        $response->assertSee('+ New Message');
    }

    public function test_authenticated_user_can_send_message_via_ajax(): void
    {
        $response = $this->actingAs($this->doctor)->postJson(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
            'message'      => 'Please review this medication order.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'conversation_id',
            'message' => ['id', 'sender_id', 'sender_name', 'message', 'is_mine', 'created_at'],
            'unread_count',
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->doctor->id,
            'message'   => 'Please review this medication order.',
        ]);
    }

    public function test_participant_can_fetch_conversation_json(): void
    {
        // Doctor sends message to pharmacist
        $this->actingAs($this->doctor)->postJson(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
            'message'      => 'Hello Pharmacist!',
        ]);

        $conversation = Conversation::latest('id')->first();

        // Pharmacist fetches conversation thread via AJAX
        $response = $this->actingAs($this->pharmacist)->getJson(route('messages.show', $conversation));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'conversation_id',
            'other_user' => ['id', 'name', 'role', 'initials'],
            'messages',
            'unread_count',
        ]);
        $response->assertSee('Hello Pharmacist!');
    }

    public function test_idor_protection_prevents_unauthorized_user_from_accessing_conversation(): void
    {
        // Doctor and Pharmacist have a conversation
        $this->actingAs($this->doctor)->postJson(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
            'message'      => 'Private discussion.',
        ]);

        $conversation = Conversation::latest('id')->first();

        // Unauthorized user attempts to read doctor's conversation
        $response = $this->actingAs($this->unauthorizedUser)->getJson(route('messages.show', $conversation));

        $response->assertStatus(403);
    }
}
