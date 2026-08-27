<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NewMessageGreetingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $doctor;
    protected User $pharmacist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::create([
            'name'        => 'Dr. Alice Smith',
            'email'       => 'alice_' . uniqid() . '@hospital.test',
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => 'Internal Medicine',
            'is_active'   => true,
        ]);

        $this->pharmacist = User::create([
            'name'        => 'Rosa Mendoza',
            'email'       => 'rosa_' . uniqid() . '@hospital.test',
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => 'Pharmacy',
            'is_active'   => true,
        ]);

        $doctorRole = Role::where('slug', 'doctor')->first();
        if ($doctorRole) $this->doctor->roles()->attach($doctorRole->id);

        $pharmacistRole = Role::where('slug', 'pharmacist')->first();
        if ($pharmacistRole) $this->pharmacist->roles()->attach($pharmacistRole->id);
    }

    /** @test */
    public function test_1_new_conversation_creates_conversation_with_zero_messages(): void
    {
        // Select staff member with no existing direct conversation
        $response = $this->actingAs($this->doctor)->post(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
        ]);

        $conversation = Conversation::whereHas('participants', function ($q) {
            $q->where('users.id', $this->doctor->id);
        })->whereHas('participants', function ($q) {
            $q->where('users.id', $this->pharmacist->id);
        })->first();

        // Conversation created: YES
        $this->assertNotNull($conversation);

        // Redirects to conversation thread
        $response->assertRedirect(route('messages.index', ['conversation' => $conversation->id]));

        // Messages count: 0 (NO automatic message created)
        $this->assertEquals(0, Message::where('conversation_id', $conversation->id)->count());
    }

    /** @test */
    public function test_2_existing_conversation_reused_and_creates_zero_messages(): void
    {
        // Setup an existing conversation with 1 historical message
        $conversation = Conversation::create([
            'created_by'      => $this->doctor->id,
            'last_message_at' => now()->subDay(),
        ]);
        $conversation->participants()->attach([$this->doctor->id, $this->pharmacist->id]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->doctor->id,
            'message'         => 'Existing chat message.',
            'created_at'      => now()->subDay(),
        ]);

        $initialConvCount = Conversation::count();
        $initialMsgCount  = Message::where('conversation_id', $conversation->id)->count();

        // Select Rosa Mendoza via New Message flow
        $response = $this->actingAs($this->doctor)->post(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
        ]);

        // Same conversation reused: YES
        $this->assertEquals($initialConvCount, Conversation::count());

        // New message created: NO (message count remains 1)
        $this->assertEquals($initialMsgCount, Message::where('conversation_id', $conversation->id)->count());
    }

    /** @test */
    public function test_3_repeated_new_message_action_creates_zero_automatic_messages(): void
    {
        // First selection
        $this->actingAs($this->doctor)->post(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
        ]);

        $convCount = Conversation::count();

        // Second selection
        $this->actingAs($this->doctor)->post(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
        ]);

        // Exactly one conversation
        $this->assertEquals($convCount, Conversation::count());

        // Zero messages created
        $conversation = Conversation::whereHas('participants', function ($q) {
            $q->where('users.id', $this->doctor->id);
        })->whereHas('participants', function ($q) {
            $q->where('users.id', $this->pharmacist->id);
        })->first();

        $this->assertEquals(0, Message::where('conversation_id', $conversation->id)->count());
    }

    /** @test */
    public function test_4_manual_typed_message_is_sent_normally(): void
    {
        // Open/create conversation first
        $this->actingAs($this->doctor)->post(route('messages.store'), [
            'recipient_id' => $this->pharmacist->id,
        ]);

        $conversation = Conversation::whereHas('participants', function ($q) {
            $q->where('users.id', $this->doctor->id);
        })->whereHas('participants', function ($q) {
            $q->where('users.id', $this->pharmacist->id);
        })->first();

        // Explicitly send a typed message
        $response = $this->actingAs($this->doctor)->post(route('messages.store'), [
            'conversation_id' => $conversation->id,
            'message'         => 'Hello, can you check this request?',
        ]);

        // Exactly one message created
        $this->assertEquals(1, Message::where('conversation_id', $conversation->id)->count());
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->doctor->id,
            'message'         => 'Hello, can you check this request?',
        ]);
    }

    /** @test */
    public function test_5_refresh_does_not_create_message(): void
    {
        $conversation = Conversation::create([
            'created_by'      => $this->doctor->id,
            'last_message_at' => now(),
        ]);
        $conversation->participants()->attach([$this->doctor->id, $this->pharmacist->id]);

        $msgCountBefore = Message::count();

        // GET request to messages.index
        $this->actingAs($this->doctor)->get(route('messages.index', ['conversation' => $conversation->id]));

        $this->assertEquals($msgCountBefore, Message::count());
    }

    /** @test */
    public function test_6_sidebar_opening_does_not_create_message(): void
    {
        $conversation = Conversation::create([
            'created_by'      => $this->doctor->id,
            'last_message_at' => now(),
        ]);
        $conversation->participants()->attach([$this->doctor->id, $this->pharmacist->id]);

        $msgCountBefore = Message::count();

        // GET request to messages.show (AJAX sidebar load)
        $this->actingAs($this->doctor)->getJson(route('messages.show', $conversation));

        $this->assertEquals($msgCountBefore, Message::count());
    }
}
