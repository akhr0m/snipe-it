<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserReport;
use Carbon\Carbon;
use Tests\TestCase;

class UserReportTest extends TestCase
{
    public function testItCastsSnapshotAndHandoverDate()
    {
        $actor = User::factory()->create();
        $recipient = User::factory()->create(['company_id' => $actor->company_id]);

        $report = UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'recipient_snapshot' => ['first_name' => 'Jack', 'last_name' => 'Sparrow'],
            'giver_snapshot' => ['first_name' => 'Tony', 'last_name' => 'Stark'],
            'header_snapshot' => ['site_name' => 'Snapshot Corp'],
            'report_number' => '00001/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [
                ['asset_tag' => 'AST-001', 'name' => 'Laptop'],
            ],
            'handover_date' => '2026-04-13 10:00:00',
        ])->fresh();

        $this->assertIsArray($report->assets_snapshot);
        $this->assertIsArray($report->recipient_snapshot);
        $this->assertIsArray($report->giver_snapshot);
        $this->assertIsArray($report->header_snapshot);
        $this->assertSame('AST-001', $report->assets_snapshot[0]['asset_tag']);
        $this->assertSame('Jack', $report->recipient_snapshot['first_name']);
        $this->assertSame('Snapshot Corp', $report->header_snapshot['site_name']);
        $this->assertInstanceOf(Carbon::class, $report->handover_date);
        $this->assertSame('2026-04-13 10:00:00', $report->handover_date->format('Y-m-d H:i:s'));
    }

    public function testItDefinesUserRecipientAndGiverRelationships()
    {
        $actor = User::factory()->create();
        $recipient = User::factory()->create(['company_id' => $actor->company_id]);

        $report = UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'report_number' => '00002/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [],
            'handover_date' => '2026-04-13 10:00:00',
        ])->fresh();

        $this->assertTrue($report->user->is($recipient));
        $this->assertTrue($report->recipient->is($recipient));
        $this->assertTrue($report->giver->is($actor));
    }

    public function testItPrefersRecipientSnapshotForDisplayName()
    {
        $actor = User::factory()->create();
        $recipient = User::factory()->create(['first_name' => 'Live', 'last_name' => 'User']);

        $report = UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'recipient_snapshot' => ['first_name' => 'Saved', 'last_name' => 'User'],
            'report_number' => '00003/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [],
            'handover_date' => '2026-04-13 10:00:00',
        ])->fresh();

        $this->assertSame('Saved User', $report->recipient_display_name);
    }
}
