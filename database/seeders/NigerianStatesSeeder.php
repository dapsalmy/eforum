<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NigerianState;
use App\Models\NigerianLga;

class NigerianStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statesData = [
            // South-West
            [
                'name' => 'Lagos',
                'code' => 'LAG',
                'capital' => 'Ikeja',
                'region' => 'South-West',
                'latitude' => 6.5244,
                'longitude' => 3.3792,
                'lgas' => [
                    'Agege', 'Ajeromi-Ifelodun', 'Alimosho', 'Amuwo-Odofin', 'Apapa',
                    'Badagry', 'Epe', 'Eti-Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye',
                    'Ikeja', 'Ikorodu', 'Kosofe', 'Lagos Island', 'Lagos Mainland',
                    'Mushin', 'Ojo', 'Oshodi-Isolo', 'Shomolu', 'Surulere'
                ]
            ],
            [
                'name' => 'Ogun',
                'code' => 'OGU',
                'capital' => 'Abeokuta',
                'region' => 'South-West',
                'latitude' => 7.1475,
                'longitude' => 3.3619,
                'lgas' => [
                    'Abeokuta North', 'Abeokuta South', 'Ado-Odo/Ota', 'Ewekoro', 'Ifo',
                    'Ijebu East', 'Ijebu North', 'Ijebu North East', 'Ijebu Ode', 'Ikenne',
                    'Imeko Afon', 'Ipokia', 'Obafemi Owode', 'Odogbolu', 'Odeda',
                    'Ogun Waterside', 'Remo North', 'Sagamu', 'Yewa North', 'Yewa South'
                ]
            ],
            [
                'name' => 'Oyo',
                'code' => 'OYO',
                'capital' => 'Ibadan',
                'region' => 'South-West',
                'latitude' => 7.8526,
                'longitude' => 3.9313,
                'lgas' => [
                    'Afijio', 'Akinyele', 'Atiba', 'Atisbo', 'Egbeda',
                    'Ibadan North', 'Ibadan North-East', 'Ibadan North-West', 'Ibadan South-East', 'Ibadan South-West',
                    'Ibarapa Central', 'Ibarapa East', 'Ibarapa North', 'Ido', 'Irepo',
                    'Iseyin', 'Itesiwaju', 'Iwajowa', 'Kajola', 'Lagelu',
                    'Ogbomosho North', 'Ogbomosho South', 'Ogo Oluwa', 'Olorunsogo', 'Oluyole',
                    'Ona Ara', 'Orelope', 'Ori Ire', 'Oyo East', 'Oyo West',
                    'Saki East', 'Saki West', 'Surulere'
                ]
            ],
            
            // Federal Capital Territory
            [
                'name' => 'Federal Capital Territory',
                'code' => 'FCT',
                'capital' => 'Abuja',
                'region' => 'North-Central',
                'latitude' => 9.0579,
                'longitude' => 7.4951,
                'lgas' => [
                    'Abaji', 'Abuja Municipal Area Council', 'Bwari', 'Gwagwalada', 'Kuje', 'Kwali'
                ]
            ],
            
            // North-West
            [
                'name' => 'Kano',
                'code' => 'KAN',
                'capital' => 'Kano',
                'region' => 'North-West',
                'latitude' => 12.0022,
                'longitude' => 8.5919,
                'lgas' => [
                    'Ajingi', 'Albasu', 'Bagwai', 'Bebeji', 'Bichi',
                    'Bunkure', 'Dala', 'Dambatta', 'Dawakin Kudu', 'Dawakin Tofa',
                    'Doguwa', 'Fagge', 'Gabasawa', 'Garko', 'Garun Mallam',
                    'Gaya', 'Gezawa', 'Gwale', 'Gwarzo', 'Kabo',
                    'Kano Municipal', 'Karaye', 'Kibiya', 'Kiru', 'Kumbotso',
                    'Kunchi', 'Kura', 'Madobi', 'Makoda', 'Minjibir',
                    'Nasarawa', 'Rano', 'Rimin Gado', 'Rogo', 'Shanono',
                    'Sumaila', 'Takai', 'Tarauni', 'Tofa', 'Tsanyawa',
                    'Tudun Wada', 'Ungogo', 'Warawa', 'Wudil'
                ]
            ],
            
            // South-South
            [
                'name' => 'Rivers',
                'code' => 'RIV',
                'capital' => 'Port Harcourt',
                'region' => 'South-South',
                'latitude' => 4.8156,
                'longitude' => 7.0498,
                'lgas' => [
                    'Abua/Odual', 'Ahoada East', 'Ahoada West', 'Akuku-Toru', 'Andoni',
                    'Asari-Toru', 'Bonny', 'Degema', 'Eleme', 'Emohua',
                    'Etche', 'Gokana', 'Ikwerre', 'Khana', 'Obio/Akpor',
                    'Ogba/Egbema/Ndoni', 'Ogu/Bolo', 'Okrika', 'Omuma', 'Opobo/Nkoro',
                    'Oyigbo', 'Port Harcourt', 'Tai'
                ]
            ],
            
            // South-East
            [
                'name' => 'Enugu',
                'code' => 'ENU',
                'capital' => 'Enugu',
                'region' => 'South-East',
                'latitude' => 6.5244,
                'longitude' => 7.5104,
                'lgas' => [
                    'Aninri', 'Awgu', 'Enugu East', 'Enugu North', 'Enugu South',
                    'Ezeagu', 'Igbo Etiti', 'Igbo Eze North', 'Igbo Eze South', 'Isi Uzo',
                    'Nkanu East', 'Nkanu West', 'Nsukka', 'Oji River', 'Udenu',
                    'Udi', 'Uzo-Uwani'
                ]
            ],
            
            // Add more states as needed...
        ];

        foreach ($statesData as $stateData) {
            $lgas = $stateData['lgas'];
            unset($stateData['lgas']);
            
            $state = NigerianState::create($stateData);
            
            foreach ($lgas as $lgaName) {
                NigerianLga::create([
                    'state_id' => $state->id,
                    'name' => $lgaName,
                ]);
            }
        }
    }
}
