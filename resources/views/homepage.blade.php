<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .hero {
            background: linear-gradient(135deg, #003c8f, #1976d2);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }
        .btn-yellow {
            background-color: #ffb300;
            color: white;
        }
        .features {
            background: #f7f9fc;
            padding: 60px 20px;
        }
        .features .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .footer-section {
            padding: 60px 20px;
        }
        .vector-bg {
        position: absolute;
        top: -40px; /* adjust as needed */
        left: 50%;
        transform: translateX(-50%);
        width: 50%;
        height: auto;
        z-index: 0;
        pointer-events: none;
        opacity: 1;
        }
    </style>
    <script
    src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs"
    type="module"
    ></script>
</head>
<body>

    <!-- Header -->
     <nav class="navbar navbar-light bg-white px-5 py-3 shadow-sm">
        <a class="navbar-brand fw-bold fs-3" href="#">Aral<span class="text-primary">Sipnayan</span></a>
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 py-2">Login</a>
    </nav>

    <!-- Hero -->
    <section class="hero d-flex align-items-center" style="height: 100vh;">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Text Left -->
                <div class="col-md-6 text-start text-white px-5">
                    <h1 class="fw-bold display-3">
                        Master <span class="text-warning">Advanced Mathematics</span><br>with Interactive Learning
                    </h1>
                    <p class="mt-3 fs-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam cursus mauris ut diam vehicula vehicula.
                    </p>
                    <div class="mt-4">
                        <a href="#" class="btn btn-yellow btn-lg me-2">Start Your Journey</a>
                        <a href="#" class="btn btn-outline-light btn-lg">Know More</a>
                    </div>
                </div>

                <!-- Animation Right -->
                <div class="col-md-6 text-center">
                    <dotlottie-player
                        src="https://lottie.host/49f63dad-e594-456d-ad02-f96acca6a471/MkfFKJ2FTZ.lottie"
                        background="transparent"
                        speed="1"
                        style="width: 100%; max-width: 450px; height: auto;"
                        loop
                        autoplay>
                    </dotlottie-player>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features position-relative style="z-index: 1; background-color: #f7f9fc; style="height: 100vh;">
     <img src="{{ asset('images/homepage/Vector 1.png') }}" alt="Decorative Curve" class="vector-bg">
        <div class="container position-relative" style="z-index: 2;">
            <h2 class="fw-bold mb-3 display-6 display-md-5 display-lg-4 text-center">How AralSipnayan <span class="text-primary">transforms</span> learning</h2>
            <p class="mb-4 lead text-center">Experience the future of mathematics education for grade 6 students</p>
            <div class="row row-cols-1 row-cols-md-5 g-4">
                @php
                    $features = [
                        [
                            'icon' => '<img src="' . asset('images/homepage/Property 1=book-open.png') . '" width="50">',
                            'title' => 'Advanced Lessons',
                            'desc' => 'Comprehensive grade 6 math content',
                        ],
                        [
                            'icon' => '<img src="' . asset('images/homepage/Property 1=smartphone.png') . '" width="50">',
                            'title' => 'Cross-Platform',
                            'desc' => 'Access anytime on any device',
                        ],
                        [
                            'icon' => '<img src="' . asset('images/homepage/Property 1=target.png') . '" width="50">',
                            'title' => 'Interactive Learning',
                            'desc' => 'Engaging, step-by-step lessons',
                        ],
                        [
                            'icon' => '<img src="' . asset('images/homepage/Property 1=users.png') . '" width="50">',
                            'title' => 'Progress Tracking',
                            'desc' => 'Monitor scores and achievements',
                        ],
                        [
                            'icon' => '<img src="' . asset('images/homepage/Property 1=star.png') . '" width="50">',
                            'title' => 'Gamified Experience',
                            'desc' => 'Fun, rewards, and leaderboards',
                        ],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="col">
                        <div class="card p-4 h-100 text-center">
                            <div class="display-5">{!! $feature['icon'] !!}</div>
                            <h5 class="fw-bold mt-3">{{ $feature['title'] }}</h5>
                            <p>{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="container-fluid position-relative text-left">
            <div class="row align-items-center ">
                <!-- Image Column -->
                <div class="col-md-6 mb-4 p-5">
                    <img src="{{ asset('images/homepage/placeholder1.jpg') }}" alt="Math Illustration" class="img-fluid">
                </div>

                <!-- Text Content Column -->
                <div class="col-md-6 p-5">
                    <h2 class="fw-bold mb-3 display-4 text-left">
                        Transform your <span class="text-primary">mathematical journey</span> today!
                    </h2>
                    <p class="mt-3 lead text-justify" style="font-size: 1.5rem;">
                        Join fellow Grade 6 students who are wanting to improved their mathematics
                        skills </br> through our innovative platform. Experience personalized learning that adapts 
                        </br>to your pace and style
                    </p>
                    <a href="#" class="btn btn-primary mt-3 btn-lg" style="font-size: 1.3rem;">Start Learning Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <section class="footer-section text-center text-md-start">

    </section>

</body>
</html>