class Enemy {
    constructor(game) {
        this.game = game;
        this.x1 = 420;
        this.y1 = 40;

        this.spriteWidth = 500;
        this.spriteHeight = 600;

        this.width = 0;
        this.height = 0;

        this.image =document.getElementById('enemy');
    }

    draw() {
        // Skaliertes Zeichnen
        const x = this.x1 * this.game.ratioWidth;
        const y = this.y1 * this.game.ratioHeight;
        // this.game.ctx.fillRect(x, y, this.width, this.height);

        // Korrekte drawImage-Syntax:
        this.game.ctx.drawImage(
            this.image,
            x,
            y,
            this.width,
            this.height
        );
    }

    update() {
       if(this.isOffScreen()) {
          this.markedForDeletion = true;
          this.game.enemy = null;
          if (!this.game.gameOver) {
              this.game.score++;
          }

       }
    }

    isOffScreen() {
        return this.x1 + this.width < 0;
    }

    resize() {
        // Breite/Höhe des Sprites entsprechend skalieren
        this.width  = this.spriteWidth  * this.game.ratioWidth;
        this.height = this.spriteHeight * this.game.ratioHeight;
    }
}
