@extends('layouts.app')

@section('title', 'Help Center & FAQ')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Help Center</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Help Center / FAQ's</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="accordion accordion-icon accordion-bg-light" id="faqAccordion">
                        @php
                            $faqs = [
                                ['How do I book a room?', 'Browse hostels on the homepage, open the hostel you like, pick your preferred room option and click "Book Now". Fill in your details and confirm. You will receive a booking reference and instructions to finalise payment.'],
                                ['How do I make payment?', 'Payments are made via mobile money to the number provided by HostelDock or directly at the hostel. After paying, send your payment confirmation to info@hosteldock.com or via WhatsApp so the hostel can confirm your booking.'],
                                ['Is there a booking fee?', 'There is no fee for making a reservation on the platform. You only pay the hostel accommodation amount directly.'],
                                ['Can I visit the hostel before booking?', 'Yes. We encourage you to visit the hostel during the day before completing your booking so you are confident in your choice. Contact us for directions.'],
                                ['What happens if I change my mind?', 'You may cancel a pending booking at any time. If you have already paid, contact the hostel or our support desk to arrange a refund according to the hostel’s cancellation policy.'],
                                ['Which institutions are covered?', 'We currently list hostels around UMaT (Tarkwa &amp; Takoradi), TTU (Takoradi), Ghana Telecom University (Takoradi) with more being added constantly with the intent of covering all Hostels around Campuses all over Ghana.'],
                                ['Are the hostels verified?', 'Yes. Every hostel on the platform has been inspected and verified by our team to ensure it meets our standards of security, comfort and value.'],
                                ['Can I book a room for someone else?', 'Yes, you can. Just provide the correct student details of the person for whom the room is being booked during the checkout process.'],
                            ];
                        @endphp
                        @foreach ($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $index }}">
                                        {{ $faq[0] }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">{{ $faq[1] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card shadow rounded-3 p-4 mt-4 text-center">
                        <h5>Still need help?</h5>
                        <p class="text-muted">Our support team is ready to assist you.</p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="{{ route('contact') }}" class="btn btn-primary mb-0">Contact Us</a>
                            <a href="mailto:info@hosteldock.com" class="btn btn-primary-soft mb-0">Email Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection