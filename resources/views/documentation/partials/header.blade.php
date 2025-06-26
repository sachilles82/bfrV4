<header class="bg-white shadow-sm border-b">
    <div class="container mx-auto px-6 py-4">
        <h1 class="text-3xl font-bold text-gray-900">User Model Dokumentation</h1>
        <p class="text-gray-600 mt-2">Vollständige Übersicht über Features, Traits und Funktionen</p>
    </div>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        .header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .section {
            background: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h2 {
            color: #667eea;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        h3 {
            color: #4a5568;
            margin-top: 1.5rem;
        }

        .code-block {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }

        .code-block pre {
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .feature-card {
            background: #f7fafc;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .feature-card h4 {
            margin-top: 0;
            color: #667eea;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }

        .alert-info {
            background: #ebf8ff;
            border-left: 4px solid #3182ce;
            color: #2c5282;
        }

        .alert-warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        .step-list {
            counter-reset: step-counter;
            list-style: none;
            padding-left: 0;
        }

        .step-list li {
            counter-increment: step-counter;
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 3rem;
        }

        .step-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: #667eea;
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-required {
            background: #fee;
            color: #c53030;
        }

        .badge-optional {
            background: #e6fffa;
            color: #047857;
        }
    </style>
</header>
