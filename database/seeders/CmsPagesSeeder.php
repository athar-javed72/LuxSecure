<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('CmsPagesSeeder skipped outside local/testing.');

            return;
        }

        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_title' => 'Privacy Policy | [PLACEHOLDER: BUSINESS_NAME]',
                'meta_description' => 'Draft privacy policy for [PLACEHOLDER: BUSINESS_NAME] — review with counsel before publishing.',
                'body' => <<<'TXT'
Privacy Policy (DRAFT — not legal advice)

Last updated: [PLACEHOLDER: LAST_UPDATED_DATE]

This draft describes how [PLACEHOLDER: BUSINESS_NAME] (“we”, “us”) may handle personal information when you use our website and services for property listings, rentals, and hotel short-stays in Pakistan. Replace every [PLACEHOLDER] marker and have a qualified lawyer review this text before you publish it. Do not treat this draft as a complete or binding privacy policy.

1. Who we are
Business name: [PLACEHOLDER: BUSINESS_NAME]
Registered / business address: [PLACEHOLDER: BUSINESS_ADDRESS]
Contact email: [PLACEHOLDER: CONTACT_EMAIL]
Contact phone: [PLACEHOLDER: CONTACT_PHONE]

2. Information we may collect
Depending on how you use the platform, we may process:
- Account details (name, email, phone, password hash)
- Listing, inquiry, booking, and payment-related records you submit
- KYC or identity documents where the product requires verification
- Technical data such as IP address, device/browser type, and approximate location derived from IP
- Messages you send through contact or support forms

3. Why we may use information
We may use personal information to:
- Operate accounts, listings, bookings, and customer support
- Process or record payments and refunds according to our policies and applicable law
- Meet legal, tax, fraud-prevention, and regulatory obligations in Pakistan
- Improve security, reliability, and product quality

4. Sharing
We may share information with service providers (hosting, email, SMS, payment gateways, analytics) under contracts, and with authorities when required by law. We do not sell personal information. Name any third parties you actually use: [PLACEHOLDER: THIRD_PARTY_PROCESSORS].

5. Retention
We retain information only as long as needed for the purposes above, or as required by law. Retention schedule: [PLACEHOLDER: RETENTION_SCHEDULE].

6. Your rights
Subject to applicable Pakistani law, you may request access, correction, or deletion of your personal information by contacting [PLACEHOLDER: CONTACT_EMAIL]. We may need to verify your identity before acting on a request.

7. Security
We use reasonable technical and organisational measures to protect information. No method of transmission or storage is completely secure.

8. Children
The platform is not directed at children under [PLACEHOLDER: MINIMUM_AGE]. Contact us if you believe we hold such data in error.

9. Changes
We may update this policy. Material changes will be posted on this page with a revised “Last updated” date.

10. Contact
Questions about this draft: [PLACEHOLDER: CONTACT_EMAIL] · [PLACEHOLDER: CONTACT_PHONE] · [PLACEHOLDER: BUSINESS_ADDRESS]
TXT,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'meta_title' => 'Terms of Service | [PLACEHOLDER: BUSINESS_NAME]',
                'meta_description' => 'Draft terms of service for [PLACEHOLDER: BUSINESS_NAME] — review with counsel before publishing.',
                'body' => <<<'TXT'
Terms of Service (DRAFT — not legal advice)

Last updated: [PLACEHOLDER: LAST_UPDATED_DATE]

These draft terms apply to use of the [PLACEHOLDER: BUSINESS_NAME] website and related services for browsing, listing, renting, selling, and booking property and hotel stays in Pakistan. Replace every [PLACEHOLDER] marker and obtain legal review before publishing. This text does not create rights or obligations until you publish an approved version and users accept it.

1. Operator
Operator: [PLACEHOLDER: BUSINESS_NAME]
Address: [PLACEHOLDER: BUSINESS_ADDRESS]
Email: [PLACEHOLDER: CONTACT_EMAIL]
Phone: [PLACEHOLDER: CONTACT_PHONE]
Governing law / disputes: [PLACEHOLDER: GOVERNING_LAW_AND_FORUM] (e.g. laws of Pakistan; courts of [PLACEHOLDER: CITY])

2. The platform
We provide an online marketplace and related tools. We are not a party to every property sale, tenancy, or hotel stay unless a written agreement says otherwise. Listing owners, agents, hosts, and guests remain responsible for the accuracy of their content and for performing their contracts.

3. Accounts
You must provide accurate information, keep credentials secure, and be legally able to enter contracts. We may suspend or terminate accounts that violate these terms, applicable law, or platform rules.

4. Listings and content
Users who create listings must have authority to offer the property, must not post unlawful or misleading content, and must comply with local housing, hotel, and advertising rules. We may remove or refuse content that appears to breach these standards.

5. Bookings and payments
Hotel and short-stay bookings, rental arrangements, and payment flows (including bank transfer proof) follow the product screens and any published policies. Prices, taxes, and fees shown at checkout apply unless corrected for obvious error. Payment gateways and banks have their own terms.

6. Prohibited use
You must not misuse the platform (fraud, scraping that harms the service, malware, unauthorised access, discrimination prohibited by law, or posting illegal content).

7. Limitation of liability
To the maximum extent permitted by applicable law, [PLACEHOLDER: BUSINESS_NAME] is not liable for indirect or consequential losses, or for disputes solely between users. Nothing in this draft excludes liability that cannot be excluded under Pakistani law. Cap (if any): [PLACEHOLDER: LIABILITY_CAP].

8. Indemnity
You agree to indemnify us against claims arising from your content, listings, or misuse of the platform, except to the extent caused by our proven negligence or wilful misconduct.

9. Changes
We may update these terms. Continued use after notice of material changes may constitute acceptance, subject to mandatory consumer protections.

10. Contact
[PLACEHOLDER: CONTACT_EMAIL] · [PLACEHOLDER: CONTACT_PHONE] · [PLACEHOLDER: BUSINESS_ADDRESS]
TXT,
            ],
            [
                'title' => 'Refund and Cancellation Policy',
                'slug' => 'refund-and-cancellation-policy',
                'meta_title' => 'Refund and Cancellation Policy | [PLACEHOLDER: BUSINESS_NAME]',
                'meta_description' => 'Draft refund and cancellation policy for [PLACEHOLDER: BUSINESS_NAME] — review with counsel before publishing.',
                'body' => <<<'TXT'
Refund and Cancellation Policy (DRAFT — not legal advice)

Last updated: [PLACEHOLDER: LAST_UPDATED_DATE]

This draft explains how cancellations and refunds may work for bookings and payments on [PLACEHOLDER: BUSINESS_NAME] for property, rental, and hotel short-stay products in Pakistan. Replace every [PLACEHOLDER] marker (including day-ranges) and obtain legal and finance review before publishing. Actual refund amounts also depend on payment method, gateway rules, and the cancellation policy attached to each booking.

1. Operator contact
Business: [PLACEHOLDER: BUSINESS_NAME]
Email: [PLACEHOLDER: CONTACT_EMAIL]
Phone: [PLACEHOLDER: CONTACT_PHONE]
Address: [PLACEHOLDER: BUSINESS_ADDRESS]

2. When you can cancel
Guests and hosts/owners should use the in-product booking tools where available. Cancellation windows may differ by listing and by the policy selected at booking (for example Flexible, Moderate, or Strict). Default day-ranges below are placeholders only — set the numbers your business actually uses:

- Flexible: full refund if cancelled at least [PLACEHOLDER: FLEXIBLE_FULL_REFUND_DAYS] days before check-in; partial refund of [PLACEHOLDER: FLEXIBLE_PARTIAL_PERCENT]% if cancelled within [PLACEHOLDER: FLEXIBLE_PARTIAL_WINDOW_DAYS] days of check-in.
- Moderate: full refund if cancelled at least [PLACEHOLDER: MODERATE_FULL_REFUND_DAYS] days before check-in; otherwise [PLACEHOLDER: MODERATE_PARTIAL_PERCENT]% refund (or none) as configured.
- Strict: full refund only if cancelled at least [PLACEHOLDER: STRICT_FULL_REFUND_DAYS] days before check-in; later cancellations may receive [PLACEHOLDER: STRICT_LATE_PERCENT]% or no refund.

No-shows and early check-out: [PLACEHOLDER: NOSHOW_AND_EARLY_CHECKOUT_RULES].

3. How refunds are calculated
Refund previews shown in the product (where available) are estimates based on the booking’s cancellation policy, paid amount, and timing. Final settlement may differ after admin review, gateway fees, and bank processing. Processing time after approval: [PLACEHOLDER: REFUND_PROCESSING_DAYS] business days to the original payment method where supported.

4. Bank transfer / manual payments
If you paid by bank transfer and uploaded proof, refunds may require additional verification and may take longer than card or wallet payments. Contact [PLACEHOLDER: CONTACT_EMAIL] with your booking reference.

5. Host / owner cancellations
If a host or listing owner cancels after confirmation, the guest may receive a full refund of amounts paid through the platform for that booking, subject to [PLACEHOLDER: HOST_CANCEL_GUEST_REMEDY]. Platform fees: [PLACEHOLDER: HOST_CANCEL_FEE_RULE].

6. Force majeure and exceptional cases
Events outside reasonable control (severe weather, government restrictions, etc.) may be handled case-by-case. Request review via [PLACEHOLDER: CONTACT_EMAIL] within [PLACEHOLDER: EXCEPTION_CLAIM_DAYS] days of the event.

7. Disputes
Contact support first. Unresolved disputes: [PLACEHOLDER: DISPUTE_PROCESS].

8. Changes
We may update this policy. Bookings already confirmed may remain subject to the policy shown at the time of booking unless law requires otherwise.

This page is a seeded draft for local/testing. Publish from Admin → CMS → Pages only after placeholders are completed and counsel has approved the text.
TXT,
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'meta_title' => $page['meta_title'],
                    'meta_description' => $page['meta_description'],
                    'is_published' => false,
                ]
            );
        }
    }
}
