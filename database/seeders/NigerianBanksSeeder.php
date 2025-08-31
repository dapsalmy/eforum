<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NigerianBank;

class NigerianBanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            ['name' => 'Access Bank', 'code' => '044', 'short_code' => '*901#', 'swift_code' => 'ABNGNGLA'],
            ['name' => 'Citibank Nigeria', 'code' => '023', 'short_code' => null, 'swift_code' => 'CITINGLA'],
            ['name' => 'Ecobank Nigeria', 'code' => '050', 'short_code' => '*326#', 'swift_code' => 'ECOCNGLA'],
            ['name' => 'Fidelity Bank', 'code' => '070', 'short_code' => '*770#', 'swift_code' => 'FIDTNGLA'],
            ['name' => 'First Bank of Nigeria', 'code' => '011', 'short_code' => '*894#', 'swift_code' => 'FBNINGLA'],
            ['name' => 'First City Monument Bank', 'code' => '214', 'short_code' => '*329#', 'swift_code' => 'FCMBNGLA'],
            ['name' => 'Guaranty Trust Bank', 'code' => '058', 'short_code' => '*737#', 'swift_code' => 'GTBINGLA'],
            ['name' => 'Heritage Bank', 'code' => '030', 'short_code' => '*322#', 'swift_code' => 'HBNGNGLA'],
            ['name' => 'Jaiz Bank', 'code' => '301', 'short_code' => '*389*301#', 'swift_code' => 'JAIZNGLA'],
            ['name' => 'Keystone Bank', 'code' => '082', 'short_code' => '*7111#', 'swift_code' => 'PLNINGLA'],
            ['name' => 'Kuda Bank', 'code' => '50211', 'short_code' => null, 'swift_code' => null],
            ['name' => 'Opay', 'code' => '305', 'short_code' => '*955#', 'swift_code' => null],
            ['name' => 'PalmPay', 'code' => '999991', 'short_code' => null, 'swift_code' => null],
            ['name' => 'Polaris Bank', 'code' => '076', 'short_code' => '*833#', 'swift_code' => 'SKLBNGLA'],
            ['name' => 'Providus Bank', 'code' => '101', 'short_code' => null, 'swift_code' => 'PRDUNGLA'],
            ['name' => 'Stanbic IBTC Bank', 'code' => '221', 'short_code' => '*909#', 'swift_code' => 'SBICNGLA'],
            ['name' => 'Standard Chartered', 'code' => '068', 'short_code' => null, 'swift_code' => 'SCBLNGLA'],
            ['name' => 'Sterling Bank', 'code' => '232', 'short_code' => '*822#', 'swift_code' => 'NAMENGLA'],
            ['name' => 'Union Bank of Nigeria', 'code' => '032', 'short_code' => '*826#', 'swift_code' => 'UBNINGLA'],
            ['name' => 'United Bank for Africa', 'code' => '033', 'short_code' => '*919#', 'swift_code' => 'UNAFNGLA'],
            ['name' => 'Unity Bank', 'code' => '215', 'short_code' => '*7799#', 'swift_code' => 'ICITNGLA'],
            ['name' => 'Wema Bank', 'code' => '035', 'short_code' => '*945#', 'swift_code' => 'WEMANGLA'],
            ['name' => 'Zenith Bank', 'code' => '057', 'short_code' => '*966#', 'swift_code' => 'ZEIBNGLA'],
        ];

        foreach ($banks as $bank) {
            NigerianBank::create($bank);
        }
    }
}
