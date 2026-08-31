// Exécutez: node generate-icons.js

const fs = require('fs');
const path = require('path');

// Créer le dossier icons s'il n'existe pas
const iconsDir = path.join(__dirname, 'public', 'icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

// SVG de base pour l'icône AutoManager
const sizes = [72, 96, 128, 144, 152, 192, 384, 512];

sizes.forEach(size => {
    const svg = `<?xml version="1.0" encoding="UTF-8"?>
<svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
        </linearGradient>
    </defs>

    <!-- Arrière-plan avec dégradé -->
    <rect width="${size}" height="${size}" rx="${size * 0.2}" fill="url(#gradient)"/>

    <!-- Icône de voiture stylisée -->
    <g transform="translate(${size * 0.15}, ${size * 0.3})">
        <!-- Carrosserie -->
        <path d="M ${size * 0.1} ${size * 0.15}
                 L ${size * 0.15} ${size * 0.05}
                 L ${size * 0.5} ${size * 0.05}
                 L ${size * 0.55} ${size * 0.15}
                 L ${size * 0.7} ${size * 0.15}
                 L ${size * 0.7} ${size * 0.3}
                 L ${size * 0.1} ${size * 0.3} Z"
              fill="white" opacity="0.95"/>

        <!-- Fenêtres -->
        <rect x="${size * 0.18}" y="${size * 0.08}" width="${size * 0.12}" height="${size * 0.06}" fill="#667eea" opacity="0.6" rx="2"/>
        <rect x="${size * 0.38}" y="${size * 0.08}" width="${size * 0.12}" height="${size * 0.06}" fill="#667eea" opacity="0.6" rx="2"/>

        <!-- Roues -->
        <circle cx="${size * 0.2}" cy="${size * 0.3}" r="${size * 0.05}" fill="#2d3748"/>
        <circle cx="${size * 0.2}" cy="${size * 0.3}" r="${size * 0.03}" fill="#4a5568"/>
        <circle cx="${size * 0.6}" cy="${size * 0.3}" r="${size * 0.05}" fill="#2d3748"/>
        <circle cx="${size * 0.6}" cy="${size * 0.3}" r="${size * 0.03}" fill="#4a5568"/>
    </g>
</svg>`;

    const filename = path.join(iconsDir, `icon-${size}x${size}.png`);
    fs.writeFileSync(filename, svg);
    console.log(`✅ Créé: icon-${size}x${size}.svg`);
});

console.log('\n🎉 Toutes les icônes SVG ont été générées avec succès!');
console.log('\n📝 Note: Pour de meilleures icônes, vous pouvez:');
console.log('   1. Utiliser un outil en ligne comme https://realfavicongenerator.net/');
console.log('   2. Créer une icône PNG 512x512 et la redimensionner');
console.log('   3. Utiliser un outil comme ImageMagick ou sharp pour convertir les SVG en PNG');
console.log('\nCommande ImageMagick pour convertir SVG → PNG:');
console.log('   for size in 72 96 128 144 152 192 384 512; do');
console.log('     convert public/icons/icon-${size}x${size}.svg public/icons/icon-${size}x${size}.png');
console.log('   done');
