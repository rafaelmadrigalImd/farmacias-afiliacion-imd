<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 40" {{ $attributes }}>
    <!-- Fondo redondeado con degradado sutil -->
    <defs>
        <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:rgb(4,123,127);stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb(14,165,170);stop-opacity:1" />
        </linearGradient>
    </defs>

    <!-- Rectángulo con bordes redondeados -->
    <rect x="2" y="2" width="116" height="36" rx="6" fill="url(#bgGradient)"/>

    <!-- Letras IMD en blanco, centradas -->
    <text x="60" y="27" font-family="Arial, sans-serif" font-size="22" font-weight="bold" fill="white" text-anchor="middle">IMD</text>
</svg>
