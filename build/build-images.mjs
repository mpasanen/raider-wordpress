#!/usr/bin/env node
/**
 * Generate optimized AVIF + WebP + JPG/PNG variants from originals.
 * Run: node build/build-images.mjs
 */
import sharp from 'sharp';
import { readdir, mkdir } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';

const root = path.resolve(path.dirname(new URL(import.meta.url).pathname).replace(/^\//, ''), '..');
const groups = [
  {
    inDir: path.join(root, 'assets/images'),
    outDir: path.join(root, 'assets/images/optimized'),
    images: {
      'hero.jpg':         { widths: [480, 768, 1024, 1440, 1920], q: { avif: 50, webp: 65, jpeg: 75 } },
      'about.jpg':        { widths: [360, 540, 720, 960],         q: { avif: 45, webp: 60, jpeg: 75 } },
      'contact.jpg':      { widths: [360, 540, 720, 960],         q: { avif: 45, webp: 60, jpeg: 75 } },
      'tomppa-quote.jpg': { widths: [400, 600, 800],              q: { avif: 50, webp: 65, jpeg: 75 } },
      'logo.png':         { widths: [120, 240, 440],              q: { avif: 70, webp: 75, png: true } },
    },
  },
  {
    inDir: path.join(root, 'assets/logos'),
    outDir: path.join(root, 'assets/logos/optimized'),
    autoDiscover: { widths: [120, 240], q: { avif: 65, webp: 70 } },
  },
];

async function ensure(dir) { if (!existsSync(dir)) await mkdir(dir, { recursive: true }); }

async function process(inPath, outDir, base, widths, q) {
  await ensure(outDir);
  const stem = base.replace(/\.[^.]+$/, '');
  const isPng = /\.png$/i.test(base);
  for (const w of widths) {
    const tasks = [];
    tasks.push(sharp(inPath).resize({ width: w, withoutEnlargement: true }).avif({ quality: q.avif, effort: 6 }).toFile(path.join(outDir, `${stem}-${w}.avif`)));
    tasks.push(sharp(inPath).resize({ width: w, withoutEnlargement: true }).webp({ quality: q.webp, effort: 6 }).toFile(path.join(outDir, `${stem}-${w}.webp`)));
    if (isPng && q.png) {
      tasks.push(sharp(inPath).resize({ width: w, withoutEnlargement: true }).png({ compressionLevel: 9, palette: true }).toFile(path.join(outDir, `${stem}-${w}.png`)));
    } else if (!isPng) {
      tasks.push(sharp(inPath).resize({ width: w, withoutEnlargement: true }).jpeg({ quality: q.jpeg ?? 75, mozjpeg: true }).toFile(path.join(outDir, `${stem}-${w}.jpg`)));
    }
    await Promise.all(tasks);
    console.log(`  ${stem} @ ${w}w`);
  }
}

for (const g of groups) {
  console.log(`\n→ ${path.relative(root, g.inDir)}`);
  if (g.images) {
    for (const [base, cfg] of Object.entries(g.images)) {
      const p = path.join(g.inDir, base);
      if (!existsSync(p)) { console.warn(`  SKIP missing: ${base}`); continue; }
      await process(p, g.outDir, base, cfg.widths, cfg.q);
    }
  }
  if (g.autoDiscover) {
    await ensure(g.outDir);
    const entries = await readdir(g.inDir);
    for (const f of entries) {
      if (!/\.(png|jpg|jpeg|webp)$/i.test(f)) continue;
      if (f === 'optimized') continue;
      const p = path.join(g.inDir, f);
      const isPng = /\.png$/i.test(f);
      const stem = f.replace(/\.[^.]+$/, '');
      for (const w of g.autoDiscover.widths) {
        await sharp(p).resize({ width: w, withoutEnlargement: true }).avif({ quality: g.autoDiscover.q.avif, effort: 6 }).toFile(path.join(g.outDir, `${stem}-${w}.avif`));
        await sharp(p).resize({ width: w, withoutEnlargement: true }).webp({ quality: g.autoDiscover.q.webp, effort: 6 }).toFile(path.join(g.outDir, `${stem}-${w}.webp`));
      }
      console.log(`  ${stem}`);
    }
  }
}

console.log('\nDone.');
