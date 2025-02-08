class Background {
    constructor(game) {
        this.game = game;
        this.image = document.getElementById('background');

        // Logische (Basis-)Größe deines Bilds
        // (z.B. wenn du weißt: dein Bild ist 360px breit × 720px hoch)
        this.baseWidth  = 2400;
        this.baseHeight = 2600;

        // Tatsächliche Zeichenposition und -größe
        this.x = 300;
        this.y = 200;
        this.width  = 0;
        this.height = 0;
    }

    update() {
        // Falls du später Animationen oder Parallax-Effekte willst,
        // könntest du das hier einbauen

        // this.x -= 0.1;
        // this.y -= 0.05;
    }

    // ---------------------------------
    //  Wichtig: Skalierung und Zuschnitt
    // ---------------------------------
    resize() {
        // Canvas-Aspektverhältnis
        const canvasAspect = this.game.width / this.game.height;
        // Bild-Aspektverhältnis (360/720 = 0.5)
        const imageAspect  = this.baseWidth / this.baseHeight;

        // =====================
        //   COVER-Logik
        // =====================
        // Wenn das Canvas "schmaler" ist als das Bild (im Verhältnis),
        // dann passen wir die Höhe des Bildes exakt an die Canvas-Höhe an
        // und vergrößern die Breite so, dass das Bild das Canvas komplett ausfüllt.
        // Dabei wird ein Teil des Bildes links/rechts abgeschnitten,
        // wenn das Verhältnis stark abweicht.
        if (canvasAspect < imageAspect) {
            // Canvas ist schmaler => Höhe füllt Canvas
            this.height = this.game.height;
            this.width  = this.height * imageAspect;
        } else {
            // Canvas ist breiter => Breite füllt Canvas
            this.width  = this.game.width;
            this.height = this.width / imageAspect;
        }

        // Bild horizontal/vertikal mittig platzieren
        this.x = (this.game.width  - this.width)  * 0.5;
        this.y = (this.game.height - this.height) * 0.5;
    }

    draw() {
        // Bild zeichnen (ab Position x,y in der ermittelten Breite/Höhe)
        this.game.ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
    }
}
