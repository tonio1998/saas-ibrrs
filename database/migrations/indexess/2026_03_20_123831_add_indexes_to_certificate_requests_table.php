<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function hasIndex($table, $index)
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return count($result) > 0;
    }

    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (!$this->hasIndex('residents', 'residents_household_id_index')) {
                $table->index('household_id');
            }
            if (!$this->hasIndex('residents', 'residents_birthdate_index')) {
                $table->index('BirthDate');
            }
            if (!$this->hasIndex('residents', 'residents_gender_index')) {
                $table->index('gender');
            }
            if (!$this->hasIndex('residents', 'residents_is_voter_index')) {
                $table->index('is_voter');
            }
            if (!$this->hasIndex('residents', 'residents_created_by_index')) {
                $table->index('created_by');
            }
            if (!$this->hasIndex('residents', 'residents_updated_by_index')) {
                $table->index('updated_by');
            }
            if (!$this->hasIndex('residents', 'residents_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('residents', 'residents_archived_index')) {
                $table->index('archived');
            }
            if (!$this->hasIndex('residents', 'residents_deleted_at_index')) {
                $table->index('deleted_at');
            }
            if (!$this->hasIndex('residents', 'residents_lastname_firstname_index')) {
                $table->index(['LastName', 'FirstName']);
            }
        });

        Schema::table('households', function (Blueprint $table) {
            if (!$this->hasIndex('households', 'households_household_code_unique')) {
                $table->unique('household_code');
            }
            if (!$this->hasIndex('households', 'households_purok_id_index')) {
                $table->index('purok_id');
            }
            if (!$this->hasIndex('households', 'households_purok_head_index')) {
                $table->index(['purok_id', 'head_id']);
            }
            if (!$this->hasIndex('households', 'households_head_id_index')) {
                $table->index('head_id');
            }
            if (!$this->hasIndex('households', 'households_created_by_index')) {
                $table->index('created_by');
            }
            if (!$this->hasIndex('households', 'households_updated_by_index')) {
                $table->index('updated_by');
            }
            if (!$this->hasIndex('households', 'households_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('households', 'households_archived_index')) {
                $table->index('archived');
            }
            if (!$this->hasIndex('households', 'households_deleted_at_index')) {
                $table->index('deleted_at');
            }
        });

        Schema::table('certificate_requests', function (Blueprint $table) {
            if (!$this->hasIndex('certificate_requests', 'certificate_requests_resident_id_index')) {
                $table->index('resident_id');
            }
            if (!$this->hasIndex('certificate_requests', 'certificate_requests_certificate_type_id_index')) {
                $table->index('certificate_type_id');
            }
            if (!$this->hasIndex('certificate_requests', 'certificate_requests_remark_index')) {
                $table->index('remark');
            }
            if (!$this->hasIndex('certificate_requests', 'certificate_requests_requested_at_index')) {
                $table->index('requested_at');
            }
            if (!$this->hasIndex('certificate_requests', 'certificate_requests_year_month_index')) {
                $table->index([DB::raw('YEAR(requested_at)'), DB::raw('MONTH(requested_at)')]);
            }
            if (!$this->hasIndex('certificate_requests', 'certificate_requests_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('puroks', function (Blueprint $table) {
            if (!$this->hasIndex('puroks', 'puroks_name_index')) {
                $table->index('PurokName');
            }
        });

        Schema::table('resident_info', function (Blueprint $table) {
            if (!$this->hasIndex('resident_info', 'resident_info_resident_id_index')) {
                $table->index('resident_id');
            }
            if (!$this->hasIndex('resident_info', 'resident_info_barangay_index')) {
                $table->index('barangay');
            }
            if (!$this->hasIndex('resident_info', 'resident_info_city_index')) {
                $table->index('city');
            }
            if (!$this->hasIndex('resident_info', 'resident_info_province_index')) {
                $table->index('province');
            }
            if (!$this->hasIndex('resident_info', 'resident_info_region_index')) {
                $table->index('region');
            }
            if (!$this->hasIndex('resident_info', 'resident_info_archived_index')) {
                $table->index('archived');
            }
            if (!$this->hasIndex('resident_info', 'resident_info_status_index')) {
                $table->index('status');
            }
        });

        Schema::table('certificates', function (Blueprint $table) {
            if (!$this->hasIndex('certificates', 'certificates_request_id_index')) {
                $table->index('request_id');
            }
            if (!$this->hasIndex('certificates', 'certificates_issued_by_index')) {
                $table->index('issued_by');
            }
            if (!$this->hasIndex('certificates', 'certificates_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('certificates', 'certificates_archived_index')) {
                $table->index('archived');
            }
            if (!$this->hasIndex('certificates', 'certificates_issued_at_index')) {
                $table->index('issued_at');
            }
            if (!$this->hasIndex('certificates', 'certificates_deleted_at_index')) {
                $table->index('deleted_at');
            }
        });
    }

    public function down(): void {}
};
