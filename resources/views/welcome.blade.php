<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /* ! tailwindcss v3.4.1 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:Figtree, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}*, ::before, ::after{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }.absolute{position:absolute}.relative{position:relative}.-left-20{left:-5rem}.top-0{top:0px}.-bottom-16{bottom:-4rem}.-left-16{left:-4rem}.-mx-3{margin-left:-0.75rem;margin-right:-0.75rem}.mt-4{margin-top:1rem}.mt-6{margin-top:1.5rem}.flex{display:flex}.grid{display:grid}.hidden{display:none}.aspect-video{aspect-ratio:16 / 9}.size-12{width:3rem;height:3rem}.size-5{width:1.25rem;height:1.25rem}.size-6{width:1.5rem;height:1.5rem}.h-12{height:3rem}.h-40{height:10rem}.h-full{height:100%}.min-h-screen{min-height:100vh}.w-full{width:100%}.w-\[calc\(100\%\+8rem\)\]{width:calc(100% + 8rem)}.w-auto{width:auto}.max-w-\[877px\]{max-width:877px}.max-w-2xl{max-width:42rem}.flex-1{flex:1 1 0%}.shrink-0{flex-shrink:0}.grid-cols-2{grid-template-columns:repeat(2, minmax(0, 1fr))}.flex-col{flex-direction:column}.items-start{align-items:flex-start}.items-center{align-items:center}.items-stretch{align-items:stretch}.justify-end{justify-content:flex-end}.justify-center{justify-content:center}.gap-2{gap:0.5rem}.gap-4{gap:1rem}.gap-6{gap:1.5rem}.self-center{align-self:center}.overflow-hidden{overflow:hidden}.rounded-\[10px\]{border-radius:10px}.rounded-full{border-radius:9999px}.rounded-lg{border-radius:0.5rem}.rounded-md{border-radius:0.375rem}.rounded-sm{border-radius:0.125rem}.bg-\[\#FF2D20\]\/10{background-color:rgb(255 45 32 / 0.1)}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity))}.bg-gradient-to-b{background-image:linear-gradient(to bottom, var(--tw-gradient-stops))}.from-transparent{--tw-gradient-from:transparent var(--tw-gradient-from-position);--tw-gradient-to:rgb(0 0 0 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.via-white{--tw-gradient-to:rgb(255 255 255 / 0)  var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), #fff var(--tw-gradient-via-position), var(--tw-gradient-to)}.to-white{--tw-gradient-to:#fff var(--tw-gradient-to-position)}.stroke-\[\#FF2D20\]{stroke:#FF2D20}.object-cover{object-fit:cover}.object-top{object-position:top}.p-6{padding:1.5rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.px-3{padding-left:0.75rem;padding-right:0.75rem}.py-16{padding-top:4rem;padding-bottom:4rem}.py-2{padding-top:0.5rem;padding-bottom:0.5rem}.pt-3{padding-top:0.75rem}.text-center{text-align:center}.font-sans{font-family:Figtree, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji}.text-sm{font-size:0.875rem;line-height:1.25rem}.text-sm\/relaxed{font-size:0.875rem;line-height:1.625}.text-xl{font-size:1.25rem;line-height:1.75rem}.font-semibold{font-weight:600}.text-black{--tw-text-opacity:1;color:rgb(0 0 0 / var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.underline{-webkit-text-decoration-line:underline;text-decoration-line:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.shadow-\[0px_14px_34px_0px_rgba\(0\2c 0\2c 0\2c 0\.08\)\]{--tw-shadow:0px 14px 34px 0px rgba(0,0,0,0.08);--tw-shadow-colored:0px 14px 34px 0px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)}.ring-transparent{--tw-ring-color:transparent}.ring-white\/\[0\.05\]{--tw-ring-color:rgb(255 255 255 / 0.05)}.drop-shadow-\[0px_4px_34px_rgba\(0\2c 0\2c 0\2c 0\.06\)\]{--tw-drop-shadow:drop-shadow(0px 4px 34px rgba(0,0,0,0.06));filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.drop-shadow-\[0px_4px_34px_rgba\(0\2c 0\2c 0\2c 0\.25\)\]{--tw-drop-shadow:drop-shadow(0px 4px 34px rgba(0,0,0,0.25));filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.transition{transition-property:color, background-color, border-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-text-decoration-color, -webkit-backdrop-filter;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-text-decoration-color, -webkit-backdrop-filter;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.duration-300{transition-duration:300ms}.selection\:bg-\[\#FF2D20\] *::selection{--tw-bg-opacity:1;background-color:rgb(255 45 32 / var(--tw-bg-opacity))}.selection\:text-white *::selection{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.selection\:bg-\[\#FF2D20\]::selection{--tw-bg-opacity:1;background-color:rgb(255 45 32 / var(--tw-bg-opacity))}.selection\:text-white::selection{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.hover\:text-black:hover{--tw-text-opacity:1;color:rgb(0 0 0 / var(--tw-text-opacity))}.hover\:text-black\/70:hover{color:rgb(0 0 0 / 0.7)}.hover\:ring-black\/20:hover{--tw-ring-color:rgb(0 0 0 / 0.2)}.focus\:outline-none:focus{outline:2px solid transparent;outline-offset:2px}.focus-visible\:ring-1:focus-visible{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)}.focus-visible\:ring-\[\#FF2D20\]:focus-visible{--tw-ring-opacity:1;--tw-ring-color:rgb(255 45 32 / var(--tw-ring-opacity))}@media (min-width: 640px){.sm\:size-16{width:4rem;height:4rem}.sm\:size-6{width:1.5rem;height:1.5rem}.sm\:pt-5{padding-top:1.25rem}}@media (min-width: 768px){.md\:row-span-3{grid-row:span 3 / span 3}}@media (min-width: 1024px){.lg\:col-start-2{grid-column-start:2}.lg\:h-16{height:4rem}.lg\:max-w-7xl{max-width:80rem}.lg\:grid-cols-3{grid-template-columns:repeat(3, minmax(0, 1fr))}.lg\:grid-cols-2{grid-template-columns:repeat(2, minmax(0, 1fr))}.lg\:flex-col{flex-direction:column}.lg\:items-end{align-items:flex-end}.lg\:justify-center{justify-content:center}.lg\:gap-8{gap:2rem}.lg\:p-10{padding:2.5rem}.lg\:pb-10{padding-bottom:2.5rem}.lg\:pt-0{padding-top:0px}.lg\:text-\[\#FF2D20\]{--tw-text-opacity:1;color:rgb(255 45 32 / var(--tw-text-opacity))}}@media (prefers-color-scheme: dark){.dark\:block{display:block}.dark\:hidden{display:none}.dark\:bg-black{--tw-bg-opacity:1;background-color:rgb(0 0 0 / var(--tw-bg-opacity))}.dark\:bg-zinc-900{--tw-bg-opacity:1;background-color:rgb(24 24 27 / var(--tw-bg-opacity))}.dark\:via-zinc-900{--tw-gradient-to:rgb(24 24 27 / 0)  var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), #18181b var(--tw-gradient-via-position), var(--tw-gradient-to)}.dark\:to-zinc-900{--tw-gradient-to:#18181b var(--tw-gradient-to-position)}.dark\:text-white\/50{color:rgb(255 255 255 / 0.5)}.dark\:text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.dark\:text-white\/70{color:rgb(255 255 255 / 0.7)}.dark\:ring-zinc-800{--tw-ring-opacity:1;--tw-ring-color:rgb(39 39 42 / var(--tw-ring-opacity))}.dark\:hover\:text-white:hover{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.dark\:hover\:text-white\/70:hover{color:rgb(255 255 255 / 0.7)}.dark\:hover\:text-white\/80:hover{color:rgb(255 255 255 / 0.8)}.dark\:hover\:ring-zinc-700:hover{--tw-ring-opacity:1;--tw-ring-color:rgb(63 63 70 / var(--tw-ring-opacity))}.dark\:focus-visible\:ring-\[\#FF2D20\]:focus-visible{--tw-ring-opacity:1;--tw-ring-color:rgb(255 45 32 / var(--tw-ring-opacity))}.dark\:focus-visible\:ring-white:focus-visible{--tw-ring-opacity:1;--tw-ring-color:rgb(255 255 255 / var(--tw-ring-opacity))}}
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        :root {
            --primary-color: #4a86e8;
            --primary-hover: #3a76d8;
            --secondary-color: #34a853;
            --text-color: #333;
            --light-bg: #f5f5f5;
            --white: #fff;
            --border-color: #ddd;
            --box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light-bg);
            color: var(--text-color);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            background-color: var(--white);
            box-shadow: var(--box-shadow);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            text-align: center;
            color: var(--primary-color);
            font-size: 2rem;
        }

        .header-subtitle {
            text-align: center;
            color: #666;
            margin-top: 10px;
        }

        .nav-container {
            background-color: var(--white);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            border-radius: 5px;
            overflow: hidden;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--border-color);
        }

        .tab {
            padding: 15px 20px;
            cursor: pointer;
            border: none;
            background-color: var(--white);
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            flex: 1;
            text-align: center;
        }

        .tab:hover {
            background-color: rgba(74, 134, 232, 0.1);
        }

        .tab.active {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .content {
            background-color: var(--white);
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: var(--box-shadow);
        }

        .content-title {
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8rem;
            color: var(--primary-color);
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .chart-container {
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 20px;
            background-color: var(--white);
            height: 400px;
            position: relative;
        }

        @media (max-width: 992px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-title {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.2rem;
            color: #555;
        }

        .chart-note {
            font-size: 0.9rem;
            color: #777;
            margin-top: 10px;
        }

        .info-list {
            padding-left: 20px;
        }

        .info-list li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 12px;
            flex-shrink: 0;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .data-table th, .data-table td {
            border: 1px solid var(--border-color);
            padding: 12px;
        }

        .data-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .data-table .number {
            text-align: right;
        }

        /* Control panels */
        .control-panel {
            background-color: #f9f9f9;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .control-panel h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: var(--primary-color);
        }

        .control-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }

        .control-item {
            margin-bottom: 15px;
        }

        .control-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .control-item input, .control-item select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .control-item input:focus, .control-item select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 134, 232, 0.2);
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
        }

        .btn-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Premium features section */
        .pricing-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .pricing-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 25px;
            background-color: white;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .pricing-card h3 {
            margin-top: 0;
            color: var(--primary-color);
            font-size: 1.4rem;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .pricing-card .price {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 15px 0;
            color: var(--text-color);
        }

        .pricing-card .price-period {
            font-size: 0.9rem;
            color: #777;
            font-weight: normal;
        }

        .pricing-card ul {
            list-style-type: none;
            padding: 0;
            margin: 20px 0;
        }

        .pricing-card li {
            padding: 8px 0;
            display: flex;
            align-items: center;
        }

        .pricing-card li:before {
            content: "✓";
            color: var(--secondary-color);
            margin-right: 10px;
            font-weight: bold;
        }

        .pricing-card .btn {
            width: 100%;
            text-align: center;
        }

        /* Recommendations section */
        .recommendations {
            margin-top: 40px;
        }

        .recommendations ul {
            padding-left: 25px;
        }

        .recommendations li {
            margin-bottom: 15px;
        }

        .recommendations li strong {
            color: var(--text-color);
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            color: #777;
            font-size: 0.9rem;
        }

        /* Hidden section */
        .section-content {
            display: none;
        }

        .section-content.active {
            display: block;
        }

        /* Print specific styles */
        @media print {
            .nav-container, .control-panel, .btn, .btn-group {
                display: none !important;
            }

            .section-content {
                display: block !important;
                page-break-inside: avoid;
                margin-bottom: 20px;
            }

            .chart-container {
                page-break-inside: avoid;
                height: 300px;
            }

            body {
                font-size: 11pt;
                line-height: 1.3;
            }

            .chart-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="font-sans antialiased dark:bg-black dark:text-white/50">
<div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
    <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" />
    <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
            <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                <div class="flex lg:justify-center lg:col-start-2">
                    <svg class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z" fill="currentColor"/></svg>
                </div>
                @if (Route::has('login'))
                    <nav class="-mx-3 flex flex-1 justify-end">
                        @auth
                            <a wire:navigate.hover
                               href="{{ url('/dashboard') }}"
                               class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Dashboard
                            </a>
                        @else
                            <a wire:navigate.hover
                               href="{{ route('login') }}"
                               class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a wire:navigate.hover
                                   href="{{ route('register') }}"
                                   class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Register
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="mt-6">
                <main class="mt-6">

                    <div class="header">
                        <div class="container">
                            <h1>Venditorix Freemium-Modell</h1>
                            <p class="header-subtitle">Interaktive Datenanalyse und Prognose</p>
                        </div>
                    </div>

                    <div class="container">
                        <div class="nav-container">
                            <div class="tabs">
                                <button class="tab active" data-section="overview">Übersicht</button>
                                <button class="tab" data-section="market">Marktpotenzial</button>
                                <button class="tab" data-section="conversion">Konversion</button>
                                <button class="tab" data-section="revenue">Umsatzprognose</button>
                                <button class="tab" data-section="costs">Betriebskosten</button>
                                <button class="tab" data-section="growth">Wachstum</button>
                                <button class="tab" data-section="roi">ROI Analyse</button>
                                <button class="tab" data-section="premium">Premium-Features</button>
                            </div>
                        </div>

                        <!-- Overview Section -->
                        <div id="overview" class="section-content active">
                            <div class="content">
                                <h2 class="content-title">Übersicht: Venditorix Freemium-Modell</h2>

                                <p>Das Venditorix Freemium-Modell bietet eine skalierbare Lösung für die Vermarktung der Verkaufstextgenerator-Software. Basierend auf umfassenden Datenanalysen zeigt dieser interaktive Report alle relevanten Metriken und Prognosen.</p>

                                <div class="control-panel">
                                    <h3>Datenparameter anpassen</h3>
                                    <p>Passen Sie die Parameter an, um verschiedene Szenarien zu simulieren.</p>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="conversionRate">Free-zu-Paid Konversionsrate (%)</label>
                                            <input type="number" id="conversionRate" min="0.1" max="20" step="0.1" value="5">
                                        </div>

                                        <div class="control-item">
                                            <label for="userBase">Geplante Nutzerbasis</label>
                                            <input type="number" id="userBase" min="1000" max="1000000" step="1000" value="100000">
                                        </div>

                                        <div class="control-item">
                                            <label for="proPrice">Professional Abo-Preis (€/Monat)</label>
                                            <input type="number" id="proPrice" min="1" max="200" step="0.5" value="29.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="businessPrice">Business Abo-Preis (€/Monat)</label>
                                            <input type="number" id="businessPrice" min="1" max="500" step="0.5" value="79.99">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateDataBtn" class="btn">Daten aktualisieren</button>
                                        <button id="printReportBtn" class="btn btn-secondary">Report drucken</button>
                                    </div>
                                </div>

                                <div class="chart-grid">
                                    <div class="chart-container">
                                        <h3 class="chart-title">Umsatzverteilung nach Nutzertyp</h3>
                                        <canvas id="overviewRevenueChart"></canvas>
                                    </div>

                                    <div class="chart-container">
                                        <h3 class="chart-title">Nutzerwachstum und Umsatzentwicklung</h3>
                                        <canvas id="overviewGrowthChart"></canvas>
                                    </div>
                                </div>

                                <div class="recommendations">
                                    <h3>Zusammenfassung der wichtigsten Erkenntnisse</h3>
                                    <ul>
                                        <li><strong>Marktpotenzial:</strong> 20-25 Millionen potenzielle Nutzer weltweit, mit Schwerpunkt auf B2B- und B2C-Vertriebsmitarbeitern.</li>
                                        <li><strong>Umsatzpotenzial:</strong> <span id="revenuePotential">284.950 €</span> monatlich bei <span id="userBaseDisplay">100.000</span> Nutzern.</li>
                                        <li><strong>Gewinnmarge:</strong> <span id="profitMargin">43%</span> bei optimaler Skalierung.</li>
                                        <li><strong>ROI:</strong> Deutlich höherer ROI des Freemium-Modells im Vergleich zum traditionellen Modell.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Market Potential Section -->
                        <div id="market" class="section-content">
                            <div class="content">
                                <h2 class="content-title">1. Marktpotenzial und Zielgruppensegmentierung</h2>

                                <div class="control-panel">
                                    <h3>Marktdaten anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="totalMarketSize">Gesamtes Marktpotenzial (Mio. Nutzer)</label>
                                            <input type="number" id="totalMarketSize" min="10" max="100" step="1" value="25">
                                        </div>

                                        <div class="control-item">
                                            <label for="b2bPercentage">Anteil B2B-Verkäufer (%)</label>
                                            <input type="number" id="b2bPercentage" min="1" max="100" step="1" value="38">
                                        </div>

                                        <div class="control-item">
                                            <label for="b2cPercentage">Anteil B2C-Vertriebsmitarbeiter (%)</label>
                                            <input type="number" id="b2cPercentage" min="1" max="100" step="1" value="32">
                                        </div>

                                        <div class="control-item">
                                            <label for="naPercentage">Anteil Nordamerika (%)</label>
                                            <input type="number" id="naPercentage" min="1" max="100" step="1" value="40">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateMarketBtn" class="btn">Marktdaten aktualisieren</button>
                                    </div>
                                </div>

                                <div class="chart-grid">
                                    <div class="chart-container">
                                        <h3 class="chart-title">Servicefähiger adressierbarer Markt (SAM) nach Regionen</h3>
                                        <canvas id="marketRegionsChart"></canvas>
                                        <p class="chart-note">SAM von insgesamt ca. <span id="samTotal">20-25</span> Millionen potenziellen Nutzern.</p>
                                    </div>

                                    <div class="chart-container">
                                        <h3 class="chart-title">Zielgruppenanalyse nach Berufstypen</h3>
                                        <canvas id="targetGroupsChart"></canvas>
                                    </div>
                                </div>

                                <div>
                                    <h3>Marktanalyse und Potenzial</h3>
                                    <p>Der servicefähige adressierbare Markt (SAM) für Venditorix umfasst Vertriebsmitarbeiter, die regelmäßig Verkaufsgespräche führen und strukturierte Verkaufstechniken anwenden. Die Analyse zeigt, dass der Hauptmarkt in Nordamerika und Europa liegt, mit wachsendem Potenzial in Asien-Pazifik.</p>

                                    <p>Bei einer realistischen Marktdurchdringung von 0.5-1% des SAM könnten mittelfristig (3-5 Jahre) 100.000-250.000 Benutzer erreicht werden.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Conversion Section -->
                        <div id="conversion" class="section-content">
                            <div class="content">
                                <h2 class="content-title">2. Freemium-Konversionstrichter</h2>

                                <div class="control-panel">
                                    <h3>Konversionsraten anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="visitorToRegistration">Besucher zu Registrierung (%)</label>
                                            <input type="number" id="visitorToRegistration" min="0.1" max="20" step="0.1" value="6.5">
                                        </div>

                                        <div class="control-item">
                                            <label for="registrationToActive">Registrierung zu aktiven Free-Nutzern (%)</label>
                                            <input type="number" id="registrationToActive" min="1" max="100" step="1" value="50">
                                        </div>

                                        <div class="control-item">
                                            <label for="activeToPayingTotal">Free zu Paid - insgesamt (%)</label>
                                            <input type="number" id="activeToPayingTotal" min="0.1" max="20" step="0.1" value="5">
                                        </div>

                                        <div class="control-item">
                                            <label for="activeToPayingEngaged">Free zu Paid - nach 90 Tagen Aktivität (%)</label>
                                            <input type="number" id="activeToPayingEngaged" min="0.1" max="30" step="0.1" value="10">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateConversionBtn" class="btn">Konversionsdaten aktualisieren</button>
                                    </div>
                                </div>

                                <div class="chart-grid">
                                    <div class="chart-container">
                                        <h3 class="chart-title">Konversionstrichter-Visualisierung</h3>
                                        <canvas id="conversionFunnelChart"></canvas>
                                    </div>

                                    <div class="chart-container">
                                        <h3 class="chart-title">Erwartete Konversionsraten</h3>
                                        <ul class="info-list">
                                            <li>
                                                <span class="dot" style="background-color: #4a86e8;"></span>
                                                Besucher zu Registrierung: <strong id="visitorToRegistrationDisplay">5-8%</strong>
                                            </li>
                                            <li>
                                                <span class="dot" style="background-color: #4bd086;"></span>
                                                Registrierung zu aktiven Free-Nutzern: <strong id="registrationToActiveDisplay">40-60%</strong>
                                            </li>
                                            <li>
                                                <span class="dot" style="background-color: #ffd044;"></span>
                                                Free zu Paid (insgesamt): <strong id="activeToPayingTotalDisplay">3-5%</strong>
                                            </li>
                                            <li>
                                                <span class="dot" style="background-color: #9c60e9;"></span>
                                                Free zu Paid (nach 90 Tagen Aktivität): <strong id="activeToPayingEngagedDisplay">8-12%</strong>
                                            </li>
                                        </ul>

                                        <div style="margin-top: 30px;">
                                            <h4>Konversionsstrategie</h4>
                                            <p>Die Konversionsraten basieren auf Benchmarks erfolgreicher SaaS-Freemium-Modelle. Strategien zur Optimierung der Konversion umfassen:</p>
                                            <ul>
                                                <li>Optimierung des Onboarding-Prozesses</li>
                                                <li>Strategisch platzierte Upgrade-CTAs</li>
                                                <li>Feature-Discovery für Premium-Funktionen</li>
                                                <li>Zeitlich begrenzte Angebote für aktive Free-Nutzer</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Section -->
                        <div id="revenue" class="section-content">
                            <div class="content">
                                <h2 class="content-title">3. Umsatzprognose bei verschiedenen Nutzerzahlen</h2>

                                <div class="control-panel">
                                    <h3>Umsatzparameter anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="professionalPrice">Professional Abo (€/Monat)</label>
                                            <input type="number" id="professionalPrice" min="1" max="200" step="0.01" value="29.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="businessPrice2">Business Abo (€/Monat)</label>
                                            <input type="number" id="businessPrice2" min="1" max="500" step="0.01" value="79.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="enterprisePrice">Enterprise Abo (€/Monat)</label>
                                            <input type="number" id="enterprisePrice" min="1" max="1000" step="0.01" value="199.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="professionalPercent">Anteil Professional Abos (%)</label>
                                            <input type="number" id="professionalPercent" min="1" max="100" step="1" value="70">
                                        </div>

                                        <div class="control-item">
                                            <label for="businessPercent">Anteil Business Abos (%)</label>
                                            <input type="number" id="businessPercent" min="0" max="99" step="1" value="25">
                                        </div>

                                        <div class="control-item">
                                            <label for="additionalRevenue">Zusatzumsatz pro Nutzer (€/Monat)</label>
                                            <input type="number" id="additionalRevenue" min="0" max="100" step="0.1" value="6">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateRevenueBtn" class="btn">Umsatzdaten aktualisieren</button>
                                    </div>
                                </div>

                                <div class="chart-grid">
                                    <div class="chart-container">
                                        <h3 class="chart-title">Umsatzentwicklung bei wachsender Nutzerbasis</h3>
                                        <canvas id="revenueByUserCountChart"></canvas>
                                    </div>

                                    <div class="chart-container">
                                        <h3 class="chart-title">Umsatzverteilung nach Einnahmequellen (bei <span id="revenueUserCount">100.000</span> Nutzern)</h3>
                                        <canvas id="revenueDistributionChart"></canvas>
                                    </div>
                                </div>

                                <div style="margin-top: 30px;">
                                    <h3>Umsatzprognose bei verschiedenen Nutzergrößen</h3>
                                    <table class="data-table" id="revenueProjectionTable">
                                        <thead>
                                        <tr>
                                            <th>Nutzerzahl</th>
                                            <th class="number">Freemium-Nutzer</th>
                                            <th class="number">Zahlende Nutzer</th>
                                            <th class="number">Monatlicher Umsatz</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>10.000</td>
                                            <td class="number">9.500</td>
                                            <td class="number">500</td>
                                            <td class="number">28.495 €</td>
                                        </tr>
                                        <tr>
                                            <td>25.000</td>
                                            <td class="number">23.750</td>
                                            <td class="number">1.250</td>
                                            <td class="number">71.238 €</td>
                                        </tr>
                                        <tr>
                                            <td>50.000</td>
                                            <td class="number">47.500</td>
                                            <td class="number">2.500</td>
                                            <td class="number">142.475 €</td>
                                        </tr>
                                        <tr>
                                            <td>100.000</td>
                                            <td class="number">95.000</td>
                                            <td class="number">5.000</td>
                                            <td class="number">284.950 €</td>
                                        </tr>
                                        <tr>
                                            <td>250.000</td>
                                            <td class="number">237.500</td>
                                            <td class="number">12.500</td>
                                            <td class="number">712.375 €</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Costs Section -->
                        <div id="costs" class="section-content">
                            <div class="content">
                                <h2 class="content-title">4. Betriebskosten und Marge</h2>

                                <div class="control-panel">
                                    <h3>Kostenparameter anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="aiCostPerUser">KI-Kosten pro Nutzer (€/Monat)</label>
                                            <input type="number" id="aiCostPerUser" min="0.01" max="10" step="0.01" value="0.52">
                                        </div>

                                        <div class="control-item">
                                            <label for="supportCostPerUser">Support & Personal pro 1000 Nutzer (€/Monat)</label>
                                            <input type="number" id="supportCostPerUser" min="10" max="2000" step="10" value="500">
                                        </div>

                                        <div class="control-item">
                                            <label for="marketingPercentage">
                                                <label for="marketingPercentage">Marketing & Akquise (% vom Umsatz)</label>
                                                <input type="number" id="marketingPercentage" min="1" max="50" step="1" value="15">
                                        </div>

                                        <div class="control-item">
                                            <label for="hostingCostPerUser">Hosting & Infrastruktur pro 10.000 Nutzer (€/Monat)</label>
                                            <input type="number" id="hostingCostPerUser" min="100" max="5000" step="100" value="750">
                                        </div>

                                        <div class="control-item">
                                            <label for="paymentProcessingPercentage">Zahlungsabwicklung (% vom Umsatz)</label>
                                            <input type="number" id="paymentProcessingPercentage" min="0.5" max="10" step="0.1" value="3.5">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateCostsBtn" class="btn">Kostendaten aktualisieren</button>
                                    </div>
                                </div>

                                <div class="chart-grid">
                                    <div class="chart-container">
                                        <h3 class="chart-title">Kostenstruktur bei verschiedenen Nutzergrößen</h3>
                                        <table class="data-table" id="costStructureTable">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th class="number">10.000 Nutzer</th>
                                                <th class="number">50.000 Nutzer</th>
                                                <th class="number">100.000 Nutzer</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Monatl. Umsatz</td>
                                                <td class="number">28.495 €</td>
                                                <td class="number">142.475 €</td>
                                                <td class="number">284.950 €</td>
                                            </tr>
                                            <tr>
                                                <td>Kosten</td>
                                                <td class="number">17.910 €</td>
                                                <td class="number">85.485 €</td>
                                                <td class="number">162.420 €</td>
                                            </tr>
                                            <tr>
                                                <td>Gewinn</td>
                                                <td class="number">10.585 €</td>
                                                <td class="number">56.990 €</td>
                                                <td class="number">122.530 €</td>
                                            </tr>
                                            <tr>
                                                <td>Marge</td>
                                                <td class="number">37%</td>
                                                <td class="number">40%</td>
                                                <td class="number">43%</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="chart-container">
                                        <h3 class="chart-title">Detaillierte Kostenaufschlüsselung bei 100.000 Nutzern</h3>
                                        <canvas id="costStructureChart"></canvas>
                                    </div>
                                </div>

                                <div style="margin-top: 30px;">
                                    <h3>Kosten-Skalierung und Effizienz</h3>
                                    <p>Mit zunehmendem Wachstum verbessert sich die Kosteneffizienz durch:</p>
                                    <ul>
                                        <li><strong>Skaleneffekte bei KI-Kosten</strong> durch Volumenrabatte und optimierte Prompts</li>
                                        <li><strong>Effizientere Support-Strukturen</strong> mit zunehmendem Automatisierungsgrad</li>
                                        <li><strong>Geringere relative Hosting-Kosten</strong> durch bessere Server-Auslastung</li>
                                        <li><strong>Optimierte Marketing-Ausgaben</strong> durch höhere Conversion-Rates und organisches Wachstum</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Growth Section -->
                        <div id="growth" class="section-content">
                            <div class="content">
                                <h2 class="content-title">5. Nutzerwachstumsprognose</h2>

                                <div class="control-panel">
                                    <h3>Wachstumsparameter anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="initialUsers">Nutzer nach 6 Monaten</label>
                                            <input type="number" id="initialUsers" min="100" max="50000" step="100" value="5000">
                                        </div>

                                        <div class="control-item">
                                            <label for="growthRate1">Wachstumsrate Jahr 1 (%/Halbjahr)</label>
                                            <input type="number" id="growthRate1" min="10" max="500" step="10" value="200">
                                        </div>

                                        <div class="control-item">
                                            <label for="growthRate2">Wachstumsrate Jahr 2 (%/Halbjahr)</label>
                                            <input type="number" id="growthRate2" min="10" max="300" step="10" value="100">
                                        </div>

                                        <div class="control-item">
                                            <label for="growthRate3">Wachstumsrate Jahr 3 (%/Halbjahr)</label>
                                            <input type="number" id="growthRate3" min="10" max="200" step="10" value="50">
                                        </div>

                                        <div class="control-item">
                                            <label for="conversionGrowth">Konversionsraten-Steigerung pro Jahr (%-Punkte)</label>
                                            <input type="number" id="conversionGrowth" min="0" max="5" step="0.1" value="1">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateGrowthBtn" class="btn">Wachstumsdaten aktualisieren</button>
                                    </div>
                                </div>

                                <div class="chart-container" style="width: 100%; height: 500px;">
                                    <h3 class="chart-title">Prognostiziertes Nutzerwachstum über 36 Monate</h3>
                                    <canvas id="userGrowthChart"></canvas>
                                </div>

                                <div style="margin-top: 30px;">
                                    <h3>Wachstumsprognose über 36 Monate</h3>
                                    <table class="data-table" id="growthProjectionTable">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th class="number">Monat 6</th>
                                            <th class="number">Monat 12</th>
                                            <th class="number">Monat 18</th>
                                            <th class="number">Monat 24</th>
                                            <th class="number">Monat 30</th>
                                            <th class="number">Monat 36</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Gesamtnutzer</td>
                                            <td class="number">5.000</td>
                                            <td class="number">15.000</td>
                                            <td class="number">30.000</td>
                                            <td class="number">60.000</td>
                                            <td class="number">100.000</td>
                                            <td class="number">150.000</td>
                                        </tr>
                                        <tr>
                                            <td>Zahlende Nutzer</td>
                                            <td class="number">150</td>
                                            <td class="number">600</td>
                                            <td class="number">1.350</td>
                                            <td class="number">2.700</td>
                                            <td class="number">5.000</td>
                                            <td class="number">7.500</td>
                                        </tr>
                                        <tr>
                                            <td>Conversion-Rate</td>
                                            <td class="number">3,0%</td>
                                            <td class="number">4,0%</td>
                                            <td class="number">4,5%</td>
                                            <td class="number">4,5%</td>
                                            <td class="number">5,0%</td>
                                            <td class="number">5,0%</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ROI Section -->
                        <div id="roi" class="section-content">
                            <div class="content">
                                <h2 class="content-title">6. Schlüsselkennzahlen und ROI-Analyse</h2>

                                <div class="control-panel">
                                    <h3>ROI-Parameter anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="freemiumInvestmentY1">Freemium Investition Jahr 1 (€)</label>
                                            <input type="number" id="freemiumInvestmentY1" min="10000" max="1000000" step="10000" value="350000">
                                        </div>

                                        <div class="control-item">
                                            <label for="traditionalInvestmentY1">Traditionell Investition Jahr 1 (€)</label>
                                            <input type="number" id="traditionalInvestmentY1" min="10000" max="1000000" step="10000" value="280000">
                                        </div>

                                        <div class="control-item">
                                            <label for="cacYear1">CAC Jahr 1 (€)</label>
                                            <input type="number" id="cacYear1" min="50" max="1000" step="10" value="230">
                                        </div>

                                        <div class="control-item">
                                            <label for="cacYear3">CAC Jahr 3 (€)</label>
                                            <input type="number" id="cacYear3" min="50" max="1000" step="10" value="190">
                                        </div>

                                        <div class="control-item">
                                            <label for="ltvYear1">LTV Jahr 1 (€)</label>
                                            <input type="number" id="ltvYear1" min="100" max="5000" step="10" value="780">
                                        </div>

                                        <div class="control-item">
                                            <label for="ltvYear3">LTV Jahr 3 (€)</label>
                                            <input type="number" id="ltvYear3" min="100" max="5000" step="10" value="1050">
                                        </div>

                                        <div class="control-item">
                                            <label for="churnYear1">Churn-Rate Jahr 1 (%)</label>
                                            <input type="number" id="churnYear1" min="0.1" max="20" step="0.1" value="5.8">
                                        </div>

                                        <div class="control-item">
                                            <label for="churnYear3">Churn-Rate Jahr 3 (%)</label>
                                            <input type="number" id="churnYear3" min="0.1" max="20" step="0.1" value="3.8">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updateRoiBtn" class="btn">ROI-Daten aktualisieren</button>
                                    </div>
                                </div>

                                <div class="chart-grid">
                                    <div class="chart-container">
                                        <h3 class="chart-title">Wichtige SaaS-Metriken im Freemium-Modell</h3>
                                        <table class="data-table" id="saasMetricsTable">
                                            <thead>
                                            <tr>
                                                <th>Metrik</th>
                                                <th class="number">Jahr 1</th>
                                                <th class="number">Jahr 2</th>
                                                <th class="number">Jahr 3</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Free-zu-Paid Konversionsrate</td>
                                                <td class="number">3,2%</td>
                                                <td class="number">4,5%</td>
                                                <td class="number">5,0%</td>
                                            </tr>
                                            <tr>
                                                <td>CAC (Customer Acquisition Cost)</td>
                                                <td class="number">230 €</td>
                                                <td class="number">210 €</td>
                                                <td class="number">190 €</td>
                                            </tr>
                                            <tr>
                                                <td>LTV (Lifetime Value)</td>
                                                <td class="number">780 €</td>
                                                <td class="number">920 €</td>
                                                <td class="number">1.050 €</td>
                                            </tr>
                                            <tr>
                                                <td>LTV:CAC Ratio</td>
                                                <td class="number">3,4</td>
                                                <td class="number">4,4</td>
                                                <td class="number">5,5</td>
                                            </tr>
                                            <tr>
                                                <td>Churn-Rate</td>
                                                <td class="number">5,8%</td>
                                                <td class="number">4,5%</td>
                                                <td class="number">3,8%</td>
                                            </tr>
                                            <tr>
                                                <td>ARPU (zahlende Nutzer)</td>
                                                <td class="number">42 €</td>
                                                <td class="number">46 €</td>
                                                <td class="number">49 €</td>
                                            </tr>
                                            <tr>
                                                <td>ARPU (alle Nutzer)</td>
                                                <td class="number">1,34 €</td>
                                                <td class="number">2,07 €</td>
                                                <td class="number">2,45 €</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="chart-container">
                                        <h3 class="chart-title">ROI-Analyse: Freemium vs. traditionelles Modell</h3>
                                        <canvas id="roiComparisonChart"></canvas>
                                    </div>
                                </div>

                                <div style="margin-top: 30px;">
                                    <h3>ROI-Vergleich über 3 Jahre</h3>
                                    <table class="data-table" id="roiComparisonTable">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th class="number">Jahr 1</th>
                                            <th class="number">Jahr 2</th>
                                            <th class="number">Jahr 3</th>
                                            <th class="number">3-Jahres-Summe</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="5"><strong>Freemium:</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Investition</td>
                                            <td class="number">350.000 €</td>
                                            <td class="number">250.000 €</td>
                                            <td class="number">200.000 €</td>
                                            <td class="number">800.000 €</td>
                                        </tr>
                                        <tr>
                                            <td>Umsatz</td>
                                            <td class="number">520.000 €</td>
                                            <td class="number">1.750.000 €</td>
                                            <td class="number">3.420.000 €</td>
                                            <td class="number">5.690.000 €</td>
                                        </tr>
                                        <tr>
                                            <td>ROI</td>
                                            <td class="number">48%</td>
                                            <td class="number">600%</td>
                                            <td class="number">1610%</td>
                                            <td class="number">611%</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"><strong>Traditionell:</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Investition</td>
                                            <td class="number">280.000 €</td>
                                            <td class="number">220.000 €</td>
                                            <td class="number">180.000 €</td>
                                            <td class="number">680.000 €</td>
                                        </tr>
                                        <tr>
                                            <td>Umsatz</td>
                                            <td class="number">380.000 €</td>
                                            <td class="number">1.100.000 €</td>
                                            <td class="number">1.950.000 €</td>
                                            <td class="number">3.430.000 €</td>
                                        </tr>
                                        <tr>
                                            <td>ROI</td>
                                            <td class="number">36%</td>
                                            <td class="number">400%</td>
                                            <td class="number">983%</td>
                                            <td class="number">404%</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Premium Features Section -->
                        <div id="premium" class="section-content">
                            <div class="content">
                                <h2 class="content-title">Premium-Features und Preisgestaltung</h2>

                                <div class="control-panel">
                                    <h3>Preisgestaltung anpassen</h3>

                                    <div class="control-grid">
                                        <div class="control-item">
                                            <label for="freeSpeeches">Free-Tier: Anzahl Speeches</label>
                                            <input type="number" id="freeSpeeches" min="1" max="10" step="1" value="1">
                                        </div>

                                        <div class="control-item">
                                            <label for="professionalSpeeches">Professional: Anzahl Speeches</label>
                                            <input type="number" id="professionalSpeeches" min="1" max="100" step="1" value="10">
                                        </div>

                                        <div class="control-item">
                                            <label for="businessSpeeches">Business: Anzahl Speeches</label>
                                            <input type="number" id="businessSpeeches" min="1" max="500" step="1" value="50">
                                        </div>

                                        <div class="control-item">
                                            <label for="professionalPrice3">Professional Preis (€/Monat)</label>
                                            <input type="number" id="professionalPrice3" min="1" max="200" step="0.1" value="29.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="businessPrice3">Business Preis (€/Monat)</label>
                                            <input type="number" id="businessPrice3" min="1" max="500" step="0.1" value="79.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="enterprisePrice3">Enterprise Preis (€/Monat)</label>
                                            <input type="number" id="enterprisePrice3" min="1" max="1000" step="0.1" value="199.99">
                                        </div>

                                        <div class="control-item">
                                            <label for="yearlyDiscount">Rabatt bei jährlicher Zahlung (%)</label>
                                            <input type="number" id="yearlyDiscount" min="0" max="50" step="1" value="16.7">
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button id="updatePricingBtn" class="btn">Preisgestaltung aktualisieren</button>
                                    </div>
                                </div>

                                <div class="pricing-container">
                                    <div class="pricing-card">
                                        <h3>Free</h3>
                                        <div class="price">0 €</div>
                                        <ul>
                                            <li><span id="freeSpeeches2">1</span> Speech erstellen und speichern</li>
                                            <li>Zugriff auf grundlegende Textbausteine</li>
                                            <li>Basis-Export (PDF)</li>
                                            <li>Community-Support</li>
                                        </ul>
                                        <button class="btn">Kostenlos starten</button>
                                    </div>

                                    <div class="pricing-card">
                                        <h3>Professional</h3>
                                        <div class="price"><span id="professionalPriceDisplay">29,99 €</span> <span class="price-period">/ Monat</span></div>
                                        <ul>
                                            <li><span id="professionalSpeeches2">10</span> Speeches erstellen und verwalten</li>
                                            <li>50 KI-Optimierungen pro Monat</li>
                                            <li>Zugriff auf erweiterte Textbausteine</li>
                                            <li>Alle Einwandtechniken</li>
                                            <li>Alle Export-Optionen (PDF, Word, E-Mail)</li>
                                            <li>Standard-Support</li>
                                        </ul>
                                        <button class="btn">Jetzt upgraden</button>
                                    </div>

                                    <div class="pricing-card">
                                        <h3>Business</h3>
                                        <div class="price"><span id="businessPriceDisplay">79,99 €</span> <span class="price-period">/ Monat</span></div>
                                        <ul>
                                            <li><span id="businessSpeeches2">50</span> Speeches erstellen und verwalten</li>
                                            <li>200 KI-Optimierungen pro Monat</li>
                                            <li>Vollständiger Zugriff auf alle Textbausteine</li>
                                            <li>Branchenspezifische Templates</li>
                                            <li>Team-Funktionen für bis zu 3 Benutzer</li>
                                            <li>Premium-Support</li>
                                        </ul>
                                        <button class="btn">Jetzt upgraden</button>
                                    </div>

                                    <div class="pricing-card">
                                        <h3>Enterprise</h3>
                                        <div class="price"><span id="enterprisePriceDisplay">199,99 €</span> <span class="price-period">/ Monat</span></div>
                                        <ul>
                                            <li>Unbegrenzte Speeches</li>
                                            <li>500 KI-Optimierungen pro Monat</li>
                                            <li>Team-Funktionen für bis zu 10 Benutzer</li>
                                            <li>Maßgeschneiderte Textbausteine für Ihr Unternehmen</li>
                                            <li>White-Label-Option</li>
                                            <li>Dedizierter Account Manager</li>
                                            <li>API-Zugang</li>
                                        </ul>
                                        <button class="btn">Kontakt aufnehmen</button>
                                    </div>
                                </div>

                                <div style="margin-top: 30px;">
                                    <h3>Zusätzliche Credit-Pakete</h3>
                                    <table class="data-table">
                                        <thead>
                                        <tr>
                                            <th>Paket</th>
                                            <th class="number">Preis</th>
                                            <th>Beschreibung</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>5 zusätzliche Speeches</td>
                                            <td class="number">19,99 €</td>
                                            <td>Erweiterung zur Erstellung zusätzlicher Speeches</td>
                                        </tr>
                                        <tr>
                                            <td>20 zusätzliche Speeches</td>
                                            <td class="number">69,99 €</td>
                                            <td>Paket für Teams mit erhöhtem Bedarf</td>
                                        </tr>
                                        <tr>
                                            <td>50 zusätzliche KI-Optimierungen</td>
                                            <td class="number">14,99 €</td>
                                            <td>Für verbesserte Textqualität und Anpassungen</td>
                                        </tr>
                                        <tr>
                                            <td>200 zusätzliche KI-Optimierungen</td>
                                            <td class="number">49,99 €</td>
                                            <td>Ideal für umfangreiche Content-Erstellung</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Footer with Recommendations -->
                        <div class="content recommendations">
                            <h2 class="content-title">Fazit und Handlungsempfehlungen</h2>

                            <p>Die Datenanalyse zeigt, dass ein gut implementiertes Freemium-Modell für Venditorix erhebliche Wachstums- und Umsatzchancen bietet.</p>

                            <h3>Die wichtigsten Handlungsempfehlungen:</h3>
                            <ul>
                                <li><strong>Implementieren Sie frühzeitig Nutzungstracking</strong> - Um das Verhalten von Free-Nutzern zu verstehen und Konversionstreiber zu identifizieren</li>
                                <li><strong>Gestalten Sie klare Wertstufungen</strong> - Mit spürbaren Vorteilen zwischen Free und verschiedenen Premium-Tiers</li>
                                <li><strong>Optimieren Sie kontinuierlich die Conversion-Touchpoints</strong> - Basierend auf Daten und A/B-Tests</li>
                                <li><strong>Erwägen Sie eine schnelle Internationalisierung</strong> - Um das Marktpotenzial vor Wettbewerbern zu erschließen</li>
                                <li><strong>Steuern Sie KI-Kosten aktiv</strong> - Durch effiziente Prompts und Caching-Strategien</li>
                            </ul>

                            <p>Mit diesem strategischen Ansatz kann Venditorix innerhalb von 2-3 Jahren eine führende Position im Markt für Verkaufstextgenerierung erreichen und eine solide Basis für weiteres Wachstum und Innovation schaffen.</p>

                            <div class="btn-group">
                                <button id="generateReportBtn" class="btn">Vollständigen Report generieren</button>
                                <button id="printFullReportBtn" class="btn btn-secondary">Als PDF speichern</button>
                            </div>
                        </div>

                        <div class="footer">
                            <p>Venditorix Freemium-Modell: Interaktive Datenanalyse und Prognose</p>
                            <p>Stand: 07.03.2025</p>
                        </div>
                    </div>

                    <script>
                        // Global chart instances for updating
                        let marketRegionsChart, targetGroupsChart, conversionFunnelChart;
                        let revenueByUserCountChart, revenueDistributionChart, costStructureChart;
                        let userGrowthChart, roiComparisonChart, overviewRevenueChart, overviewGrowthChart;

                        // Tab switching functionality
                        document.addEventListener('DOMContentLoaded', function() {
                            const tabs = document.querySelectorAll('.tab');

                            tabs.forEach(tab => {
                                tab.addEventListener('click', function() {
                                    // Update active tab
                                    tabs.forEach(t => t.classList.remove('active'));
                                    this.classList.add('active');

                                    // Show corresponding section
                                    const sectionId = this.getAttribute('data-section');
                                    document.querySelectorAll('.section-content').forEach(section => {
                                        section.classList.remove('active');
                                    });
                                    document.getElementById(sectionId).classList.add('active');
                                });
                            });

                            // Initialize charts
                            initializeCharts();

                            // Setup event listeners for control panels
                            setupEventListeners();
                        });

                        // Format number with thousand separator
                        function formatNumber(number, decimals = 0) {
                            return new Intl.NumberFormat('de-DE', {
                                minimumFractionDigits: decimals,
                                maximumFractionDigits: decimals
                            }).format(number);
                        }

                        // Initialize all charts
                        function initializeCharts() {
                            initializeMarketCharts();
                            initializeConversionCharts();
                            initializeRevenueCharts();
                            initializeCostCharts();
                            initializeGrowthCharts();
                            initializeRoiCharts();
                            initializeOverviewCharts();
                        }

                        // Setup event listeners
                        function setupEventListeners() {
                            // Main data update
                            document.getElementById('updateDataBtn').addEventListener('click', function() {
                                updateAllData();
                            });

                            // Market data update
                            document.getElementById('updateMarketBtn').addEventListener('click', function() {
                                updateMarketData();
                            });

                            // Conversion data update
                            document.getElementById('updateConversionBtn').addEventListener('click', function() {
                                updateConversionData();
                            });

                            // Revenue data update
                            document.getElementById('updateRevenueBtn').addEventListener('click', function() {
                                updateRevenueData();
                            });
                            // Costs data update
                            document.getElementById('updateCostsBtn').addEventListener('click', function() {
                                updateCostsData();
                            });

                            // Growth data update
                            document.getElementById('updateGrowthBtn').addEventListener('click', function() {
                                updateGrowthData();
                            });

                            // ROI data update
                            document.getElementById('updateRoiBtn').addEventListener('click', function() {
                                updateRoiData();
                            });

                            // Pricing data update
                            document.getElementById('updatePricingBtn').addEventListener('click', function() {
                                updatePricingData();
                            });

                            // Print report button
                            document.getElementById('printReportBtn').addEventListener('click', function() {
                                window.print();
                            });

                            document.getElementById('printFullReportBtn').addEventListener('click', function() {
                                window.print();
                            });

                            // Generate full report
                            document.getElementById('generateReportBtn').addEventListener('click', function() {
                                // Show all sections for printing
                                document.querySelectorAll('.section-content').forEach(section => {
                                    section.classList.add('active');
                                });

                                // Create a slight delay to ensure charts are rendered properly
                                setTimeout(() => {
                                    window.print();

                                    // After printing, restore to current active tab
                                    document.querySelectorAll('.section-content').forEach(section => {
                                        section.classList.remove('active');
                                    });

                                    const activeTab = document.querySelector('.tab.active');
                                    const sectionId = activeTab.getAttribute('data-section');
                                    document.getElementById(sectionId).classList.add('active');
                                }, 500);
                            });
                        }

                        // Update all data based on main parameters
                        function updateAllData() {
                            const conversionRate = parseFloat(document.getElementById('conversionRate').value) / 100;
                            const userBase = parseInt(document.getElementById('userBase').value);
                            const proPrice = parseFloat(document.getElementById('proPrice').value);
                            const businessPrice = parseFloat(document.getElementById('businessPrice').value);

                            // Calculate revenue
                            const payingUsers = Math.round(userBase * conversionRate);
                            const freeUsers = userBase - payingUsers;

                            // Assume distribution: 70% Pro, 25% Business, 5% Enterprise
                            const proUsers = Math.round(payingUsers * 0.7);
                            const businessUsers = Math.round(payingUsers * 0.25);
                            const enterpriseUsers = payingUsers - proUsers - businessUsers;

                            const proRevenue = proUsers * proPrice;
                            const businessRevenue = businessUsers * businessPrice;
                            const enterpriseRevenue = enterpriseUsers * 199.99;

                            const totalRevenue = proRevenue + businessRevenue + enterpriseRevenue;
                            const additionalRevenue = payingUsers * 6; // Average additional revenue per paying user (credits, etc.)

                            const grandTotalRevenue = totalRevenue + additionalRevenue;

                            // Update overview summary
                            document.getElementById('revenuePotential').textContent = formatNumber(grandTotalRevenue) + ' €';
                            document.getElementById('userBaseDisplay').textContent = formatNumber(userBase);

                            // Calculate costs and profit margin
                            const aiCosts = userBase * 0.52;
                            const supportCosts = userBase * 0.5;
                            const marketingCosts = grandTotalRevenue * 0.15;
                            const hostingCosts = userBase * 0.075;
                            const paymentCosts = grandTotalRevenue * 0.035;

                            const totalCosts = aiCosts + supportCosts + marketingCosts + hostingCosts + paymentCosts;
                            const profit = grandTotalRevenue - totalCosts;
                            const margin = (profit / grandTotalRevenue * 100).toFixed(0);

                            document.getElementById('profitMargin').textContent = margin + '%';

                            // Update all other sections
                            updateMarketData();
                            updateConversionData();
                            updateRevenueData();
                            updateCostsData();
                            updateGrowthData();
                            updateRoiData();
                            updatePricingData();
                            updateOverviewCharts();
                        }

                        // Market section data update
                        function updateMarketData() {
                            const totalMarketSize = parseInt(document.getElementById('totalMarketSize').value);
                            const b2bPercentage = parseInt(document.getElementById('b2bPercentage').value);
                            const b2cPercentage = parseInt(document.getElementById('b2cPercentage').value);
                            const naPercentage = parseInt(document.getElementById('naPercentage').value);

                            // Calculate Europe percentage based on NA
                            const europePercentage = Math.min(100 - naPercentage, Math.round(naPercentage * 0.7));

                            // Calculate APAC percentage
                            const apacPercentage = Math.min(100 - naPercentage - europePercentage, Math.round(naPercentage * 0.5));

                            // Calculate LATAM and ROW
                            const latamPercentage = Math.min(100 - naPercentage - europePercentage - apacPercentage, Math.round(naPercentage * 0.2));
                            const rowPercentage = 100 - naPercentage - europePercentage - apacPercentage - latamPercentage;

                            // Calculate values based on percentages and total size
                            const naValue = (naPercentage / 100 * totalMarketSize).toFixed(1);
                            const europeValue = (europePercentage / 100 * totalMarketSize).toFixed(1);
                            const apacValue = (apacPercentage / 100 * totalMarketSize).toFixed(1);
                            const latamValue = (latamPercentage / 100 * totalMarketSize).toFixed(1);
                            const rowValue = (rowPercentage / 100 * totalMarketSize).toFixed(1);

                            // Update the market regions chart
                            marketRegionsChart.data.datasets[0].data = [naValue, europeValue, apacValue, latamValue, rowValue];
                            marketRegionsChart.data.labels = [
                                `Nordamerika (${naPercentage}%)`,
                                `Europa (${europePercentage}%)`,
                                `Asien-Pazifik (${apacPercentage}%)`,
                                `Lateinamerika (${latamPercentage}%)`,
                                `Rest der Welt (${rowPercentage}%)`
                            ];
                            marketRegionsChart.update();

                            // Update the target groups chart
                            const freelancerPercentage = Math.max(5, 100 - b2bPercentage - b2cPercentage - 15);
                            const managerPercentage = Math.min(15, 100 - b2bPercentage - b2cPercentage - freelancerPercentage);
                            const smbPercentage = 100 - b2bPercentage - b2cPercentage - freelancerPercentage - managerPercentage;

                            targetGroupsChart.data.datasets[0].data = [
                                b2bPercentage,
                                b2cPercentage,
                                freelancerPercentage,
                                managerPercentage,
                                smbPercentage
                            ];
                            targetGroupsChart.update();

                            // Update SAM total display
                            document.getElementById('samTotal').textContent = totalMarketSize;
                        }

                        // Conversion section data update
                        function updateConversionData() {
                            const visitorToRegistration = parseFloat(document.getElementById('visitorToRegistration').value);
                            const registrationToActive = parseFloat(document.getElementById('registrationToActive').value);
                            const activeToPayingTotal = parseFloat(document.getElementById('activeToPayingTotal').value);
                            const activeToPayingEngaged = parseFloat(document.getElementById('activeToPayingEngaged').value);

                            // Calculate funnel values based on 100,000 initial visitors
                            const visitors = 100000;
                            const registrations = Math.round(visitors * (visitorToRegistration / 100));
                            const activeUsers = Math.round(registrations * (registrationToActive / 100));
                            const payingUsers = Math.round(activeUsers * (activeToPayingTotal / 100));

                            // Update the chart
                            conversionFunnelChart.data.datasets[0].data = [visitors, registrations, activeUsers, payingUsers];
                            conversionFunnelChart.update();

                            // Update the text displays
                            document.getElementById('visitorToRegistrationDisplay').textContent = `${visitorToRegistration}%`;
                            document.getElementById('registrationToActiveDisplay').textContent = `${registrationToActive}%`;
                            document.getElementById('activeToPayingTotalDisplay').textContent = `${activeToPayingTotal}%`;
                            document.getElementById('activeToPayingEngagedDisplay').textContent = `${activeToPayingEngaged}%`;
                        }

                        // Revenue section data update
                        function updateRevenueData() {
                            const professionalPrice = parseFloat(document.getElementById('professionalPrice').value);
                            const businessPrice = parseFloat(document.getElementById('businessPrice2').value);
                            const enterprisePrice = parseFloat(document.getElementById('enterprisePrice').value);
                            const professionalPercent = parseFloat(document.getElementById('professionalPercent').value);
                            const businessPercent = parseFloat(document.getElementById('businessPercent').value);
                            const additionalRevenue = parseFloat(document.getElementById('additionalRevenue').value);

                            // Calculate enterprise percentage
                            const enterprisePercent = 100 - professionalPercent - businessPercent;

                            // Calculate revenue for different user counts
                            const userCounts = [10000, 25000, 50000, 100000, 250000];
                            const conversionRate = 0.05; // 5% conversion rate

                            const revenueData = userCounts.map(userCount => {
                                const payingUsers = Math.round(userCount * conversionRate);
                                const freemiumUsers = userCount - payingUsers;

                                const proUsers = Math.round(payingUsers * (professionalPercent / 100));
                                const businessUsers = Math.round(payingUsers * (businessPercent / 100));
                                const enterpriseUsers = payingUsers - proUsers - businessUsers;

                                const proRevenue = proUsers * professionalPrice;
                                const businessRevenue = businessUsers * businessPrice;
                                const enterpriseRevenue = enterpriseUsers * enterprisePrice;
                                const addRevenue = payingUsers * additionalRevenue;

                                const totalRevenue = proRevenue + businessRevenue + enterpriseRevenue + addRevenue;

                                return {
                                    users: userCount,
                                    freemium: freemiumUsers,
                                    paying: payingUsers,
                                    revenue: Math.round(totalRevenue)
                                };
                            });

                            // Update the chart
                            revenueByUserCountChart.data.labels = revenueData.map(item => `${item.users/1000}k`);
                            revenueByUserCountChart.data.datasets[0].data = revenueData.map(item => item.revenue);
                            revenueByUserCountChart.update();

                            // Update revenue distribution chart
                            const sampleRevenue = revenueData.find(item => item.users === 100000);
                            const proTotalRevenue = sampleRevenue.paying * (professionalPercent / 100) * professionalPrice;
                            const businessTotalRevenue = sampleRevenue.paying * (businessPercent / 100) * businessPrice;
                            const enterpriseTotalRevenue = sampleRevenue.paying * (enterprisePercent / 100) * enterprisePrice;
                            const speechCreditRevenue = sampleRevenue.paying * additionalRevenue * 0.55;
                            const aiCreditRevenue = sampleRevenue.paying * additionalRevenue * 0.45;

                            const totalRevenue = proTotalRevenue + businessTotalRevenue + enterpriseTotalRevenue + speechCreditRevenue + aiCreditRevenue;

                            const proPct = (proTotalRevenue / totalRevenue * 100).toFixed(1);
                            const businessPct = (businessTotalRevenue / totalRevenue * 100).toFixed(1);
                            const enterprisePct = (enterpriseTotalRevenue / totalRevenue * 100).toFixed(1);
                            const speechPct = (speechCreditRevenue / totalRevenue * 100).toFixed(1);
                            const aiPct = (aiCreditRevenue / totalRevenue * 100).toFixed(1);

                            revenueDistributionChart.data.datasets[0].data = [
                                proTotalRevenue,
                                businessTotalRevenue,
                                enterpriseTotalRevenue,
                                speechCreditRevenue,
                                aiCreditRevenue
                            ];

                            revenueDistributionChart.data.labels = [
                                `Professional-Abos (${proPct}%)`,
                                `Business-Abos (${businessPct}%)`,
                                `Enterprise-Abos (${enterprisePct}%)`,
                                `Credit-Pakete (Speech) (${speechPct}%)`,
                                `Credit-Pakete (KI) (${aiPct}%)`
                            ];

                            revenueDistributionChart.update();

                            // Update revenue table
                            const table = document.getElementById('revenueProjectionTable').getElementsByTagName('tbody')[0];
                            const rows = table.getElementsByTagName('tr');

                            for (let i = 0; i < rows.length; i++) {
                                const cells = rows[i].getElementsByTagName('td');
                                cells[1].textContent = formatNumber(revenueData[i].freemium);
                                cells[2].textContent = formatNumber(revenueData[i].paying);
                                cells[3].textContent = formatNumber(revenueData[i].revenue) + ' €';
                            }

                            // Update revenue user count label
                            document.getElementById('revenueUserCount').textContent = formatNumber(100000);
                        }

                        // Costs section data update
                        function updateCostsData() {
                            const aiCostPerUser = parseFloat(document.getElementById('aiCostPerUser').value);
                            const supportCostPerUser = parseFloat(document.getElementById('supportCostPerUser').value);
                            const marketingPercentage = parseFloat(document.getElementById('marketingPercentage').value);
                            const hostingCostPerUser = parseFloat(document.getElementById('hostingCostPerUser').value);
                            const paymentProcessingPercentage = parseFloat(document.getElementById('paymentProcessingPercentage').value);

                            // Calculate costs for 100,000 users
                            const users100k = 100000;
                            const revenuePer100k = 284950; // Using fixed revenue value for simplicity

                            const aiCosts = users100k * aiCostPerUser;
                            const supportCosts = users100k / 1000 * supportCostPerUser;
                            const marketingCosts = revenuePer100k * (marketingPercentage / 100);
                            const hostingCosts = users100k / 10000 * hostingCostPerUser;
                            const paymentCosts = revenuePer100k * (paymentProcessingPercentage / 100);

                            const totalCosts = aiCosts + supportCosts + marketingCosts + hostingCosts + paymentCosts;
                            const profit = revenuePer100k - totalCosts;
                            const margin = (profit / revenuePer100k * 100).toFixed(0);

                            // Calculate costs for different user bases
                            const userBases = [10000, 50000, 100000];
                            const costData = userBases.map(users => {
                                const revenue = (users / 100000) * revenuePer100k;
                                const aiCost = users * aiCostPerUser;
                                const supportCost = users / 1000 * supportCostPerUser;
                                const marketingCost = revenue * (marketingPercentage / 100);
                                const hostingCost = users / 10000 * hostingCostPerUser;
                                const paymentCost = revenue * (paymentProcessingPercentage / 100);

                                const totalCost = aiCost + supportCost + marketingCost + hostingCost + paymentCost;
                                const profit = revenue - totalCost;
                                const margin = (profit / revenue * 100).toFixed(0);

                                return { users, revenue, cost: totalCost, profit, margin };
                            });

                            // Update cost structure pie chart
                            const aiPct = (aiCosts / totalCosts * 100).toFixed(1);
                            const supportPct = (supportCosts / totalCosts * 100).toFixed(1);
                            const marketingPct = (marketingCosts / totalCosts * 100).toFixed(1);
                            const paymentPct = (paymentCosts / totalCosts * 100).toFixed(1);
                            const hostingPct = (hostingCosts / totalCosts * 100).toFixed(1);

                            costStructureChart.data.datasets[0].data = [
                                supportCosts,
                                aiCosts,
                                marketingCosts,
                                paymentCosts,
                                hostingCosts
                            ];

                            costStructureChart.data.labels = [
                                `Support & Personal (${supportPct}%)`,
                                `KI-Kosten (${aiPct}%)`,
                                `Marketing & Akquise (${marketingPct}%)`,
                                `Zahlungsabwicklung (${paymentPct}%)`,
                                `Hosting & Infrastr. (${hostingPct}%)`
                            ];

                            costStructureChart.update();

                            // Update cost structure table
                            const table = document.getElementById('costStructureTable').getElementsByTagName('tbody')[0];
                            const rows = table.getElementsByTagName('tr');

                            for (let i = 0; i < costData.length; i++) {
                                // Update revenue row
                                rows[0].getElementsByTagName('td')[i+1].textContent = formatNumber(costData[i].revenue) + ' €';

                                // Update costs row
                                rows[1].getElementsByTagName('td')[i+1].textContent = formatNumber(costData[i].cost) + ' €';

                                // Update profit row
                                rows[2].getElementsByTagName('td')[i+1].textContent = formatNumber(costData[i].profit) + ' €';

                                // Update margin row
                                rows[3].getElementsByTagName('td')[i+1].textContent = costData[i].margin + '%';
                            }
                        }

                        // Growth section data update
                        function updateGrowthData() {
                            const initialUsers = parseInt(document.getElementById('initialUsers').value);
                            const growthRate1 = parseFloat(document.getElementById('growthRate1').value) / 100;
                            const growthRate2 = parseFloat(document.getElementById('growthRate2').value) / 100;
                            const growthRate3 = parseFloat(document.getElementById('growthRate3').value) / 100;
                            const conversionGrowth = parseFloat(document.getElementById('conversionGrowth').value) / 100;

                            // Calculate user growth over 36 months
                            const month6Users = initialUsers;
                            const month12Users = Math.round(month6Users * (1 + growthRate1));
                            const month18Users = Math.round(month12Users * (1 + growthRate2));
                            const month24Users = Math.round(month18Users * (1 + growthRate2));
                            const month30Users = Math.round(month24Users * (1 + growthRate3));
                            const month36Users = Math.round(month30Users * (1 + growthRate3));

                            // Calculate conversion rates
                            const month6Conv = 0.03;
                            const month12Conv = month6Conv + conversionGrowth;
                            const month18Conv = month12Conv + conversionGrowth/2;
                            const month24Conv = month18Conv;
                            const month30Conv = month24Conv + conversionGrowth/2;
                            const month36Conv = month30Conv;

                            // Calculate paying users
                            const month6Paying = Math.round(month6Users * month6Conv);
                            const month12Paying = Math.round(month12Users * month12Conv);
                            const month18Paying = Math.round(month18Users * month18Conv);
                            const month24Paying = Math.round(month24Users * month24Conv);
                            const month30Paying = Math.round(month30Users * month30Conv);
                            const month36Paying = Math.round(month36Users * month36Conv);

                            // Update the user growth chart
                            userGrowthChart.data.datasets[0].data = [
                                month6Users,
                                month12Users,
                                month18Users,
                                month24Users,
                                month30Users,
                                month36Users
                            ];

                            userGrowthChart.data.datasets[1].data = [
                                month6Paying,
                                month12Paying,
                                month18Paying,
                                month24Paying,
                                month30Paying,
                                month36Paying
                            ];

                            userGrowthChart.data.datasets[2].data = [
                                month6Conv * 100,
                                month12Conv * 100,
                                month18Conv * 100,
                                month24Conv * 100,
                                month30Conv * 100,
                                month36Conv * 100
                            ];

                            userGrowthChart.update();

                            // Update growth projection table
                            const table = document.getElementById('growthProjectionTable').getElementsByTagName('tbody')[0];
                            const rows = table.getElementsByTagName('tr');

                            // Update total users row
                            const cells1 = rows[0].getElementsByTagName('td');
                            cells1[1].textContent = formatNumber(month6Users);
                            cells1[2].textContent = formatNumber(month12Users);
                            cells1[3].textContent = formatNumber(month18Users);
                            cells1[4].textContent = formatNumber(month24Users);
                            cells1[5].textContent = formatNumber(month30Users);
                            cells1[6].textContent = formatNumber(month36Users);

                            // Update paying users row
                            const cells2 = rows[1].getElementsByTagName('td');
                            cells2[1].textContent = formatNumber(month6Paying);
                            cells2[2].textContent = formatNumber(month12Paying);
                            cells2[3].textContent = formatNumber(month18Paying);
                            cells2[4].textContent = formatNumber(month24Paying);
                            cells2[5].textContent = formatNumber(month30Paying);
                            cells2[6].textContent = formatNumber(month36Paying);

                            // Update conversion rate row
                            const cells3 = rows[2].getElementsByTagName('td');
                            cells3[1].textContent = (month6Conv * 100).toFixed(1) + '%';
                            cells3[2].textContent = (month12Conv * 100).toFixed(1) + '%';
                            cells3[3].textContent = (month18Conv * 100).toFixed(1) + '%';
                            cells3[4].textContent = (month24Conv * 100).toFixed(1) + '%';
                            cells3[5].textContent = (month30Conv * 100).toFixed(1) + '%';
                            cells3[6].textContent = (month36Conv * 100).toFixed(1) + '%';
                        }

                        // ROI section data update
                        function updateRoiData() {
                            const freemiumInvestmentY1 = parseInt(document.getElementById('freemiumInvestmentY1').value);
                            const traditionalInvestmentY1 = parseInt(document.getElementById('traditionalInvestmentY1').value);
                            const cacYear1 = parseInt(document.getElementById('cacYear1').value);
                            const cacYear3 = parseInt(document.getElementById('cacYear3').value);
                            const ltvYear1 = parseInt(document.getElementById('ltvYear1').value);
                            const ltvYear3 = parseInt(document.getElementById('ltvYear3').value);
                            const churnYear1 = parseFloat(document.getElementById('churnYear1').value);
                            const churnYear3 = parseFloat(document.getElementById('churnYear3').value);

                            // Calculate intermediate values
                            const cacYear2 = Math.round(cacYear1 - (cacYear1 - cacYear3) / 2);
                            const ltvYear2 = Math.round(ltvYear1 + (ltvYear3 - ltvYear1) / 2);
                            const churnYear2 = parseFloat((churnYear1 - (churnYear1 - churnYear3) / 2).toFixed(1));

                            // Calculate LTV:CAC Ratio
                            const ltvCacYear1 = (ltvYear1 / cacYear1).toFixed(1);
                            const ltvCacYear2 = (ltvYear2 / cacYear2).toFixed(1);
                            const ltvCacYear3 = (ltvYear3 / cacYear3).toFixed(1);

                            // Calculate investments and revenue
                            const freemiumInvestmentY2 = Math.round(freemiumInvestmentY1 * 0.71);
                            const freemiumInvestmentY3 = Math.round(freemiumInvestmentY2 * 0.8);

                            const traditionalInvestmentY2 = Math.round(traditionalInvestmentY1 * 0.79);
                            const traditionalInvestmentY3 = Math.round(traditionalInvestmentY2 * 0.82);

                            // Assume freemium revenue is higher due to network effects and conversion optimization
                            const freemiumRevenueY1 = 520000;
                            const freemiumRevenueY2 = 1750000;
                            const freemiumRevenueY3 = 3420000;

                            const traditionalRevenueY1 = 380000;
                            const traditionalRevenueY2 = 1100000;
                            const traditionalRevenueY3 = 1950000;

                            // Calculate ROI percentages
                            const freemiumRoiY1 = Math.round((freemiumRevenueY1 - freemiumInvestmentY1) / freemiumInvestmentY1 * 100);
                            const freemiumRoiY2 = Math.round((freemiumRevenueY2 - freemiumInvestmentY2) / freemiumInvestmentY2 * 100);
                            const freemiumRoiY3 = Math.round((freemiumRevenueY3 - freemiumInvestmentY3) / freemiumInvestmentY3 * 100);

                            const traditionalRoiY1 = Math.round((traditionalRevenueY1 - traditionalInvestmentY1) / traditionalInvestmentY1 * 100);
                            const traditionalRoiY2 = Math.round((traditionalRevenueY2 - traditionalInvestmentY2) / traditionalInvestmentY2 * 100);
                            const traditionalRoiY3 = Math.round((traditionalRevenueY3 - traditionalInvestmentY3) / traditionalInvestmentY3 * 100);

                            // Update ROI Comparison Chart
                            roiComparisonChart.data.datasets[0].data = [freemiumRoiY1, freemiumRoiY2, freemiumRoiY3];
                            roiComparisonChart.data.datasets[1].data = [traditionalRoiY1, traditionalRoiY2, traditionalRoiY3];
                            roiComparisonChart.update();

                            // Update SaaS Metrics Table
                            const table = document.getElementById('saasMetricsTable').getElementsByTagName('tbody')[0];
                            const rows = table.getElementsByTagName('tr');

                            // Free-to-paid row
                            rows[0].getElementsByTagName('td')[1].textContent = '3,2%';
                            rows[0].getElementsByTagName('td')[2].textContent = '4,5%';
                            rows[0].getElementsByTagName('td')[3].textContent = '5,0%';

                            // CAC row
                            rows[1].getElementsByTagName('td')[1].textContent = cacYear1 + ' €';
                            rows[1].getElementsByTagName('td')[2].textContent = cacYear2 + ' €';
                            rows[1].getElementsByTagName('td')[3].textContent = cacYear3 + ' €';

                            // LTV row
                            rows[2].getElementsByTagName('td')[1].textContent = ltvYear1 + ' €';
                            rows[2].getElementsByTagName('td')[2].textContent = ltvYear2 + ' €';
                            rows[2].getElementsByTagName('td')[3].textContent = ltvYear3 + ' €';

                            // LTV:CAC ratio row
                            rows[3].getElementsByTagName('td')[1].textContent = ltvCacYear1;
                            rows[3].getElementsByTagName('td')[2].textContent = ltvCacYear2;
                            rows[3].getElementsByTagName('td')[3].textContent = ltvCacYear3;

                            // Churn rate row
                            rows[4].getElementsByTagName('td')[1].textContent = churnYear1 + '%';
                            rows[4].getElementsByTagName('td')[2].textContent = churnYear2 + '%';
                            rows[4].getElementsByTagName('td')[3].textContent = churnYear3 + '%';

                            // Update ROI Comparison Table
                            const roiTable = document.getElementById('roiComparisonTable').getElementsByTagName('tbody')[0];
                            const roiRows = roiTable.getElementsByTagName('tr');

                            // Freemium investment row
                            roiRows[1].getElementsByTagName('td')[1].textContent = formatNumber(freemiumInvestmentY1) + ' €';
                            roiRows[1].getElementsByTagName('td')[2].textContent = formatNumber(freemiumInvestmentY2) + ' €';
                            roiRows[1].getElementsByTagName('td')[3].textContent = formatNumber(freemiumInvestmentY3) + ' €';
                            roiRows[1].getElementsByTagName('td')[4].textContent = formatNumber(freemiumInvestmentY1 + freemiumInvestmentY2 + freemiumInvestmentY3) + ' €';

                            // Freemium revenue row
                            roiRows[2].getElementsByTagName('td')[1].textContent = formatNumber(freemiumRevenueY1) + ' €';
                            roiRows[2].getElementsByTagName('td')[2].textContent = formatNumber(freemiumRevenueY2) + ' €';
                            roiRows[2].getElementsByTagName('td')[3].textContent = formatNumber(freemiumRevenueY3) + ' €';
                            roiRows[2].getElementsByTagName('td')[4].textContent = formatNumber(freemiumRevenueY1 + freemiumRevenueY2 + freemiumRevenueY3) + ' €';

                            // Freemium ROI row
                            roiRows[3].getElementsByTagName('td')[1].textContent = freemiumRoiY1 + '%';
                            roiRows[3].getElementsByTagName('td')[2].textContent = freemiumRoiY2 + '%';
                            roiRows[3].getElementsByTagName('td')[3].textContent = freemiumRoiY3 + '%';
                            const freemiumTotalRoi = Math.round(((freemiumRevenueY1 + freemiumRevenueY2 + freemiumRevenueY3) - (freemiumInvestmentY1 + freemiumInvestmentY2 + freemiumInvestmentY3)) / (freemiumInvestmentY1 + freemiumInvestmentY2 + freemiumInvestmentY3) * 100);
                            roiRows[3].getElementsByTagName('td')[4].textContent = freemiumTotalRoi + '%';

                            // Traditional investment row
                            roiRows[5].getElementsByTagName('td')[1].textContent = formatNumber(traditionalInvestmentY1) + ' €';
                            roiRows[5].getElementsByTagName('td')[2].textContent = formatNumber(traditionalInvestmentY2) + ' €';
                            roiRows[5].getElementsByTagName('td')[3].textContent = formatNumber(traditionalInvestmentY3) + ' €';
                            roiRows[5].getElementsByTagName('td')[4].textContent = formatNumber(traditionalInvestmentY1 + traditionalInvestmentY2 + traditionalInvestmentY3) + ' €';

                            // Traditional revenue row
                            roiRows[6].getElementsByTagName('td')[1].textContent = formatNumber(traditionalRevenueY1) + ' €';
                            roiRows[6].getElementsByTagName('td')[2].textContent = formatNumber(traditionalRevenueY2) + ' €';
                            roiRows[6].getElementsByTagName('td')[3].textContent = formatNumber(traditionalRevenueY3) + ' €';
                            roiRows[6].getElementsByTagName('td')[4].textContent = formatNumber(traditionalRevenueY1 + traditionalRevenueY2 + traditionalRevenueY3) + ' €';

                            // Traditional ROI row
                            roiRows[7].getElementsByTagName('td')[1].textContent = traditionalRoiY1 + '%';
                            roiRows[7].getElementsByTagName('td')[2].textContent = traditionalRoiY2 + '%';
                            roiRows[7].getElementsByTagName('td')[3].textContent = traditionalRoiY3 + '%';
                            const traditionalTotalRoi = Math.round(((traditionalRevenueY1 + traditionalRevenueY2 + traditionalRevenueY3) - (traditionalInvestmentY1 + traditionalInvestmentY2 + traditionalInvestmentY3)) / (traditionalInvestmentY1 + traditionalInvestmentY2 + traditionalInvestmentY3) * 100);
                            roiRows[7].getElementsByTagName('td')[4].textContent = traditionalTotalRoi + '%';
                        }

                        // Pricing section data update
                        function updatePricingData() {
                            const freeSpeeches = parseInt(document.getElementById('freeSpeeches').value);
                            const professionalSpeeches = parseInt(document.getElementById('professionalSpeeches').value);
                            const businessSpeeches = parseInt(document.getElementById('businessSpeeches').value);
                            const professionalPrice = parseFloat(document.getElementById('professionalPrice3').value).toFixed(2);
                            const businessPrice = parseFloat(document.getElementById('businessPrice3').value).toFixed(2);
                            const enterprisePrice = parseFloat(document.getElementById('enterprisePrice3').value).toFixed(2);
                            const yearlyDiscount = parseFloat(document.getElementById('yearlyDiscount').value);

                            // Update display
                            document.getElementById('freeSpeeches2').textContent = freeSpeeches;
                            document.getElementById('professionalSpeeches2').textContent = professionalSpeeches;
                            document.getElementById('businessSpeeches2').textContent = businessSpeeches;

                            document.getElementById('professionalPriceDisplay').textContent = professionalPrice.replace('.', ',') + ' €';
                            document.getElementById('businessPriceDisplay').textContent = businessPrice.replace('.', ',') + ' €';
                            document.getElementById('enterprisePriceDisplay').textContent = enterprisePrice.replace('.', ',') + ' €';
                        }

                        // Overview charts update
                        function updateOverviewCharts() {
                            const conversionRate = parseFloat(document.getElementById('conversionRate').value) / 100;
                            const userBase = parseInt(document.getElementById('userBase').value);
                            const proPrice = parseFloat(document.getElementById('proPrice').value);
                            const businessPrice = parseFloat(document.getElementById('businessPrice').value);

                            // Calculate revenue
                            const payingUsers = Math.round(userBase * conversionRate);
                            const freeUsers = userBase - payingUsers;

                            // Assume distribution: 70% Pro, 25% Business, 5% Enterprise
                            const proUsers = Math.round(payingUsers * 0.7);
                            const businessUsers = Math.round(payingUsers * 0.25);
                            const enterpriseUsers = payingUsers - proUsers - businessUsers;

                            const proRevenue = proUsers * proPrice;
                            const businessRevenue = businessUsers * businessPrice;
                            const enterpriseRevenue = enterpriseUsers * 199.99;

                            const totalRevenue = proRevenue + businessRevenue + enterpriseRevenue;
                            const additionalRevenue = payingUsers * 6; // Average additional revenue per paying user (credits, etc.)

                            const grandTotalRevenue = totalRevenue + additionalRevenue;

                            // Update Overview Revenue Chart
                            overviewRevenueChart.data.datasets[0].data = [proRevenue, businessRevenue, enterpriseRevenue, additionalRevenue];
                            overviewRevenueChart.update();

                            // Update Overview Growth Chart
                            const months = [6, 12, 18, 24, 30, 36];

                            // Recalculate user growth data
                            const initialUsers = 5000;
                            const userGrowthData = months.map((month, index) => {
                                let totalUsers = initialUsers;
                                if (index >= 1) totalUsers = 15000;
                                if (index >= 2) totalUsers = 30000;
                                if (index >= 3) totalUsers = 60000;
                                if (index >= 4) totalUsers = 100000;
                                if (index >= 5) totalUsers = 150000;

                                let convRate = 0.03;
                                if (index >= 1) convRate = 0.04;
                                if (index >= 2) convRate = 0.045;
                                if (index >= 4) convRate = 0.05;

                                const payingUsers = Math.round(totalUsers * convRate);

                                // Calculate revenue based on the current pricing model
                                const proUsers = Math.round(payingUsers * 0.7);
                                const businessUsers = Math.round(payingUsers * 0.25);
                                const enterpriseUsers = payingUsers - proUsers - businessUsers;

                                const proRev = proUsers * proPrice;
                                const businessRev = businessUsers * businessPrice;
                                const enterpriseRev = enterpriseUsers * 199.99;
                                const additionalRev = payingUsers * 6;

                                const totalRev = proRev + businessRev + enterpriseRev + additionalRev;

                                return {
                                    month,
                                    totalUsers,
                                    payingUsers,
                                    revenue: Math.round(totalRev)
                                };
                            });

                            overviewGrowthChart.data.labels = userGrowthData.map(item => `Monat ${item.month}`);
                            overviewGrowthChart.data.datasets[0].data = userGrowthData.map(item => item.totalUsers);
                            overviewGrowthChart.data.datasets[1].data = userGrowthData.map(item => item.revenue / 20); // Scaled for visibility
                            overviewGrowthChart.update();
                        }

                        // Initialize Market Charts
                        function initializeMarketCharts() {
                            // Market Regions Chart
                            const marketRegionsCtx = document.getElementById('marketRegionsChart').getContext('2d');
                            marketRegionsChart = new Chart(marketRegionsCtx, {
                                type: 'pie',
                                data: {
                                    labels: ['Nordamerika (40%)', 'Europa (28%)', 'Asien-Pazifik (20%)', 'Lateinamerika (8%)', 'Rest der Welt (4%)'],
                                    datasets: [{
                                        data: [9, 6.5, 4.5, 1.5, 1],
                                        backgroundColor: ['#4a86e8', '#34a853', '#fbbc04', '#ea4335', '#673ab7'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${value}M Nutzer`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });

                            // Target Groups Chart
                            const targetGroupsCtx = document.getElementById('targetGroupsChart').getContext('2d');
                            targetGroupsChart = new Chart(targetGroupsCtx, {
                                type: 'bar',
                                data: {
                                    labels: ['B2B-Verkäufer', 'B2C-Vertriebsmitarbeiter', 'Freiberufliche Berater', 'Vertriebsleiter/Trainer', 'Unternehmer/KMUs'],
                                    datasets: [{
                                        label: 'Anteil in %',
                                        data: [38, 32, 15, 8, 7],
                                        backgroundColor: ['#4a86e8', '#34a853', '#fbbc04', '#ea4335', '#673ab7'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            max: 40,
                                            title: {
                                                display: true,
                                                text: 'Anteil in %'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        }

                        // Initialize Conversion Charts
                        function initializeConversionCharts() {
                            // Conversion Funnel Chart
                            const conversionFunnelCtx = document.getElementById('conversionFunnelChart').getContext('2d');
                            conversionFunnelChart = new Chart(conversionFunnelCtx, {
                                type: 'bar',
                                data: {
                                    labels: ['Website-Besucher', 'Registrierungen', 'Aktive Free-User', 'Zahlende Kunden'],
                                    datasets: [{
                                        label: 'Anzahl',
                                        data: [100000, 6500, 3250, 162],
                                        backgroundColor: ['#4a86e8', '#34a853', '#fbbc04', '#ea4335'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: {
                                            type: 'logarithmic',
                                            min: 100,
                                            max: 100000,
                                            title: {
                                                display: true,
                                                text: 'Anzahl (logarithmische Skala)'
                                            },
                                            ticks: {
                                                callback: function(value) {
                                                    if (value === 100 || value === 1000 || value === 10000 || value === 100000) {
                                                        return formatNumber(value);
                                                    }
                                                    return '';
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const value = context.raw || 0;
                                                    const percentage = [100, 6.5, 3.25, 0.162][context.dataIndex];
                                                    return `Anzahl: ${formatNumber(value)} (${percentage}%)`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Initialize Revenue Charts
                        function initializeRevenueCharts() {
                            // Revenue by User Count Chart
                            const revenueByUserCountCtx = document.getElementById('revenueByUserCountChart').getContext('2d');
                            revenueByUserCountChart = new Chart(revenueByUserCountCtx, {
                                type: 'line',
                                data: {
                                    labels: ['10k', '25k', '50k', '100k', '250k'],
                                    datasets: [{
                                        label: 'Monatlicher Umsatz (€)',
                                        data: [28495, 71238, 142475, 284950, 712375],
                                        borderColor: '#4a86e8',
                                        backgroundColor: 'rgba(74, 134, 232, 0.2)',
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Monatlicher Umsatz (€)'
                                            },
                                            ticks: {
                                                callback: value => formatNumber(value) + ' €'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Anzahl Nutzer'
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const value = context.raw || 0;
                                                    return `Umsatz: ${formatNumber(value)} €`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });

                            // Revenue Distribution Chart
                            const revenueDistributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
                            revenueDistributionChart = new Chart(revenueDistributionCtx, {
                                type: 'pie',
                                data: {
                                    labels: [
                                        'Professional-Abos (36.8%)',
                                        'Business-Abos (35.1%)',
                                        'Enterprise-Abos (17.5%)',
                                        'Credit-Pakete (Speech) (5.8%)',
                                        'Credit-Pakete (KI) (4.8%)'
                                    ],
                                    datasets: [{
                                        data: [104965, 99988, 49998, 16500, 13500],
                                        backgroundColor: ['#4a86e8', '#34a853', '#673ab7', '#fbbc04', '#ea4335'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${formatNumber(value)} €`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Initialize Cost Charts
                        function initializeCostCharts() {
                            // Cost Structure Chart
                            const costStructureCtx = document.getElementById('costStructureChart').getContext('2d');
                            costStructureChart = new Chart(costStructureCtx, {
                                type: 'pie',
                                data: {
                                    labels: [
                                        'Support & Personal (30.8%)',
                                        'KI-Kosten (32.0%)',
                                        'Marketing & Akquise (26.3%)',
                                        'Zahlungsabwicklung (6.3%)',
                                        'Hosting & Infrastr. (4.6%)'
                                    ],
                                    datasets: [{
                                        data: [50000, 52000, 42700, 10220, 7500],
                                        backgroundColor: ['#4a86e8', '#ea4335', '#34a853', '#fbbc04', '#673ab7'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${formatNumber(value)} €`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Initialize Growth Charts
                        function initializeGrowthCharts() {
                            // User Growth Chart
                            const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
                            userGrowthChart = new Chart(userGrowthCtx, {
                                type: 'line',
                                data: {
                                    labels: ['Monat 6', 'Monat 12', 'Monat 18', 'Monat 24', 'Monat 30', 'Monat 36'],
                                    datasets: [
                                        {
                                            label: 'Gesamtnutzer',
                                            data: [5000, 15000, 30000, 60000, 100000, 150000],
                                            borderColor: '#4a86e8',
                                            backgroundColor: 'rgba(74, 134, 232, 0.2)',
                                            borderWidth: 2,
                                            fill: true,
                                            yAxisID: 'y-users'
                                        },
                                        {
                                            label: 'Zahlende Nutzer',
                                            data: [150, 600, 1350, 2700, 5000, 7500],
                                            borderColor: '#34a853',
                                            backgroundColor: 'rgba(52, 168, 83, 0.2)',
                                            borderWidth: 2,
                                            fill: true,
                                            yAxisID: 'y-users'
                                        },
                                        {
                                            label: 'Konversionsrate (%)',
                                            data: [3, 4, 4.5, 4.5, 5, 5],
                                            borderColor: '#fbbc04',
                                            backgroundColor: 'transparent',
                                            borderWidth: 2,
                                            fill: false,
                                            borderDash: [5, 5],
                                            yAxisID: 'y-rate'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        'y-users': {
                                            beginAtZero: true,
                                            position: 'left',
                                            title: {
                                                display: true,
                                                text: 'Nutzer'
                                            },
                                            ticks: {
                                                callback: value => `${value/1000}k`
                                            }
                                        },
                                        'y-rate': {
                                            beginAtZero: true,
                                            position: 'right',
                                            min: 0,
                                            max: 8,
                                            title: {
                                                display: true,
                                                text: 'Konversionsrate (%)'
                                            },
                                            ticks: {
                                                callback: value => `${value}%`
                                            },
                                            grid: {
                                                drawOnChartArea: false
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const datasetIndex = context.datasetIndex;
                                                    const value = context.raw || 0;

                                                    if (datasetIndex === 0) {
                                                        return `Gesamtnutzer: ${formatNumber(value)}`;
                                                    } else if (datasetIndex === 1) {
                                                        return `Zahlende Nutzer: ${formatNumber(value)}`;
                                                    } else {
                                                        return `Konversionsrate: ${value}%`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Initialize ROI Charts
                        function initializeRoiCharts() {
                            // ROI Comparison Chart
                            const roiComparisonCtx = document.getElementById('roiComparisonChart').getContext('2d');
                            roiComparisonChart = new Chart(roiComparisonCtx, {
                                type: 'bar',
                                data: {
                                    labels: ['Jahr 1', 'Jahr 2', 'Jahr 3'],
                                    datasets: [
                                        {
                                            label: 'Freemium Modell',
                                            data: [48, 600, 1610],
                                            backgroundColor: '#4a86e8',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Traditionelles Modell',
                                            data: [36, 400, 983],
                                            backgroundColor: '#34a853',
                                            borderWidth: 1
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'ROI (%)'
                                            },
                                            ticks: {
                                                callback: value => `${value}%`
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.dataset.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${value}% ROI`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Initialize Overview Charts
                        function initializeOverviewCharts() {
                            // Overview Revenue Chart
                            const overviewRevenueCtx = document.getElementById('overviewRevenueChart').getContext('2d');
                            overviewRevenueChart = new Chart(overviewRevenueCtx, {
                                type: 'pie',
                                data: {
                                    labels: ['Professional Abos', 'Business Abos', 'Enterprise Abos', 'Zusatzumsätze'],
                                    datasets: [{
                                        data: [104965, 99988, 49998, 30000],
                                        backgroundColor: ['#4a86e8', '#34a853', '#673ab7', '#fbbc04'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${formatNumber(value)} €`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });

                            // Overview Growth Chart
                            const overviewGrowthCtx = document.getElementById('overviewGrowthChart').getContext('2d');
                            overviewGrowthChart = new Chart(overviewGrowthCtx, {
                                type: 'line',
                                data: {
                                    labels: ['Monat 6', 'Monat 12', 'Monat 18', 'Monat 24', 'Monat 30', 'Monat 36'],
                                    datasets: [
                                        {
                                            label: 'Nutzer',
                                            data: [5000, 15000, 30000, 60000, 100000, 150000],
                                            borderColor: '#4a86e8',
                                            backgroundColor: 'rgba(74, 134, 232, 0.2)',
                                            borderWidth: 2,
                                            fill: true,
                                            yAxisID: 'y-users'
                                        },
                                        {
                                            label: 'Umsatz (skaliert)',
                                            data: [465, 2070, 5175, 13800, 28500, 45000],
                                            borderColor: '#34a853',
                                            backgroundColor: 'transparent',
                                            borderWidth: 2,
                                            borderDash: [5, 5],
                                            fill: false,
                                            yAxisID: 'y-users'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        'y-users': {
                                            beginAtZero: true,
                                            position: 'left',
                                            title: {
                                                display: true,
                                                text: 'Nutzer'
                                            },
                                            ticks: {
                                                callback: value => `${value/1000}k`
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const datasetIndex = context.datasetIndex;
                                                    const value = context.raw || 0;

                                                    if (datasetIndex === 0) {
                                                        return `Nutzer: ${formatNumber(value)}`;
                                                    } else {
                                                        return `Umsatz: ${formatNumber(value * 20)} €`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    </script>





                </main>
            </main>

            <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </footer>
        </div>
    </div>
</div>
</body>
</html>
