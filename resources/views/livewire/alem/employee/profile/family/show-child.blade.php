<div class="flex flex-col gap-8 min-w-[40rem]">
    <h1 class="text-3xl font-semibold leading-6 text-slate-900">Blog childs</h1>

    <div class="shadow rounded-xl overflow-hidden bg-white">
        <table class="min-w-full divide-y divide-slate-300">
            <thead class="bg-slate-50 py-2">
            <tr class="text-left text-slate-800">
                <th class="pl-6 py-4 font-semibold">Title</th>
                <th class="pl-4 py-4 font-semibold">Content</th>
                <th class="pl-4 pr-4">
                    ffg
                    <livewire:alem.employee.profile.family.add-child-dialog
                        :user-id="$userId"
                        @added="$refresh" />
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200" wire:loading.class="opacity-50">
            @foreach ($children as $child)
                <livewire:alem.employee.profile.family.child-row :key="$child->id" :$child @deleted="delete({{ $child->id }})" />
            @endforeach
            </tbody>
        </table>
    </div>
</div>
