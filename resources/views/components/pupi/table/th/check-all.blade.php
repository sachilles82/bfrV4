<th scope="col" class="relative border-b dark:border-gray-700/50 border-gray-200 px-7 sm:w-12 sm:px-6 rounded-tl-lg">
    <div x-data="checkAll"
         x-on:update-table.window="$refs.checkbox.checked = false; $refs.checkbox.indeterminate = false;"
         class="flex rounded-md shadow-sm">
        <input x-ref="checkbox" @change="handleCheck" type="checkbox"
               class="absolute top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-600 dark:checked:border-indigo-600 dark:focus:ring-offset-gray-800">
    </div>
</th>

@script
<script>
    Alpine.data('checkAll', () => ({
        init() {
            this.updateCheckAllState();
            this.$wire.$watch('selectedIds', () => {
                this.updateCheckAllState()
            })
            this.$wire.$watch('idsOnPage', () => {
                this.updateCheckAllState()
            })
        },
        updateCheckAllState() {
            if (this.pageIsSelected()) {
                this.$refs.checkbox.checked = true
                this.$refs.checkbox.indeterminate = false
            } else if (this.pageIsEmpty()) {
                this.$refs.checkbox.checked = false
                this.$refs.checkbox.indeterminate = false
            } else {
                this.$refs.checkbox.checked = false
                this.$refs.checkbox.indeterminate = true
            }
        },
        pageIsSelected() {
            return this.$wire.idsOnPage.every(id => this.$wire.selectedIds.includes(id));
        },
        pageIsEmpty() {
            return this.$wire.selectedIds.length === 0;
        },
        handleCheck(e) {
            e.target.checked ? this.selectAllItems() : this.deselectAll();
            this.$dispatch('check-all', e.target.checked);
        },
        selectAllItems() {
            this.$wire.idsOnPage.forEach(id => {
                if (!this.$wire.selectedIds.includes(id)) {
                    this.$wire.selectedIds.push(id);
                }
            });
        },
        deselectAll() {
            this.$wire.selectedIds = [];
        },
    }));
</script>
@endscript
