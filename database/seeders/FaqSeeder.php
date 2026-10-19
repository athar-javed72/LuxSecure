<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // TODO owner: confirm answers match real enquiry process and response times.
        $items = [
            [
                'question' => 'How do I enquire about a property?',
                'answer' => 'Open any listing and use the enquiry form, or send us a message from the Contact page. Our team will get back to you.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Can I enquire about a stay or a partnership?',
                'answer' => 'Yes. Tell us whether you are exploring a stay, a property, or a potential partnership when you contact us, and we will route your message appropriately.',
                'sort_order' => 2,
            ],
            [
                'question' => 'How are listings added?',
                'answer' => 'Listings are submitted by owners, agents, or our team and reviewed before they appear. Send us a message if you would like to list a property.',
                'sort_order' => 3,
            ],
            [
                'question' => 'How quickly will someone respond?',
                'answer' => 'Send us a message and our team will get back to you. Response times can vary by enquiry type and local hours.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Do you offer property management?',
                'answer' => 'We can discuss property management needs as part of a conversation with our team. Reach out via Contact and share what you are looking for.',
                'sort_order' => 5,
            ],
        ];

        foreach ($items as $item) {
            Faq::query()->updateOrCreate(
                ['question' => $item['question']],
                [
                    'answer' => $item['answer'],
                    'sort_order' => $item['sort_order'],
                    'is_published' => true,
                ]
            );
        }
    }
}
