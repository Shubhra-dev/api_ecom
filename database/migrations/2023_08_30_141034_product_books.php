<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW product_books AS

                SELECT p.id
                FROM products p JOIN versions v ON p.productable_id = v.id AND p.productable_type= 'App\\Models\\Version'
                WHERE productable_type = 'App\\Models\\Version' AND v.`type`= 1

                UNION

                SELECT p.id
                FROM products p JOIN packages pac ON p.productable_id = pac.id AND p.productable_type= 'App\\Models\\Package'
                WHERE productable_type = 'App\\Models\\Package' 
                AND 1 IN 
                (
                    SELECT DISTINCT v.`type`
                    FROM package_product pac_p JOIN products pr ON pac_p.product_id = pr.id
                        JOIN versions v ON pr.productable_id = v.id AND pr.productable_type = 'App\\Models\\Version'
                    WHERE pac_p.package_id = pac.id
                ) 

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS product_names');
    }
}
