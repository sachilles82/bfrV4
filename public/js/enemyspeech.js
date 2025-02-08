class EnemySpeech {
    constructor(game) {
        this.game = game;
        this.x1 = 220;
        this.y1 = 30;
        this.spriteWidth = 90;
        this.spriteHeight = 60;

        this.width = 0;
        this.height = 0;
        this.color = 'orange';
    }

    draw() {
        const ctx = this.game.ctx;
        const x = this.x1 * this.game.ratioWidth;
        const y = this.y1 * this.game.ratioHeight;

        // Farbe und Style setzen
        ctx.save(); // Aktuellen Zeichenzustand speichern
        ctx.fillStyle = this.color;
        ctx.strokeStyle = 'darkorange';
        ctx.lineWidth = 2 * this.game.ratioWidth;

        // Rechteck zeichnen
        ctx.fillRect(x, y, this.width, this.height);
        ctx.strokeRect(x, y, this.width, this.height);

        ctx.restore(); // Vorherigen Zeichenzustand wiederherstellen
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
