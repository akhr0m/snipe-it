<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Asset;
use App\Services\CustomAssetTagService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Carbon\Carbon;

class CustomAssetTagServiceTest extends TestCase
{
    public function test_it_generates_initial_tag()
    {
        // Mock current date to July 2026
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        
        // Ensure no assets match this prefix
        Asset::withTrashed()->where('asset_tag', 'LIKE', 'IT/HO/VII/2026/%')->delete();

        $tag = CustomAssetTagService::generateTag();

        $this->assertEquals('IT/HO/VII/2026/1', $tag);
    }

    public function test_it_increments_sequence()
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        
        // Clean up first
        Asset::withTrashed()->where('asset_tag', 'LIKE', 'IT/HO/VII/2026/%')->forceDelete();

        // Create an asset with sequence 1
        Asset::factory()->create([
            'asset_tag' => 'IT/HO/VII/2026/1'
        ]);

        $tag = CustomAssetTagService::generateTag();

        $this->assertEquals('IT/HO/VII/2026/2', $tag);
    }

    public function test_it_resets_sequence_next_month()
    {
        // Set date to August 2026
        Carbon::setTestNow(Carbon::create(2026, 8, 1));
        
        // Clean up first
        Asset::withTrashed()->where('asset_tag', 'LIKE', 'IT/HO/VIII/2026/%')->forceDelete();

        // Ensure some assets from previous month exist (should not affect August)
        Asset::factory()->create([
            'asset_tag' => 'IT/HO/VII/2026/5'
        ]);

        $tag = CustomAssetTagService::generateTag();

        $this->assertEquals('IT/HO/VIII/2026/1', $tag);
    }
}
