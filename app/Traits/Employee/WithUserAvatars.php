<?php

namespace App\Traits\Employee;

/**
 * Trait um die User in verschiedenen Tables anzuzeigen
 *
 * Einfach den User im Livewire-Component laden und den Trait hinzufügen
 */
trait WithUserAvatars
{
    /**
     * Bereitet die Benutzerdaten für die Anzeige der Avatare vor
     *
     * @param  \App\Models\Alem\Department  $department
     */
    public function prepareUserAvatars($department): array
    {
        $userCount = $department->users->count();

        $result = [
            'has_users' => $userCount > 0,
            'total_count' => $userCount,
            'visible_users' => [],
            'remaining_count' => 0,
            'remaining_user_groups' => [],
        ];

        if ($result['has_users']) {
            // Sichtbare Benutzer (max 3)
            $visibleUsers = $department->users->take(3);

            foreach ($visibleUsers as $index => $user) {
                $result['visible_users'][] = [
                    'name' => $user->name,
                    'last_name' => $user->last_name ?? '',
                    'full_name' => trim($user->name.' '.($user->last_name ?? '')),
                    'avatar_url' => 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF',
                    'z_index' => 30 - ($index + 1) * 10,
                ];
            }

            // Restliche Benutzer für den Tooltip
            if ($userCount > 3) {
                $result['remaining_count'] = $userCount - 3;

                // Bereite jeden verbleibenden Benutzer als separaten Eintrag für den Tooltip vor
                $remainingUsers = $department->users->skip(3);

                foreach ($remainingUsers as $user) {
                    $result['remaining_user_groups'][] = trim($user->name.' '.($user->last_name ?? ''));
                }
            }
        }

        return $result;
    }
}
