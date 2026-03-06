<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $presentation }} - SlideWire</title>
    @php
        $configuredCodeFontSize = match (trim($slidesConfig->highlight->fontSize)) {
            'xs' => '0.75rem',
            'sm' => '0.875rem',
            'md' => '1rem',
            'lg' => '1.125rem',
            'xl' => '1.25rem',
            '2xl' => '1.5rem',
            default => trim($slidesConfig->highlight->fontSize),
        };
    @endphp
    @if($googleFontsUrl)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ $googleFontsUrl }}" rel="stylesheet">
    @endif
    @if(!empty($inlineCss))
        <style>{!! $inlineCss !!}</style>
    @endif
    <style>
        html, body { margin: 0; padding: 0; width: 100%; }
        .slidewire-pdf-slide {
            page-break-after: always;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1.5rem, 4vw, 4rem);
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }
        .slidewire-pdf-slide:last-child { page-break-after: auto; }
        .slidewire-pdf-slide::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgb(3 8 20 / var(--slidewire-background-opacity, 0));
            z-index: 0;
        }
        .slidewire-pdf-background {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .slidewire-pdf-content {
            position: relative;
            z-index: 1;
            width: min(1200px, 90%);
        }
        .slidewire-pdf-content h1, .slidewire-pdf-content h2, .slidewire-pdf-content h3,
        .slidewire-pdf-content h4, .slidewire-pdf-content h5, .slidewire-pdf-content h6 { line-height: 1.2; }
        .slidewire-pdf-content pre.phiki,
        .slidewire-pdf-content pre.slidewire-code {
            padding: 1.25rem 1.5rem;
            margin: 1rem 0;
            border-radius: 0.75rem;
            overflow-x: auto;
            font-size: {{ $configuredCodeFontSize }};
            line-height: 1.6;
        }
        .slidewire-pdf-content pre.slidewire-code { background: #24292e; color: #e1e4e8; }
        .slidewire-pdf-content pre code, .slidewire-pdf-content code { font-family: {{ $codeFontFamily }}; }
        .slidewire-fragment { opacity: 1 !important; transform: none !important; }
    </style>
</head>
<body>
@foreach ($effectiveSlides as $slide)
    @php
        $effective = $slide->effective;
        $meta = $slide->meta;

        $themeName = $effective['theme'] ?? 'default';
        $themeBackground = $configuredThemes[$themeName] ?? '';
        $slideTypography = $themeTypography[$themeName] ?? ['title' => '', 'text' => ''];

        $backgroundImage = $meta['background_image'] ?? null;
        $rawBackground = $meta['background'] ?? null;
        $isBackgroundAsset = is_string($rawBackground) && preg_match('/^(https?:|\/|\.\/|\.\.\/)/', $rawBackground) === 1;
        if ($isBackgroundAsset && $backgroundImage === null) {
            $backgroundImage = $rawBackground;
        }

        $slideStyles = [];
        if (is_string($backgroundImage) && $backgroundImage !== '') {
            $slideStyles[] = 'background-image: url('.$backgroundImage.')';
            $slideStyles[] = 'background-size: '.($meta['background_size'] ?? 'cover');
            $slideStyles[] = 'background-position: '.($meta['background_position'] ?? 'center');
            $slideStyles[] = 'background-repeat: '.($meta['background_repeat'] ?? 'no-repeat');
        }
        if (isset($meta['background_opacity']) && $meta['background_opacity'] !== '') {
            $slideStyles[] = '--slidewire-background-opacity: '.$meta['background_opacity'];
        }
        $slideStyle = implode(';', $slideStyles);
    @endphp
    <section class="slidewire-pdf-slide {{ $themeBackground }} {{ $slide->class }}" style="{{ $slideStyle }}">
        <div class="slidewire-pdf-content {{ $slideTypography['text'] }}">
            {!! $slide->html !!}
        </div>
    </section>
@endforeach
</body>
</html>
