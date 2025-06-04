<?php

namespace Tests\Feature;

use App\Jobs\GenerateSalesReportJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminReportControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function authenticateAdmin()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);
        return $admin;
    }

    public function test_admin_can_generate_report()
    {
        $this->authenticateAdmin();

        Queue::fake();

        $response = $this->postJson('/api/admin/reports');

        $response->assertStatus(202)
            ->assertJsonStructure([
                'uuid',
                'message',
                'status',
            ]);

        $this->assertDatabaseHas('reports', [
            'uuid' => $response['uuid'],
            'status' => 'pending',
        ]);

        Queue::assertPushed(GenerateSalesReportJob::class, function ($job) use ($response) {
            return $job->report->uuid == $response['uuid'];
        });
    }
}
