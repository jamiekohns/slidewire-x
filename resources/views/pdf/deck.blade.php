<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $presentation }} - SlideWire</title>
    <style>
        body { font-family: sans-serif; margin: 0; color: #0f172a; }
        .slide { page-break-after: always; min-height: 90vh; padding: 2rem; box-sizing: border-box; }
        .slide:last-child { page-break-after: auto; }
        .notes { margin-top: 1rem; border-top: 1px solid #cbd5e1; padding-top: 0.75rem; color: #475569; }
    </style>
</head>
<body>
@foreach ($slides as $slide)
    <section class="slide">
        {!! $slide['html'] !!}
        @if ($includeNotes && isset($slide['meta']['notes']))
            <div class="notes">{{ $slide['meta']['notes'] }}</div>
        @endif
    </section>
@endforeach
</body>
</html>
