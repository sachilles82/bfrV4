<div class="sm:col-span-3" x-data="industryDropdownHandler()">
    <!-- Industry Dropdown -->
    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-white" for="industry">
        {{ __('Industry') }}
    </label>
    <div class="relative mt-2">
        <div
            class="block relative content-center w-full py-1.5 text-left bg-white dark:bg-white/5 border-0 ring-1 ring-inset ring-gray-300 dark:ring-white/10 rounded-md sm:text-sm sm:leading-5 focus:ring-2 focus-within:ring-inset focus:ring-indigo-600 dark:focus-within:ring-inset dark:focus-within:ring-indigo-500"
            :class="{'focus:ring-2 focus-within:ring-inset focus:ring-indigo-600 dark:focus-within:ring-inset dark:focus-within:ring-indigo-500': openIndustry, 'bg-gray-200 cursor-default': disabled}"
            @click.prevent="toggleSelect()"
            @click.away="closeSelect()"
            @keydown.escape="closeSelect()"
            @keydown.arrow-down.prevent="increaseIndex()"
            @keydown.arrow-up.prevent="decreaseIndex()"
            @keydown.enter="selectOption()">

            <div class="inline-block m-1 pl-2 text-sm text-gray-400" x-show="!selectedIndustryName"
                 x-text="'{{ __('Select an industry') }}'">&nbsp;</div>
            <div class="flex flex-wrap" x-cloak x-show="selectedIndustryName">
                <div class="text-gray-800 dark:text-gray-400 truncate px-2 py-0.5 my-0.5 flex items-center">
                    <div class="px-2 truncate" x-text="selectedIndustryName"></div>
                    <svg class="w-2.5 h-2.5 ms-2.5 justify-end" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="mt-1 w-full dark:bg-gray-800 shadow-md dark:border dark:border-gray-700 bg-white rounded-b-md absolute top-full left-0 z-30"
            x-show="openIndustry" x-cloak>
            <div class="relative z-30 w-full p-2 bg-white dark:bg-gray-800">
                <input type="search" x-model="searchIndustry" @click.prevent.stop="openIndustry=true"
                       placeholder="{{ __('Search..') }}"
                       class="block w-full px-2.5 pl-10 text-gray-900 placeholder:dark:text-gray-500 placeholder:text-gray-400 rounded-md text-sm border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 dark:focus:ring-indigo-500 dark:ring-gray-700/50 dark:bg-white/5 dark:text-white">
            </div>
            <div x-ref="dropdown" class="relative z-30 p-2 overflow-y-auto max-h-60">
                <div x-cloak x-show="filteredIndustries().length === 0"
                     class="text-gray-400 dark:text-gray-400 flex justify-center items-center">
                    {{ __('No results match your search') }}
                </div>
                <template x-for="industry in filteredIndustries()" :key="industry.id">
                    <div class="relative">
                        <div class="py-2 px-3 mb-1 rounded-lg text-sm cursor-pointer"
                             :class="{'dark:bg-gray-700/50 dark:text-gray-600 bg-gray-50 text-gray-600': selectedIndustryId === industry.id, 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300': selectedIndustryId !== industry.id}"
                             @click.prevent.stop="selectIndustry(industry)">
                            <span class="ml-2" x-text="industry.name"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>



<script>
    function industryDropdownHandler() {
        return {
            industries: @json($industries),  // Branchen aus dem Backend laden
            selectedIndustry: null,
            selectedIndustryId: null,
            selectedIndustryName: '',
            searchIndustry: '',
            openIndustry: false,
            currentIndexIndustry: -1,

            filteredIndustries() {
                if (!this.searchIndustry) {
                    return this.industries;
                }

                // Aufteilen der Suchanfrage in einzelne Begriffe
                const searchTerms = this.searchIndustry.toLowerCase().split(' ');

                return this.industries.filter(industry => {
                    // Konvertiert den Branchenname in Kleinbuchstaben und prüft auf alle Suchbegriffe
                    const industryName = industry.name.toLowerCase();
                    return searchTerms.every(term => industryName.includes(term));
                });
            },

            selectIndustry(industry) {
                this.selectedIndustry = industry;
                this.selectedIndustryId = industry.id;
                this.selectedIndustryName = industry.name;
                this.closeSelect();
            },

            toggleSelect() {
                this.openIndustry = !this.openIndustry;
                this.searchIndustry = '';
            },

            closeSelect() {
                this.openIndustry = false;
                this.currentIndexIndustry = -1;
            },

            increaseIndex() {
                const items = this.filteredIndustries();
                if (this.currentIndexIndustry < items.length - 1) {
                    this.currentIndexIndustry++;
                }
            },

            decreaseIndex() {
                if (this.currentIndexIndustry > 0) {
                    this.currentIndexIndustry--;
                }
            },

            selectOption() {
                const items = this.filteredIndustries();
                if (this.currentIndexIndustry >= 0 && this.currentIndexIndustry < items.length) {
                    this.selectIndustry(items[this.currentIndexIndustry]);
                }
            },
        };
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('industryDropdownHandler', industryDropdownHandler);
    });


</script>
