/**
 * Regenerates the README screenshots in art/.
 *
 *   npm run screenshots
 *
 * Builds the dev assets and the workbench, serves it with the screenshot-only
 * actions enabled (NMR_SCREENSHOTS=1), opens each scene in a light and a dark
 * browser, and composites the pair into one image split diagonally: light
 * top-left, dark bottom-right.
 */
import { execSync, spawn } from 'node:child_process'
import { mkdir } from 'node:fs/promises'
import { fileURLToPath } from 'node:url'
import { chromium } from 'playwright'
import sharp from 'sharp'

const ROOT = fileURLToPath(new URL('..', import.meta.url))
const OUT = `${ROOT}art`
const PORT = 8799
const BASE = `http://127.0.0.1:${PORT}`

const SHOTS = [
    { scene: 'hero', file: 'hero.png' },
    { scene: 'text', file: 'blocks-text.png' },
    { scene: 'code', file: 'blocks-code.png' },
    { scene: 'inline', file: 'blocks-inline.png' },
    { scene: 'containers', file: 'blocks-containers.png' },
    { scene: 'one-liner', file: 'one-liner.png' },
]

const SCALE = 2 // device pixel ratio
const RADIUS = 8 * SCALE // matches the modal's rounded-lg
const MARGIN = 24 * SCALE // transparent room for the drop shadow

const run = (command) => execSync(command, { cwd: ROOT, stdio: 'inherit' })

async function waitForServer(url, timeoutMs = 30000) {
    const start = Date.now()
    while (Date.now() - start < timeoutMs) {
        try {
            const response = await fetch(url, { redirect: 'manual' })
            if (response.status < 500) return
        } catch {
            // not up yet
        }
        await new Promise((resolve) => setTimeout(resolve, 250))
    }
    throw new Error(`Workbench didn't start on ${url}`)
}

async function capture(browser, theme, scene) {
    const context = await browser.newContext({
        viewport: { width: 1280, height: 2400 },
        deviceScaleFactor: SCALE,
        colorScheme: theme,
    })
    const page = await context.newPage()

    await page.goto(`${BASE}/_workbench`)
    await page.goto(`${BASE}/nova/resources/users/1`)
    await page.locator('[dusk="1-control-selector"]').waitFor()

    // Force the theme regardless of a stored Nova preference, and hide toasts.
    await page.evaluate((dark) => document.documentElement.classList.toggle('dark', dark), theme === 'dark')
    await page.addStyleTag({ content: '.toasted-container { display: none !important; }' })

    await page.locator('[dusk="1-control-selector"]').click()
    await page.getByText(`Screenshot: ${scene}`, { exact: true }).click()

    const card = page.locator('[role="alertdialog"] .rounded-lg.shadow-lg').first()
    await card.waitFor()
    await page.waitForTimeout(400) // highlight.js + modal transition

    const png = await card.screenshot({ animations: 'disabled' })
    await context.close()

    return png
}

function roundedMask(width, height) {
    return Buffer.from(
        `<svg width="${width}" height="${height}"><rect width="${width}" height="${height}" rx="${RADIUS}" ry="${RADIUS}"/></svg>`,
    )
}

function diagonalMask(width, height) {
    // Bottom-right triangle: the dark half.
    return Buffer.from(
        `<svg width="${width}" height="${height}"><polygon points="${width},0 ${width},${height} 0,${height}"/></svg>`,
    )
}

async function compose(lightPng, darkPng) {
    // The two themes can differ by a few pixels in height; crop both to the
    // shorter one (top-aligned) rather than stretching.
    const [light, dark] = await Promise.all([sharp(lightPng).metadata(), sharp(darkPng).metadata()])
    const width = Math.min(light.width, dark.width)
    const height = Math.min(light.height, dark.height)
    const crop = (png) => sharp(png).extract({ left: 0, top: 0, width, height }).png().toBuffer()
    ;[lightPng, darkPng] = await Promise.all([crop(lightPng), crop(darkPng)])

    const darkHalf = await sharp(darkPng)
        .composite([{ input: diagonalMask(width, height), blend: 'dest-in' }])
        .png()
        .toBuffer()

    const card = await sharp(lightPng)
        .composite([
            { input: darkHalf },
            { input: roundedMask(width, height), blend: 'dest-in' },
        ])
        .png()
        .toBuffer()

    const shadow = await sharp({
        create: {
            width: width + MARGIN * 2,
            height: height + MARGIN * 2,
            channels: 4,
            background: { r: 0, g: 0, b: 0, alpha: 0 },
        },
    })
        .composite([{
            input: Buffer.from(
                `<svg width="${width}" height="${height}"><rect width="${width}" height="${height}" rx="${RADIUS}" fill="rgba(0,0,0,0.25)"/></svg>`,
            ),
            left: MARGIN,
            top: MARGIN + 4 * SCALE,
        }])
        .png()
        .toBuffer()

    return sharp(await sharp(shadow).blur(10 * SCALE).toBuffer())
        .composite([{ input: card, left: MARGIN, top: MARGIN }])
        .png({ compressionLevel: 9, palette: true, quality: 90 })
        .toBuffer()
}

async function main() {
    run('npm run dev')
    run('php vendor/bin/testbench workbench:build --ansi')

    const server = spawn('php', ['vendor/bin/testbench', 'serve', `--port=${PORT}`, '--no-reload'], {
        cwd: ROOT,
        env: { ...process.env, NMR_SCREENSHOTS: '1' },
        stdio: 'ignore',
    })

    try {
        await waitForServer(`${BASE}/_workbench`)
        await mkdir(OUT, { recursive: true })

        const browser = await chromium.launch()

        for (const { scene, file } of SHOTS) {
            const light = await capture(browser, 'light', scene)
            const dark = await capture(browser, 'dark', scene)

            if (process.env.NMR_DEBUG) {
                await sharp(light).toFile(`${OUT}/${file}.light.png`)
                await sharp(dark).toFile(`${OUT}/${file}.dark.png`)
            }

            await sharp(await compose(light, dark)).toFile(`${OUT}/${file}`)
            console.log(`art/${file}`)
        }

        await browser.close()
    } finally {
        server.kill()
    }
}

main().catch((error) => {
    console.error(error)
    process.exitCode = 1
})
