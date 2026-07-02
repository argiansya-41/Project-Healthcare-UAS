<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIChatTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Warga Tester',
            'email' => 'tester@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
        ]);
    }

    public function test_guest_cannot_access_ai_chat_page()
    {
        $response = $this->get(route('ai-chat.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_ai_chat_page()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('ai-chat.index'));
        $response->assertStatus(200);
        $response->assertSee('Tanya AI Kesehatan');
    }

    public function test_off_topic_questions_are_politely_rejected()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('ai-chat.send'), [
            'message' => 'Siapa presiden pertama Indonesia?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        
        $reply = $response->json('reply');
        $this->assertStringContainsString('Maaf, sebagai <strong>Asisten AI Kesehatan</strong>', $reply);
    }

    public function test_health_related_questions_are_answered_accurately()
    {
        $this->actingAs($this->user);

        // Test specific illness query (DBD)
        $response1 = $this->postJson(route('ai-chat.send'), [
            'message' => 'Tolong jelaskan tentang penyakit demam berdarah'
        ]);
        $response1->assertStatus(200);
        $this->assertStringContainsString('Demam Berdarah Dengue (DBD)', $response1->json('reply'));

        // Test specific illness query (Sakit Hati)
        $response3 = $this->postJson(route('ai-chat.send'), [
            'message' => 'Apa penyebab sakit hati?'
        ]);
        $response3->assertStatus(200);
        $this->assertStringContainsString('Gangguan Organ Hati', $response3->json('reply'));

        // Test general health query fallback
        $response2 = $this->postJson(route('ai-chat.send'), [
            'message' => 'Bagaimana cara menjaga kesehatan tubuh?'
        ]);
        $response2->assertStatus(200);
        $this->assertStringContainsString('Edukasi Kesehatan Umum', $response2->json('reply'));
    }
}
