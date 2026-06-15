<div style="border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;background:#f8fafc">
    <p style="margin:0 0 4px;font-weight:600;font-size:18px">Nova Modal Response</p>
    <p style="margin:0 0 16px;color:#475569">
        A Nova action response that opens a modal built from an ordered stack of typed blocks.
        This whole modal body is one Blade view, rendered server-side via <code>ModalResponse::view()</code>.
    </p>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <span style="border-radius:9999px;padding:2px 10px;background:#e0e7ff;color:#3730a3;font-size:13px">PHP {{ $php }}</span>
        <span style="border-radius:9999px;padding:2px 10px;background:#fee2e2;color:#991b1b;font-size:13px">Laravel {{ $laravel }}</span>
        <span style="border-radius:9999px;padding:2px 10px;background:#dcfce7;color:#166534;font-size:13px">Nova {{ $nova }}</span>
    </div>
</div>
