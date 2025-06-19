<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }} - {{ $storeSettings['store_name'] ?? config('app.name', 'Kedai Coffee') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6a3412;
            --secondary-color: #a87d56;
            --accent-color: #ffb74d;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --text-color: #333333;
            --light-text-color: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            max-width: 600px;
            padding: 2rem;
            text-align: center;
        }

        .error-icon {
            font-size: 5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(106, 52, 18, 0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1 class="error-title">{{ $title ?? 'Error' }}</h1>
        <p class="error-message">{{ $message ?? 'Terjadi kesalahan pada sistem.' }}</p>
        <div class="mt-4">
            <a href="{{ route('kiosk.index') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
        <div class="mt-3">
            <small class="text-muted">Jika masalah berlanjut, silakan hubungi admin.</small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
