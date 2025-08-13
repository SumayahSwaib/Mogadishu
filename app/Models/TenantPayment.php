<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenantPayment extends Model
{
    use HasFactory;
    public function process_balance($m)
    {
        $rent = Renting::find($m->renting_id);
        if ($rent == null) {
            throw new Exception("Invoice not found.", 1);
        }

        $rent->balance += $m->amount;
        if ($rent->balance > -1) {
            $rent->fully_paid = 'Yes';
        } else {
            $rent->fully_paid = 'No';
        }
        $rent->save();
        $m->tenant->update_balance();
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function renting()
    {
        return $this->belongsTo(Renting::class);
    }
    public static function boot()
    {
        parent::boot();
        self::created(function ($m) {
            $m->process_balance($m);
            $total_paid = $m->renting->payments->sum('amount');
            $balance =  $total_paid - $m->renting->payable_amount;
            $m->renting->balance = $balance;
            $m->renting->update_billing = 'Yes';
            $m->renting->save();
            DB::table('tenant_payments')->where('id', $m->id)->update(['balance' => $balance]);
            return $m;
        });
        self::updated(function ($m) {
            $m->process_balance($m);
            
            $total_paid = $m->renting->payments->sum('amount');
            $balance = $m->renting->payable_amount - $total_paid;
            $balance =  $total_paid - $m->renting->payable_amount;
            $m->renting->balance = $balance;
            $m->renting->update_billing = 'Yes';
            $m->renting->save();
            DB::table('tenant_payments')->where('id', $m->id)->update(['balance' => $balance]);

 
            return $m;
        });
        self::updating(function ($m) {
            $amount = ((int)($m->rent_amount))
                + ((int)($m->securty_deposit))
                + ((int)($m->days_before))
                + ((int)($m->garbage_amount));
            if ($amount <= 0) {
                throw new Exception("Amount must be greater than zero.", 1);
            }
            $m->amount = $amount;
            return $m;
        });

        self::creating(function ($m) {
            $rent = Renting::find($m->renting_id);
            if ($rent == null) {
                $m->delete();
                throw new Exception("Invoice not found.", 1);
            }

            $amount = ((int)($m->rent_amount))
                + ((int)($m->securty_deposit))
                + ((int)($m->days_before))
                + ((int)($m->garbage_amount));
            if ($amount <= 0) {
                throw new Exception("Amount must be greater than zero.", 1);
            }
            $m->amount = $amount;
            $rent->balance += $m->amount;
            $m->tenant_id = $rent->tenant_id;
            $m->balance = $rent->balance;
            $stat_rent = Utils::my_date($rent->start_date);
            $end_rent = Utils::my_date($rent->end_date);
            $m->details = "Being payment  of $rent->number_of_months months from {$stat_rent} to {$end_rent}
             Invoice 0{$rent->id}{$rent->room_number}";
            return $m;
        });
    }

  
    public function house()
    {
        return $this->belongsTo(House::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function get_grande_total()
    {
        return $this->amount + $this->securty_deposit + $this->days_before;
    }
}
