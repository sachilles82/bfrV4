class Question {
    constructor(game) {
        this.game = game;
        this.x1 = 30;
        this.y1 = 500;

        this.spriteWidth = 300;
        this.spriteHeight = 200;

        this.width = 0;
        this.height = 0;
    }

    draw() {
        // Skaliertes Zeichnen
        const x = this.x1 * this.game.ratioWidth;
        const y = this.y1 * this.game.ratioHeight;
        this.game.ctx.fillRect(x, y, this.width, this.height);
    }

    update() {

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
