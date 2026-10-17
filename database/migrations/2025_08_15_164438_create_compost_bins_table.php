    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('compost_bins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to the user who owns the bin
                $table->string('bin_code')->unique(); // Unique code for the bin (e.g., CB-001)
                $table->string('bin_size')->nullable(); // Size of the bin (e.g., '100L', '200L')
                $table->date('registration_date'); // Date the bin was registered
                $table->string('status')->default('active'); // Status of the bin (active, inactive, maintenance)
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('compost_bins');
        }
    };
    