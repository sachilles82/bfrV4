<x-app-layout>
    <style>
        /* BASIC RESET & LAYOUT */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        /*body {*/
        /*    background: #f0f0f0;*/
        /*    min-height: 100vh;*/
        /*    display: flex;*/
        /*    flex-direction: column;*/
        /*    align-items: center;*/
        /*    padding: 20px;*/
        /*    color: #333;*/
        /*}*/
        h1 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            text-align: center;
        }

        /* Top row: left = girl image, right = boy image */
        #topRow {
            width: 100%;
            max-width: 900px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .char-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #999;
        }

        /* 3-Column Layout: left => arguments, center => chat, right => scoreboard */
        #mainWrapper {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 10px;
            min-height: 600px;
        }

        /* LEFT column => arguments or reference info */
        #leftCol {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
        }
        #leftCol h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        #argumentsList {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* CENTER => chat area */
        #centerCol {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 20px;
        }

        /* Chat area: a scrollable list of messages */
        #chatHistory {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 10px;
            padding-right: 10px; /* space for scrollbar */
        }
        .chat-bubble {
            max-width: 70%;
            margin-bottom: 10px;
            padding: 10px 14px;
            border-radius: 15px;
            line-height: 1.4;
            position: relative;
            clear: both;
        }
        .girl-bubble {
            background: #ffc0cb; /* pink bubble */
            margin-left: 0;
            margin-right: auto;
            text-align: left;
        }
        .boy-bubble {
            background: #87ceeb; /* light-blue bubble */
            margin-left: auto;
            margin-right: 0;
            text-align: right;
        }

        /* Answers below chat */
        #answersContainer {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }
        .answer-btn {
            background: #0066cc;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-align: left;
            transition: background 0.3s;
        }
        .answer-btn:hover {
            background: #005bb5;
        }

        #resultMsg {
            margin-top: 10px;
            color: #f00;
            font-style: italic;
        }
        #restartBtn {
            display: none;
            margin-top: 10px;
            padding: 10px 14px;
            background: #ff9800;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #restartBtn:hover {
            background: #e68900;
        }

        /* RIGHT => scoreboard with 12 milestone buttons */
        #rightCol {
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 6px;
            padding: 10px;
        }
        #rightCol h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        #scoreboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .milestone-btn {
            width: 60px;
            height: 60px;
            background: #ddd;
            border-radius: 9999px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            border: 2px solid transparent;
            text-align: center;
            font-size: 0.85rem;
            box-shadow: 0 2px 3px rgba(0,0,0,0.2);
        }
        .milestone-btn.active {
            border-color: #0066cc; /* highlight active stage */
        }
        .milestone-label {
            font-size: 0.7rem;
            margin-top: 2px;
        }
    </style>
    </head>
    <body>
    <h1>Flirt Chat: 12 Milestones (Answers Below Chat)</h1>

    <!-- Top images: Girl left, Boy right -->
    <div id="topRow">
        <img alt="Girl"
             class="char-img"src="{{ asset('images/selfie.jpg') }}"/>

        <img
            src="https://img.freepik.com/free-photo/portrait-man-cartoon-style_23-2151134017.jpg"
            alt="Boy"
            class="char-img"
        />
    </div>

    <div id="mainWrapper">
        <!-- LEFT: arguments or reference info -->
        <div id="leftCol">
            <h3>Arguments</h3>
            <div id="argumentsList">
                <p>- Argument 1: Some detail</p>
                <p>- Argument 2: Another detail</p>
                <p>- Argument 3: Possibly more reference info</p>
            </div>
        </div>

        <!-- CENTER: Chat + answers below chat -->
        <div id="centerCol">
            <!-- Chat area -->
            <div id="chatHistory"></div>
            <!-- result or final message -->
            <div id="resultMsg"></div>
            <!-- answer buttons below the chat -->
            <div id="answersContainer"></div>
            <button id="restartBtn">Restart</button>
        </div>

        <!-- RIGHT: scoreboard of 12 milestones -->
        <div id="rightCol">
            <h3>Milestones</h3>
            <div id="scoreboard"></div>
        </div>
    </div>

    <script>
        /***********************
         12 MILESTONES
         Each milestone has:
         - label: "M1", "M2", ...
         - boyLine: the statement from the girl
         - answers: array of { text, isCorrect, negResponse }, boy's possible answers
         ***********************/
        const milestoneData = [
            {
                label: "M1",
                boyLine: "Hello, my name is Jake. You might be wondering who is Jake and what does he want from me?",
                answers: [
                    { text: "Hello Jake! I'm Anna. I'd love to talk.", isCorrect: true, negResponse: "" },
                    { text: "No, not interested. Sorry!", isCorrect: false, negResponse: "Girl: That's rude! Another attempt?" },
                    { text: "Uh, I'm not sure who you are either...", isCorrect: false, negResponse: "Girl: That's confusing. Another try?"}
                ]
            },
            {
                label: "M2",
                boyLine: "So what do you want, Albert?",
                answers: [
                    { text: "I'd like to get to know you and maybe go on a date.", isCorrect: true, negResponse: "" },
                    { text: "I just like to talk nonsense. (Wrong)", isCorrect: false, negResponse: "Girl: That's silly. Another option?" }
                ]
            },
            {
                label: "M3",
                boyLine: "Ok, I'd like that too. But only if you can make me laugh. So what's your best trait?",
                answers: [
                    { text: "Sense of humor, obviously!", isCorrect: true, negResponse: "" },
                    { text: "Uh, I'm super rich. (Wrong)", isCorrect: false, negResponse: "Girl: That's superficial. Another try?" }
                ]
            },
            {
                label: "M4",
                boyLine: "Haha, that’s good. Next question: what's the most important value in a relationship?",
                answers: [
                    { text: "Honesty & kindness. (Correct => next)", isCorrect: true, negResponse: "" },
                    { text: "All about looks. (Wrong)", isCorrect: false, negResponse: "Girl: That’s shallow. Another attempt?" }
                ]
            },
            {
                label: "M5",
                boyLine: "Awesome. Let’s confirm date details soon!",
                answers: [
                    { text: "Yes, let's finalize the date! (Correct => next)", isCorrect: true, negResponse: "" },
                    { text: "Actually I'm not sure now. (Wrong)", isCorrect: false, negResponse: "Girl: Another try maybe?"}
                ]
            },
        ];
        // fill up to M12 placeholders
        while (milestoneData.length < 12) {
            milestoneData.push({
                label: "M" + (milestoneData.length + 1),
                boyLine: "Placeholder for milestone " + (milestoneData.length + 1),
                answers: [
                    { text: "A correct possible answer for M" + (milestoneData.length + 1), isCorrect: true, negResponse: "" },
                    { text: "A wrong answer", isCorrect: false, negResponse: "Girl: Wrong, try again" }
                ]
            });
        }

        let currentMilestone = 0;
        let gameOver = false;

        const chatHistory = document.getElementById("chatHistory");
        const answersContainer = document.getElementById("answersContainer");
        const resultMsg = document.getElementById("resultMsg");
        const restartBtn = document.getElementById("restartBtn");
        const scoreboardDiv = document.getElementById("scoreboard");

        /**********************
         * RENDER SCOREBOARD
         **********************/
        function renderScoreboard() {
            scoreboardDiv.innerHTML = "";
            milestoneData.forEach((ms, idx) => {
                const btn = document.createElement("div");
                btn.className = "milestone-btn";
                if (idx === currentMilestone && !gameOver) {
                    btn.classList.add("active");
                }
                // label
                const labelSpan = document.createElement("div");
                labelSpan.textContent = ms.label;
                labelSpan.className = "milestone-label";
                btn.appendChild(labelSpan);
                scoreboardDiv.appendChild(btn);
            });
        }

        /***********************
         * LOAD MILESTONE
         ***********************/
        function loadMilestone(index) {
            chatHistory.innerHTML = "";
            answersContainer.innerHTML = "";
            resultMsg.textContent = "";
            restartBtn.style.display = "none";

            const ms = milestoneData[index];
            addGirlBubble(ms.boyLine);

            // create answer buttons below chat
            ms.answers.forEach((ansObj, ansIdx) => {
                const btn = document.createElement("button");
                btn.className = "answer-btn";
                btn.textContent = ansObj.text;
                btn.onclick = () => handleAnswer(ansObj, ansIdx);
                answersContainer.appendChild(btn);
            });

            renderScoreboard();
        }

        /***********************
         * Handle a chosen answer
         ***********************/
        function handleAnswer(ansObj, ansIdx) {
            if (gameOver) return;

            // boy bubble
            addBoyBubble(ansObj.text);

            if (ansObj.isCorrect) {
                // correct => go next milestone
                currentMilestone++;
                if (currentMilestone >= milestoneData.length) {
                    // all done => success
                    addGirlBubble("We reached milestone 12. Great job! Let's arrange the date. <3");
                    answersContainer.innerHTML = "";
                    resultMsg.textContent = "You completed all milestones successfully!";
                    gameOver = true;
                    restartBtn.style.display = "inline-block";
                } else {
                    // load next
                    setTimeout(() => {
                        loadMilestone(currentMilestone);
                    }, 600);
                }
            } else {
                // wrong => show negative response from the girl
                addGirlBubble(ansObj.negResponse || "Girl: Not correct. Another attempt?");
                // remove that button or disable it
                const btns = answersContainer.querySelectorAll(".answer-btn");
                btns[ansIdx].disabled = true;
                btns[ansIdx].style.opacity = 0.5;
            }
        }

        /***********************
         * Chat bubble helpers
         ***********************/
        function addGirlBubble(text) {
            const bubble = document.createElement("div");
            bubble.classList.add("chat-bubble", "girl-bubble");
            bubble.textContent = text;
            chatHistory.appendChild(bubble);
            scrollChatToBottom();
        }
        function addBoyBubble(text) {
            const bubble = document.createElement("div");
            bubble.classList.add("chat-bubble", "boy-bubble");
            bubble.textContent = text;
            chatHistory.appendChild(bubble);
            scrollChatToBottom();
        }
        function scrollChatToBottom() {
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }

        /***********************
         * Restart
         ***********************/
        restartBtn.onclick = startGame;
        function startGame() {
            currentMilestone = 0;
            gameOver = false;
            chatHistory.innerHTML = "";
            answersContainer.innerHTML = "";
            resultMsg.textContent = "";
            restartBtn.style.display = "none";

            loadMilestone(currentMilestone);
        }

        /***********************
         * INIT
         ***********************/
        startGame();
    </script>
{{--    <div class="py-12">--}}
{{--        <h1 class="text-2xl font-bold text-center text-gray-800 py-4">--}}
{{--            Who Wants to Be a Millionaire?--}}
{{--        </h1>--}}

{{--        <!-- 3 Column Wrapper -->--}}
{{--        <div class="mx-auto w-full max-w-7xl grow lg:flex xl:px-2">--}}

{{--            <!-- Left sidebar & main wrapper -->--}}
{{--            <div class="flex-1 xl:flex">--}}
{{--                <!-- LEFT COLUMN: random icons -->--}}
{{--                <div--}}
{{--                    class="border-b border-gray-200 px-4 py-6 sm:px-6 lg:pl-8 xl:w-64 xl:shrink-0 xl:border-b-0 xl:border-r xl:pl-6">--}}
{{--                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Random Icons</h2>--}}
{{--                    <div id="leftIcons" class="flex flex-wrap gap-3">--}}
{{--                        <!-- We'll inject random icons here via JS -->--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- MIDDLE COLUMN: question & answers -->--}}
{{--                <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:flex-1 xl:pl-6">--}}

{{--                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Question & Answers</h2>--}}
{{--                    <!-- The question text -->--}}
{{--                    <div id="questionText" class="mb-4 text-xl font-bold text-blue-700">--}}
{{--                        [No Question]--}}
{{--                    </div>--}}
{{--                    <!-- The answers container -->--}}
{{--                    <div id="answersContainer" class="flex flex-col gap-2 max-w-sm">--}}
{{--                        <!-- We'll generate 4 answer buttons from JS -->--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- RIGHT COLUMN: scoreboard (Q1..Q12) -->--}}
{{--            <div--}}
{{--                class="shrink-0 border-t border-gray-200 px-4 py-6 sm:px-6 lg:w-96 lg:border-l lg:border-t-0 lg:pr-8 xl:pr-6">--}}
{{--                <h2 class="text-lg font-semibold text-gray-700 mb-4">Scoreboard</h2>--}}
{{--                <!-- 12 question scoreboard cells -->--}}
{{--                <div id="scoreboard" class="grid grid-cols-3 gap-3">--}}
{{--                    <!-- We'll fill from JS -->--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <script>--}}
{{--            // EXAMPLE QUIZ DATA--}}
{{--            const questions = [--}}
{{--                {--}}
{{--                    question: "Cili planet njihet si 'Planeti i Kuq'?",--}}
{{--                    answers: ["Mars", "Venera", "Jupiter", "Saturn"],--}}
{{--                    correct: 0,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Kush e shkroi romanin '1984'?",--}}
{{--                    answers: ["George Orwell", "Aldous Huxley", "Ernest Hemingway", "Mark Twain"],--}}
{{--                    correct: 0,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cili është gjitari më i madh në Tokë?",--}}
{{--                    answers: ["Elefanti afrikan", "Balena blu", "Girafa", "Ariu polar"],--}}
{{--                    correct: 1,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cili shtet ia dhuroi SHBA-ve Statujën e Lirisë?",--}}
{{--                    answers: ["Kanada", "Franca", "Spanja", "Anglia"],--}}
{{--                    correct: 1,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cila gjuhë është më e folura në botë?",--}}
{{--                    answers: ["Anglishtja", "Spanjishtja", "Kinezçja Mandarine", "Hindia"],--}}
{{--                    correct: 2,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Kush e pikturoi 'Mona Lizën'?",--}}
{{--                    answers: ["Mikelanxhelo", "Leonardo da Vinçi", "Rafael", "Vincent van Gogh"],--}}
{{--                    correct: 1,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cili është kryeqyteti i Australisë?",--}}
{{--                    answers: ["Sidnei", "Melburn", "Kanberra", "Brisbane"],--}}
{{--                    correct: 2,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cili gaz është më i bollshëm në atmosferën e Tokës?",--}}
{{--                    answers: ["Oksigjen", "Azot", "Dioksid Karboni", "Hidrogjen"],--}}
{{--                    correct: 1,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Në cilin sport përdoret 'shuttlecock'?",--}}
{{--                    answers: ["Tenis", "Pingpong", "Badminton", "Skuash"],--}}
{{--                    correct: 2,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cili është simboli kimik i arit?",--}}
{{--                    answers: ["Au", "Ag", "Pt", "Pb"],--}}
{{--                    correct: 0,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Cili qytet organizoi Lojërat Olimpike Verore të vitit 2012?",--}}
{{--                    answers: ["Pekin", "Athinë", "Londër", "Rio de Janeiro"],--}}
{{--                    correct: 2,--}}
{{--                },--}}
{{--                {--}}
{{--                    question: "Sa kontinente ka Toka?",--}}
{{--                    answers: ["5", "6", "7", "8"],--}}
{{--                    correct: 2,--}}
{{--                },--}}
{{--            ];--}}

{{--            // EXAMPLE RANDOM ICONS for the left--}}
{{--            const icons = [--}}
{{--                "🍎", "🍌", "🍇", "🍓", "🍍", "🍑",--}}
{{--                "🍒", "🍐", "🍊", "🍋", "🍉", "🥭",--}}
{{--                "🥝", "🥥", "🍅", "🍄", "🧀", "🥨",--}}
{{--            ];--}}

{{--            let currentIndex = 0;--}}
{{--            let isGameOver = false;--}}

{{--            // DOM references--}}
{{--            const questionText = document.getElementById("questionText");--}}
{{--            const answersContainer = document.getElementById("answersContainer");--}}
{{--            const scoreboardElem = document.getElementById("scoreboard");--}}
{{--            const leftIcons = document.getElementById("leftIcons"); // We'll populate random icons here--}}

{{--            // shuffle helper--}}
{{--            function shuffle(arr) {--}}
{{--                for (let i = arr.length - 1; i > 0; i--) {--}}
{{--                    const j = Math.floor(Math.random() * (i + 1));--}}
{{--                    [arr[i], arr[j]] = [arr[j], arr[i]];--}}
{{--                }--}}
{{--            }--}}

{{--            // BUILD scoreboard--}}
{{--            function buildScoreboard() {--}}
{{--                scoreboardElem.innerHTML = "";--}}
{{--                for (let i = 0; i < questions.length; i++) {--}}
{{--                    const cell = document.createElement("div");--}}
{{--                    cell.textContent = `Q${i + 1}`;--}}
{{--                    cell.className = "bg-gray-800 text-yellow-300 font-bold flex items-center justify-center rounded shadow";--}}
{{--                    if (i === currentIndex) {--}}
{{--                        cell.classList.add("bg-yellow-500", "text-black");--}}
{{--                    }--}}
{{--                    scoreboardElem.appendChild(cell);--}}
{{--                }--}}
{{--            }--}}

{{--            function updateScoreboard() {--}}
{{--                const cells = scoreboardElem.querySelectorAll("div");--}}
{{--                cells.forEach((c, i) => {--}}
{{--                    c.classList.remove("bg-yellow-500", "text-black");--}}
{{--                    c.classList.add("bg-gray-800", "text-yellow-300");--}}
{{--                    if (i === currentIndex) {--}}
{{--                        c.classList.add("bg-yellow-500", "text-black");--}}
{{--                    }--}}
{{--                });--}}
{{--            }--}}

{{--            function loadQuestion() {--}}
{{--                const q = questions[currentIndex];--}}
{{--                questionText.textContent = q.question;--}}

{{--                // clear old answers--}}
{{--                answersContainer.innerHTML = "";--}}

{{--                // create answers--}}
{{--                q.answers.forEach((ans, idx) => {--}}
{{--                    const btn = document.createElement("button");--}}
{{--                    btn.className = "answer-btn";--}}
{{--                    btn.textContent = ans;--}}
{{--                    btn.onclick = () => checkAnswer(idx);--}}
{{--                    answersContainer.appendChild(btn);--}}
{{--                });--}}
{{--            }--}}

{{--            function checkAnswer(selectedIdx) {--}}
{{--                if (isGameOver) return;--}}
{{--                const correctIdx = questions[currentIndex].correct;--}}
{{--                if (selectedIdx === correctIdx) {--}}
{{--                    if (currentIndex === questions.length - 1) {--}}
{{--                        alert("Urime! Të gjitha pyetjet e sakta!");--}}
{{--                        isGameOver = true;--}}
{{--                    } else {--}}
{{--                        alert("Saktë! Kalon në pyetjen tjetër.");--}}
{{--                        currentIndex++;--}}
{{--                        loadQuestion();--}}
{{--                        updateScoreboard();--}}
{{--                    }--}}
{{--                } else {--}}
{{--                    alert("Gabim! Loja përfundoi.");--}}
{{--                    isGameOver = true;--}}
{{--                }--}}
{{--            }--}}

{{--            // random icons in left panel--}}
{{--            function fillLeftIcons() {--}}
{{--                // pick 6 random icons--}}
{{--                const iconsCopy = [...icons];--}}
{{--                shuffle(iconsCopy);--}}
{{--                const chosen = iconsCopy.slice(0, 6);--}}

{{--                chosen.forEach(icon => {--}}
{{--                    const span = document.createElement("span");--}}
{{--                    span.textContent = icon;--}}
{{--                    span.className = "text-3xl";--}}

{{--                    // wrap in a bubble style--}}
{{--                    const bubble = document.createElement("div");--}}
{{--                    bubble.className = "inline-flex items-center justify-center rounded-lg bg-white p-2 text-gray-800 ring-4 ring-white m-1";--}}
{{--                    bubble.appendChild(span);--}}

{{--                    leftIcons.appendChild(bubble);--}}
{{--                });--}}
{{--            }--}}

{{--            // init--}}
{{--            function initGame() {--}}
{{--                fillLeftIcons();--}}
{{--                buildScoreboard();--}}
{{--                loadQuestion();--}}
{{--            }--}}

{{--            window.onload = initGame;--}}
{{--        </script>--}}
{{--    </div>--}}
</x-app-layout>
