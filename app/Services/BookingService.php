<?php

class BookingService {
    // Service implementation
    public function createBooking($data) {
        // Logic to create a booking
        $Service = ServiceRequest::create($data);
        return $Service;
    }
    


}
?>