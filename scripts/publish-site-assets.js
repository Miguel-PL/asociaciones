import {
    cpSync,
    existsSync,
    mkdirSync,
    realpathSync,
    readdirSync,
    statSync,
} from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const sitesDirectory = join(projectRoot, 'sites');
const publicSitesDirectory = join(projectRoot, 'public', 'sites');

function sameRealPath(firstPath, secondPath) {
    try {
        const firstRealPath = realpathSync.native(firstPath);
        const secondRealPath = realpathSync.native(secondPath);

        return process.platform === 'win32'
            ? firstRealPath.toLowerCase() === secondRealPath.toLowerCase()
            : firstRealPath === secondRealPath;
    } catch {
        return false;
    }
}

if (!existsSync(sitesDirectory)) {
    throw new Error(`No existe el directorio de sitios: ${sitesDirectory}`);
}

if (sameRealPath(sitesDirectory, publicSitesDirectory)) {
    console.log('public/sites ya apunta a sites; no se copian assets sobre sí mismos.');
    process.exit(0);
}

mkdirSync(publicSitesDirectory, { recursive: true });

let publishedSites = 0;

for (const entry of readdirSync(sitesDirectory, { withFileTypes: true })) {
    if (!entry.isDirectory()) {
        continue;
    }

    const sourceAssets = join(sitesDirectory, entry.name, 'assets');

    if (!existsSync(sourceAssets) || !statSync(sourceAssets).isDirectory()) {
        continue;
    }

    const destinationAssets = join(publicSitesDirectory, entry.name, 'assets');

    if (sameRealPath(sourceAssets, destinationAssets)) {
        console.log(`Se omite ${entry.name}: el destino ya apunta a sus assets de origen.`);
        continue;
    }

    mkdirSync(dirname(destinationAssets), { recursive: true });
    cpSync(sourceAssets, destinationAssets, { recursive: true, force: true });
    publishedSites++;
    console.log(`Assets publicados: ${entry.name}`);
}

console.log(`Publicación terminada (${publishedSites} sitio(s)).`);
