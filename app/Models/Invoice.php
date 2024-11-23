<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Filters\InvoiceFilter;
use App\Http\Resources\V1\InvoiceResource;
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'value',
        'paid',
        'payment_date',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function filter(Request $request){
        $queryFilter =(new InvoiceFilter($request))->filter($request);
        
        if(empty($queryFilter)){
            return InvoiceResource::collection(Invoice::with('user')->get());
        }
        $data = Invoice::with('user');
        if(!empty($queryFilter['whereIn'])){
            foreach ($queryFilter['whereIn'] as $value){
                $data->whereIn($value[0],$value[1]);
            }
        }

        $resource = $data->where($queryFilter['where'])->get();

        return InvoiceResource::collection($resource);


    }
}
