<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionStats extends Model {
	protected $table = 'omcollectionstats';
	protected $primaryKey = 'collid';
	public $timestamps = false;

	protected $fillable = [
		'collid', 'recordcnt', 'uploadedby'
	];

	protected $hidden = [];
	public static $snakeAttributes = false;

    protected $casts = [
        'dynamicProperties' => 'json',
    ];

    public function media() {
        $media_stats = [
            'media_count' => 0,
            'total_media_count' => 0
        ];

        if($str = $this->dynamicProperties['imgcnt']) {
            list($total_count, $count) = explode(':', $str);
            $media_stats['media_count'] = $count;
            $media_stats['total_media_count'] = $total_count;
        }

        return $media_stats;
    }

    private function extra(string $key): mixed {
        return $this->dynamicProperties[$key] ?? 0;
    }

    public function specimen() { return $this->extra('SpecimensCountID'); }
    public function genbank() { return $this->extra('gencnt'); }
    public function other_genetic() { return $this->extra('geneticcnt'); }
    public function bold() { return $this->extra('boldcnt'); }
    public function references() { return $this->extra('refcnt'); }
    public function total_taxa() { return $this->extra('TotalTaxaCount'); }

    public function recordcnt_percent(mixed $number): int {
        if($this->recordcnt === 0 || !is_numeric($number)) return 0;
        return $number? round(($number/ $this->recordcnt) * 100) : 0;
    }
}
