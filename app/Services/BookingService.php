<?php

use App\Models\Service;
use App\Models\NurseService;
use App\Models\DriverService;
use App\Models\CompanionService;

class BookingService {

    // Service implementation
    public function createBooking($request) {
        $user = auth()->guard('api')->user();

        $Service = Service::create([
            'elder_id' => $user->id,
            'service_type' => $request['service_type'],
            'service_condition' => $request['service_condition'],
            'service_address' => $request['service_address'],
            'service_latitude' => $request['service_latitude'],
            'service_longitude' => $request['service_longitude'],
            'status' => 'pending',
        ]);
        if ($request['service_type'] === 'nurse') {
            $this->nurseService($Service->id ,$request);
        } elseif ($request['service_type'] === 'driver') {
            $this->driverService($Service->id, $request);
        } elseif ($request['service_type'] === 'companion') {
            $this->companionService($Service->id, $request);
        }
        return $Service;
    }

    public function nurseService($serviceId, $request)
    {
        $nurseService = NurseService::create([
            'service_id' => $serviceId,
            'nurse_major' => $request['nurse_major'],
        ]);
        return $nurseService;

    }

    public function driverService($serviceId, $request)
    {
       
            $driverService = DriverService::create([
                'service_id' => $serviceId,
                'pickup_address' => $request['pickup_address'],
                'pickup_latitude' => $request['pickup_latitude'],
                'pickup_longitude' => $request['pickup_longitude'],
                'dropoff_address' => $request['dropoff_address'],
                'dropoff_latitude' => $request['dropoff_latitude'],
                'dropoff_longitude' => $request['dropoff_longitude'],
            ]);
            return $driverService;

    }

    public function companionService($serviceId, $request)
    {
     
        $companionService = CompanionService::create([
            'service_id' => $serviceId,
            'start_time' => $request['start_time'],
            'end_time' => $request['end_time'],
            'period' => $request['period'],
        ]);
        return $companionService;
    }



}
?>