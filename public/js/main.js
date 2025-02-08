class Game {
    constructor(canvas, context) {
        // z.B. im constructor, ganz am Ende:
        this.canvas = canvas;
        this.ctx = context;

        this.width  = this.canvas.width;
        this.height = this.canvas.height;

        this.baseHeight = 720;
        this.baseWidth  = 360;

        this.ratio = this.height / this.baseHeight;



        this.background = new Background(this);
        this.player = new Player(this);
        this.enemy = new Enemy(this);
        this.question = new Question(this);
        this.playerSpeech = new PlayerSpeech(this);
        this.enemySpeech = new EnemySpeech(this);

        this.energy = 30;
        this.maxEnergy = this.energy *2;
        this.minEnergy = 15;

        this.score;
        this.gameOver;
        this.timer;

        // Starte das erste Resize
        this.resize(window.innerWidth, window.innerHeight);

        // Fenstergrößen-Änderung abfangen
        window.addEventListener('resize', (e) => {
            this.resize(e.currentTarget.innerWidth, e.currentTarget.innerHeight);
        });
    }

    resize(width, height) {

        this.canvas.width  = width;
        this.canvas.height = height;
        this.ctx.fillStyle = 'blue';
        this.width  = this.canvas.width;
        this.height = this.canvas.height;

        this.ratio = this.height / this.baseHeight;
        this.player.resize();

        console.log(this.height, this.baseHeight, this.ratio);

        this.ctx.font = '15px Bungee';
        this.ctx.textAlign = 'right';


        // Hintergrund neu skalieren
        this.background.resize();

        // ===========================
        //  PORTRAIT vs. LANDSCAPE
        // ===========================
        if (this.height > this.width) {
            // PORTRAIT (Hochformat)
            this.player.x = 50;
            this.player.y = 60;

            this.playerSpeech.x1 = 105;
            this.playerSpeech.y1 = 70;

            this.enemy.x1 = 210;
            this.enemy.y1 = 100;

            this.enemySpeech.x1 = 165;
            this.enemySpeech.y1 = 70;

            this.question.x1 = 30;
            this.question.y1 = 500;

        } else {
            // LANDSCAPE (Querformat)
            this.player.x = 180;
            this.player.y = 30;

            this.playerSpeech.x1 = 120;
            this.playerSpeech.y1 = 90;

            this.enemy.x1 = 30;
            this.enemy.y1 = 480;

            this.enemySpeech.x1 = 220;
            this.enemySpeech.y1 = 90;

            this.question.x1 = 50;
            this.question.y1 = 500;

        }

        // Rufe Resize-Logik des Players auf
        this.player.resize();
        this.playerSpeech.resize();
        this.enemy.resize();
        this.enemySpeech.resize();
        this.question.resize();

        this.score = 0;
        this.gameOver = false;
        this.timer = 0;
    }

    render(deltaTime) {
        // 1) Hintergrund
        if (!this.gameOver) this.timer += deltaTime;

        this.background.update();
        this.background.draw();
        this.drawStatusText();

        this.player.update();
        this.player.draw();

        this.playerSpeech.draw();
        this.playerSpeech.update();

        this.enemy.update();
        this.enemy.draw();

        this.enemySpeech.draw();
        this.enemySpeech.update();

        this.question.update();
        this.question.draw();

    }

    formatTimer() {
       // return (this.timer * 0.001).toFixed(2);
    }

    drawStatusText() {
        this.ctx.save();
        this.ctx.textAlign = 'right';
        this.ctx.fillText('Score: ' + this.score, this.width - 10, 30);

        this.ctx.textAlign = 'left';
        this.ctx.fillText('Timer: ' + this.formatTimer(),  10, 30);

        if(this.gameOver) {
            // if(this.player.collided) {
            //
            // } else if (this.enemy.answer === this.player.question bad) {
            //     this.ctx.fillText('Wrong Answer: ' + this.enemy.answer, this.width * 0.5, this.height * 0.5);
            //
            // }

            this.ctx.textAlign = 'center';
            this.ctx.font = '30px Bungee';
            this.ctx.fillText('GAME OVER', this.width * 0.5, this.height * 0.5);
        }

        for(let i = 0; i < this.energy; i++) {
            this.ctx.fillRect(10 + i * 6,  40, 5, 15);
        }

        this.ctx.restore();
    }
}

// Spiel starten
window.addEventListener('load', function () {
    const canvas = document.getElementById('canvas1');
    const ctx = canvas.getContext('2d');

    // Anfangsgröße (wird vom resize() überschrieben)
    canvas.width  = 720;
    canvas.height = 720;

    const game = new Game(canvas, ctx);

    let lastTime = 0;
    function animate(timeStamp) {
        const deltaTime = timeStamp - lastTime;
        lastTime = timeStamp;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        game.render(deltaTime);
        if(!game.gameOver) {
            requestAnimationFrame(animate);
        }
    }
    requestAnimationFrame(animate);
});
