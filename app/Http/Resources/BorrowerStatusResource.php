<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:51
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BorrowerStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid'                          => $this->uuid,
            'borrower_status_name'          => $this->borrower_status_name,
            'borrower_status_description'   => $this->borrower_status_description,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}