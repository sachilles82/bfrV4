{{--<th scope="col" class="relative border-b dark:border-gray-700/50 border-gray-200 px-7 sm:w-12 sm:px-6 rounded-tl-lg">--}}
{{--    <div x-data="checkAll"--}}
{{--         x-on:update-table.window="$refs.checkbox.checked = false; $refs.checkbox.indeterminate = false;"--}}
{{--         class="flex rounded-md shadow-sm">--}}
{{--        <input x-ref="checkbox" @change="handleCheck" type="checkbox"--}}
{{--               class="absolute top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-600 dark:checked:border-indigo-600 dark:focus:ring-offset-gray-800">--}}
{{--    </div>--}}
{{--</th>--}}

{{--@script--}}
{{--<script>--}}
{{--    Alpine.data('checkAll', () => ({--}}
{{--        init() {--}}
{{--            this.updateCheckAllState();--}}
{{--            this.$wire.$watch('selectedIds', () => {--}}
{{--                this.updateCheckAllState()--}}
{{--            })--}}
{{--            this.$wire.$watch('idsOnPage', () => {--}}
{{--                this.updateCheckAllState()--}}
{{--            })--}}
{{--        },--}}
{{--        updateCheckAllState() {--}}
{{--            if (this.pageIsSelected()) {--}}
{{--                this.$refs.checkbox.checked = true--}}
{{--                this.$refs.checkbox.indeterminate = false--}}
{{--            } else if (this.pageIsEmpty()) {--}}
{{--                this.$refs.checkbox.checked = false--}}
{{--                this.$refs.checkbox.indeterminate = false--}}
{{--            } else {--}}
{{--                this.$refs.checkbox.checked = false--}}
{{--                this.$refs.checkbox.indeterminate = true--}}
{{--            }--}}
{{--        },--}}
{{--        pageIsSelected() {--}}
{{--            return this.$wire.idsOnPage.every(id => this.$wire.selectedIds.includes(id));--}}
{{--        },--}}
{{--        pageIsEmpty() {--}}
{{--            return this.$wire.selectedIds.length === 0;--}}
{{--        },--}}
{{--        handleCheck(e) {--}}
{{--            e.target.checked ? this.selectAllItems() : this.deselectAll();--}}
{{--            this.$dispatch('check-all', e.target.checked);--}}
{{--        },--}}
{{--        selectAllItems() {--}}
{{--            this.$wire.idsOnPage.forEach(id => {--}}
{{--                if (!this.$wire.selectedIds.includes(id)) {--}}
{{--                    this.$wire.selectedIds.push(id);--}}
{{--                }--}}
{{--            });--}}
{{--        },--}}
{{--        deselectAll() {--}}
{{--            this.$wire.selectedIds = [];--}}
{{--        },--}}
{{--    }));--}}
{{--</script>--}}
{{--@endscript--}}




{{--<th scope="col" class="relative border-b dark:border-gray-700/50 border-gray-200 px-7 sm:w-12 sm:px-6 rounded-tl-lg">--}}
{{--    <div x-data="checkAll"--}}
{{--         x-on:update-table.window="$refs.checkbox.checked = false; $refs.checkbox.indeterminate = false;"--}}
{{--         class="flex rounded-md shadow-sm">--}}
{{--        <input x-ref="checkbox"--}}
{{--               @change="handleCheck"--}}
{{--               type="checkbox"--}}
{{--               class="absolute top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-600 dark:checked:border-indigo-600 dark:focus:ring-offset-gray-800">--}}
{{--    </div>--}}
{{--</th>--}}

{{--@script--}}
{{--<script>--}}
{{--    Alpine.data('checkAll', () => ({--}}
{{--        init() {--}}
{{--            // Initialize and watch for changes in the selectedIds and idsOnPage arrays.--}}
{{--            this.updateCheckAllState();--}}
{{--            this.$wire.$watch('selectedIds', () => {--}}
{{--                this.updateCheckAllState();--}}
{{--            });--}}
{{--            this.$wire.$watch('idsOnPage', () => {--}}
{{--                this.updateCheckAllState();--}}
{{--            });--}}
{{--        },--}}

{{--        // This is a new function to handle both events--}}
{{--        updateCheckbox() {--}}
{{--            this.$refs.checkbox.checked = false;--}}
{{--            this.$refs.checkbox.indeterminate = false;--}}
{{--            this.$nextTick(() => {--}}
{{--                this.updateCheckAllState();--}}
{{--            });--}}
{{--        },--}}
{{--        updateCheckAllState() {--}}
{{--            // First, check if there are any rows on the current page.--}}
{{--            if (!this.$wire.idsOnPage || this.$wire.idsOnPage.length === 0) {--}}
{{--                this.$refs.checkbox.checked = false;--}}
{{--                this.$refs.checkbox.indeterminate = false;--}}
{{--            }--}}
{{--            // If all rows on the page are selected, check the box.--}}
{{--            else if (this.pageIsSelected()) {--}}
{{--                this.$refs.checkbox.checked = true;--}}
{{--                this.$refs.checkbox.indeterminate = false;--}}
{{--            }--}}
{{--            // If no rows are selected, uncheck the box.--}}
{{--            else if (this.$wire.selectedIds.length === 0) {--}}
{{--                this.$refs.checkbox.checked = false;--}}
{{--                this.$refs.checkbox.indeterminate = false;--}}
{{--            }--}}
{{--            // Otherwise, some rows are selected so set the checkbox as indeterminate.--}}
{{--            else {--}}
{{--                this.$refs.checkbox.checked = false;--}}
{{--                this.$refs.checkbox.indeterminate = true;--}}
{{--            }--}}
{{--        },--}}
{{--        pageIsSelected() {--}}
{{--            // Only perform the check if there are rows on the page.--}}
{{--            if (!this.$wire.idsOnPage || this.$wire.idsOnPage.length === 0) {--}}
{{--                return false;--}}
{{--            }--}}
{{--            return this.$wire.idsOnPage.every(id => this.$wire.selectedIds.includes(id));--}}
{{--        },--}}
{{--        handleCheck(e) {--}}
{{--            // When the checkbox is toggled, either select or deselect all items.--}}
{{--            e.target.checked ? this.selectAllItems() : this.deselectAll();--}}
{{--            this.$dispatch('check-all', e.target.checked);--}}
{{--        },--}}
{{--        selectAllItems() {--}}
{{--            // Loop over idsOnPage and add any missing id into selectedIds.--}}
{{--            this.$wire.idsOnPage.forEach(id => {--}}
{{--                if (!this.$wire.selectedIds.includes(id)) {--}}
{{--                    this.$wire.selectedIds.push(id);--}}
{{--                }--}}
{{--            });--}}
{{--        },--}}
{{--        deselectAll() {--}}
{{--            // Clear all selected ids.--}}
{{--            this.$wire.selectedIds = [];--}}
{{--        },--}}
{{--    }));--}}
{{--</script>--}}
{{--@endscript--}}

<th scope="col" class="relative border-b dark:border-gray-700/50 border-gray-200 px-7 sm:w-12 sm:px-6 rounded-tl-lg">
    <div x-data="checkAll"
         x-on:update-table.window="handleTableUpdate"
         x-on:employee-updated.window="handleEmployeeUpdate"
         class="flex rounded-md shadow-sm">
        <input x-ref="checkbox"
               @change="handleCheckboxChange"
               type="checkbox"
               class="absolute top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-600 dark:checked:border-indigo-600 dark:focus:ring-offset-gray-800">
    </div>
</th>

@script
<script>
    Alpine.data('checkAll', () => ({
        init() {
            this.updateCheckboxState();

            // Überwache Änderungen an den ausgewählten IDs und den IDs auf der Seite
            this.$wire.$watch('selectedIds', () => {
                this.updateCheckboxState();
            });

            this.$wire.$watch('idsOnPage', () => {
                this.updateCheckboxState();
            });
        },

        // Handler für das update-table Event
        handleTableUpdate() {
            this.resetCheckbox();
            this.updateCheckboxState();
        },

        // Handler für das employee-updated Event
        handleEmployeeUpdate() {
            this.$nextTick(() => {
                this.updateCheckboxState();
            });
        },

        // Checkbox zurücksetzen
        resetCheckbox() {
            this.$refs.checkbox.checked = false;
            this.$refs.checkbox.indeterminate = false;
        },

        // Aktualisiert den Zustand der Checkbox basierend auf den Selektionen
        updateCheckboxState() {
            // Sicherstellen, dass es gültige Daten gibt
            if (!this.hasValidIdsOnPage()) {
                this.resetCheckbox();
                return;
            }

            if (this.areAllItemsSelected()) {
                this.setFullyCheckedState();
            } else if (this.areNoItemsSelected()) {
                this.setUncheckedState();
            } else {
                this.setIndeterminateState();
            }
        },

        // Prüft, ob es gültige IDs auf der Seite gibt
        hasValidIdsOnPage() {
            return this.$wire.idsOnPage && this.$wire.idsOnPage.length > 0;
        },

        // Prüft, ob alle Elemente auf der Seite ausgewählt sind
        areAllItemsSelected() {
            return this.$wire.idsOnPage.every(id => this.$wire.selectedIds.includes(id));
        },

        // Prüft, ob keine Elemente ausgewählt sind
        areNoItemsSelected() {
            return this.$wire.selectedIds.length === 0;
        },

        // Setzt den Zustand auf vollständig ausgewählt
        setFullyCheckedState() {
            this.$refs.checkbox.checked = true;
            this.$refs.checkbox.indeterminate = false;
        },

        // Setzt den Zustand auf nicht ausgewählt
        setUncheckedState() {
            this.$refs.checkbox.checked = false;
            this.$refs.checkbox.indeterminate = false;
        },

        // Setzt den Zustand auf teilweise ausgewählt
        setIndeterminateState() {
            this.$refs.checkbox.checked = false;
            this.$refs.checkbox.indeterminate = true;
        },

        // Handler für Änderungen an der Checkbox
        handleCheckboxChange(e) {
            if (e.target.checked) {
                this.selectAllItems();
            } else {
                this.deselectAllItems();
            }

            // check-all Event senden, um Zeilen-Checkboxen zu aktualisieren
            this.$dispatch('check-all', e.target.checked);
        },

        // Wählt alle Elemente auf der aktuellen Seite aus
        selectAllItems() {
            // Neue Array erstellen, um Referenzprobleme zu vermeiden
            const updatedSelection = [...this.$wire.selectedIds];

            // Fehlende IDs hinzufügen
            this.$wire.idsOnPage.forEach(id => {
                if (!updatedSelection.includes(id)) {
                    updatedSelection.push(id);
                }
            });

            // Livewire-Status aktualisieren
            this.$wire.selectedIds = updatedSelection;
        },

        // Wählt alle Elemente ab
        deselectAllItems() {
            this.$wire.selectedIds = [];
        },
    }));
</script>
@endscript
