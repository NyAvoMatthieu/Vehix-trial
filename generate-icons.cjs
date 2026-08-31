// Exécutez: node generate-icons.cjs
// Ce script génère des fichiers PNG avec le logo VEHIX original

const fs = require('fs');
const path = require('path');
const { createCanvas, loadImage } = require('canvas');

console.log('🚀 Démarrage de la génération des icônes PNG VEHIX...\n');

// Créer le dossier icons s'il n'existe pas
const iconsDir = path.join(__dirname, 'public', 'icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

// Fonction pour détecter le type d'image
function detectImageType(filePath) {
    const buffer = fs.readFileSync(filePath);

    // Vérifier les magic numbers
    if (buffer[0] === 0x89 && buffer[1] === 0x50 && buffer[2] === 0x4E && buffer[3] === 0x47) {
        return 'PNG';
    }
    if (buffer[0] === 0xFF && buffer[1] === 0xD8 && buffer[2] === 0xFF) {
        return 'JPEG';
    }
    if (buffer[0] === 0x52 && buffer[1] === 0x49 && buffer[2] === 0x46 && buffer[3] === 0x46) {
        return 'WEBP';
    }
    if (buffer[0] === 0x47 && buffer[1] === 0x49 && buffer[2] === 0x46) {
        return 'GIF';
    }

    return 'UNKNOWN';
}

// Fonction pour créer une icône PNG avec le logo VEHIX
async function createIconPNG(size) {
    const canvas = createCanvas(size, size);
    const ctx = canvas.getContext('2d');

    // Chercher l'image du logo dans différents emplacements et formats
    const possiblePaths = [
        // Racine
        path.join(__dirname, 'logo-vehix.png'),
        path.join(__dirname, 'logo-vehix.jpg'),
        path.join(__dirname, 'logo-vehix.jpeg'),
        path.join(__dirname, 'logo-vehix.webp'),
        path.join(__dirname, 'vehix.png'),
        path.join(__dirname, 'vehix.jpg'),
        // Public
        path.join(__dirname, 'public', 'logo-vehix.png'),
        path.join(__dirname, 'public', 'logo-vehix.jpg'),
        path.join(__dirname, 'public', 'logo-vehix.jpeg'),
        path.join(__dirname, 'public', 'vehix.png'),
        // Assets
        path.join(__dirname, 'src', 'assets', 'logo-vehix.png'),
        path.join(__dirname, 'src', 'assets', 'logo-vehix.jpg'),
        path.join(__dirname, 'assets', 'logo-vehix.png'),
        path.join(__dirname, 'assets', 'logo-vehix.jpg')
    ];

    let logoPath = null;
    let imageType = null;

    for (const testPath of possiblePaths) {
        if (fs.existsSync(testPath)) {
            const type = detectImageType(testPath);
            if (type === 'PNG' || type === 'JPEG') {
                logoPath = testPath;
                imageType = type;
                break;
            } else if (type === 'WEBP') {
                console.warn(`⚠️  Format WEBP détecté. Convertissez votre logo en PNG ou JPEG.`);
                console.warn(`   Vous pouvez utiliser un outil en ligne comme: https://convertio.co/webp-png/\n`);
            }
        }
    }

    if (!logoPath) {
        console.error(`\n❌ Logo introuvable ou format non supporté!`);
        console.error(`\n📋 Vérifications effectuées:`);
        console.error(`   - Votre fichier 'logo-vehix.png' existe mais est peut-être en format WEBP`);
        console.error(`   - La bibliothèque 'canvas' supporte uniquement PNG et JPEG\n`);
        console.error(`💡 Solution:`);
        console.error(`   1. Ouvrez votre logo avec un éditeur d'image (Paint, Photoshop, GIMP, etc.)`);
        console.error(`   2. Exportez/Sauvegardez-le au format PNG`);
        console.error(`   3. Renommez-le 'logo-vehix.png'`);
        console.error(`   4. Placez-le dans: ${__dirname}/`);
        console.error(`\n   OU utilisez un convertisseur en ligne: https://convertio.co/webp-png/\n`);

        throw new Error('Logo non trouvé ou format non supporté');
    }

    if (size === 72) {
        console.log(`📷 Logo trouvé: ${logoPath}`);
        console.log(`📄 Format détecté: ${imageType}\n`);
    }

    // Charger l'image du logo
    const image = await loadImage(logoPath);

    // Redimensionner l'image pour remplir tout le canvas
    ctx.drawImage(image, 0, 0, size, size);

    return canvas;
}

// Générer les icônes
const sizes = [72, 96, 128, 144, 152, 192, 384, 512];

async function generateAllIcons() {
    console.log('🔍 Recherche du logo VEHIX...\n');

    let successCount = 0;
    let errorCount = 0;

    for (const size of sizes) {
        try {
            const canvas = await createIconPNG(size);
            const buffer = canvas.toBuffer('image/png');
            const filename = path.join(iconsDir, `icon-${size}x${size}.png`);

            // Suppression de l'ancien fichier SVG s'il existe
            const svgFilename = path.join(iconsDir, `icon-${size}x${size}.svg`);
            if (fs.existsSync(svgFilename)) {
                fs.unlinkSync(svgFilename);
                console.log(`🗑️  Supprimé: icon-${size}x${size}.svg`);
            }

            fs.writeFileSync(filename, buffer);
            console.log(`✅ Créé: icon-${size}x${size}.png (${(buffer.length / 1024).toFixed(1)} KB)`);
            successCount++;
        } catch (error) {
            console.error(`❌ Erreur pour la taille ${size}: ${error.message}`);
            errorCount++;

            if (error.message.includes('Logo non trouvé')) {
                break;
            }
        }
    }

    console.log(`\n📊 Résumé:`);
    console.log(`   ✅ Succès: ${successCount}/${sizes.length}`);
    console.log(`   ❌ Erreurs: ${errorCount}/${sizes.length}`);

    if (successCount > 0) {
        console.log(`\n🎉 ${successCount} icône(s) générée(s) avec succès!`);
        console.log(`📁 Les fichiers sont dans: public/icons/`);
    }
}

generateAllIcons().catch(error => {
    console.error('\n❌ Erreur fatale:', error.message);
    process.exit(1);
});
