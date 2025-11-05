<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Purchase Request', 'code' => 'PR', 'prefix' => 'PR', 'requires_approval' => true],
            ['name' => 'Obligation Request', 'code' => 'OBR', 'prefix' => 'OBR', 'requires_approval' => true],
            ['name' => 'Purchase Order', 'code' => 'PO', 'prefix' => 'PO', 'requires_approval' => true],
            ['name' => 'Disbursement Voucher', 'code' => 'DV', 'prefix' => 'DV', 'requires_approval' => true],
            ['name' => 'Invitation to Bid', 'code' => 'ITB', 'prefix' => 'ITB', 'requires_approval' => true],
            ['name' => 'Abstract of Bids', 'code' => 'AB', 'prefix' => 'AB', 'requires_approval' => true],
            ['name' => 'Notice of Award', 'code' => 'NOA', 'prefix' => 'NOA', 'requires_approval' => true],
            ['name' => 'Notice to Proceed', 'code' => 'NTP', 'prefix' => 'NTP', 'requires_approval' => true],
            ['name' => 'Contract', 'code' => 'CONTRACT', 'prefix' => 'CON', 'requires_approval' => true],
            ['name' => 'Memorandum', 'code' => 'MEMO', 'prefix' => 'MEMO', 'requires_approval' => false],
        ];

        foreach ($types as $type) {
            DocumentType::create($type);
        }
    }
}