<?php

namespace App\Enums\Role;

enum UserHasRole: string
{
    /** Das sind die Standart Rolen für die User
     *
     * Anstatt dass ich die rolen als string erstelle, tue ich das mit Enums. Ist sicherer
     *
     * Beim estellen des Employee User, erhält er die Rolle Employee automatisch zugewiesen, siehe CreateEmployee Livewire Component
     * Beim erstellen des Partner User, erhält er die Rolle Partner automatisch zugewiesen, siehe CreatePartner Livewire Component
     *
     * Der Owner User wird automatisch bei seiner Registrierung die Rolle Owner zugewiesen, siehe CreateNewUser
     *
     *
     * SuperAdmin: Der SuperAdmin hat Zugriff auf alle Bereiche
     * Admin: Der NovaAdmin hat Zugriff auf das Nova Panel
     * Owner: Der Owner hat Zugriff auf das Owner Panel
     * Employee: Der Employee hat Zugriff auf das Employee Panel
     * Partner: Der Partner hat Zugriff auf das Partner Panel
     *
     */

    case SuperAdmin = 'Super Admin';
    case Admin = 'admin';
    case Support = 'Support Team';
    case Marketing = 'Marketing Team';
    case Sales = 'Sales Team';

    /** Employee Roles */
    case Employee = 'employee'; // Standart Role, der User erhält diese Rolle automatisch bei der Registrierung um sich im Employee Panel anmelden zu können
    case Worker = 'Worker';
    case Manager = 'Manager';
    case Editor = 'Editor';
    case Temporary = 'Temporary';

    /** Partner Roles */
//    case Partner = 'partner'; // Standart Role, der User erhält diese Rolle automatisch bei der Registrierung um sich im Partner Panel anmelden zu können
//    case Akkordant = 'akkordant';
//    case Subunternehmer = 'subunternehmer';
//    case Lieferant = 'lieferant';
//    case Auftraggeber = 'auftraggeber';
//    case Bauherr = 'bauherr';

    case Partner = 'partner'; // Standart Role, der User erhält diese Rolle automatisch bei der Registrierung um sich im Partner Panel anmelden zu können
    case Partner1 = 'Pieceworker'; // Akkordant
    case Partner2 = 'Subcontractor'; // Subunternehmer
    case Partner3 = 'Supplier'; // Lieferant
    case Partner4 = 'Client'; // Auftraggeber
    case Partner5  = 'Building Owner'; // Bauherr

    /** Owner Roles */
    case Owner = 'owner'; // Standart Role, erhält jeder erstellte Owner User damit er sich im Owner Panel anmelden kann
    case Owner1 = 'Owner1';
    case Owner2 = 'Owner2';
    case Owner3 = 'Owner3';

    public function label(): string
    {
        return __("role.user_role.{$this->value}");
    }

}
