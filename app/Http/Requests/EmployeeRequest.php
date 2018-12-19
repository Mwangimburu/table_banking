<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:11
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class EmployeeRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'first_name'            => 'required|',
                        'email'                 => 'required|unique:employees,email,NULL,uuid,deleted_at,NULL',
                        'last_name'             => 'required',
                        'salutation'            => '',
                        'country'               => '',
                        'national_id_number'    => 'required|unique:employees,national_id_number,NULL,uuid,deleted_at,NULL',
                        'passport_number'       => 'required|unique:employees,passport_number,NULL,uuid,deleted_at,NULL',

                        'telephone_number'      => '',
                        'address'               => '',
                        'postal_code'           => '',
                        'county'                => '',
                        'city'                  => '',
                        'nhif_number'           => '',
                        'nssf_number'           => '',
                        'kra_pin'               => '',
                        'gender'                => '',
                        'job_group'             => '',
                        'designation_id'        => '',
                        'department_id'         => '',
                        'staff_no'              => 'required|unique:employees,staff_no,NULL,uuid,deleted_at,NULL',
                        'profile_picture'       => '',
                        'national_id_image'     => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name'              => '',
                        'email'                 => ['email', Rule::unique('employees')->ignore($this->user, 'uuid')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'national_id_number'                 => ['national_id_number', Rule::unique('employees')->ignore($this->user, 'uuid')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'passport_number'                 => ['passport_number', Rule::unique('employees')->ignore($this->user, 'uuid')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'staff_no'                 => ['staff_no', Rule::unique('employees')->ignore($this->user, 'uuid')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }
}