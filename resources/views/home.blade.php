@extends('layouts.app')

@section('content')
<style>
    body, main {
        margin: 0;
        padding: 0;
        width: 100%;
        overflow-x: hidden;
    }

    /* 2. main or first part ni sya sa home*/
    .hero-section {
        width: 100%;
        min-height: 92vh; 
        background-image: url("{{ asset('images/homebg2.png') }}");
        background-size: cover; 
        background-position: center; 
        display: flex;
        align-items: center; 
        margin-top: -3px; 
    }

    .hero-content-overlay {
        width: 100%;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 5%;
    }

    .hero-text-box {
        max-width: 600px;
    }

    .hero-title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(2.5rem, 5vw, 4rem);
        color: #1a1a1a;
        line-height: 1.1;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .hero-title span { color: #d4af37; } 

    /* 4. STEPS SECTION kani sya*/
    .steps-section {
        width: 100%;
        background-color: #f5f5f5;
        padding: 100px 0;
    }

    .steps-content {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 60px;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 5%;
        align-items: start;
    }
    .steps-images {
        display: flex; 
        flex-direction: column; 
        gap: 30px; 
        align-items: stretch; 
    }
    .steps-images img {
        width: 100%;
        border-radius: 30px;
        box-shadow: 20px 20px 0px rgba(128, 0, 0, 0.1); 
        transition: 0.5s ease;
    }

    .step-subtitle {
        color: #d4af37;
        font-family: 'Orbitron', sans-serif;
        letter-spacing: 3px;
        font-size: 0.8rem;
        margin-bottom: 10px;
        display: block;
    }

    .step-main-title {
        font-family: 'Orbitron', sans-serif;
        color: #800000;
        font-size: 2.5rem;
        margin-bottom: 40px;
        text-transform: uppercase;
    }

    .modern-step-card {
        display: flex;
        align-items: flex-start;
        gap: 25px;
        padding: 25px;
        margin-bottom: 20px;
        background: #fdfdfd;
        border-radius: 20px;
        border: 1px solid #eee;
        transition: all 0.3s ease;
    }

    .modern-step-card:hover {
        transform: translateX(15px);
        background: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border-color: #800000;
    }

    .step-badge {
        background: #800000;
        color: #d4af37;
        font-family: 'Orbitron', sans-serif;
        min-width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .step-info h4 {
        margin: 0 0 8px 0;
        color: #1a1a1a;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .step-info p {
        margin: 0;
        color: #666;
        line-height: 1.5;
        font-size: 0.95rem;
    }

    /* 5. DATES SECTION glassmorphism design naa diri */
    .dates-section {
        width: 100%;
        background-image: url("{{ asset('images/datebg1.png') }}");
        background-size: cover; 
        background-position: center; 
        padding: 100px 0;
        position: relative;
        z-index: 10;
        color: white;
    }

    .dates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 5%;
    }

    .date-card {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 20px; 
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        padding: 35px;
        transition: all 0.3s ease;
    }

    .date-card:hover {
        transform: translateY(-10px);
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* MAROON BUTTON ni sya */
    .btn-maroon {
        background-color: #800000;
        color: white;
        padding: 16px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.9rem;
        transition: 0.3s;
        display: inline-block;
        font-family: 'Orbitron', sans-serif;
        border: none;
        cursor: pointer;
    }

    .btn-maroon:hover {
        background-color: #600000;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>

<section class="hero-section">
    <div class="hero-content-overlay">
        <div class="hero-text-box">
            <h1 class="hero-title">SECURE YOUR <span>FUTURE</span> STARTING TODAY</h1>
            <p style="color: #555; font-size: 1.1rem; margin-bottom: 30px; line-height: 1.6;">
                Easily settle your UM Online Enrollment payments—including tuition, ID, 
                and more—anytime, anywhere with our secure portal.
            </p>
            <a href="{{ route('enrollments.step3') }}" class="btn-maroon">Enroll Now</a>
        </div>
    </div>
</section>

<section class="steps-section">
    <div class="steps-content">
        <div class="steps-images">
            <img src="{{ asset('images/datebg1.png') }}" alt="University Campus">
            <img src="{{ asset('images/datebg1.png') }}" alt="University Campus">
        </div>
        

        <div class="steps-details">
            <span class="step-subtitle">ADMISSIONS PROCESS</span>
            <h2 class="step-main-title">Steps to Enroll</h2>
            
            <div class="modern-step-container">
                <div class="modern-step-card">
                    <div class="step-badge">01</div>
                    <div class="step-info">
                        <h4>Get Assessed</h4>
                        <p>Visit the Registrar or go online to request your official assessment form and evaluate your subjects for the semester.</p>
                    </div>
                </div>

                <div class="modern-step-card">
                    <div class="step-badge">02</div>
                    <div class="step-info">
                        <h4>Secure Payment</h4>
                        <p>Proceed to the Payment Portal to settle tuition and miscellaneous fees via our secure online gateway.</p>
                    </div>
                </div>

                <div class="modern-step-card">
                    <div class="step-badge">03</div>
                    <div class="step-info">
                        <h4>Verify Transaction</h4>
                        <p>Upload your proof of payment or wait for the system to auto-verify your transaction status.</p>
                    </div>
                </div>

                <div class="modern-step-card">
                    <div class="step-badge">04</div>
                    <div class="step-info">
                        <h4>Claim ID & Certificate</h4>
                        <p>Once verified, visit the Admissions office to claim your official University ID and Certificate of Matriculation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="dates-section">
    <div class="dates-grid">
        <div style="grid-column: 1 / -1;">
            <h2 style="font-family:'Orbitron'; color:white; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 2px;">
                Enrollment for Academic Year 2025-2026
            </h2>
        </div>

        <div class="date-card">
            <h4 style="color:#d4af37; font-family:'Orbitron'; margin-bottom: 15px;">ENROLLMENT START</h4>
            <p style="font-size: 0.95rem; line-height: 1.6; opacity: 0.9;">
                Officially begins on <strong>July 15, 2025</strong>. Complete assessment and payment process early to secure slots.
            </p>
        </div>

        <div class="date-card">
            <h4 style="color:#d4af37; font-family:'Orbitron'; margin-bottom: 15px;">PAYMENT DEADLINE</h4>
            <p style="font-size: 0.95rem; line-height: 1.6; opacity: 0.9;">
                Ensure assessed fees are paid on or before <strong>September 10, 2025</strong> to avoid late penalties and account holds.
            </p>
        </div>

        <div class="date-card">
            <h4 style="color:#d4af37; font-family:'Orbitron'; margin-bottom: 15px;">CLASS BEGINS</h4>
            <p style="font-size: 0.95rem; line-height: 1.6; opacity: 0.9;">
                Official start of classes for the First Semester is <strong>September 16, 2025</strong>. Attendance is mandatory for orientation.
            </p>
        </div>
    </div>
</section>
@endsection