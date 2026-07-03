<?php

namespace App\Services;

use App\Models\Asset;

class CustomAssetTagService
{
    /**
     * Generate custom asset tag with format: IT/HO/{BulanRomawi}/{Tahun}/{Urutan}
     * The sequence resets to 1 every month/year.
     */
    public static function generateTag(): string
    {
        $prefix = 'IT/HO/';
        $monthRoman = self::getRomanMonth(now()->format('n'));
        $year = now()->format('Y');
        
        $baseString = "{$prefix}{$monthRoman}/{$year}/";
        
        // Find all assets matching this pattern (including deleted ones)
        // to avoid duplicating sequences if an asset was deleted.
        $assets = Asset::withTrashed()
            ->where('asset_tag', 'LIKE', $baseString . '%')
            ->pluck('asset_tag');

        $maxSequence = 0;
        foreach ($assets as $tag) {
            $parts = explode('/', $tag);
            $lastPart = end($parts);
            
            if (is_numeric($lastPart)) {
                $num = (int) $lastPart;
                if ($num > $maxSequence) {
                    $maxSequence = $num;
                }
            }
        }

        $nextSequence = $maxSequence + 1;

        return $baseString . $nextSequence;
    }

    /**
     * Helper to get Roman numeral for the given month (1-12)
     */
    private static function getRomanMonth($month)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[(int)$month] ?? '';
    }
}
