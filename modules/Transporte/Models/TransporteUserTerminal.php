<?php

namespace Modules\Transporte\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransporteUserTerminal extends ModelTenant
{
    //

    protected $table = 'transporte_user_terminal';
    protected $fillable = ['terminal_id','user_id'];

    protected $with=['terminal'];

    public function terminal() : BelongsTo{
        return $this->belongsTo(TransporteTerminales::class,'terminal_id','id');
    }

    public function user() : BelongsTo{
        return $this->belongsTo(User::class,'user_id','id');
    }

}
