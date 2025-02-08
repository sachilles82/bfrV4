<?php

namespace Database\Seeders\Address;

use App\Models\Address\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $states = [
            ['id' => 1, 'name' => 'Aargau','created_by' => 1, 'code' => 'AG', 'country_id' => 1],
            ['id' => 2, 'name' => 'Appenzell Ausserrhoden','created_by' => 1, 'code' => 'AR', 'country_id' => 1],
            ['id' => 3, 'name' => 'Appenzell Innerrhoden','created_by' => 1, 'code' => 'AI', 'country_id' => 1],
            ['id' => 4, 'name' => 'Basel-Landschaft','created_by' => 1, 'code' => 'BL', 'country_id' => 1],
            ['id' => 5, 'name' => 'Basel-Stadt','created_by' => 1, 'code' => 'BS', 'country_id' => 1],
            ['id' => 6, 'name' => 'Bern','created_by' => 1, 'code' => 'BE', 'country_id' => 1],
            ['id' => 7, 'name' => 'Freiburg','created_by' => 1, 'code' => 'FR', 'country_id' => 1],
            ['id' => 8, 'name' => 'Genf','created_by' => 1, 'code' => 'GE', 'country_id' => 1],
            ['id' => 9, 'name' => 'Glarus','created_by' => 1, 'code' => 'GL', 'country_id' => 1],
            ['id' => 10, 'name' => 'Graubünden','created_by' => 1, 'code' => 'GR', 'country_id' => 1],
            ['id' => 11, 'name' => 'Jura','created_by' => 1, 'code' => 'JU', 'country_id' => 1],
            ['id' => 12, 'name' => 'Luzern','created_by' => 1, 'code' => 'LU', 'country_id' => 1],
            ['id' => 13, 'name' => 'Neuenburg','created_by' => 1, 'code' => 'NE', 'country_id' => 1],
            ['id' => 14, 'name' => 'Nidwalden','created_by' => 1, 'code' => 'NW', 'country_id' => 1],
            ['id' => 15, 'name' => 'Obwalden','created_by' => 1, 'code' => 'OW', 'country_id' => 1],
            ['id' => 16, 'name' => 'Schaffhausen','created_by' => 1, 'code' => 'SH', 'country_id' => 1],
            ['id' => 17, 'name' => 'Schwyz','created_by' => 1, 'code' => 'SZ', 'country_id' => 1],
            ['id' => 18, 'name' => 'Solothurn','created_by' => 1, 'code' => 'SO', 'country_id' => 1],
            ['id' => 19, 'name' => 'St. Gallen','created_by' => 1, 'code' => 'SG', 'country_id' => 1],
            ['id' => 20, 'name' => 'Thurgau','created_by' => 1, 'code' => 'TG', 'country_id' => 1],
            ['id' => 21, 'name' => 'Tessin','created_by' => 1, 'code' => 'TI', 'country_id' => 1],
            ['id' => 22, 'name' => 'Uri','created_by' => 1, 'code' => 'UR', 'country_id' => 1],
            ['id' => 23, 'name' => 'Wallis','created_by' => 1, 'code' => 'VS', 'country_id' => 1],
            ['id' => 24, 'name' => 'Waadt','created_by' => 1, 'code' => 'VD', 'country_id' => 1],
            ['id' => 25, 'name' => 'Zug','created_by' => 1, 'code' => 'ZG', 'country_id' => 1],
            ['id' => 26, 'name' => 'Zürich','created_by' => 1, 'code' => 'ZH', 'country_id' => 1],

            ['id' => 27, 'name' => 'Baden-Württemberg','created_by' => 1, 'code' => 'DE-BW', 'country_id' => 2],
            ['id' => 28, 'name' => 'Bayern','created_by' => 1, 'code' => 'DE-BY', 'country_id' => 2],
            ['id' => 29, 'name' => 'Berlin','created_by' => 1, 'code' => 'DE-BE', 'country_id' => 2],
            ['id' => 30, 'name' => 'Brandenburg','created_by' => 1, 'code' => 'DE-BB', 'country_id' => 2],
            ['id' => 31, 'name' => 'Bremen','created_by' => 1, 'code' => 'DE-HB', 'country_id' => 2],
            ['id' => 32, 'name' => 'Hamburg','created_by' => 1, 'code' => 'DE-HH', 'country_id' => 2],
            ['id' => 33, 'name' => 'Hessen','created_by' => 1, 'code' => 'DE-HE', 'country_id' => 2],
            ['id' => 34, 'name' => 'Mecklenburg-Vorpommern','created_by' => 1, 'code' => 'DE-MV', 'country_id' => 2],
            ['id' => 35, 'name' => 'Niedersachsen','created_by' => 1, 'code' => 'DE-NI', 'country_id' => 2],
            ['id' => 36, 'name' => 'Nordrhein-Westfalen','created_by' => 1, 'code' => 'DE-NW', 'country_id' => 2],
            ['id' => 37, 'name' => 'Rheinland-Pfalz','created_by' => 1, 'code' => 'DE-RP', 'country_id' => 2],
            ['id' => 38, 'name' => 'Saarland','created_by' => 1, 'code' => 'DE-SL', 'country_id' => 2],
            ['id' => 39, 'name' => 'Sachsen','created_by' => 1, 'code' => 'DE-SN', 'country_id' => 2],
            ['id' => 40, 'name' => 'Sachsen-Anhalt','created_by' => 1, 'code' => 'DE-ST', 'country_id' => 2],
            ['id' => 41, 'name' => 'Schleswig-Holstein','created_by' => 1, 'code' => 'DE-SH', 'country_id' => 2],
            ['id' => 42, 'name' => 'Thüringen','created_by' => 1, 'code' => 'DE-TH', 'country_id' => 2],

            // Austria (Country ID 3)
            ['id' => 43, 'name' => 'Burgenland','created_by' => 1, 'code' => 'ABG', 'country_id' => 3],
            ['id' => 44, 'name' => 'Kärnten','created_by' => 1, 'code' => 'AKA', 'country_id' => 3],
            ['id' => 45, 'name' => 'Niederösterreich','created_by' => 1, 'code' => 'ANO', 'country_id' => 3],
            ['id' => 46, 'name' => 'Oberösterreich','created_by' => 1, 'code' => 'AOO', 'country_id' => 3],
            ['id' => 47, 'name' => 'Salzburg','created_by' => 1, 'code' => 'ASA', 'country_id' => 3],
            ['id' => 48, 'name' => 'Steiermark','created_by' => 1, 'code' => 'AST', 'country_id' => 3],
            ['id' => 49, 'name' => 'Tirol','created_by' => 1, 'code' => 'ATI', 'country_id' => 3],
            ['id' => 50, 'name' => 'Vorarlberg','created_by' => 1, 'code' => 'AVO', 'country_id' => 3],
            ['id' => 51, 'name' => 'Wien','created_by' => 1, 'code' => 'AWI', 'country_id' => 3],

            //Liechtenstein
            ['id' => 52, 'name' => 'Ruggell','created_by' => 1, 'code' => 'RGL', 'country_id' => 4],
            ['id' => 53, 'name' => 'Schaan','created_by' => 1, 'code' => 'SCH', 'country_id' => 4],
            ['id' => 54, 'name' => 'Triesen','created_by' => 1, 'code' => 'TIN', 'country_id' => 4],
            ['id' => 55, 'name' => 'Vaduz','created_by' => 1, 'code' => 'VAD', 'country_id' => 4],
            ['id' => 56, 'name' => 'Schellenberg','created_by' => 1, 'code' => 'SEL', 'country_id' => 4],
            ['id' => 57, 'name' => 'Gamprin','created_by' => 1, 'code' => 'GAM', 'country_id' => 4],
            ['id' => 58, 'name' => 'Eschen','created_by' => 1, 'code' => 'ESC', 'country_id' => 4],
            ['id' => 59, 'name' => 'Mauren','created_by' => 1, 'code' => 'MAU', 'country_id' => 4],
            ['id' => 60, 'name' => 'Planken','created_by' => 1, 'code' => 'PLK', 'country_id' => 4],
            ['id' => 61, 'name' => 'Triesenberg','created_by' => 1, 'code' => 'TRB', 'country_id' => 4],
            ['id' => 62, 'name' => 'Balzers','created_by' => 1, 'code' => 'BAL', 'country_id' => 4],

            // Italy (Country ID 22)
            ['id' => 77, 'name' => 'Abruzzen (Abruzzo)','created_by' => 1, 'code' => 'ABR', 'country_id' => 5],
            ['id' => 78, 'name' => 'Aostatal (Valle d\'Aosta)','created_by' => 1, 'code' => 'VDA', 'country_id' => 5],
            ['id' => 79, 'name' => 'Apulien (Puglia)','created_by' => 1, 'code' => 'PUG', 'country_id' => 5],
            ['id' => 80, 'name' => 'Basilikata (Basilicata)','created_by' => 1, 'code' => 'BAS', 'country_id' => 5],
            ['id' => 81, 'name' => 'Emilia-Romagna','created_by' => 1, 'code' => 'EMR', 'country_id' => 5],
            ['id' => 82, 'name' => 'Friaul-Julisch Venetien (Friuli Venezia Giulia)','created_by' => 1, 'code' => 'FVG', 'country_id' => 5],
            ['id' => 83, 'name' => 'Kalabrien (Calabria)','created_by' => 1, 'code' => 'CAL', 'country_id' => 5],
            ['id' => 84, 'name' => 'Kampanien (Campania)','created_by' => 1, 'code' => 'CAM', 'country_id' => 5],
            ['id' => 85, 'name' => 'Latium (Lazio)','created_by' => 1, 'code' => 'LAZ', 'country_id' => 5],
            ['id' => 86, 'name' => 'Ligurien (Liguria)','created_by' => 1, 'code' => 'LIG', 'country_id' => 5],
            ['id' => 87, 'name' => 'Lombardei (Lombardia)','created_by' => 1, 'code' => 'LOM', 'country_id' => 5],
            ['id' => 88, 'name' => 'Marken (Marche)','created_by' => 1, 'code' => 'MAR', 'country_id' => 5],
            ['id' => 89, 'name' => 'Molise','created_by' => 1, 'code' => 'MOL', 'country_id' => 5],
            ['id' => 90, 'name' => 'Piemont (Piemonte)','created_by' => 1, 'code' => 'PIE', 'country_id' => 5],
            ['id' => 91, 'name' => 'Sardinien (Sardegna)','created_by' => 1, 'code' => 'SAR', 'country_id' => 5],
            ['id' => 92, 'name' => 'Sizilien (Sicilia)','created_by' => 1, 'code' => 'SIC', 'country_id' => 5],
            ['id' => 93, 'name' => 'Toskana (Toscana)','created_by' => 1, 'code' => 'TOS', 'country_id' => 5],
            ['id' => 94, 'name' => 'Trentino-Südtirol (Trentino-Alto Adige)','created_by' => 1, 'code' => 'TAA', 'country_id' => 5],
            ['id' => 95, 'name' => 'Umbrien (Umbria)','created_by' => 1, 'code' => 'UMB', 'country_id' => 5],
            ['id' => 96, 'name' => 'Venetien (Veneto)','created_by' => 1, 'code' => 'VEN', 'country_id' => 5],

            // France (Country ID 16)
            ['id' => 105, 'name' => 'Auvergne-Rhône-Alpes','created_by' => 1, 'code' => 'ARA', 'country_id' => 6],
            ['id' => 106, 'name' => 'Bourgogne-Franche-Comté','created_by' => 1, 'code' => 'BFC', 'country_id' => 6],
            ['id' => 107, 'name' => 'Bretagne','created_by' => 1, 'code' => 'BRE', 'country_id' => 6],
            ['id' => 108, 'name' => 'Centre-Val de Loire','created_by' => 1, 'code' => 'CVL', 'country_id' => 6],
            ['id' => 109, 'name' => 'Corse','created_by' => 1, 'code' => 'COR', 'country_id' => 6],
            ['id' => 110, 'name' => 'Grand Est','created_by' => 1, 'code' => 'GES', 'country_id' => 6],
            ['id' => 111, 'name' => 'Hauts-de-France','created_by' => 1, 'code' => 'HDF', 'country_id' => 6],
            ['id' => 112, 'name' => 'Île-de-France','created_by' => 1, 'code' => 'IDF', 'country_id' => 6],
            ['id' => 113, 'name' => 'Normandie','created_by' => 1, 'code' => 'NOR', 'country_id' => 6],
            ['id' => 114, 'name' => 'Nouvelle-Aquitaine','created_by' => 1, 'code' => 'NAQ', 'country_id' => 6],
            ['id' => 115, 'name' => 'Occitanie','created_by' => 1, 'code' => 'OCC', 'country_id' => 6],
            ['id' => 116, 'name' => 'Pays de la Loire','created_by' => 1, 'code' => 'PDL', 'country_id' => 6],
            ['id' => 117, 'name' => 'Provence-Alpes-Côte d’Azur','created_by' => 1, 'code' => 'PAC', 'country_id' => 6],


            // Spain (Country ID 42)
            ['id' => 97, 'name' => 'Andalusien','created_by' => 1, 'code' => 'EAN', 'country_id' => 7],
            ['id' => 98, 'name' => 'Aragonien','created_by' => 1, 'code' => 'EAR', 'country_id' => 7],
            ['id' => 99, 'name' => 'Baskenland','created_by' => 1, 'code' => 'EPV', 'country_id' => 7],
            ['id' => 100, 'name' => 'Galicien','created_by' => 1, 'code' => 'EGA', 'country_id' => 7],
            ['id' => 101, 'name' => 'Kanarische Inseln','created_by' => 1, 'code' => 'ECN', 'country_id' => 7],
            ['id' => 102, 'name' => 'Katalonien','created_by' => 1, 'code' => 'ECT', 'country_id' => 7],
            ['id' => 103, 'name' => 'Madrid','created_by' => 1, 'code' => 'EMD', 'country_id' => 7],
            ['id' => 104, 'name' => 'Valencianische Gemeinschaft','created_by' => 1, 'code' => 'EVC', 'country_id' => 7],





            // Belgium (Country ID 7)
//            ['id' => 64, 'name' => 'Antwerpen','created_by' => 1, 'code' => 'VAN', 'country_id' => 7],
//            ['id' => 65, 'name' => 'Flämische Region','created_by' => 1, 'code' => 'VLG', 'country_id' => 7],
//            ['id' => 66, 'name' => 'Limburg','created_by' => 1, 'code' => 'VLI', 'country_id' => 7],
//            ['id' => 67, 'name' => 'Oost-Vlaanderen','created_by' => 1, 'code' => 'VOV', 'country_id' => 7],
//            ['id' => 68, 'name' => 'Vlaams-Brabant','created_by' => 1, 'code' => 'VBR', 'country_id' => 7],
//            ['id' => 69, 'name' => 'West-Vlaanderen','created_by' => 1, 'code' => 'VWV', 'country_id' => 7],
//            ['id' => 70, 'name' => 'Hennegau (Hainaut)','created_by' => 1, 'code' => 'WHT', 'country_id' => 7],
//            ['id' => 71, 'name' => 'Lüttich (Liège)','created_by' => 1, 'code' => 'WLG', 'country_id' => 7],
//            ['id' => 72, 'name' => 'Luxemburg (Luxembourg)','created_by' => 1, 'code' => 'WLX', 'country_id' => 7],
//            ['id' => 73, 'name' => 'Namur','created_by' => 1, 'code' => 'WNA', 'country_id' => 7],
//            ['id' => 74, 'name' => 'Region Brüssel-Hauptstadt','created_by' => 1, 'code' => 'BRU', 'country_id' => 7],
//            ['id' => 75, 'name' => 'Wallonisch-Brabant (Brabant Wallon)','created_by' => 1, 'code' => 'WBR', 'country_id' => 7],
//            ['id' => 76, 'name' => 'Wallonische Region','created_by' => 1, 'code' => 'WAL', 'country_id' => 7],






//            ['id' => 105, 'name' => 'Auvergne-Rhône-Alpes','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 106, 'name' => 'Bourgogne-Franche-Comté','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 107, 'name' => 'Bretagne','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 108, 'name' => 'Centre-Val de Loire','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 109, 'name' => 'Corse','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 110, 'name' => 'Grand Est','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 111, 'name' => 'Hauts-de-France','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 112, 'name' => 'Île-de-France','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 113, 'name' => 'Normandie','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 114, 'name' => 'Nouvelle-Aquitaine','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 115, 'name' => 'Occitanie','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 116, 'name' => 'Pays de la Loire','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],
//            ['id' => 117, 'name' => 'Provence-Alpes-Côte d’Azur','created_by' => 1, 'code' => 'FRA', 'country_id' => 16],

            // Poland (Country ID 34)
//            ['id' => 118, 'name' => 'Dolnoslaskie','created_by' => 1, 'code' => 'DS', 'country_id' => 34],
//            ['id' => 119, 'name' => 'Kujawsko-Pomorskie','created_by' => 1, 'code' => 'KP', 'country_id' => 34],
//            ['id' => 120, 'name' => 'Lubelskie','created_by' => 1, 'code' => 'LU', 'country_id' => 34],
//            ['id' => 121, 'name' => 'Lubuskie','created_by' => 1, 'code' => 'LB', 'country_id' => 34],
//            ['id' => 122, 'name' => 'Lodzkie','created_by' => 1, 'code' => 'LD', 'country_id' => 34],
//            ['id' => 123, 'name' => 'Malopolskie','created_by' => 1, 'code' => 'MA', 'country_id' => 34],
//            ['id' => 124, 'name' => 'Mazowieckie','created_by' => 1, 'code' => 'MZ', 'country_id' => 34],
//            ['id' => 125, 'name' => 'Opolskie','created_by' => 1, 'code' => 'OP', 'country_id' => 34],
//            ['id' => 126, 'name' => 'Podkarpackie','created_by' => 1, 'code' => 'PK', 'country_id' => 34],
//            ['id' => 127, 'name' => 'Podlaskie','created_by' => 1, 'code' => 'PD', 'country_id' => 34],
//            ['id' => 128, 'name' => 'Pomorskie','created_by' => 1, 'code' => 'PM', 'country_id' => 34],
//            ['id' => 129, 'name' => 'Slaskie','created_by' => 1, 'code' => 'SL', 'country_id' => 34],
//            ['id' => 130, 'name' => 'Swietokrzyskie','created_by' => 1, 'code' => 'SK', 'country_id' => 34],
//            ['id' => 131, 'name' => 'Warminsko-Mazurskie','created_by' => 1, 'code' => 'WM', 'country_id' => 34],
//            ['id' => 132, 'name' => 'Wielkopolskie','created_by' => 1, 'code' => 'WP', 'country_id' => 34],
//            ['id' => 133, 'name' => 'Zachodniopomorskie','created_by' => 1, 'code' => 'ZP', 'country_id' => 34],
        ];

        State::insert($states);
    }
}
