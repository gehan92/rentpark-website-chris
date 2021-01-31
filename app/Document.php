<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{	
    /**
     * Get the Provider Document record associated with Document.
     */
    public function providerDocuments() {
        
        return $this->hasMany(ProviderDocument::class, 'document_id');
    }
}
