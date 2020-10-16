<?php

namespace App\Http\Controllers\Api\V1\Pwa;

use App\Data\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\Agent\AgentResource;
use App\Http\Resources\Pwa\customer\CustomerAutoResource;
use App\Http\Resources\Pwa\customer\CustomerSearchResource;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Trap;
use App\Rules\DeviceCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiStatusTrait;
use DB;  // laravel fasad 

class NewCustomerController extends Controller
{
    use ApiStatusTrait;

    public function index(Request $request)
    {
        //return  $request;
        try {
            $request->validate([
                'users_id' => ['required']
            ]);
            $users_id = $request->users_id;
            $device = CustomerAutoResource::collection( Device::with(['traps'=> function ($q){
                $q->with('customers');
            }])->whereusers_id($users_id)->get());
            $message = "Devices Data";
            return $this->successApiResponse($device, $message);
        } catch (\Exception $e) {
            return $this->failureApiResponse($e);

        }

    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'serial' => ['required','string','min:7','max:18'],
                'user_id' => ['required'],
            ]);
            $serial = $request->serial;
            $user_id = $request->user_id;
            $device = CustomerSearchResource::collection( Device::with(['traps'=> function ($q){
                $q->with('customers');
            }])->with('clients','users','storages')->whereSerial($serial)->whereusers_id($user_id)->get());
            $message = "Search info";
            return $this->successApiResponse($device, $message);


        }catch (\ Exception $e){
            return $this->failureApiResponse($e);
        }


    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|min:1|max:255',
                'customer_address' => 'required|string',
                'customer_lat' => 'nullable',
                'customer_lon' => 'nullable',
                'clients_id' => 'nullable',
                'users_id' => 'nullable',
                'traps_name' => 'required|string|min:1|max:255',
                'traps_address' => 'required|string',
                'traps_lat' => 'nullable',
                'traps_lon' => 'nullable',
                'devices_id' => 'required',
            ]);

          
            $customer = new Customer();
            $customer->name = $request->customer_name;
            $customer->address = $request->customer_address;
            $customer->address_latitude = $request->customer_lat;
            $customer->address_longitude = $request->customer_lon;
            $customer->clients_id = $request->clients_id;
            $customer->users_id = $request->users_id;

            if ($customer->save()) {

                  $device_id = ['devices_id' => $request->devices_id];
                  $device_result = Trap::where($device_id)->get();

          
           if(count($device_result) > 0){
            Trap::where('devices_id',$request->devices_id)->update([
                    'name'=>$request->traps_name,
                    'address'=>$request->traps_address,
                    'latitude'=>$request->traps_lat,
                    'longitude'=>$request->traps_lon,
                    'clients_id'=>$request->clients_id
             ]);
             $message = "Data Updated Successfully";
             return $this->successApiResponseSaveData($message);
           }else{
             $trap = new Trap();
                $trap->name = $request->traps_name;
                $trap->address = $request->traps_address;
                $trap->latitude = $request->traps_lat;
                $trap->longitude = $request->traps_lon;
                $trap->clients_id = $request->clients_id;
                $trap->customers_id = $customer->id;
                $trap->devices_id = $request->devices_id;
                $trap->save();
                $message = "Data Saved Successfully";
                return $this->successApiResponseSaveData($message);
           }
              
        }

        }catch (\Exception $e){

            return $this->failureApiResponse($e);
        }

    }

}
