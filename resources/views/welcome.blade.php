<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rowland Plasticos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #E31E24;     /* Rojo Rowland */
            --secondary-red: #FF3337;    /* Rojo más claro */
            --dark-gray: #333333;        /* Gris oscuro */
            --light-gray: #F5F5F5;       /* Gris claro */
            --white: #FFFFFF;            /* Blanco */
        }

        body {
            background: var(--light-gray);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: var(--white) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            height: 50px;
            max-width: 100%;
            object-fit: contain;
        }

        .nav-link {
            color: var(--dark-gray) !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: var(--primary-red) !important;
        }

        .btn-custom {
            background-color: var(--primary-red);
            border: none;
            color: var(--white);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: var(--secondary-red);
            color: var(--white);
            transform: translateY(-2px);
        }

        .hero-section {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            background: var(--white);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(227, 30, 36, 0.1);
        }

        .text-primary {
            color: var(--primary-red) !important;
        }

        .display-4 {
            color: var(--dark-gray);
            font-weight: 700;
        }

        .lead {
            color: var(--dark-gray);
        }

        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px;
            }
            .hero-section {
                padding: 100px 0 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" alt="Rowland Plasticos" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/nosotros') }}">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/ubicacion') }}">Ubicación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-custom ms-2 px-4" href="{{ url('/Dashboard/login') }}">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="display-4 mb-4">Bienvenido a <span class="text-primary">Rowland Plasticos</span></h1>
                    <p class="lead mb-4">Sistema de gestión y control de producción</p>
                    <a href="{{ url('/Dashboard/login') }}" class="btn btn-custom btn-lg px-4">Comenzar</a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 p-4">
                                <h3>Nosotros</h3>
                                <p>Conoce más sobre nuestra empresa y nuestros servicios.</p>
                                <a href="{{ url('/nosotros') }}" class="btn btn-custom mt-auto">Ver más</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 p-4">
                                <h3>Ubicación</h3>
                                <p>Encuentra nuestra ubicación y datos de contacto.</p>
                                <a href="{{ url('/ubicacion') }}" class="btn btn-custom mt-auto">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
