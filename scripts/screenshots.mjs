/**
 * Regenerates the README screenshots in art/.
 *
 *   npm run screenshots
 *
 * Builds the dev assets and the workbench, serves it with the screenshot-only
 * actions enabled (NMR_SCREENSHOTS=1), opens each scene in a light and a dark
 * browser, and composites the pair into one image: light on the left, dark on
 * the right, split by a slightly slanted line through the middle.
 *
 * The hero is the whole Nova page (users index behind the modal), the
 * one-liner is the whole modal, and the block scenes are cropped to just the
 * blocks (no title bar or footer).
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
    { scene: 'hero', file: 'hero.png', frame: 'page' },
    { scene: 'text', file: 'blocks-text.png', frame: 'body' },
    { scene: 'code', file: 'blocks-code.png', frame: 'body' },
    { scene: 'inline', file: 'blocks-inline.png', frame: 'body' },
    { scene: 'containers', file: 'blocks-containers.png', frame: 'body' },
    { scene: 'one-liner', file: 'one-liner.png', frame: 'modal' },
]

const SCALE = 2 // device pixel ratio
const PAGE_VIEWPORT = { width: 1510, height: 712 } // the hero's frame
const MODAL_VIEWPORT = { width: 1280, height: 2400 } // tall enough for any modal
const SPLIT = { top: 0.54, bottom: 0.43 } // where the light/dark seam crosses the top and bottom edge
const RADIUS = 8 * SCALE // matches the modal's rounded-lg
const MARGIN = 24 * SCALE // transparent room for the modal's drop shadow

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

async function capture(browser, theme, { scene, frame }) {
    const fullPage = frame === 'page'

    const context = await browser.newContext({
        viewport: fullPage ? PAGE_VIEWPORT : MODAL_VIEWPORT,
        deviceScaleFactor: SCALE,
        colorScheme: theme,
    })
    const page = await context.newPage()

    await page.goto(`${BASE}/_workbench`)
    await page.goto(`${BASE}/nova/resources/users`)

    // The first row's action menu; the top row keeps the index scrolled to the top.
    const rowActions = page.locator('[dusk$="-control-selector"]').first()
    await rowActions.waitFor()

    // Force the theme regardless of a stored Nova preference. Hide toasts and
    // the dev workbench's "Unregistered" license label.
    await page.evaluate((dark) => document.documentElement.classList.toggle('dark', dark), theme === 'dark')
    await page.addStyleTag({
        content: '.toasted-container, a[href="https://nova.laravel.com/licenses"] { display: none !important; }',
    })

    await rowActions.click()
    await page.getByText(`Screenshot: ${scene}`, { exact: true }).click()

    const card = page.locator('[role="alertdialog"] .rounded-lg.shadow-lg').first()
    await card.waitFor()
    await page.waitForTimeout(600) // highlight.js + modal transition

    let png
    if (frame === 'page') {
        png = await page.screenshot({ animations: 'disabled' })
    } else if (frame === 'body') {
        // Everything between the modal's title bar (first child) and footer (last child).
        const clip = await card.evaluate((element) => {
            const children = [...element.children]
            const first = children[1].getBoundingClientRect()
            const last = children[children.length - 2].getBoundingClientRect()
            const box = element.getBoundingClientRect()

            return { x: box.left, y: first.top, width: box.width, height: last.bottom - first.top }
        })
        png = await page.screenshot({ clip, animations: 'disabled' })
    } else {
        png = await card.screenshot({ animations: 'disabled' })
    }
    await context.close()

    return png
}

function svg(width, height, body) {
    return Buffer.from(`<svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">${body}</svg>`)
}

/** The dark side: everything right of the seam. */
function darkSideMask(width, height) {
    const top = Math.round(width * SPLIT.top)
    const bottom = Math.round(width * SPLIT.bottom)

    return svg(width, height, `<polygon points="${top},0 ${width},0 ${width},${height} ${bottom},${height}"/>`)
}

async function split(lightPng, darkPng) {
    // The two themes can differ by a pixel or two; crop both to the overlap
    // (top-left aligned) rather than stretching.
    const [light, dark] = await Promise.all([sharp(lightPng).metadata(), sharp(darkPng).metadata()])
    const width = Math.min(light.width, dark.width)
    const height = Math.min(light.height, dark.height)
    const crop = (png) => sharp(png).extract({ left: 0, top: 0, width, height }).png().toBuffer()
    ;[lightPng, darkPng] = await Promise.all([crop(lightPng), crop(darkPng)])

    const darkSide = await sharp(darkPng)
        .composite([{ input: darkSideMask(width, height), blend: 'dest-in' }])
        .png()
        .toBuffer()

    const image = await sharp(lightPng).composite([{ input: darkSide }]).png().toBuffer()

    return { image, width, height }
}

/** Rounded corners and a soft drop shadow for the modal-only shots. */
async function asCard({ image, width, height }) {
    const card = await sharp(image)
        .composite([{ input: svg(width, height, `<rect width="${width}" height="${height}" rx="${RADIUS}"/>`), blend: 'dest-in' }])
        .png()
        .toBuffer()

    const shadow = await sharp({
        create: { width: width + MARGIN * 2, height: height + MARGIN * 2, channels: 4, background: { r: 0, g: 0, b: 0, alpha: 0 } },
    })
        .composite([{
            input: svg(width, height, `<rect width="${width}" height="${height}" rx="${RADIUS}" fill="rgba(0,0,0,0.18)"/>`),
            left: MARGIN,
            top: MARGIN + 4 * SCALE,
        }])
        .png()
        .toBuffer()

    return sharp(await sharp(shadow).blur(10 * SCALE).toBuffer())
        .composite([{ input: card, left: MARGIN, top: MARGIN }])
        .png()
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

        for (const shot of SHOTS) {
            const light = await capture(browser, 'light', shot)
            const dark = await capture(browser, 'dark', shot)

            if (process.env.NMR_DEBUG) {
                await sharp(light).toFile(`${OUT}/${shot.file}.light.png`)
                await sharp(dark).toFile(`${OUT}/${shot.file}.dark.png`)
            }

            const result = await split(light, dark)
            const png = shot.frame === 'page' ? result.image : await asCard(result)

            await sharp(png).png({ compressionLevel: 9 }).toFile(`${OUT}/${shot.file}`)
            console.log(`art/${shot.file}`)
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
