<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 3)->unique();
                $table->string('symbol', 8);
                $table->unsignedTinyInteger('decimals')->default(2);
                $table->decimal('rate_to_base', 16, 8)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('code', 12)->unique();
                $table->string('name');
                $table->string('native_name')->nullable();
                $table->boolean('is_rtl')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->char('iso2', 2)->unique();
                $table->string('name');
                $table->string('currency_code', 3);
                $table->string('default_locale', 12)->default('en');
                $table->string('timezone', 64)->default('UTC');
                $table->string('phone_code', 8)->nullable();
                $table->string('phone_regex')->nullable();
                $table->json('area_units')->nullable();
                $table->string('tax_name')->nullable();
                $table->decimal('tax_percent', 8, 4)->default(0);
                $table->string('invoice_prefix', 20)->nullable();
                $table->unsignedBigInteger('invoice_sequence')->default(0);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('country_settings')) {
            Schema::create('country_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained()->cascadeOnDelete();
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->string('label')->nullable();
                $table->timestamps();
                $table->unique(['country_id', 'key']);
            });
        }

        if (! Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('logo')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cancellation_policies')) {
            Schema::create('cancellation_policies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
                $table->unique(['slug', 'country_id', 'organization_id'], 'cancel_policy_scope_unique');
            });
        }

        if (! Schema::hasTable('cancellation_policy_rules')) {
            Schema::create('cancellation_policy_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cancellation_policy_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('days_before_min')->default(0);
                $table->decimal('refund_percent', 5, 2)->default(0);
                $table->decimal('fee_fixed', 12, 2)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('kyc_document_types')) {
            Schema::create('kyc_document_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->boolean('is_required')->default(false);
                $table->json('for_roles')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('country_payment_methods')) {
            Schema::create('country_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained()->cascadeOnDelete();
                $table->string('gateway');
                $table->boolean('is_enabled')->default(false);
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->unique(['country_id', 'gateway']);
            });
        }

        if (! Schema::hasTable('legal_acceptances')) {
            Schema::create('legal_acceptances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('page_id')->constrained()->cascadeOnDelete();
                $table->string('version');
                $table->string('ip', 45)->nullable();
                $table->timestamp('accepted_at');
                $table->timestamps();
                $table->unique(['user_id', 'page_id', 'version']);
            });
        }

        $this->addColumnIfMissing('cities', 'country_id', fn (Blueprint $t) => $t->foreignId('country_id')->nullable()->after('id')->constrained()->nullOnDelete());
        $this->addColumnIfMissing('areas', 'country_id', fn (Blueprint $t) => $t->foreignId('country_id')->nullable()->after('id')->constrained()->nullOnDelete());

        Schema::table('listings', function (Blueprint $table) {
            if (! Schema::hasColumn('listings', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('listings', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('country_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('listings', 'branch_name')) {
                $table->string('branch_name')->nullable()->after('title');
            }
            if (! Schema::hasColumn('listings', 'address')) {
                $table->string('address')->nullable();
            }
            if (! Schema::hasColumn('listings', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable();
            }
            if (! Schema::hasColumn('listings', 'lng')) {
                $table->decimal('lng', 10, 7)->nullable();
            }
            if (! Schema::hasColumn('listings', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (! Schema::hasColumn('listings', 'email')) {
                $table->string('email')->nullable();
            }
            if (! Schema::hasColumn('listings', 'timezone')) {
                $table->string('timezone', 64)->nullable();
            }
            if (! Schema::hasColumn('listings', 'check_in_time')) {
                $table->time('check_in_time')->nullable();
            }
            if (! Schema::hasColumn('listings', 'check_out_time')) {
                $table->time('check_out_time')->nullable();
            }
            if (! Schema::hasColumn('listings', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('listings', 'cancellation_policy_id')) {
                $table->foreignId('cancellation_policy_id')->nullable()->constrained()->nullOnDelete();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('country_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'preferred_locale')) {
                $table->string('preferred_locale', 12)->nullable();
            }
            if (! Schema::hasColumn('users', 'preferred_currency')) {
                $table->string('preferred_currency', 3)->nullable();
            }
        });

        foreach (['bookings', 'tenancies', 'payments', 'agent_profiles'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'country_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
                });
            }
        }

        if (Schema::hasColumn('bookings', 'cancellation_policy') && ! Schema::hasColumn('bookings', 'cancellation_policy_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('cancellation_policy_id')->nullable()->after('cancellation_policy')->constrained()->nullOnDelete();
                $table->string('currency', 3)->nullable()->after('total');
            });
        } elseif (! Schema::hasColumn('bookings', 'currency')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('currency', 3)->nullable()->after('total');
            });
        }

        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                if (! Schema::hasColumn('pages', 'type')) {
                    $table->string('type', 32)->nullable()->after('slug');
                }
                if (! Schema::hasColumn('pages', 'country_id')) {
                    $table->foreignId('country_id')->nullable()->after('type')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('pages', 'locale')) {
                    $table->string('locale', 12)->default('en')->after('country_id');
                }
                if (! Schema::hasColumn('pages', 'version')) {
                    $table->string('version', 32)->default('1.0')->after('locale');
                }
                if (! Schema::hasColumn('pages', 'effective_from')) {
                    $table->date('effective_from')->nullable()->after('version');
                }
                if (! Schema::hasColumn('pages', 'status')) {
                    $table->string('status', 20)->default('draft')->after('effective_from');
                }
            });
        }

        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'currency')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('currency', 3)->nullable()->after('total');
                $table->string('tax_name')->nullable()->after('tax');
                $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            });
        }

        $this->backfill();
    }

    public function down(): void
    {
        // Non-destructive down omitted for production safety; use migrate:fresh in local.
    }

    private function addColumnIfMissing(string $table, string $column, callable $callback): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }
        Schema::table($table, $callback);
    }

    private function backfill(): void
    {
        $pkrId = DB::table('currencies')->where('code', 'PKR')->value('id');
        if (! $pkrId) {
            $pkrId = DB::table('currencies')->insertGetId([
                'code' => 'PKR',
                'symbol' => 'Rs',
                'decimals' => 0,
                'rate_to_base' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ([
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_rtl' => false, 'sort_order' => 1],
            ['code' => 'ur', 'name' => 'Urdu', 'native_name' => 'اردو', 'is_rtl' => true, 'sort_order' => 2],
        ] as $lang) {
            $exists = DB::table('languages')->where('code', $lang['code'])->exists();
            if (! $exists) {
                DB::table('languages')->insert(array_merge($lang, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        $pkId = DB::table('countries')->where('iso2', 'PK')->value('id');
        if (! $pkId) {
            $pkId = DB::table('countries')->insertGetId([
                'iso2' => 'PK',
                'name' => 'Pakistan',
                'currency_code' => 'PKR',
                'default_locale' => 'en',
                'timezone' => 'Asia/Karachi',
                'phone_code' => '+92',
                'phone_regex' => '^\+?92[0-9]{10}$|^0?3[0-9]{9}$',
                'area_units' => json_encode(['marla', 'kanal', 'sq_ft']),
                'tax_name' => 'Sales Tax',
                'tax_percent' => 0,
                'invoice_prefix' => 'PK',
                'invoice_sequence' => 0,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ([
            ['gateway' => 'bank_transfer', 'is_enabled' => true],
            ['gateway' => 'jazzcash', 'is_enabled' => false],
            ['gateway' => 'easypaisa', 'is_enabled' => false],
        ] as $method) {
            $exists = DB::table('country_payment_methods')
                ->where('country_id', $pkId)
                ->where('gateway', $method['gateway'])
                ->exists();
            if (! $exists) {
                DB::table('country_payment_methods')->insert([
                    'country_id' => $pkId,
                    'gateway' => $method['gateway'],
                    'is_enabled' => $method['is_enabled'],
                    'meta' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $kycTypes = [
            ['name' => 'CNIC', 'code' => 'cnic', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager']],
            ['name' => 'Driving License', 'code' => 'license', 'is_required' => false, 'for_roles' => ['agent', 'landlord', 'hotel_manager']],
            ['name' => 'Property Registry', 'code' => 'registry', 'is_required' => false, 'for_roles' => ['landlord', 'hotel_manager']],
        ];
        foreach ($kycTypes as $i => $type) {
            $exists = DB::table('kyc_document_types')
                ->where('country_id', $pkId)
                ->where('code', $type['code'])
                ->exists();
            if (! $exists) {
                DB::table('kyc_document_types')->insert([
                    'country_id' => $pkId,
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'is_required' => $type['is_required'],
                    'for_roles' => json_encode($type['for_roles']),
                    'sort_order' => $i + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $policies = [
            'flexible' => [
                'name' => 'Flexible',
                'rules' => [
                    ['days_before_min' => 1, 'refund_percent' => 100],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            'moderate' => [
                'name' => 'Moderate',
                'rules' => [
                    ['days_before_min' => 5, 'refund_percent' => 100],
                    ['days_before_min' => 1, 'refund_percent' => 50],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            'strict' => [
                'name' => 'Strict',
                'rules' => [
                    ['days_before_min' => 14, 'refund_percent' => 100],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
        ];

        $policyIds = [];
        foreach ($policies as $slug => $def) {
            $id = DB::table('cancellation_policies')
                ->where('slug', $slug)
                ->whereNull('country_id')
                ->whereNull('organization_id')
                ->value('id');
            if (! $id) {
                $id = DB::table('cancellation_policies')->insertGetId([
                    'name' => $def['name'],
                    'slug' => $slug,
                    'country_id' => null,
                    'organization_id' => null,
                    'is_default' => $slug === 'flexible',
                    'is_active' => true,
                    'description' => 'Global default '.$def['name'].' policy (backfilled).',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                foreach ($def['rules'] as $order => $rule) {
                    DB::table('cancellation_policy_rules')->insert([
                        'cancellation_policy_id' => $id,
                        'days_before_min' => $rule['days_before_min'],
                        'refund_percent' => $rule['refund_percent'],
                        'fee_fixed' => null,
                        'sort_order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $policyIds[$slug] = $id;
        }

        if (Schema::hasTable('cities')) {
            DB::table('cities')->whereNull('country_id')->update(['country_id' => $pkId]);
        }
        if (Schema::hasTable('areas')) {
            DB::table('areas')->whereNull('country_id')->update(['country_id' => $pkId]);
        }
        if (Schema::hasTable('users')) {
            DB::table('users')->whereNull('country_id')->update(['country_id' => $pkId]);
        }
        if (Schema::hasTable('agent_profiles') && Schema::hasColumn('agent_profiles', 'country_id')) {
            DB::table('agent_profiles')->whereNull('country_id')->update(['country_id' => $pkId]);
        }

        if (Schema::hasTable('listings')) {
            DB::table('listings')->whereNull('country_id')->update(['country_id' => $pkId]);
            DB::table('listings')->where(function ($q) {
                $q->whereNull('currency')->orWhere('currency', '');
            })->update(['currency' => 'PKR']);

            $hotelTypeId = DB::table('listing_types')->where('slug', 'hotel')->value('id');
            if ($hotelTypeId) {
                $hotels = DB::table('listings')->where('listing_type_id', $hotelTypeId)->get();
                foreach ($hotels as $hotel) {
                    if ($hotel->organization_id) {
                        continue;
                    }
                    $slug = Str::slug($hotel->title).'-org';
                    $base = $slug;
                    $i = 1;
                    while (DB::table('organizations')->where('slug', $slug)->exists()) {
                        $slug = $base.'-'.$i++;
                    }
                    $orgId = DB::table('organizations')->insertGetId([
                        'name' => $hotel->title,
                        'slug' => $slug,
                        'logo' => null,
                        'description' => 'Backfilled organization for '.$hotel->title,
                        'owner_id' => $hotel->owner_id,
                        'country_id' => $pkId,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('listings')->where('id', $hotel->id)->update([
                        'organization_id' => $orgId,
                        'branch_name' => $hotel->branch_name ?: $hotel->title,
                        'timezone' => 'Asia/Karachi',
                        'check_in_time' => '14:00:00',
                        'check_out_time' => '11:00:00',
                        'cancellation_policy_id' => $policyIds['flexible'] ?? null,
                        'manager_id' => $hotel->owner_id,
                    ]);
                }
            }
        }

        if (Schema::hasTable('bookings')) {
            DB::table('bookings')->whereNull('country_id')->update(['country_id' => $pkId]);
            DB::table('bookings')->whereNull('currency')->update(['currency' => 'PKR']);
            foreach ($policyIds as $slug => $id) {
                DB::table('bookings')
                    ->whereNull('cancellation_policy_id')
                    ->where('cancellation_policy', $slug)
                    ->update(['cancellation_policy_id' => $id]);
            }
            DB::table('bookings')
                ->whereNull('cancellation_policy_id')
                ->update(['cancellation_policy_id' => $policyIds['flexible'] ?? null]);
        }

        if (Schema::hasTable('tenancies') && Schema::hasColumn('tenancies', 'country_id')) {
            DB::table('tenancies')->whereNull('country_id')->update(['country_id' => $pkId]);
        }
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'country_id')) {
            DB::table('payments')->whereNull('country_id')->update(['country_id' => $pkId]);
            DB::table('payments')->where(function ($q) {
                $q->whereNull('currency')->orWhere('currency', '');
            })->update(['currency' => 'PKR']);
        }

        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'status')) {
            DB::table('pages')->where('is_published', true)->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '');
            })->update(['status' => 'published']);
            DB::table('pages')->where('is_published', false)->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '');
            })->update(['status' => 'draft']);
            if (Schema::hasColumn('pages', 'locale')) {
                DB::table('pages')->whereNull('locale')->update(['locale' => 'en']);
            }
            if (Schema::hasColumn('pages', 'version')) {
                DB::table('pages')->whereNull('version')->update(['version' => '1.0']);
            }
        }
    }
};
