// class Game {
//     constructor(canvas, context) {
//         // z.B. im constructor, ganz am Ende:
//         this.canvas = canvas;
//         this.ctx = context;
//
//         this.width  = this.canvas.width;
//         this.height = this.canvas.height;
//
//         this.baseHeight = 720;
//         this.baseWidth  = 360;
//
//         this.ratio = this.height / this.baseHeight;
//
//
//
//         this.background = new Background(this);
//         this.player = new Player(this);
//         this.enemy = new Enemy(this);
//         this.question = new Question(this);
//         this.playerSpeech = new PlayerSpeech(this);
//         this.enemySpeech = new EnemySpeech(this);
//
//         this.energy = 30;
//         this.maxEnergy = this.energy *2;
//         this.minEnergy = 15;
//
//         this.score;
//         this.gameOver;
//         this.timer;
//
//         // Starte das erste Resize
//         this.resize(window.innerWidth, window.innerHeight);
//
//         // Fenstergrößen-Änderung abfangen
//         window.addEventListener('resize', (e) => {
//             this.resize(e.currentTarget.innerWidth, e.currentTarget.innerHeight);
//         });
//     }
//
//     resize(width, height) {
//
//         this.canvas.width  = width;
//         this.canvas.height = height;
//         this.ctx.fillStyle = 'blue';
//         this.width  = this.canvas.width;
//         this.height = this.canvas.height;
//
//         this.ratio = this.height / this.baseHeight;
//         this.player.resize();
//
//         console.log(this.height, this.baseHeight, this.ratio);
//
//         this.ctx.font = '15px Bungee';
//         this.ctx.textAlign = 'right';
//
//
//         // Hintergrund neu skalieren
//         this.background.resize();
//
//         // ===========================
//         //  PORTRAIT vs. LANDSCAPE
//         // ===========================
//         if (this.height > this.width) {
//             // PORTRAIT (Hochformat)
//             this.player.x = 50;
//             this.player.y = 60;
//
//             this.playerSpeech.x1 = 105;
//             this.playerSpeech.y1 = 70;
//
//             this.enemy.x1 = 210;
//             this.enemy.y1 = 100;
//
//             this.enemySpeech.x1 = 165;
//             this.enemySpeech.y1 = 70;
//
//             this.question.x1 = 30;
//             this.question.y1 = 500;
//
//         } else {
//             // LANDSCAPE (Querformat)
//             this.player.x = 180;
//             this.player.y = 30;
//
//             this.playerSpeech.x1 = 120;
//             this.playerSpeech.y1 = 90;
//
//             this.enemy.x1 = 30;
//             this.enemy.y1 = 480;
//
//             this.enemySpeech.x1 = 220;
//             this.enemySpeech.y1 = 90;
//
//             this.question.x1 = 50;
//             this.question.y1 = 500;
//
//         }
//
//         // Rufe Resize-Logik des Players auf
//         this.player.resize();
//         this.playerSpeech.resize();
//         this.enemy.resize();
//         this.enemySpeech.resize();
//         this.question.resize();
//
//         this.score = 0;
//         this.gameOver = false;
//         this.timer = 0;
//     }
//
//     render(deltaTime) {
//         // 1) Hintergrund
//         if (!this.gameOver) this.timer += deltaTime;
//
//         this.background.update();
//         this.background.draw();
//         this.drawStatusText();
//
//         this.player.update();
//         this.player.draw();
//
//         this.playerSpeech.draw();
//         this.playerSpeech.update();
//
//         this.enemy.update();
//         this.enemy.draw();
//
//         this.enemySpeech.draw();
//         this.enemySpeech.update();
//
//         this.question.update();
//         this.question.draw();
//
//     }
//
//     formatTimer() {
//        // return (this.timer * 0.001).toFixed(2);
//     }
//
//     drawStatusText() {
//         this.ctx.save();
//         this.ctx.textAlign = 'right';
//         this.ctx.fillText('Score: ' + this.score, this.width - 10, 30);
//
//         this.ctx.textAlign = 'left';
//         this.ctx.fillText('Timer: ' + this.formatTimer(),  10, 30);
//
//         if(this.gameOver) {
//             // if(this.player.collided) {
//             //
//             // } else if (this.enemy.answer === this.player.question bad) {
//             //     this.ctx.fillText('Wrong Answer: ' + this.enemy.answer, this.width * 0.5, this.height * 0.5);
//             //
//             // }
//
//             this.ctx.textAlign = 'center';
//             this.ctx.font = '30px Bungee';
//             this.ctx.fillText('GAME OVER', this.width * 0.5, this.height * 0.5);
//         }
//
//         for(let i = 0; i < this.energy; i++) {
//             this.ctx.fillRect(10 + i * 6,  40, 5, 15);
//         }
//
//         this.ctx.restore();
//     }
// }
//
// // Spiel starten
// window.addEventListener('load', function () {
//     const canvas = document.getElementById('canvas1');
//     const ctx = canvas.getContext('2d');
//
//     // Anfangsgröße (wird vom resize() überschrieben)
//     canvas.width  = 720;
//     canvas.height = 720;
//
//     const game = new Game(canvas, ctx);
//
//     let lastTime = 0;
//     function animate(timeStamp) {
//         const deltaTime = timeStamp - lastTime;
//         lastTime = timeStamp;
//         ctx.clearRect(0, 0, canvas.width, canvas.height);
//         game.render(deltaTime);
//         if(!game.gameOver) {
//             requestAnimationFrame(animate);
//         }
//     }
//     requestAnimationFrame(animate);
// });

// Background class (minimal implementation)
class Background {
    constructor(game) {
        this.game = game;
    }
    resize() {}
    update() {}
    draw() {
        this.game.ctx.fillStyle = 'lightgray';
        this.game.ctx.fillRect(0, 0, this.game.width, this.game.height);
    }
}

// Player class (male player)
class Player {
    constructor(game) {
        this.game = game;
        this.image = new Image();
        this.image.src = 'images/male.png';
        this.width = 300;
        this.height = 300;
        this.x = 0;
        this.y = 0;
        this.loaded = false;
        this.image.onload = () => { this.loaded = true; };
    }
    resize() {
        this.x = this.game.width * 0.1; // 10% from left
        this.y = this.game.height - this.height - 50;
    }
    update() {}
    draw() {
        if (this.loaded) {
            this.game.ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
        }
    }
}

// Enemy class (female player)
class Enemy {
    constructor(game) {
        this.game = game;
        this.image = new Image();
        // Set the default image to female.png
        this.image.src = 'images/female.png';
        this.width = 300;
        this.height = 300;
        this.x = 0;
        this.y = 0;
        this.loaded = false;
        this.image.onload = () => { this.loaded = true; };
    }
    resize() {
        this.x = this.game.width * 0.6; // 60% from left
        this.y = this.game.height - this.height - 50;
    }
    update() {}
    draw() {
        if (this.loaded) {
            this.game.ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
        }
    }
}

// PlayerSpeech class (male speech bubble)
class PlayerSpeech {
    constructor(game) {
        this.game = game;
        this.image = new Image();
        this.image.src = 'images/speechbubble.png';
        this.width = 200;
        this.height = 100;
        this.x = 0;
        this.y = 0;
        this.text = '';
        this.loaded = false;
        this.image.onload = () => { this.loaded = true; };
    }
    resize() {
        this.x = this.game.player.x + 50;
        this.y = this.game.player.y - this.height - 10;
    }
    update() {}
    draw() {
        if (this.text && this.loaded) {
            this.game.ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
            this.game.ctx.font = '15px Arial';
            this.game.ctx.fillStyle = 'black';
            this.game.ctx.textAlign = 'center';
            this.game.ctx.fillText(this.text, this.x + this.width / 2, this.y + this.height / 2);
        }
    }
}

// EnemySpeech class (female speech bubble)
class EnemySpeech {
    constructor(game) {
        this.game = game;
        this.image = new Image();
        this.image.src = 'images/speechbubble.png';
        this.width = 200;
        this.height = 100;
        this.x = 0;
        this.y = 0;
        this.text = '';
        this.loaded = false;
        this.image.onload = () => { this.loaded = true; };
    }
    resize() {
        this.x = this.game.enemy.x + 50;
        this.y = this.game.enemy.y - this.height - 10;
    }
    update() {}
    draw() {
        if (this.text && this.loaded) {
            this.game.ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
            this.game.ctx.font = '15px Arial';
            this.game.ctx.fillStyle = 'black';
            this.game.ctx.textAlign = 'center';
            this.game.ctx.fillText(this.text, this.x + this.width / 2, this.y + this.height / 2);
        }
    }
}

// Question class (question buttons)
class Question {
    constructor(game) {
        this.game = game;
        this.buttons = [];
        this.buttonWidth = 200;
        this.buttonHeight = 50;
        this.spacing = 20;
    }
    resize() {
        this.updateButtons();
    }
    updateButtons() {
        this.buttons = this.game.currentQuestions.map((q, index) => ({
            text: q.text,
            x: (this.game.width - (this.buttonWidth * 3 + this.spacing * 2)) / 2 + (this.buttonWidth + this.spacing) * index,
            y: this.game.height - this.buttonHeight - 20,
            width: this.buttonWidth,
            height: this.buttonHeight,
            index: index
        }));
    }
    update() {}
    draw() {
        this.buttons.forEach(button => {
            this.game.ctx.fillStyle = 'white';
            this.game.ctx.fillRect(button.x, button.y, button.width, button.height);
            this.game.ctx.strokeStyle = 'black';
            this.game.ctx.strokeRect(button.x, button.y, button.width, button.height);
            this.game.ctx.font = '15px Arial';
            this.game.ctx.fillStyle = 'black';
            this.game.ctx.textAlign = 'center';
            this.game.ctx.fillText(button.text, button.x + button.width / 2, button.y + button.height / 2);
        });
    }
}

// Game class
class Game {
    constructor(canvas, context) {
        this.canvas = canvas;
        this.ctx = context;
        this.width = this.canvas.width;
        this.height = this.canvas.height;
        this.baseHeight = 720;
        this.baseWidth = 360;
        this.ratio = this.height / this.baseHeight;

        this.background = new Background(this);
        this.player = new Player(this);
        this.enemy = new Enemy(this);
        this.question = new Question(this);
        this.playerSpeech = new PlayerSpeech(this);
        this.enemySpeech = new EnemySpeech(this);

        this.heartImg = new Image();
        this.heartImg.src = 'images/heart.png';
        this.heartLoaded = false;
        this.heartImg.onload = () => { this.heartLoaded = true; };

        this.heartCount = 5;
        this.currentStage = 0;
        this.currentQuestions = [];
        this.gameOver = false;
        this.gameStarted = false;
        this.timer = 0; // Initialize timer

        this.questionCatalog = [
            [
                { text: "Hi, what's your name?", correct: true, response: "My name is Jane, nice to meet you!" },
                { text: "Hey, do you come here often?", correct: false, response: "That's a bit cliché, don't you think?" },
                { text: "Can I buy you a drink?", correct: true, response: "Sure, that would be nice!" }
            ],
            [
                { text: "What do you do for a living?", correct: true, response: "I'm a graphic designer." },
                { text: "How old are you?", correct: false, response: "That's a bit personal, don't you think?" },
                { text: "Do you like this place?", correct: true, response: "Yes, it's one of my favorite spots." }
            ],
            [
                { text: "What are your hobbies?", correct: true, response: "I love painting and hiking." },
                { text: "Do you have any pets?", correct: true, response: "Yes, I have a cat named Whiskers." },
                { text: "What's your favorite TV show?", correct: false, response: "I don't watch much TV." }
            ],
            [
                { text: "Where do you see yourself in 5 years?", correct: false, response: "That's a serious question for a first chat." },
                { text: "Do you travel often?", correct: true, response: "Yes, I love exploring new places." },
                { text: "What's your dream job?", correct: true, response: "I'd love to own my own art gallery." }
            ],
            [
                { text: "Can I get your number?", correct: true, response: "Sure, here's my number!" },
                { text: "Want to go out sometime?", correct: true, response: "That sounds great!" },
                { text: "Are you seeing anyone?", correct: false, response: "That's none of your business!" }
            ]
        ];

        this.resize(window.innerWidth, window.innerHeight);
        window.addEventListener('resize', (e) => {
            this.resize(e.currentTarget.innerWidth, e.currentTarget.innerHeight);
        });
        // Handle clicks on canvas only when the game has started
        this.canvas.addEventListener('click', (e) => this.handleClick(e));
    }

    resize(width, height) {
        this.canvas.width = width;
        this.canvas.height = height;
        this.width = width;
        this.height = height;
        this.ratio = this.height / this.baseHeight;

        this.background.resize();
        this.player.resize();
        this.enemy.resize();
        this.playerSpeech.resize();
        this.enemySpeech.resize();
        this.question.resize();

        this.ctx.font = '15px Arial';
        this.ctx.textAlign = 'right';
    }

    render(deltaTime) {
        if (!this.gameOver && this.gameStarted) {
            this.timer += deltaTime;
        }

        this.background.update();
        this.background.draw();

        if (this.gameStarted) {
            this.player.update();
            this.player.draw();
            this.enemy.update();
            this.enemy.draw();
            this.playerSpeech.draw();
            this.playerSpeech.update();
            this.enemySpeech.draw();
            this.enemySpeech.update();
            this.question.update();
            this.question.draw();
            this.drawHearts();
        }

        if (this.gameOver) {
            this.ctx.font = '30px Arial';
            this.ctx.fillStyle = 'red';
            this.ctx.textAlign = 'center';
            this.ctx.fillText('Game Over', this.width / 2, this.height / 2);
        }
    }

    drawHearts() {
        const startX = this.enemy.x + this.enemy.width + 10;
        const startY = this.enemy.y;
        const spacing = 25;
        for (let i = 0; i < 5; i++) {
            if (i < this.heartCount && this.heartLoaded) {
                this.ctx.drawImage(this.heartImg, startX, startY + i * spacing, 20, 20);
            }
        }
    }

    handleClick(e) {
        if (!this.gameStarted) return;
        const rect = this.canvas.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;

        this.question.buttons.forEach(button => {
            if (clickX >= button.x && clickX <= button.x + button.width &&
                clickY >= button.y && clickY <= button.y + button.height) {
                this.handleQuestion(button.index);
            }
        });
    }

    // Handle a question answer
    handleQuestion(index) {
        const selectedQuestion = this.currentQuestions[index];
        this.playerSpeech.text = selectedQuestion.text;
        this.enemySpeech.text = '';
        // After 2 seconds, show enemy's response
        setTimeout(() => {
            this.enemySpeech.text = selectedQuestion.response;
            // After another 2 seconds, process the answer and (if correct) show reaction image
            setTimeout(() => {
                if (selectedQuestion.correct) {
                    // Change to reaction image female2.png if correct
                    this.enemy.image.src = 'images/female2.png';
                    this.heartCount++;
                    // Wait 1 second and then revert to the original enemy image
                    setTimeout(() => {
                        this.enemy.image.src = 'images/female.png';
                        this.nextQuestion();
                    }, 1000);
                } else {
                    this.heartCount--;
                    if (this.heartCount <= 0) {
                        this.gameOver = true;
                        this.playerSpeech.text = '';
                        this.enemySpeech.text = '';
                        return;
                    }
                    this.nextQuestion();
                }
            }, 2000);
        }, 2000);
    }

    // Proceed to the next question or end the game
    nextQuestion() {
        this.currentStage++;
        if (this.currentStage < this.questionCatalog.length) {
            this.currentQuestions = this.questionCatalog[this.currentStage];
            this.question.updateButtons();
            this.playerSpeech.text = '';
            this.enemySpeech.text = '';
        } else {
            this.gameOver = true;
            this.playerSpeech.text = '';
            this.enemySpeech.text = '';
            this.ctx.font = '30px Arial';
            this.ctx.fillStyle = 'green';
            this.ctx.textAlign = 'center';
            this.ctx.fillText('Game Completed', this.width / 2, this.height / 2);
        }
    }

    startGame() {
        this.gameStarted = true;
        this.currentStage = 0;
        this.heartCount = 5;
        this.gameOver = false;
        this.playerSpeech.text = '';
        this.enemySpeech.text = '';
        this.currentQuestions = this.questionCatalog[this.currentStage];
        this.question.updateButtons();
    }
}

// Start the game and bind the external Start button
window.addEventListener('load', function () {
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');

    const game = new Game(canvas, ctx);

    // External Start button logic
    const startButton = document.getElementById('start-button');
    startButton.style.display = 'block'; // Ensure it is visible
    startButton.addEventListener('click', function () {
        game.startGame();
        startButton.style.display = 'none'; // Hide the button after starting
    });

    let lastTime = 0;
    function animate(timeStamp) {
        const deltaTime = timeStamp - lastTime;
        lastTime = timeStamp;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        game.render(deltaTime);
        requestAnimationFrame(animate);
    }
    requestAnimationFrame(animate);
});
