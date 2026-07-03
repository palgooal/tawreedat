<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "سجّل شركتك" replacement flow: a company submits its details as a
     * request, an admin reviews it, and payment/collection for actually
     * appearing in the directory happens manually (WhatsApp/phone/email) -
     * there is no online payment or subscription-plan billing in this
     * table or anywhere in v1. See docs/DECISIONS.md.
     */
    public function up(): void
    {
        Schema::create('company_registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            // pending | approved | rejected | contacted - see
            // CompanyRegistrationRequest::STATUSES for the Arabic labels.
            $table->string('status')->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_registration_requests');
    }
};
