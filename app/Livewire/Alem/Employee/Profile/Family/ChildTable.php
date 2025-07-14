<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Models\Alem\Child;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class ChildTable extends Component
{
    use AuthorizesRequests;
    use WithPerPagePagination;

    #[Locked]
    public ?int $userId = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

//    public function delete($childId): void
//    {
//        try {
//            $child = Child::where('id', $childId)
//                ->where('user_id', $this->userId)
//                ->first();
//
//            if (!$child) {
//                throw new \Exception('Child not found or unauthorized');
//            }
//
//            //Authorize the action
//
//            $childName = $child->name;
//            $child->delete();
//
//            $this->resetPage();
//
//            Flux::toast(
//                text: __('Child :name removed successfully.', ['name' => $childName]),
//                heading: __('Success'),
//                variant: 'success'
//            );
//
//        } catch (\Throwable $e) {
//            Flux::toast(
//                text: __('Error deleting child.'),
//                heading: __('Error'),
//                variant: 'danger'
//            );
//        }
//    }

    public function delete($childId): void
    {
        // ✅ RATE LIMITING
        // Auskommentiert wie gewünscht - einfach entkommentieren wenn bereit
        /*
        $rateLimitKey = 'delete-child:' . auth()->id() . ':' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            Flux::toast(
                text: __('Too many delete attempts. Please wait.'),
                heading: __('Rate Limit'),
                variant: 'warning'
            );

            // IP Logging bei Rate Limit Überschreitung
            Log::warning('Rate limit exceeded for child deletion', [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'attempted_child_id' => $childId
            ]);

            return;
        }
        RateLimiter::hit($rateLimitKey, 60); // 5 Versuche pro Minute
        */

        try {
            DB::transaction(function () use ($childId) {
                // ✅ LOCK FOR UPDATE - Verhindert Race Conditions
                $child = Child::where('id', $childId)
                    ->where('user_id', $this->userId)
                    ->lockForUpdate() // <-- HIER: Sperrt die Row bis Transaction fertig
                    ->first();

                if (!$child) {
                    throw new ModelNotFoundException('Child not found or unauthorized');
                }

                // ✅ AUTHORIZATION via Spatie Permissions
                // Option 1: Direct Permission Check
//                if (!auth()->user()->can('delete-children')) {
//                    abort(403, 'Unauthorized to delete children');
//                }

                $childName = $child->name;

                // ✅ AUDIT LOGGING mit Spatie Activity Log
                // Auskommentiert - aktiviere wenn spatie/laravel-activitylog installiert
                /*
                activity()
                    ->performedOn($child)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'child_name' => $childName,
                        'parent_user_id' => $this->userId,
                        'ip_address' => request()->ip(), // ✅ IP LOGGING
                        'user_agent' => request()->userAgent(),
                        'deleted_at' => now()->toDateTimeString(),
                    ])
                    ->log('Child deleted from family records');
                */

                // Eigentliche Löschung
                $child->delete();

                Flux::toast(
                    text: __('Child :name removed successfully.', ['name' => $childName]),
                    heading: __('Success'),
                    variant: 'success'
                );
            }); // Ende der Transaction - Lock wird automatisch freigegeben

            $this->resetPage();

        } catch (ModelNotFoundException $e) {
            // ✅ Security Logging bei verdächtigen Aktivitäten
            Log::warning('Attempted to delete non-existent or unauthorized child', [
                'attempted_child_id' => $childId,
                'user_id' => auth()->id(),
                'parent_user_id' => $this->userId,
                'ip' => request()->ip(),
                'error' => $e->getMessage()
            ]);

            Flux::toast(
                text: __('Child not found or unauthorized.'),
                heading: __('Error'),
                variant: 'danger'
            );

        } catch (\Throwable $e) {
            // ✅ Error Logging mit IP
            Log::error('Error deleting child', [
                'child_id' => $childId,
                'user_id' => auth()->id(),
                'parent_user_id' => $this->userId,
                'ip' => request()->ip(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Flux::toast(
                text: __('Error deleting child.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        $children = Child::query()
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'name',
                'gender',
                'birthdate',
                'ahv_number',
                'valid_until',
                'user_id',
            ])
            ->latest()
            ->simplePaginate($this->perPage);

        return view('livewire.alem.employee.profile.family.child-table', [
            'children' => $children
        ]);
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.employee.family.child-table');
    }
}
