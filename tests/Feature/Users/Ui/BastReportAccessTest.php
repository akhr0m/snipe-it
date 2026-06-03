<?php

namespace Tests\Feature\Users\Ui;

use App\Models\Department;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use BastModule\Models\UserReport;
use Carbon\Carbon;
use Tests\TestCase;

class BastReportAccessTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function testSearchPageDisplaysStoredBastReports()
    {
        $actor = User::factory()->create();
        $recipient = User::factory()->create(['company_id' => $actor->company_id]);

        UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'report_number' => '00001/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [],
            'handover_date' => '2026-04-13 10:00:00',
        ]);

        $this->actingAs($actor)
            ->get(route('bast.search'))
            ->assertOk()
            ->assertSee('00001/BAST/IT/HO/IV/2026')
            ->assertSee($recipient->first_name);
    }

    public function testFindBastReportDisplaysStoredDocumentByReportNumber()
    {
        $actor = User::factory()->create();
        $recipient = User::factory()->create(['company_id' => $actor->company_id]);

        UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'report_number' => '00002/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [
                ['name' => 'Laptop Kerja', 'serial' => 'SN-001', 'notes' => 'Unit test'],
            ],
            'handover_date' => '2026-04-13 10:00:00',
        ]);

        $this->actingAs($actor)
            ->get(route('bast.find', ['number' => '00002/BAST/IT/HO/IV/2026']))
            ->assertOk()
            ->assertSee('00002/BAST/IT/HO/IV/2026')
            ->assertSee('Laptop Kerja')
            ->assertSee($recipient->present()->fullName());
    }

    public function testViewBastReportByIdDisplaysStoredDocument()
    {
        $actor = User::factory()->create();
        $recipient = User::factory()->create(['company_id' => $actor->company_id]);

        $report = UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'report_number' => '00003/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [
                ['name' => 'Monitor', 'serial' => 'SN-002', 'notes' => 'Spare'],
            ],
            'handover_date' => '2026-04-13 10:00:00',
        ]);

        $this->actingAs($actor)
            ->get(url('/bast-report/view/' . $report->id))
            ->assertOk()
            ->assertSee('00003/BAST/IT/HO/IV/2026')
            ->assertSee('Monitor')
            ->assertSee($actor->present()->fullName());
    }

    public function testStoreAndPrintBastReportCreatesRecordAndReturnsPrintablePage()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 13, 10, 0, 0));

        $actor = User::factory()->create();
        $recipient = User::factory()->create(['company_id' => $actor->company_id]);

        $this->actingAs($actor)
            ->post(route('users.bast_report.print', $recipient))
            ->assertOk()
            ->assertSee('BAST berhasil disimpan.')
            ->assertSee('00001/BAST/IT/HO/IV/2026')
            ->assertSee('window.print()');

        $this->assertDatabaseHas('user_reports', [
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'report_number' => '00001/BAST/IT/HO/IV/2026',
        ]);
    }

    public function testRecipientDepartmentIsShownNextToJobTitleOnBastReport()
    {
        $department = Department::factory()->create(['name' => 'HRGA-IT']);
        $actor = User::factory()->create();
        $recipient = User::factory()->create([
            'company_id' => $actor->company_id,
            'jobtitle' => 'Senior Backend Engineer',
            'department_id' => $department->id,
        ]);

        $report = UserReport::create([
            'user_id' => $recipient->id,
            'recipient_id' => $recipient->id,
            'giver_id' => $actor->id,
            'report_number' => '00004/BAST/IT/HO/IV/2026',
            'assets_snapshot' => [],
            'handover_date' => '2026-04-13 10:00:00',
        ]);

        $this->actingAs($actor)
            ->get(url('/bast-report/view/' . $report->id))
            ->assertOk()
            ->assertSee($recipient->present()->fullName() . ' - Senior Backend Engineer (HRGA-IT)');
    }

    public function testStoredBastReportUsesSnapshotsWhenUserAndAssetChangeLater()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 13, 10, 0, 0));

        $actorLocation = Location::factory()->create(['name' => 'HO Jakarta']);
        $recipientLocation = Location::factory()->create(['name' => 'Warehouse A']);
        $recipientDepartment = Department::factory()->create(['name' => 'HRGA-IT']);
        $newDepartment = Department::factory()->create(['name' => 'Finance']);

        $actor = User::factory()->create([
            'first_name' => 'Tony',
            'last_name' => 'Stark',
            'jobtitle' => 'IT Manager',
            'location_id' => $actorLocation->id,
        ]);

        $recipient = User::factory()->create([
            'company_id' => $actor->company_id,
            'first_name' => 'Jack',
            'last_name' => 'Sparrow',
            'jobtitle' => 'Senior Backend Engineer',
            'department_id' => $recipientDepartment->id,
            'location_id' => $recipientLocation->id,
        ]);

        Asset::factory()->create([
            'name' => 'Laptop Lama',
            'serial' => 'SN-LAMA',
            'notes' => 'Catatan lama',
            'assigned_to' => $recipient->id,
            'assigned_type' => User::class,
        ]);

        $this->actingAs($actor)
            ->post(route('users.bast_report.print', $recipient))
            ->assertOk();

        $report = UserReport::firstOrFail();

        $recipient->update([
            'first_name' => 'James',
            'last_name' => 'Cook',
            'jobtitle' => 'Chief Finance Officer',
            'department_id' => $newDepartment->id,
        ]);

        $asset = Asset::firstOrFail();
        $asset->update([
            'name' => 'Laptop Baru',
            'serial' => 'SN-BARU',
            'notes' => 'Catatan baru',
        ]);

        $this->actingAs($actor)
            ->get(url('/bast-report/view/' . $report->id))
            ->assertOk()
            ->assertSee('Jack Sparrow - Senior Backend Engineer (HRGA-IT)')
            ->assertSee('Laptop Lama')
            ->assertSee('SN-LAMA')
            ->assertSee('Catatan lama')
            ->assertDontSee('James Cook - Chief Finance Officer (Finance)')
            ->assertDontSee('Laptop Baru')
            ->assertDontSee('SN-BARU')
            ->assertDontSee('Catatan baru');

        $this->actingAs($actor)
            ->get(route('bast.search'))
            ->assertOk()
            ->assertSee('Jack Sparrow')
            ->assertDontSee('James Cook');
    }
}
