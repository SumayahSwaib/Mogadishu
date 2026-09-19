<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Server-side data source for the admin's large select2 dropdowns.
 *
 * Previously these dropdowns were built by loading whole tables into memory and
 * touching relations per row (Renting::room()/tenant() each run an extra find()),
 * which made screens like tenant-payments/create issue thousands of queries.
 *
 * Every endpoint here returns Laravel's paginator JSON, which is exactly what
 * laravel-admin's Select::ajax() expects:
 *   { "data": [ {"id": .., "text": ".."} ], "next_page_url": ".." }
 *
 * The *Text() helpers are the single definition of each label, reused by both the
 * list endpoints and the *Option() lookups (used to render the already-selected
 * value on edit forms), so labels stay identical to the original dropdowns.
 */
class SelectOptionsController extends Controller
{
    /** Rows per select2 page (infinite scroll fetches the next page). */
    const PER_PAGE = 30;

    /* ===================== rentings (invoices) ===================== */

    protected static function rentingQuery()
    {
        return DB::table('rentings')
            ->leftJoin('rooms', 'rooms.id', '=', 'rentings.room_id')
            ->leftJoin('tenants', 'tenants.id', '=', 'rentings.tenant_id')
            ->select([
                'rentings.id as id',
                'rentings.balance as balance',
                'rooms.name as room_name',
                'tenants.name as tenant_name',
            ]);
    }

    public static function rentingText($r)
    {
        return '#' . $r->id . ' - ROOM: ' . $r->room_name
            . ', Tenant: ' . $r->tenant_name
            . ' , Balance: UGX ' . number_format((float) $r->balance);
    }

    public function rentings(Request $request)
    {
        $query = static::rentingQuery()->orderBy('rentings.id', 'desc');

        if ($q = static::term($request)) {
            $query->where(function ($w) use ($q) {
                $w->where('rentings.id', 'like', "%{$q}%")
                    ->orWhere('rooms.name', 'like', "%{$q}%")
                    ->orWhere('tenants.name', 'like', "%{$q}%");
            });
        }

        return static::respond($query, $request, [static::class, 'rentingText']);
    }

    public static function rentingOption($id)
    {
        return static::single(static::rentingQuery(), 'rentings.id', $id, [static::class, 'rentingText']);
    }

    /* ===================== tenants ===================== */

    protected static function tenantQuery()
    {
        return DB::table('tenants')->select(['tenants.id as id', 'tenants.name as name']);
    }

    public static function tenantText($t)
    {
        return '#' . $t->id . ' - ' . $t->name;
    }

    public function tenants(Request $request)
    {
        $query = static::tenantQuery()->orderBy('tenants.name', 'asc');

        if ($q = static::term($request)) {
            $query->where(function ($w) use ($q) {
                $w->where('tenants.name', 'like', "%{$q}%")
                    ->orWhere('tenants.id', 'like', "%{$q}%");
            });
        }

        return static::respond($query, $request, [static::class, 'tenantText']);
    }

    public static function tenantOption($id)
    {
        return static::single(static::tenantQuery(), 'tenants.id', $id, [static::class, 'tenantText']);
    }

    /* ===================== rooms (appartments) ===================== */

    protected static function roomQuery()
    {
        return DB::table('rooms')
            ->leftJoin('houses', 'houses.id', '=', 'rooms.house_id')
            ->select([
                'rooms.id as id',
                'rooms.name as name',
                'rooms.price as price',
                'houses.name as house_name',
            ]);
    }

    public static function roomText($r)
    {
        return '#' . $r->id . ' - ' . $r->name . ', ' . $r->house_name
            . ' - UGX ' . number_format((float) $r->price);
    }

    public function rooms(Request $request)
    {
        return $this->roomsResponse($request, null);
    }

    /** Only rooms marked Vacant - mirrors Room::get_vacant_rooms(). */
    public function vacantRooms(Request $request)
    {
        return $this->roomsResponse($request, 'Vacant');
    }

    protected function roomsResponse(Request $request, $status)
    {
        $query = static::roomQuery()->orderBy('rooms.name', 'asc');

        if ($status !== null) {
            $query->where('rooms.status', $status);
        }

        if ($q = static::term($request)) {
            $query->where(function ($w) use ($q) {
                $w->where('rooms.name', 'like', "%{$q}%")
                    ->orWhere('houses.name', 'like', "%{$q}%")
                    ->orWhere('rooms.id', 'like', "%{$q}%");
            });
        }

        return static::respond($query, $request, [static::class, 'roomText']);
    }

    public static function roomOption($id)
    {
        return static::single(static::roomQuery(), 'rooms.id', $id, [static::class, 'roomText']);
    }

    /* ===================== houses (estates) ===================== */

    protected static function houseQuery()
    {
        return DB::table('houses')->select(['houses.id as id', 'houses.name as name']);
    }

    public static function houseText($h)
    {
        return $h->name;
    }

    public function houses(Request $request)
    {
        $query = static::houseQuery()->orderBy('houses.name', 'asc');

        if ($q = static::term($request)) {
            $query->where('houses.name', 'like', "%{$q}%");
        }

        return static::respond($query, $request, [static::class, 'houseText']);
    }

    public static function houseOption($id)
    {
        return static::single(static::houseQuery(), 'houses.id', $id, [static::class, 'houseText']);
    }

    /* ===================== locations ===================== */

    /** Mirrors Location::get_sub_counties(): locations joined to the districts table. */
    protected static function subCountyQuery()
    {
        return DB::table('locations')
            ->join('districts', 'locations.parent', '=', 'districts.id')
            ->where('locations.parent', '>', 0)
            ->select([
                'locations.id as id',
                'locations.name as name',
                'districts.name as district_name',
            ]);
    }

    public static function subCountyText($s)
    {
        return ((string) $s->name) . ', ' . ((string) $s->district_name);
    }

    public function subCounties(Request $request)
    {
        $query = static::subCountyQuery();

        if ($q = static::term($request)) {
            $query->where(function ($w) use ($q) {
                $w->where('locations.name', 'like', "%{$q}%")
                    ->orWhere('districts.name', 'like', "%{$q}%");
            });
        }

        return static::respond($query, $request, [static::class, 'subCountyText']);
    }

    public static function subCountyOption($id)
    {
        return static::single(static::subCountyQuery(), 'locations.id', $id, [static::class, 'subCountyText']);
    }

    /** Mirrors Location::get_districts(): locations rows with parent < 1. */
    protected static function districtQuery()
    {
        return DB::table('locations')
            ->where('parent', '<', 1)
            ->select(['locations.id as id', 'locations.name as name']);
    }

    public static function districtText($d)
    {
        return (string) $d->name;
    }

    public function districts(Request $request)
    {
        $query = static::districtQuery();

        if ($q = static::term($request)) {
            $query->where('locations.name', 'like', "%{$q}%");
        }

        return static::respond($query, $request, [static::class, 'districtText']);
    }

    public static function districtOption($id)
    {
        return static::single(static::districtQuery(), 'locations.id', $id, [static::class, 'districtText']);
    }

    /* ===================== plumbing ===================== */

    /** Normalised search term, or '' when nothing was typed. */
    protected static function term(Request $request)
    {
        return trim((string) $request->get('q', ''));
    }

    /** Paginated {id,text} payload in the shape select2 expects. */
    protected static function respond($query, Request $request, callable $label)
    {
        $page = (int) $request->get('page', 1);
        $paginator = $query->paginate(static::PER_PAGE, ['*'], 'page', $page > 0 ? $page : 1);

        $paginator->getCollection()->transform(function ($row) use ($label) {
            return ['id' => $row->id, 'text' => $label($row)];
        });

        return $paginator;
    }

    /**
     * One [id => text] pair for a value already stored on a record, so edit forms
     * can render the current selection without loading the whole table.
     */
    protected static function single($query, $idColumn, $id, callable $label)
    {
        // Grid filters may hand us an array (or nothing at all).
        if (is_array($id)) {
            $id = reset($id);
        }

        if ($id === null || $id === '' || $id === false) {
            return [];
        }

        $row = $query->where($idColumn, $id)->first();

        return $row ? [$row->id => $label($row)] : [];
    }
}
