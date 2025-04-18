<th scope="col" class="relative border-b dark:border-gray-700/50 border-gray-200 px-7 sm:w-12 sm:px-6 rounded-tl-lg">
    <div x-data="checkAll"
         x-on:update-table.window="handleTableUpdate"
         x-on:employee-updated.window="handleEmployeeUpdate"
         class="flex rounded-md shadow-xs">
        <div class="group grid size-4 grid-cols-1">
            <input x-ref="checkbox"
                   @change="handleCheckboxChange"
                   type="checkbox"
                   class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-600 dark:checked:border-indigo-600 dark:focus:ring-offset-gray-800">
            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
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
            if (!this.$refs.checkbox) return;
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
            if (!this.$refs.checkbox) return;
            this.$refs.checkbox.checked = true;
            this.$refs.checkbox.indeterminate = false;
        },

        // Setzt den Zustand auf nicht ausgewählt
        setUncheckedState() {
            if (!this.$refs.checkbox) return;
            this.$refs.checkbox.checked = false;
            this.$refs.checkbox.indeterminate = false;
        },

        // Setzt den Zustand auf teilweise ausgewählt
        setIndeterminateState() {
            if (!this.$refs.checkbox) return;
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
