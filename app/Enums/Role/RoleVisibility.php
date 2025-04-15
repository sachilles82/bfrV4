<?php

namespace App\Enums\Role;

enum RoleVisibility: string
{
    /** Diese Enums werden in der Migrationstabelle von roles verwendet im Feld visible (Sichtbarkeit) gespeichert
     *
     * Sie zeigen welche Rolle in welchem Panel sichtbar oder unsichtbar sein darf
     *
     * Das brauche ich damit bei der Erstellung einer Role mit einem Formular die Standart Rolen aus
     * dem RoleEnum nicht gezeigt werden
     */
    case VisibleInNova = 'visible_in_nova';
    case HiddenInNova = 'hidden_in_nova';

    case Visible = 'visible';
    case Hidden = 'hidden';

}
