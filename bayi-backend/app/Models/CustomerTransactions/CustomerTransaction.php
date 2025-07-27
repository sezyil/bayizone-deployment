<?php

namespace App\Models\CustomerTransactions;

use App\Enums\CustomerTransactionsFicheTypeEnum;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerBankAccount;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTransaction extends Model
{
    use HasFactory, HasUuids;
    /**
     * @var int Borçlandır (-)
     */
    const DEBT = 0;
    /**
     * @var int Alacaklandır (+)
     */
    const CREDIT = 1;

    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'fiche_no',
        'fiche_type',
        'date',
        'description',
        'is_paid',
        'io_type',
        'due_date',
        'payment_closed_date',
        'customer_order_id',
        'currency',
        'amount',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'io_type' => 'boolean',
        'amount' => 'float',
    ];

    //on create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->fiche_no = $model->generateFicheNo();
        });
    }

    public function generateFicheNo()
    {
        $fiche_no = 'FS-' . now()->format('YmdHis') . rand(1000, 9999);
        //check if fiche_no exists
        $fiche_no_exists = CustomerTransaction::where('fiche_no', $fiche_no)->exists();
        if ($fiche_no_exists) {
            return $this->generateFicheNo();
        }
        return $fiche_no;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class, 'company_customer_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id', 'id');
    }

    public function getCurrency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    #region Scopes
    public function scopeDebt($query)
    {
        return $query->where('io_type', self::DEBT);
    }

    public function scopeCredit($query)
    {
        return $query->where('io_type', self::CREDIT);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeNotOverdue($query)
    {
        return $query->where('due_date', '>=', now());
    }

    //currency
    public function scopeIsCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    #endregion

    #region Accessors
    //get fiche type description
    public function getFicheTypeDescription()
    {
        return CustomerTransactionsFicheTypeEnum::description($this->fiche_type);
    }

    //get io type description
    public function getIoTypeDescription()
    {
        return $this->io_type == self::DEBT ? 'Borç' : 'Alacak';
    }


    //isDue
    public function isOverdue()
    {
        return $this->due_date ? $this->due_date < now() && !$this->is_paid : false;
    }

    #endregion

    #region Methods
    /**
     * Assign order totals to transaction.
     * @return void
     */
    public function assignOrderTotals(): void
    {
        /** @var CustomerOrder $order */
        $order = $this->order;
        $this->amount = $order->grand_total;
        $this->currency = $order->currency;
        $this->save();
    }
    #endregion
}
