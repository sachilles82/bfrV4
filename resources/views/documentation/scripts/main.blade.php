<script>
    function documentation() {
        return {
            activeSection: 'overview',
            methodFilter: 'all',

            get filteredMethods() {
                if (this.methodFilter === 'all') {
                    return this.methods;
                }
                return this.methods.filter(method => method.category === this.methodFilter);
            },

            supervisorComponents: [
                {
                    name: 'UserHasSupervisor Trait',
                    type: 'Trait',
                    description: 'Stellt alle supervisor-relevanten Methoden zur Verfügung',
                    code: 'use App\\Traits\\User\\UserHasSupervisor;'
                },
                // Weitere Komponenten...
            ],

            // Alle anderen Datenstrukturen hier einfügen...
            traits: [],
            relationships: [],
            attributes: [],
            methods: [],
            implementationSteps: []
        }
    }
</script>
