<?php

namespace Tests\Feature\Users\Ui;

use App\Models\User;
use BastModule\Models\UserReport;
use Carbon\Carbon;
use Tests\TestCase;

class BastReportPreviewTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function testPreviewDoesNotCreateANewBastReportRecord()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 13, 10, 0, 0));

        $actor = User::factory()->viewUsers()->create();
        $user = User::factory()->create(['company_id' => $actor->company_id]);

        $this->actingAs($actor)
            ->get(route('users.bast_report', $user))
            ->assertOk()
            ->assertSee('00001/BAST/IT/HO/IV/2026');

        $this->assertDatabaseCount('user_reports', 0);
    }

    public function testPreviewUsesTheNextSequenceFromTheCurrentYearOnly()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 13, 10, 0, 0));

        $actor = User::factory()->viewUsers()->create();
        $user = User::factory()->create(['company_id' => $actor->company_id]);
        $otherUser = User::factory()->create(['company_id' => $actor->company_id]);

        UserReport::create([
            'user_id' => $user->id,
            'recipient_id' => $user->id,
            'giver_id' => $actor->id,
            'report_number' => '99999/BAST/IT/HO/XII/2025',
            'assets_snapshot' => json_encode([]),
            'handover_date' => Carbon::create(2025, 12, 1, 8, 0, 0),
        ]);

        UserReport::create([
            'user_id' => $otherUser->id,
            'recipient_id' => $otherUser->id,
            'giver_id' => $actor->id,
            'report_number' => '00007/BAST/IT/HO/I/2026',
            'assets_snapshot' => json_encode([]),
            'handover_date' => Carbon::create(2026, 1, 10, 8, 0, 0),
        ]);

        $this->actingAs($actor)
            ->get(route('users.bast_report', $user))
            ->assertOk()
            ->assertSee('00008/BAST/IT/HO/IV/2026');

        $this->assertDatabaseCount('user_reports', 2);
    }
}
