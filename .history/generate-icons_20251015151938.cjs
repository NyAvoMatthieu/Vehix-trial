// Exécutez: node generate-icons.cjs
// Ce script génère des fichiers PNG uniquement

const fs = require('fs');
const path = require('path');
const { createCanvas } = require('canvas');

console.log('🚀 Démarrage de la génération des icônes PNG...\n');

// Créer le dossier icons s'il n'existe pas
const iconsDir = path.join(__dirname, 'public', 'icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

// Fonction pour créer une icône PNG
function createIconPNG(size) {
    const canvas = createCanvas(size, size);
    const ctx = canvas.getContext('2d');

    // Arrière-plan avec dégradé
    const gradient = ctx.createLinearGradient(0, 0, size, size);
    gradient.addColorStop(0, '#667eea');
    gradient.addColorStop(1, '#764ba2');
    
    // Rectangle arrondi pour le fond
    const radius = size * 0.2;
    ctx.fillStyle = gradient;
    ctx.beginPath();
    ctx.moveTo(radius, 0);
    ctx.lineTo(size - radius, 0);
    ctx.quadraticCurveTo(size, 0, size, radius);
    ctx.lineTo(size, size - radius);
    ctx.quadraticCurveTo(size, size, size - radius, size);
    ctx.lineTo(radius, size);
    ctx.quadraticCurveTo(0, size, 0, size - radius);
    ctx.lineTo(0, radius);
    ctx.quadraticCurveTo(0, 0, radius, 0);
    ctx.closePath();
    ctx.fill();

    // Décalage pour centrer la voiture
    const offsetX = size * 0.15;
    const offsetY = size * 0.3;

    // Carrosserie de la voiture
    ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
    ctx.beginPath();
    ctx.moveTo(offsetX + size * 0.1, offsetY + size * 0.15);
    ctx.lineTo(offsetX + size * 0.15, offsetY + size * 0.05);
    ctx.lineTo(offsetX + size * 0.5, offsetY + size * 0.05);
    ctx.lineTo(offsetX + size * 0.55, offsetY + size * 0.15);
    ctx.lineTo(offsetX + size * 0.7, offsetY + size * 0.15);
    ctx.lineTo(offsetX + size * 0.7, offsetY + size * 0.3);
    ctx.lineTo(offsetX + size * 0.1, offsetY + size * 0.3);
    ctx.closePath();
    ctx.fill();

    // Fenêtres
    ctx.fillStyle = 'rgba(102, 126, 234, 0.6)';
    const windowRadius = 2;
    
    // Fenêtre gauche
    ctx.beginPath();
    ctx.roundRect(offsetX + size * 0.18, offsetY + size * 0.08, size * 0.12, size * 0.06, windowRadius);
    ctx.fill();
    
    // Fenêtre droite
    ctx.beginPath();
    ctx.roundRect(offsetX + size * 0.38, offsetY + size * 0.08, size * 0.12, size * 0.06, windowRadius);
    ctx.fill();

    // Roues
    const wheelY = offsetY + size * 0.3;
    
    // Roue gauche - extérieur
    ctx.fillStyle = '#2d3748';
    ctx.beginPath();
    ctx.arc(offsetX + size * 0.2, wheelY, size * 0.05, 0, Math.PI * 2);
    ctx.fill();
    
    // Roue gauche - intérieur
    ctx.fillStyle = '#4a5568';
    ctx.beginPath();
    ctx.arc(offsetX + size * 0.2, wheelY, size * 0.03, 0, Math.PI * 2);
    ctx.fill();
    
    // Roue droite - extérieur
    ctx.fillStyle = '#2d3748';
    ctx.beginPath();
    ctx.arc(offsetX + size * 0.6, wheelY, size * 0.05, 0, Math.PI * 2);
    ctx.fill();
    
    // Roue droite - intérieur
    ctx.fillStyle = '#4a5568';
    ctx.beginPath();
    ctx.arc(offsetX + size * 0.6, wheelY, size * 0.03, 0, Math.PI * 2);
    ctx.fill();

    return canvas;
}

// Générer les icônes
const sizes = [72, 96, 128, 144, 152, 192, 384, 512];

sizes.forEach(size => {
    try {
        const canvas = createIconPNG(size);
        const buffer = canvas.toBuffer('image/png');
        const filename = path.join(iconsDir, `icon-${size}x${size}.png`);
        
        // Supprimer l'ancien fichier SVG s'il existe
        const svgFilename = path.join(iconsDir, `icon-${size}x${size}.svg`);
        if (fs.existsSync(svgFilename)) {
            fs.unlinkSync(svgFilename);
            console.log(`🗑️  Supprimé: icon-${size}x${size}.svg`);
        }
        
        fs.writeFileSync(filename, buffer);
        console.log(`✅ Créé: icon-${size}x${size}.png (${buffer.length} bytes)`);
    } catch (error) {
        console.error(`❌ Erreur pour la taille ${size}:`, error.message);
    }
});

console.log('\n🎉 Toutes les icônes PNG ont été générées avec succès!');
console.log('\n📁 Les fichiers sont dans: public/icons/');