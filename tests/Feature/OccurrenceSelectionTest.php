<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Collection;
use App\Models\Occurrence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class OccurrenceSelectionTest extends TestCase {
    use RefreshDatabase;

    /*
     * A basic test example.
     */
    public function test_selecting_on_collid(): void {
        $inst = Institution::factory()->create();
        $collection = Collection::factory()->create([
            'iid' => $inst->iid,
            'institutionCode' => $inst->InstitutionCode,
        ]);

        $params = [
            'collid' => $collection->collid,
            'locality' => fake()->country(),
        ];

        $inserted = DB::table('omoccurrences')->insert($params);
        assertTrue($inserted);

        $occurrence = Occurrence::buildSelectQuery([
            'collid' => $collection->collid
        ])
        ->select('*')
        ->first();

        assertEquals($occurrence->collid, $params['collid']);
        assertEquals($occurrence->locality, $params['locality']);
    }
}
