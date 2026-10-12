{{-- Translucent colours + inherited text colour so the card reads in both light and dark mode. --}}
<div style="border:1px solid rgba(127,127,127,.25);border-radius:8px;padding:16px 20px;background:rgba(127,127,127,.06)">
    <p style="margin:0 0 4px;font-weight:600;font-size:18px">Nova Modal Response</p>
    <p style="margin:0 0 16px;opacity:.75">
        A Nova action response that opens a modal built from an ordered stack of typed blocks.
        This whole modal body is one Blade view, rendered server-side via <code>ModalResponse::view()</code>.
    </p>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <span style="border-radius:9999px;padding:2px 10px;background:rgba(99,102,241,.18);font-size:13px">PHP {{ $php }}</span>
        <span style="border-radius:9999px;padding:2px 10px;background:rgba(239,68,68,.18);font-size:13px">Laravel {{ $laravel }}</span>
        <span style="border-radius:9999px;padding:2px 10px;background:rgba(34,197,94,.18);font-size:13px">Nova {{ $nova }}</span>
    </div>
</div>
