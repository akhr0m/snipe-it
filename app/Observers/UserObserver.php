<?php

namespace App\Observers;

use App\Models\Actionlog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\UserReport;
use Carbon\Carbon;

class UserObserver
{
    /**
     * Listen to the User updating event. This fires automatically every time an existing asset is saved.
     *
     * @param  User  $user
     * @return void
     */
    public function updating(User $user)
    {

        // ONLY allow these fields to be stored
        $allowed_fields = [
            'email',
            'activated',
            'first_name',
            'last_name',
            'website',
            'country',
            'gravatar',
            'location_id',
            'phone',
            'jobtitle',
            'manager_id',
            'employee_num',
            'username',
            'notes',
            'company_id',
            'ldap_import',
            'locale',
            'two_factor_enrolled',
            'two_factor_optin',
            'department_id',
            'address',
            'address2',
            'city',
            'state',
            'zip',
            'remote',
            'start_date',
            'end_date',
            'autoassign_licenses',
            'vip',
            'password'
        ];
        
        $changed = [];

        foreach ($user->getRawOriginal() as $key => $value) {

            // Make sure the info is in the allow fields array
            if (in_array($key, $allowed_fields)) {

                // Check and see if the value changed
                if ($user->getRawOriginal()[$key] != $user->getAttributes()[$key]) {

                    $changed[$key]['old'] = $user->getRawOriginal()[$key];
                    $changed[$key]['new'] = $user->getAttributes()[$key];

                    // Do not store the hashed password in changes
                    if ($key == 'password') {
                        $changed['password']['old'] = '*************';
                        $changed['password']['new'] = '*************';
                    }

                }
            }

        }

        if (count($changed) > 0) {
            $logAction = new Actionlog();
            $logAction->item_type = User::class;
            $logAction->item_id = $user->id;
            $logAction->target_type = User::class; // can we instead say $logAction->item = $asset ?
            $logAction->target_id = $user->id;
            $logAction->created_at = date('Y-m-d H:i:s');
            $logAction->created_by = auth()->id();
            $logAction->log_meta = json_encode($changed);
            $logAction->logaction('update');
        }


    }

    /**
     * Listen to the User created event, and increment
     * the next_auto_tag_base value in the settings table when i
     * a new asset is created.
     *
     * @param  User $user
     * @return void
     */
    public function created(User $user)
    {

    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User $user
     * @return void
     */
    public function deleting(User $user)
    {
        $logAction = new Actionlog();
        $logAction->item_type = User::class;
        $logAction->item_id = $user->id;
        $logAction->target_type = User::class; // can we instead say $logAction->item = $asset ?
        $logAction->target_id = $user->id;
        $logAction->created_at = date('Y-m-d H:i:s');
        $logAction->created_by = auth()->id();
        $logAction->logaction('delete');
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User $user
     * @return void
     */
    public function restoring(User $user)
    {
        $logAction = new Actionlog();
        $logAction->item_type = User::class;
        $logAction->item_id = $user->id;
        $logAction->target_type = User::class; // can we instead say $logAction->item = $asset ?
        $logAction->target_id = $user->id;
        $logAction->created_at = date('Y-m-d H:i:s');
        $logAction->created_by = auth()->id();
        $logAction->logaction('restore');
    }


    //==================================================================================
    // ▼▼▼ TAMBAHKAN DUA FUNGSI "HELPER" BARU DI SINI, DI BAGIAN AKHIR CLASS ▼▼▼
    //==================================================================================

    /**
     * Generate a unique report number for a new user.
     *
     * @param \App\Models\User $user
     */
    private function generateReportNumberForUser(User $user)
    {
        // 1. Dapatkan nomor urut terakhir + 1
        $lastId = UserReport::max('id') ?? 0;
        $sequence = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        // 2. Dapatkan bulan dalam romawi
        $monthRoman = $this->toRoman(Carbon::now()->month);
        
        // 3. Dapatkan tahun
        $year = Carbon::now()->year;

        // 4. Gabungkan format
        $reportNumber = "{$sequence}/BAST/IT/HO/{$monthRoman}/{$year}";

        // 5. Simpan ke tabel user_reports
        UserReport::create([
            'user_id' => $user->id,
            'report_number' => $reportNumber,
        ]);
    }

    /**
     * Convert a number to Roman numeral.
     *
     * @param int $number
     * @return string
     */
    private function toRoman($number) {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

}
