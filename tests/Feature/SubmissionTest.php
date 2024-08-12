<?php

namespace Tests\Feature;

use App\Events\SubmissionSaved;
use App\Jobs\ProcessSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_submit_success(): void
    {
        $response = $this->post(
            '/api/submit',
            [
                'name' => 'Test',
                'email' => 'test@example.com',
                'message' => 'Test message',
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(201);
    }

    public function test_post_submit_success_with_jobs()
    {
        Queue::fake();

        $this->post(
            '/api/submit',
            [
                'name' => 'Test',
                'email' => 'test@example.com',
                'message' => 'Test message',
            ],
            ['Accept' => 'application/json']
        );

        Queue::assertPushed(ProcessSubmission::class);
    }

    public function test_post_submit_success_with_events()
    {
        Event::fake([SubmissionSaved::class]);

        $this->post(
            '/api/submit',
            [
                'name' => 'Test',
                'email' => 'test@example.com',
                'message' => 'Test message',
            ],
            ['Accept' => 'application/json']
        );

        Event::assertDispatched(SubmissionSaved::class);
    }

    public function test_post_submit_fail_empty_body(): void
    {
        $response = $this->post(
            '/api/submit',
            [],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The name field is required. (and 2 more errors)',
            'errors' => [
                'name' => ['The name field is required.'],
                'email' => ['The email field is required.'],
                'message' => ['The message field is required.']
            ],
        ]);
    }

    public function test_post_submit_fail_max_string_length(): void
    {
        $longString = Str::random(256);

        $response = $this->post(
            '/api/submit',
            [
                'name' => $longString,
                'email' => "{$longString}@example.com",
                'message' => $longString,
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The name field must not be greater than 255 characters. (and 2 more errors)',
            'errors' => [
                'name' => ['The name field must not be greater than 255 characters.'],
                'email' => ['The email field must not be greater than 255 characters.'],
                'message' => ['The message field must not be greater than 255 characters.']
            ],
        ]);
    }

    public function test_post_submit_fail_invalid_email(): void
    {
        $response = $this->post(
            '/api/submit',
            [
                'name' => 'Test',
                'email' => 'some-random-string',
                'message' => 'Test message',
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The email field must be a valid email address.',
            'errors' => [
                'email' => ['The email field must be a valid email address.'],
            ],
        ]);
    }
}
