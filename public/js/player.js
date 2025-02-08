class Player {
    constructor(game) {
        this.game = game;
        this.x = 50;
        this.y = 60;

        this.spriteWidth = 200;
        this.spriteHeight = 500;

        this.canvasHeight = 720;
        this.canvasWidht  = 360;

        this.width;
        this.height;

        // this.image =document.getElementById('player');
    }

    draw() {
        // // Skaliertes Zeichnen
        // const x = this.x * this.game.ratioWidth;
        // const y = this.y * this.game.ratioHeight;

        this.game.ctx.fillRect(this.x, this.y, this.width, this.height);
    }

    update() {
        // Bewegung o. Ä. hier
    }

    resize() {
        // Breite/Höhe des Sprites entsprechend skalieren
        // if(this.canvasHeight < this.canvasWidht) {
        //     this.width  = this.spriteWidth * this.game.ratio;
        //     this.height = this.spriteHeight * this.game.ratio;
        // } else {
        //     this.width  = this.spriteWidth * this.game.ratio * 2.3;
        //     this.height = this.spriteHeight * this.game.ratio * 2.3;
        // }

        this.width  = this.spriteWidth * this.game.ratio;
        this.height = this.spriteHeight * this.game.ratio;
    }
}
