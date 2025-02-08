<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.settings.sidebar />
        </x-slot:sidebar>

        {{-- Header-Slot nur einfügen, wenn du einen Header auf diese Seite hast --}}
        <x-slot:header>
            <x-navigation.employee.header />
        </x-slot:header>

        {{--Hier werden die livewire Componenten gerendert--}}
        <livewire:setting.theme
        />

    </x-pupi.layout.container>

</x-app-layout>



{{--<x-app-layout>--}}

{{--    <div class="flex flex-col xl:flex-row overflow-hidden">--}}
{{--        <div--}}
{{--            class="2xl:min-h-screen xl:min-h-screen sticky top-0 z-98 bg-gray-100 dark:bg-gray-900 dark:border-gray-700/50 border-gray-200 px-4 xl:py-6 sm:py-2 sm:px-2 lg:pl-6 xl:w-64 xl:shrink-0 xl:border-r xl:pl-6">--}}
{{--            <x-navigation.settings.sidebar/>--}}
{{--        </div>--}}

{{--        <div class="flex-1 overflow-y-auto">--}}

{{--            <x-navigation.employee.header/>--}}

{{--            <div class="space-y-10 divide-y dark:divide-white/5 divide-gray-900/10">--}}
{{--                <livewire:setting.theme--}}
{{--                />--}}

{{--                <table>--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>Feld</th>--}}
{{--                        <th>Information</th>--}}
{{--                        <th>Wer hat Zugriff?</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    <tr>--}}
{{--                        <td>Vorname</td>--}}
{{--                        <td>Gina</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Nachname</td>--}}
{{--                        <td>Pacino</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Position</td>--}}
{{--                        <td>HR</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Abteilung</td>--}}
{{--                        <td>Accounting</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Team</td>--}}
{{--                        <td>Standard</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Standort</td>--}}
{{--                        <td></td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>E-Mail-Adresse</td>--}}
{{--                        <td>kina98@gmx.ch</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Telefon (Geschäft)</td>--}}
{{--                        <td>testgelf</td>--}}
{{--                        <td>GP</td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--                <h2>Anstellungsdaten</h2>--}}
{{--                <table>--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>Feld</th>--}}
{{--                        <th>Information</th>--}}
{{--                        <th>Wer hat Zugriff?</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    <tr>--}}
{{--                        <td>Status</td>--}}
{{--                        <td>aktiv</td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Anstellungsverhältnis</td>--}}
{{--                        <td>100% an 5 Tagen pro Woche (seit 28.12.2024)</td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Vorgesetzter</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Personalnummer</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Eintrittsdatum</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Austrittsdatum</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Kündigungsfrist</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Probezeit</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--                <h2>Personalien</h2>--}}
{{--                <table>--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>Feld</th>--}}
{{--                        <th>Information</th>--}}
{{--                        <th>Wer hat Zugriff?</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    <tr>--}}
{{--                        <td>AHV-Nummer</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Adresse</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Geburtsdatum</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Nationalität</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Heimatort</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Telefon (Privat)</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Geschlecht</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Konfession</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Zivilstand</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Aufenthaltsbewilligung</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Bankverbindung (IBAN)</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--                <h2>Administratives</h2>--}}
{{--                <table>--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>Feld</th>--}}
{{--                        <th>Information</th>--}}
{{--                        <th>Wer hat Zugriff?</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    <tr>--}}
{{--                        <td>Interne Notizen</td>--}}
{{--                        <td>test</td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--                <h2>Notfallkontakt</h2>--}}
{{--                <table>--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>Feld</th>--}}
{{--                        <th>Information</th>--}}
{{--                        <th>Wer hat Zugriff?</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    <tr>--}}
{{--                        <td>Notfallkontakt Name</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Notfallkontakt Telefon</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Notfallkontakt Beziehung</td>--}}
{{--                        <td></td>--}}
{{--                        <td></td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}

{{--</x-app-layout>--}}
