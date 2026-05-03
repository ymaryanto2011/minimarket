<?php

namespace App\Http\Controllers;

use App\Models\StoreProfile;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function profile()
    {
        $store = StoreProfile::firstOrNew([]);
        return view('setting.index', compact('store'));
    }

    public function profileUpdate(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'nullable|string',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'owner_name' => 'nullable|string|max:255',
            'bank_accounts'                => 'nullable|array|max:5',
            'bank_accounts.*.bank_name'    => 'nullable|string|max:100',
            'bank_accounts.*.account_no'   => 'nullable|string|max:50',
            'bank_accounts.*.account_name' => 'nullable|string|max:100',
        ]);

        // Filter out empty bank account rows
        $banks = [];
        foreach ($data['bank_accounts'] ?? [] as $bank) {
            if (!empty($bank['bank_name']) || !empty($bank['account_no'])) {
                $banks[] = [
                    'bank_name'    => $bank['bank_name'] ?? '',
                    'account_no'   => $bank['account_no'] ?? '',
                    'account_name' => $bank['account_name'] ?? '',
                ];
            }
        }
        $data['bank_accounts'] = $banks ?: null;

        StoreProfile::updateOrCreate(['id' => 1], $data);

        return redirect()->route('setting.profile')->with('success', 'Profil toko berhasil disimpan.');
    }
}
