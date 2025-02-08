<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $industries = [
            // Administration und Management
            'Administration: Büroverwaltung und Organisation',
            'Betriebswirtschaft: Unternehmensführung und -verwaltung',
            'Managementberatung und Consulting',
            'Projektmanagement',

            // Bankwesen und Finanzdienstleistungen
            'Bankwesen: Kredit- und Anlagedienstleistungen',
            'Finanzberatung und Vermögensverwaltung',
            'Versicherungen: Lebens-, Unfall- und Sachversicherung',
            'Immobilienfinanzierung und Hypotheken',

            // Baugewerbe und Gebäudetechnik
            'Baugewerbe: Hoch- und Tiefbau',
            'Planung und Architektur',
            'Innenausbau und Renovierung',
            'Gebäudetechnik: Sanitär, Heizung, Klima',
            'Elektroinstallation und Energieversorgung',
            'Dachdecker und Spenglerarbeiten',

            // Chemie und Biotechnologie
            'Chemische Industrie: Herstellung und Verarbeitung',
            'Pharmazeutische Industrie',
            'Biotechnologie und Medizintechnik',
            'Umwelttechnologie und Recycling',

            // Druck und Papier
            'Druckgewerbe: Druck und Druckvorstufe',
            'Papierherstellung und -verarbeitung',
            'Verpackungsindustrie',

            // Elektronik und Elektrotechnik
            'Elektronikfertigung und -montage',
            'Elektrotechnik und Maschinenbau',
            'Automatisierung und Steuerungstechnik',

            // Fahrzeuge und Logistik
            'Fahrzeugbau und Reparatur',
            'Logistik und Lagerhaltung',
            'Transport: Güter- und Personenverkehr',
            'Verkehrswesen: Bahn, Luftfahrt und Schifffahrt',

            // Gastgewerbe und Tourismus
            'Gastgewerbe: Hotellerie und Gastronomie',
            'Catering und Eventmanagement',
            'Tourismus: Reiseveranstalter und Reisebüros',
            'Freizeit und Unterhaltung',

            // Handel und Verkauf
            'Einzelhandel und Fachgeschäfte',
            'Großhandel und Distributionsdienstleistungen',
            'E-Commerce und Online-Handel',
            'Vertrieb und Verkaufsberatung',

            // Handwerk und Baugewerbe
            'Maler und Gipserarbeiten',
            'Zimmerei und Holzbau',
            'Bodenleger und Parkettarbeiten',
            'Fliesenleger und Plattenleger',
            'Gerüstbau und Baulogistik',
            'Sanitär und Heizungsbau',

            // Informatik und Kommunikation
            'Softwareentwicklung und IT-Dienstleistungen',
            'Medien- und Kommunikationstechnologie',
            'Telekommunikation und Netzwerktechnik',
            'Webdesign und UX-Design',

            // Kunststoffe und Oberflächentechnik
            'Kunststoffverarbeitung und Formtechnik',
            'Oberflächenbehandlung und Beschichtung',

            // Landwirtschaft und Forstwirtschaft
            'Landwirtschaft: Ackerbau und Tierhaltung',
            'Forstwirtschaft und Holzproduktion',
            'Gartenbau und Landschaftspflege',
            'Tierzucht und Tierpflege',

            // Lebensmittel und Nahrungsmittelindustrie
            'Lebensmittelproduktion und -verarbeitung',
            'Getränkeherstellung',
            'Bäckerei und Konditorei',
            'Nahrung: Biologische und nachhaltige Produkte',

            // Maschinen- und Anlagenbau
            'Maschinenbau und Fertigungstechnik',
            'Anlagenbau und Instandhaltung',
            'Industrielle Automation',

            // Medizin und Gesundheitswesen
            'Gesundheitswesen: Krankenhäuser und Pflegeeinrichtungen',
            'Medizintechnik und Laborausstattung',
            'Apotheken und Pharmazeutische Produkte',
            'Wellness und Gesundheitsförderung',

            // Metallgewerbe und Giesserei
            'Metallbau und Schweißtechnik',
            'Gießerei und Metallverarbeitung',

            // Naturwissenschaften und Forschung
            'Naturwissenschaftliche Forschung und Entwicklung',
            'Labortechnik und Analysen',
            'Umweltwissenschaften und Nachhaltigkeit',

            // Rechts- und Beratungsdienstleistungen
            'Rechtsberatung und Anwaltskanzleien',
            'Notariat und Patentrecht',
            'Steuerberatung und Wirtschaftsprüfung',

            // Textilien und Mode
            'Textilindustrie und Bekleidungsherstellung',
            'Modehandel und Design',
            'Schuh- und Lederwarenherstellung',

            // Unterhaltung und Medien
            'Film- und Fernsehproduktion',
            'Musikindustrie und Veranstaltungsmanagement',
            'Verlage und Buchhandel',

            // Wissenschaft und Bildung
            'Bildung und Erwachsenenbildung',
            'Berufs- und Weiterbildung',
            'Forschungseinrichtungen und Institute',

            // Sonstige Branchen
            'Reinigungsdienste und Facility Management',
            'Sicherheitsdienste und Detekteien',
            'Öffentliche Verwaltung und Behörden'
        ];



        foreach ($industries as $industry) {
            DB::table('industries')->insert([
                'name' => $industry,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
