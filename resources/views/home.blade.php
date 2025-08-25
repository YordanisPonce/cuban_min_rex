@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
<!-- Hero: Start -->
<section id="hero-animation">
    <div id="landingHero" class="section-py landing-hero position-relative">
        <img src="{{ asset('assets/img/front-pages/backgrounds/hero-bg.png') }}" alt="hero background" class="position-absolute top-0 start-50 translate-middle-x object-fit-cover w-100 h-100" data-speed="1" />
        <div class="container">
            <div class="hero-text-box text-center position-relative">
                <h1 class="text-primary hero-title display-6 fw-extrabold">One dashboard to manage all your businesses</h1>
                <h2 class="hero-sub-title h6 mb-6">
                    Production-ready & easy to use Admin Template<br class="d-none d-lg-block" />
                    for Reliability and Customizability.
                </h2>
                <div class="landing-hero-btn d-inline-block position-relative">
                    <span class="hero-btn-item position-absolute d-none d-md-flex fw-medium">Join community <img src="{{ asset('assets/img/front-pages/icon/Join-community-arrow.png') }}" alt="Join community arrow" class="scaleX-n1-rtl" /></span>
                    <a href="#landingPricing" class="btn btn-primary btn-lg">Get early access</a>
                </div>
            </div>
            <div id="heroDashboardAnimation" class="hero-animation-img">
                <a href="https://demos.pixinvent.com/vuexy-html-admin-template/html/vertical-menu-template/app-ecommerce-dashboard.html" target="_blank">
                    <div id="heroAnimationImg" class="position-relative hero-dashboard-img">
                        <img src="{{ asset('assets/img/front-pages/landing-page/hero-dashboard-light.png')}}" alt="hero dashboard" class="animation-img" data-app-light-img="{{ asset('assets/front-pages/front-pages/landing-page/hero-dashboard-dark.png')}}" />
                        <img src="{{ asset('assets/img/front-pages/landing-page/hero-elements-light.png')}}" alt="hero elements" class="position-absolute hero-elements-img animation-img top-0 start-0" data-app-light-img="front-pages/landing-page/hero-elements-light.png" data-app-dark-img="assets/img/front-pages/landing-page/hero-elements-dark.png" />
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="landing-hero-blank"></div>
</section>
<!-- Hero: End -->

<!-- Useful features: Start -->
<section id="landingFeatures" class="section-py landing-features">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">Useful Features</span>
        </div>
        <h4 class="text-center mb-1">
            <span class="position-relative fw-extrabold z-1"
                >Everything you need
                <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png')}}" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
            </span>
            to start your next project
        </h4>
        <p class="text-center mb-12">Not just a set of tools, the package includes ready-to-deploy conceptual application.</p>
        <div class="features-icon-wrapper row gx-0 gy-6 g-sm-12">
            <!-- Feature 1 -->
            <div class="col-lg-4 col-sm-6 text-center features-icon-box">
                <div class="mb-4 text-primary text-center">
                    <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.2" d="M10 44.4663V18.4663C10 17.4054 10.4214 16.388 11.1716 15.6379C11.9217 14.8877 12.9391 14.4663 14 14.4663H50C51.0609 14.4663 52.0783 14.8877 52.8284 15.6379C53.5786 16.388 54 17.4054 54 18.4663V44.4663H10Z" fill="currentColor" />
                        <path
                            d="M10 44.4663V18.4663C10 17.4054 10.4214 16.388 11.1716 15.6379C11.9217 14.8877 12.9391 14.4663 14 14.4663H50C51.0609 14.4663 52.0783 14.8877 52.8284 15.6379C53.5786 16.388 54 17.4054 54 18.4663V44.4663M36 22.4663H28M6 44.4663H58V48.4663C58 49.5272 57.5786 50.5446 56.8284 51.2947C56.0783 52.0449 55.0609 52.4663 54 52.4663H10C8.93913 52.4663 7.92172 52.0449 7.17157 51.2947C6.42143 50.5446 6 49.5272 6 48.4663V44.4663Z"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <h5 class="mb-2">Quality Code</h5>
                <p class="features-icon-description">Code structure that all developers will easily understand and fall in love with.</p>
            </div>

            <!-- Feature 2 -->
            <div class="col-lg-4 col-sm-6 text-center features-icon-box">
                <div class="mb-4 text-primary text-center">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- SVG content -->
                    </svg>
                </div>
                <h5 class="mb-2">Continuous Updates</h5>
                <p class="features-icon-description">Free updates for the next 12 months, including new demos and features.</p>
            </div>

            <!-- Más features... -->
        </div>
    </div>
</section>
<!-- Useful features: End -->

<!-- Real customers reviews: Start -->
<section id="landingReviews" class="section-py bg-body landing-reviews pb-0">
    <!-- Reviews content -->
</section>
<!-- Real customers reviews: End -->

<!-- Our great team: Start -->
<section id="landingTeam" class="section-py landing-team">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">Our Great Team</span>
        </div>
        <h4 class="text-center mb-1">
            <span class="position-relative fw-extrabold z-1"
                >Supported
                <img src=""{{ asset('assets/img/front-pages/icon/section-title-icon.png')}}" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
            </span>
            by Real People
        </h4>
        <p class="text-center mb-md-11 pb-0 pb-xl-12">Who is behind these great-looking interfaces?</p>
        <div class="row gy-12 mt-2">
            <!-- Team member 1 -->
            <div class="col-lg-3 col-sm-6">
                <div class="card mt-3 mt-lg-0 shadow-none">
                    <div class="bg-label-primary border border-bottom-0 border-label-primary position-relative team-image-box">
                        <img src="{{ asset('assets/img/front-pages/landing-page/team-member-1.png')}}" class="position-absolute card-img-position bottom-0 start-50" alt="human image" />
                    </div>
                    <div class="card-body border border-top-0 border-label-primary text-center">
                        <h5 class="card-title mb-0">Sophie Gilbert</h5>
                        <p class="text-body-secondary mb-0">Project Manager</p>
                    </div>
                </div>
            </div>

            <!-- Más team members... -->
        </div>
    </div>
</section>
<!-- Our great team: End -->

<!-- Pricing plans: Start -->
<section id="landingPricing" class="section-py bg-body landing-pricing">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">Pricing Plans</span>
        </div>
        <h4 class="text-center mb-1">
            <span class="position-relative fw-extrabold z-1"
                >Tailored pricing plans
                <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png')}}" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
            </span>
            designed for you
        </h4>
        <p class="text-center pb-2 mb-7">All plans include 40+ advanced tools and features to boost your product.<br />Choose the best plan to fit your needs.</p>

        <div class="row g-6 pt-lg-5">
            <!-- Basic Plan -->
            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="text-center">
                            <img src="{{ asset('assets/img/front-pages/icon/paper-airplane.png')}}" alt="paper airplane icon" class="mb-8 pb-2" />
                            <h4 class="mb-0">Basic</h4>
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="price-monthly h2 text-primary fw-extrabold mb-0">$19</span>
                                <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">$14</span>
                                <sub class="h6 text-body-secondary mb-n1 ms-1">/mo</sub>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled pricing-list">
                            <li><h6 class="d-flex align-items-center mb-3"><span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i class="icon-base ti tabler-check icon-12px"></i></span>Timeline</h6></li>
                            <!-- Más características... -->
                        </ul>
                        <div class="d-grid mt-8">
                            <a href="https://demos.pixinvent.com/vuexy-html-admin-template/html/front-pages/payment-page.html" class="btn btn-label-primary">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Más planes de precios... -->
        </div>
    </div>
</section>
<!-- Pricing plans: End -->

<!-- Fun facts: Start -->
<section id="landingFunFacts" class="section-py landing-fun-facts">
    <div class="container">
        <div class="row gy-6">
            <!-- Fun fact 1 -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border border-primary shadow-none">
                    <div class="card-body text-center">
                        <div class="mb-4 text-primary">
                            <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- SVG content -->
                            </svg>
                        </div>
                        <h3 class="mb-0">7.1k+</h3>
                        <p class="fw-medium mb-0">Support Tickets<br />Resolved</p>
                    </div>
                </div>
            </div>

            <!-- Más fun facts... -->
        </div>
    </div>
</section>
<!-- Fun facts: End -->

<!-- FAQ: Start -->
<section id="landingFAQ" class="section-py bg-body landing-faq">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">FAQ</span>
        </div>
        <h4 class="text-center mb-1">
            Frequently asked
            <span class="position-relative fw-extrabold z-1"
                >questions
                <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png')}}" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
            </span>
        </h4>
        <p class="text-center mb-12 pb-md-4">Browse through these FAQs to find answers to commonly asked questions.</p>

        <div class="accordion" id="accordionExample">
            <!-- FAQ Item 1 -->
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">Do you charge for each upgrade?</button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping...</div>
                </div>
            </div>

            <!-- Más FAQ items... -->
        </div>
    </div>
</section>
<!-- FAQ: End -->

<!-- CTA: Start -->
<section id="landingCTA" class="section-py landing-cta position-relative p-lg-0 pb-0">
    <img src="{{ asset('assets/img/front-pages/backgrounds/cta-bg-light.png')}}" class="position-absolute bottom-0 end-0 scaleX-n1-rtl h-100 w-100 z-n1" alt="cta image" />
    <div class="container">
        <div class="row align-items-center gy-12">
            <div class="col-lg-6 text-start text-sm-center text-lg-start">
                <h3 class="cta-title text-primary fw-bold mb-1">Ready to Get Started?</h3>
                <h5 class="text-body mb-8">Start your project with a 14-day free trial</h5>
                <a href="https://demos.pixinvent.com/vuexy-html-admin-template/html/front-pages/payment-page.html" class="btn btn-lg btn-primary">Get Started</a>
            </div>
            <div class="col-lg-6 pt-lg-12 text-center text-lg-end">
                <img src="{{ asset('assets/img/front-pages/landing-page/cta-dashboard.png')}}" alt="cta dashboard" class="img-fluid mt-lg-4" />
            </div>
        </div>
    </div>
</section>
<!-- CTA: End -->

<!-- Contact Us: Start -->
<section id="landingContact" class="section-py bg-body landing-contact">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-label-primary">Contact US</span>
        </div>
        <h4 class="text-center mb-1">
            <span class="position-relative fw-extrabold z-1"
                >Let's work
                <img src="{{ asset('assets/img/front-pages/icon/section-title-icon.png')}}" alt="laptop charging" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
            </span>
            together
        </h4>
        <p class="text-center mb-12 pb-md-4">Any question or remark? just write us a message</p>

        <div class="row g-6">
            <div class="col-lg-5">
                <!-- Contact info -->
            </div>
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="mb-2">Send a message</h4>
                        <form>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-fullname">Full Name</label>
                                    <input type="text" class="form-control" id="contact-form-fullname" placeholder="john" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-email">Email</label>
                                    <input type="text" id="contact-form-email" class="form-control" placeholder="johndoe@gmail.com" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contact-form-message">Message</label>
                                    <textarea id="contact-form-message" class="form-control" rows="7" placeholder="Write a message"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Send inquiry</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Us: End -->
@endsection

@push('scripts')
<script>
    // Scripts específicos para la página home
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar sliders, tooltips, etc.
        console.log('Home page loaded');
    });
</script>
@endpush
