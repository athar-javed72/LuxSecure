<footer class="mg-footer">
    <div class="mg-container">
        <div class="mg-footer__grid">
            <div class="mg-footer__brand">
                <a href="{{ route('home') }}" class="mg-logo" aria-label="Maskanoor home">
                    <img class="mg-logo__mark" src="{{ \App\Support\BrandAssets::logo('monogram-nostar', 'dark') }}" alt="" width="28" height="28" decoding="async">
                    <span>Maskanoor</span>
                </a>
                <p class="mg-footer__tagline">Elevating Spaces. Enriching Stays.</p>
                <div class="mg-footer__social" aria-label="Social">
                    <a href="https://instagram.com" rel="noopener noreferrer" target="_blank">Instagram</a>
                    <a href="https://facebook.com" rel="noopener noreferrer" target="_blank">Facebook</a>
                    <a href="https://linkedin.com" rel="noopener noreferrer" target="_blank">LinkedIn</a>
                    <a href="https://youtube.com" rel="noopener noreferrer" target="_blank">YouTube</a>
                </div>
            </div>

            <div>
                <h2>Explore</h2>
                <ul>
                    <li><a href="{{ route('properties') }}">Properties</a></li>
                    <li><a href="{{ route('divisions.hospitality') }}">Hotels</a></li>
                    <li><a href="{{ route('divisions.living') }}">Living</a></li>
                    <li><a href="{{ url('/#about') }}">About</a></li>
                </ul>
            </div>

            <div>
                <h2>Legal</h2>
                <ul>
                    <li><a href="{{ url('/pages/privacy-policy') }}">Privacy policy</a></li>
                    <li><a href="{{ url('/pages/terms-of-service') }}">Terms of service</a></li>
                    <li><a href="{{ url('/pages/refund-and-cancellation') }}">Refund and cancellation</a></li>
                    <li><a href="{{ url('/#faq') }}">FAQ</a></li>
                </ul>
            </div>

            <div class="mg-footer__contact">
                <h2>Contact</h2>
                <p><a href="mailto:support@maskanoor.com">support@maskanoor.com</a></p>
                <p><a href="tel:+923228439536">+92 322 8439536</a></p>
                <p>Available 24 hours</p>
                <a href="{{ route('contact') }}" class="mg-btn mg-btn--gold">Send a message</a>
            </div>
        </div>

        <div class="mg-footer__bottom">
            <p>© {{ date('Y') }} Maskanoor Group · Developed by Nexora Labs</p>
            <p>EN · PKR</p>
        </div>
    </div>
</footer>
