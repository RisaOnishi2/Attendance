<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date'); // 勤務日
            $table->enum('work_status', ['off', 'working', 'break', 'finished'])->default('off');
            $table->timestamp('clock_in_time')->nullable();  // 出勤時刻
            $table->timestamp('clock_out_time')->nullable(); // 退勤時刻
            $table->text('note')->nullable(); // 備考

            // 承認判定機能
            $table->json('pending_data')->nullable(); 
            $table->enum('approval_status', ['approved', 'pending'])
                  ->default('approved');
            
            // 修正申請が出された日時
            $table->timestamp('requested_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'date']); // 1ユーザー1日1レコード
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');

    }
}
