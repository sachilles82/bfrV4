<x-game-layout>
{{--    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">--}}
{{--        <!-- Content goes here -->--}}
{{--        <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow">--}}
{{--            <div class="px-4 py-5 sm:px-6">--}}
{{--                <!-- Content goes here -->--}}
{{--                <!-- We use less vertical padding on card headers on desktop than on body sections -->--}}
{{--            </div>--}}
{{--            <div class="px-4 py-5 sm:p-6 max-h-24">--}}
{{--                <!-- Content goes here -->--}}
{{--                <comic-gen name="ethan" angle="side" emotion="wink" pose="normal" ext="svg"></comic-gen>--}}
{{--            </div>--}}
{{--            <div class="px-4 py-4 sm:px-6">--}}
{{--                <!-- Content goes here -->--}}
{{--                <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->--}}
{{--            </div>--}}
{{--        </div>--}}


{{--    </div>--}}



{{--    <!---->--}}
{{--    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto bg-pink-100 border-2 overflow-hidden">--}}

{{--        <!-- Grid -->--}}
{{--        <div class="grid sm:grid-cols-2 sm:items-center gap-8">--}}
{{--            <!-- IMAGE on top for mobile, on the right for desktop -->--}}
{{--            <div class="order-1 sm:order-2">--}}
{{--                <div class="relative pt-[100%] sm:pt-[100%] rounded-lg">--}}
{{--                    <img--}}
{{--                        class="size-full absolute top-0 start-0 w-full h-full object-cover rounded-lg"--}}
{{--                        id="situation-image"--}}
{{--                        src=""--}}
{{--                        alt="Scenario Image"--}}
{{--                    >--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- End Image Col -->--}}

{{--            <!-- QUIZ on bottom for mobile, on the left for desktop -->--}}
{{--            <div class="order-2 sm:order-1 space-y-6">--}}
{{--                <h2--}}
{{--                    class="text-2xl font-bold md:text-3xl lg:text-4xl lg:leading-tight xl:text-5xl xl:leading-tight text-gray-800 dark:text-neutral-200"--}}
{{--                    id="situation-title"--}}
{{--                >--}}
{{--                    Situation-Titel--}}
{{--                </h2>--}}

{{--                <!-- Answer Slots -->--}}
{{--                <div>--}}
{{--                    <h3 class="text-lg font-semibold text-gray-700 dark:text-neutral-200 mb-2">--}}
{{--                        Antwort-Slots--}}
{{--                    </h3>--}}
{{--                    <div id="answer-slots" class="grid grid-cols-1 sm:grid-cols-2 gap-2">--}}
{{--                        <div--}}
{{--                            class="slot border rounded p-2 text-gray-800 dark:text-neutral-100 bg-white dark:bg-neutral-700"--}}
{{--                            data-slot="0"--}}
{{--                            data-selected-index="-1"--}}
{{--                        >--}}
{{--                            1. (Leer)--}}
{{--                        </div>--}}
{{--                        <div--}}
{{--                            class="slot border rounded p-2 text-gray-800 dark:text-neutral-100 bg-white dark:bg-neutral-700"--}}
{{--                            data-slot="1"--}}
{{--                            data-selected-index="-1"--}}
{{--                        >--}}
{{--                            2. (Leer)--}}
{{--                        </div>--}}
{{--                        <div--}}
{{--                            class="slot border rounded p-2 text-gray-800 dark:text-neutral-100 bg-white dark:bg-neutral-700"--}}
{{--                            data-slot="2"--}}
{{--                            data-selected-index="-1"--}}
{{--                        >--}}
{{--                            3. (Leer)--}}
{{--                        </div>--}}
{{--                        <div--}}
{{--                            class="slot border rounded p-2 text-gray-800 dark:text-neutral-100 bg-white dark:bg-neutral-700"--}}
{{--                            data-slot="3"--}}
{{--                            data-selected-index="-1"--}}
{{--                        >--}}
{{--                            4. (Leer)--}}
{{--                        </div>--}}
{{--                        <div--}}
{{--                            class="slot border rounded p-2 text-gray-800 dark:text-neutral-100 bg-white dark:bg-neutral-700 sm:col-span-2"--}}
{{--                            data-slot="4"--}}
{{--                            data-selected-index="-1"--}}
{{--                        >--}}
{{--                            5. (Leer)--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- End Slots -->--}}

{{--                <!-- Options -->--}}
{{--                <div>--}}
{{--                    <h3 class="text-lg font-semibold text-gray-700 dark:text-neutral-200 mb-2">--}}
{{--                        Optionen--}}
{{--                    </h3>--}}
{{--                    <div id="options-container" class="flex flex-wrap gap-2">--}}
{{--                        <!-- Populated by JS -->--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- End Options -->--}}

{{--                <!-- Buttons + Status -->--}}
{{--                <div class="flex items-center gap-4">--}}
{{--                    <button--}}
{{--                        id="check-button"--}}
{{--                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"--}}
{{--                    >--}}
{{--                        Prüfen--}}
{{--                    </button>--}}
{{--                    <button--}}
{{--                        id="reset-button"--}}
{{--                        class="inline-flex items-center justify-center rounded-md border border-blue-600 bg-white px-4 py-2 text-sm font-medium text-blue-600 shadow-sm hover:bg-gray-50"--}}
{{--                    >--}}
{{--                        Neustart--}}
{{--                    </button>--}}
{{--                </div>--}}

{{--                <div--}}
{{--                    id="status-message"--}}
{{--                    class="font-bold text-red-600 dark:text-red-400 min-h-[1.5rem]"--}}
{{--                ></div>--}}
{{--            </div>--}}
{{--            <!-- End Quiz Col -->--}}
{{--        </div>--}}
{{--        <!-- End Grid -->--}}
{{--    </div>--}}

{{--    <!-- JS: Quiz Logic -->--}}
{{--    <script>--}}
{{--        // 1) Define up to 12 scenarios (hier nur beispielhaft einige dupliziert).--}}
{{--        //    Passen nachher natürlich auf deine wahren Szenarien an:--}}
{{--        const scenarios = [--}}
{{--            // Level 1--}}
{{--            {--}}
{{--                title: "Situation 1: Bushaltestelle",--}}
{{--                image: "{{ asset('images/busstation.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Hallo",--}}
{{--                    "Mein Name ist Jake.",--}}
{{--                    "Ich weiß, du fragst dich: Wer ist Jack? Und was will er?",--}}
{{--                    "Oder du sagst dir: Warum spricht er mich an?",--}}
{{--                    "Oder??"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 2--}}
{{--            {--}}
{{--                title: "Situation 2: Im Stadtpark",--}}
{{--                image: "{{ asset('images/park.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Hi! Es ist so ein schöner Tag!",--}}
{{--                    "Mein Name ist Jake!",--}}
{{--                    "Du fragst dich bestimmt warum ich mich vorstelle und was ich von dir will?",--}}
{{--                    "Vielleicht denkst du dir.. OMG! was will der Typ von mir?",--}}
{{--                    "oder?"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 3--}}
{{--            {--}}
{{--                title: "Situation 3: Am Bahnhof",--}}
{{--                image: "{{ asset('images/bahnhof.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Wow, was für ein schönes Lächeln?",--}}
{{--                    "Ich bin Jake!",--}}
{{--                    "Es quatchen dich bestimmt viele Typen an",--}}
{{--                    "Und du denkt dir. Schon wieder einer, was will dieser von mir?",--}}
{{--                    "stimmts?"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 4--}}
{{--            {--}}
{{--                title: "Situation 4: Im Supermarkt",--}}
{{--                image: "{{ asset('images/supermarkt.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Hallo, kann ich kurz vorbei?",--}}
{{--                    "Ich brauche nur eine Packung Milch.",--}}
{{--                    "Hast du vielleicht schon gesehen, wo die Eier stehen?",--}}
{{--                    "Ach, dort hinten im Regal!",--}}
{{--                    "Danke, bis dann!"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 5--}}
{{--            {--}}
{{--                title: "Situation 5: Beim Bäcker",--}}
{{--                image: "{{ asset('images/baeckerei.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Guten Morgen!",--}}
{{--                    "Ich nehme zwei Brötchen.",--}}
{{--                    "Kannst du mir noch ein Croissant dazugeben?",--}}
{{--                    "Danke, das wäre alles.",--}}
{{--                    "Schönen Tag noch!"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 6--}}
{{--            {--}}
{{--                title: "Situation 6: Am Strand",--}}
{{--                image: "{{ asset('images/strand.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Wow, hier ist es richtig warm.",--}}
{{--                    "Ich liebe das Meeresrauschen.",--}}
{{--                    "Hast du schon ein kühles Getränk?",--}}
{{--                    "Ich gehe später schwimmen.",--}}
{{--                    "Willst du mitkommen?"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 7 (Beispiel: Wiederholung oder etwas Neues)--}}
{{--            {--}}
{{--                title: "Situation 7: Bushaltestelle (2)",--}}
{{--                image: "{{ asset('images/busstation.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Level 7 Satz 1",--}}
{{--                    "Level 7 Satz 2",--}}
{{--                    "Level 7 Satz 3",--}}
{{--                    "Level 7 Satz 4",--}}
{{--                    "Level 7 Satz 5"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 8--}}
{{--            {--}}
{{--                title: "Situation 8: Im Stadtpark (2)",--}}
{{--                image: "{{ asset('images/park.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Level 8 Satz 1",--}}
{{--                    "Level 8 Satz 2",--}}
{{--                    "Level 8 Satz 3",--}}
{{--                    "Level 8 Satz 4",--}}
{{--                    "Level 8 Satz 5"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 9--}}
{{--            {--}}
{{--                title: "Situation 9: Am Bahnhof (2)",--}}
{{--                image: "{{ asset('images/bahnhof.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Level 9 Satz 1",--}}
{{--                    "Level 9 Satz 2",--}}
{{--                    "Level 9 Satz 3",--}}
{{--                    "Level 9 Satz 4",--}}
{{--                    "Level 9 Satz 5"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 10--}}
{{--            {--}}
{{--                title: "Situation 10: Im Supermarkt (2)",--}}
{{--                image: "{{ asset('images/supermarkt.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Level 10 Satz 1",--}}
{{--                    "Level 10 Satz 2",--}}
{{--                    "Level 10 Satz 3",--}}
{{--                    "Level 10 Satz 4",--}}
{{--                    "Level 10 Satz 5"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 11--}}
{{--            {--}}
{{--                title: "Situation 11: Beim Bäcker (2)",--}}
{{--                image: "{{ asset('images/baeckerei.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Level 11 Satz 1",--}}
{{--                    "Level 11 Satz 2",--}}
{{--                    "Level 11 Satz 3",--}}
{{--                    "Level 11 Satz 4",--}}
{{--                    "Level 11 Satz 5"--}}
{{--                ]--}}
{{--            },--}}
{{--            // Level 12--}}
{{--            {--}}
{{--                title: "Situation 12: Am Strand (2)",--}}
{{--                image: "{{ asset('images/strand.jpg') }}",--}}
{{--                correctOrder: [--}}
{{--                    "Level 12 Satz 1",--}}
{{--                    "Level 12 Satz 2",--}}
{{--                    "Level 12 Satz 3",--}}
{{--                    "Level 12 Satz 4",--}}
{{--                    "Level 12 Satz 5"--}}
{{--                ]--}}
{{--            },--}}
{{--        ];--}}

{{--        let currentScenarioIndex = 0;--}}
{{--        let shuffledSentences = [];--}}

{{--        // Grab DOM elements--}}
{{--        const situationTitle = document.getElementById('situation-title');--}}
{{--        const situationImage = document.getElementById('situation-image');--}}
{{--        const slotsContainer = document.getElementById('answer-slots');--}}
{{--        const optionsContainer = document.getElementById('options-container');--}}
{{--        const checkButton = document.getElementById('check-button');--}}
{{--        const resetButton = document.getElementById('reset-button');--}}
{{--        const statusMessage = document.getElementById('status-message');--}}

{{--        // Level-Buttons--}}
{{--        const levelButtons = document.querySelectorAll('.level-btn');--}}

{{--        // On page load--}}
{{--        window.onload = () => {--}}
{{--            // Falls du willst, dass beim Laden direkt Level 1 startet:--}}
{{--            initScenario(0);--}}
{{--        };--}}

{{--        // LEVEL-AUSWAHL-KLICK--}}
{{--        levelButtons.forEach(btn => {--}}
{{--            btn.addEventListener('click', () => {--}}
{{--                const level = parseInt(btn.getAttribute('data-level'), 10);--}}
{{--                // Level 1 -> Index 0, Level 2 -> Index 1, ...--}}
{{--                initScenario(level - 1);--}}
{{--            });--}}
{{--        });--}}

{{--        // FUNCTIONS--}}
{{--        function initScenario(index) {--}}
{{--            if (index < 0 || index >= scenarios.length) return;--}}
{{--            currentScenarioIndex = index;--}}

{{--            const scenario = scenarios[index];--}}
{{--            situationTitle.textContent = scenario.title;--}}
{{--            situationImage.src = scenario.image;--}}

{{--            // Clear & fill--}}
{{--            clearSlots();--}}
{{--            shuffledSentences = shuffleArray([...scenario.correctOrder]);--}}
{{--            renderOptions(shuffledSentences);--}}

{{--            statusMessage.textContent = "";--}}
{{--        }--}}

{{--        function clearSlots() {--}}
{{--            const slotDivs = slotsContainer.querySelectorAll('.slot');--}}
{{--            slotDivs.forEach((slot, idx) => {--}}
{{--                slot.textContent = (idx + 1) + ". (Leer)";--}}
{{--                slot.setAttribute('data-selected-index', '-1');--}}
{{--            });--}}
{{--        }--}}

{{--        function renderOptions(sentences) {--}}
{{--            optionsContainer.innerHTML = "";--}}
{{--            sentences.forEach((sentence, index) => {--}}
{{--                const div = document.createElement('div');--}}
{{--                div.classList.add(--}}
{{--                    'option',--}}
{{--                    'border',--}}
{{--                    'rounded',--}}
{{--                    'p-2',--}}
{{--                    'cursor-pointer',--}}
{{--                    'hover:bg-gray-100',--}}
{{--                    'transition'--}}
{{--                );--}}
{{--                div.textContent = sentence;--}}

{{--                div.setAttribute('data-index', index);--}}
{{--                div.setAttribute('data-selected', 'false');--}}

{{--                optionsContainer.appendChild(div);--}}
{{--            });--}}
{{--        }--}}

{{--        function shuffleArray(array) {--}}
{{--            for (let i = array.length - 1; i > 0; i--) {--}}
{{--                const j = Math.floor(Math.random() * (i + 1));--}}
{{--                [array[i], array[j]] = [array[j], array[i]];--}}
{{--            }--}}
{{--            return array;--}}
{{--        }--}}

{{--        function checkOrder() {--}}
{{--            const scenario = scenarios[currentScenarioIndex];--}}
{{--            const correctOrder = scenario.correctOrder;--}}
{{--            const slotDivs = slotsContainer.querySelectorAll('.slot');--}}
{{--            const userOrder = [];--}}

{{--            slotDivs.forEach(slot => {--}}
{{--                const selectedIndex = parseInt(slot.getAttribute('data-selected-index'), 10);--}}
{{--                if (selectedIndex === -1) {--}}
{{--                    userOrder.push(null);--}}
{{--                } else {--}}
{{--                    userOrder.push(shuffledSentences[selectedIndex]);--}}
{{--                }--}}
{{--            });--}}

{{--            // All filled?--}}
{{--            if (userOrder.includes(null)) {--}}
{{--                statusMessage.textContent = "Bitte alle Slots befüllen!";--}}
{{--                return;--}}
{{--            }--}}

{{--            // Compare--}}
{{--            for (let i = 0; i < correctOrder.length; i++) {--}}
{{--                if (userOrder[i] !== correctOrder[i]) {--}}
{{--                    statusMessage.textContent = "Falsche Reihenfolge! Game Over!";--}}
{{--                    return;--}}
{{--                }--}}
{{--            }--}}

{{--            // Correct--}}
{{--            if (currentScenarioIndex < scenarios.length - 1) {--}}
{{--                statusMessage.textContent = "Richtig! Weiter zur nächsten Situation...";--}}
{{--                setTimeout(() => {--}}
{{--                    initScenario(currentScenarioIndex + 1);--}}
{{--                }, 1500);--}}
{{--            } else {--}}
{{--                statusMessage.textContent = "Gratulation! Du hast alle Situationen gemeistert!";--}}
{{--            }--}}
{{--        }--}}

{{--        // EVENT LISTENERS--}}
{{--        checkButton.addEventListener('click', checkOrder);--}}

{{--        resetButton.addEventListener('click', () => {--}}
{{--            initScenario(currentScenarioIndex);--}}
{{--        });--}}

{{--        optionsContainer.addEventListener('click', (e) => {--}}
{{--            if (e.target.classList.contains('option')) {--}}
{{--                const optionDiv = e.target;--}}
{{--                const isSelected = optionDiv.getAttribute('data-selected') === 'true';--}}
{{--                const index = parseInt(optionDiv.getAttribute('data-index'), 10);--}}

{{--                if (!isSelected) {--}}
{{--                    const freeSlot = findFreeSlot();--}}
{{--                    if (freeSlot) {--}}
{{--                        fillSlot(freeSlot, index);--}}
{{--                        optionDiv.classList.add('opacity-50');--}}
{{--                        optionDiv.setAttribute('data-selected', 'true');--}}
{{--                    } else {--}}
{{--                        statusMessage.textContent = "Alle Slots sind belegt.";--}}
{{--                    }--}}
{{--                } else {--}}
{{--                    removeFromSlot(index);--}}
{{--                    optionDiv.classList.remove('opacity-50');--}}
{{--                    optionDiv.setAttribute('data-selected', 'false');--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        slotsContainer.addEventListener('click', (e) => {--}}
{{--            if (e.target.classList.contains('slot')) {--}}
{{--                const selectedIndex = parseInt(e.target.getAttribute('data-selected-index'), 10);--}}
{{--                if (selectedIndex !== -1) {--}}
{{--                    removeFromSlot(selectedIndex);--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        function findFreeSlot() {--}}
{{--            const slotDivs = slotsContainer.querySelectorAll('.slot');--}}
{{--            for (let slot of slotDivs) {--}}
{{--                const selectedIndex = parseInt(slot.getAttribute('data-selected-index'), 10);--}}
{{--                if (selectedIndex === -1) {--}}
{{--                    return slot;--}}
{{--                }--}}
{{--            }--}}
{{--            return null;--}}
{{--        }--}}

{{--        function fillSlot(slotElem, optionIndex) {--}}
{{--            const slotNumber = parseInt(slotElem.getAttribute('data-slot'), 10) + 1;--}}
{{--            slotElem.textContent = slotNumber + ". " + shuffledSentences[optionIndex];--}}
{{--            slotElem.setAttribute('data-selected-index', optionIndex.toString());--}}
{{--        }--}}

{{--        function removeFromSlot(optionIndex) {--}}
{{--            // Clear the slot--}}
{{--            const slotDivs = slotsContainer.querySelectorAll('.slot');--}}
{{--            for (let slot of slotDivs) {--}}
{{--                if (parseInt(slot.getAttribute('data-selected-index'), 10) === optionIndex) {--}}
{{--                    const slotNumber = parseInt(slot.getAttribute('data-slot'), 10) + 1;--}}
{{--                    slot.textContent = slotNumber + ". (Leer)";--}}
{{--                    slot.setAttribute('data-selected-index', '-1');--}}
{{--                    break;--}}
{{--                }--}}
{{--            }--}}
{{--            // Reactivate option--}}
{{--            const optionDivs = optionsContainer.querySelectorAll('.option');--}}
{{--            for (let opt of optionDivs) {--}}
{{--                if (parseInt(opt.getAttribute('data-index'), 10) === optionIndex) {--}}
{{--                    opt.classList.remove('opacity-50');--}}
{{--                    opt.setAttribute('data-selected', 'false');--}}
{{--                    break;--}}
{{--                }--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
</x-game-layout>
