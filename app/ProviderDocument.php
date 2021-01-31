<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderDocument extends Model
{
    public function providerDetails() {

        return $this->belongsTo(Provider::class, 'provider_id');

    }    

    public function documentDetails() {

        return $this->belongsTo(Document::class, 'document_id');

    }
}
