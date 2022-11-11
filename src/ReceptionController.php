<?php

class ReceptionController{

    private ReceptionGateway $gateway;
    
    public function __construct(ReceptionGateway $gateway){
        $this->gateway = $gateway;
    }


    public function processRequest(string $method, string $service, ?string $id): void{
        $data = $_POST;
        
        $errors = $this->getValidationErrors($data, $service, $method);
        if(!empty($errors)){
            $this->respondUnprocessableEntity($errors);
            return;
        }

        if($id === null){
            if($method === "POST"){                
                if($service === "reservation"){
                    $output = $this->gateway->addReservation($data);
                    $this->respondReservationCreated($id);
                }
                else if($service === "room"){
                    $output = $this->gateway->createNewRoom($data);
                    $this->respondRoomCreated($id);
                }
                else if($service === "customer"){
                    $output = $this->gateway->newCustomer($data);
                    $this->respondCustomerCreated($id);
                }
                else{
                    $this->respondServiceNotFound();
                }

            }
            else if($method === "GET"){
                if($service === "reservation"){
                    $reservations = $this->gateway->getAllReservations();
                    if($reservations === false){
                        $this->respondEmptyData();
                    }
                    else{
                        echo json_encode($reservations);
                    }
                }
                else if($service === "room"){
                    $rooms = $this->gateway->getAllRooms();
                    if($rooms === false){
                        $this->respondEmptyData();
                    }
                    else{
                        echo json_encode($rooms);
                    }            
                }
                else if($service === "customer"){
                    $customers = $this->gateway->getAllCustomers();
                    if($customers === false){
                        $this->respondEmptyData();
                    }
                    else{
                        echo json_encode($customers);
                    }
                }
                else{
                    $this->respondServiceNotFound();
                }
            }
            else{
                $this->respondMethodNotAllowed("POST");
            }
        }
        else{
            switch($method){
                case "GET":
                    if($service === "reservation"){
                        if(!filter_var($id, FILTER_VALIDATE_INT) == false){
                            $reservation = $this->gateway->getReservasionInfo((int)$id);
                            if($reservation === false){
                                $this->respondReservationNotFound($id);
                            }
                            else{
                                echo json_encode($reservation);
                            }
                        }
                        else{
                            $this->respondUnprocessableEntity(["wrong id format"]);
                        }
                    }
                    else if($service === "room"){
                        if($id === "available"){
                            //TODO
                        }
                        else if(!filter_var($id, FILTER_VALIDATE_INT) === false){
                            $room = $this->gateway->getRoomByID((int)$id);
                            if($room === false){
                                $this->respondRoomNotFound($id);
                            }
                            else{
                                echo json_encode($room);
                            }
                        }
                        else{
                            $this->respondUnprocessableEntity(["wrong id format"]);
                        }
                    }
                    else if($service === "customer"){
                        if(!filter_var($id, FILTER_VALIDATE_INT) === false){
                            $customer = $this->gateway->getCustomerByID((int)$id);
                            if($customer === false){
                                $this->respondCustomerNotFound($id);
                            }
                            else{
                                echo json_encode($customer);
                            }
                        }
                        else{
                            $this->respondUnprocessableEntity("wrong id format");
                        }
                    }
                    break;
                case "PATCH":
                    if($service === "reservation"){
                        $output = $this->gateway->updateReservationInfo((int)$id, $data);
                        if($output > 0){
                            $this->respondReservationUpdated($id);
                        }
                        else{
                            $this->respondReservationNotFound($id);
                        }
                    }
                    else if($service === "room"){
                        $output = $this->gateway->updateRoonInfo((int)$id, $data);
                        if($output > 0){
                            $this->respondRoomUpdated($id);
                        }
                        else{
                            $this->respondRoomNotFound($id);
                        }
                    }
                    else if($service === "customer"){
                        $output = $this->gateway->updateCustomerInfo((int)$id, $data);
                        if($output > 0){
                            $this->respondCustomerUpdated($id);
                        }
                        else{
                            $this->respondCustomerNotFound((int)$id, $data);
                        }
                    }
                    break;
                case "DELETE":
                    if($service === "reservation"){
                        $output = $this->gateway->deleteReservation((int)$id);
                        if($output > 0){
                            $this->respondReservationDeleted($id);
                        }
                        else{
                            echo json_encode(["message" => "Problem on reservation delete"]);
                        }
                    }
                    else if($service === "room"){
                        $output = $this->gateway->deleteRoom((int)$id);
                        if($output > 0){
                            $this->respondRoomDeleted($id);
                        }
                        else{
                            echo json_encode(["message" => "Problem on room delete"]);
                        }
                    }
                    else if($service === "customer"){
                        $output = $this->gateway->deleteCustomer((int)$id);
                        if($output > 0){
                            $this->respondCustomerDeleted($id);
                        }
                        else{
                            echo json_encode(["message" => "Problem on customer delete"]);
                        }
                    }
                    break;
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
                    break;
            }
        }

        return;
    }


    private function respondUnprocessableEntity(array $errors): void{
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }


    private function respondMethodNotAllowed(string $allowed_methods): void{
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondServiceNotFound(){
        http_response_code(404);
        echo json_encode(["message" => "Service not found"]);
    }


    private function respondRoomNotFound(string $id): void{
        http_response_code(404);
        echo json_encode(["message" => "Room with id $id not found"]);
    }

    private function respondReservationNotFound(string $id): void{
        http_response_code(404);
        echo json_encode(["message" => "Reservation with id $id not found"]);
    }


    private function respondCustomerNotFound(string $id): void{
        http_response_code(404);
        echo json_encode(["message" => "Customer with id $id not found"]);
    }


    private function respondRoomCreated(string $id): void{
        http_response_code(201);
        echo json_encode(["message" => "Room created", "id" => $id]);
    }


    private function respondReservationCreated(string $id): void{
        http_response_code(201);
        echo json_encode(["message" => "Reservation created", "id" => $id]);
    }


    private function respondCustomerCreated(string $id): void{
        http_response_code(201);
        echo json_encode(["message" => "Customer created", "id" => $id]);
    }

    private function respondReservationDeleted(string $id): void{
        http_response_code(204);
        echo json_encode(["message" => "Reservation deleted", "id" => $id]);
    }

    private function respondRoomDeleted(string $id): void{
        http_response_code(204);
        echo json_encode(["message" => "Room deleted", "id" => $id]);
    }

    private function respondCustomerDeleted(string $id): void{
        http_response_code(204);
        echo json_encode(["message" => "Customer deleted", "id" => $id]);
    }

    private function respondReservationUpdated(string $id): void{
        http_response_code(204);
        echo json_encode(["message" => "Reservation updated", "id" => $id]);
    }

    private function respondRoomUpdated(string $id): void{
        http_response_code(204);
        echo json_encode(["message" => "Room updated", "id" => $id]);
    }

    private function respondCustomerUpdated(string $id): void{
        http_response_code(204);
        echo json_encode(["message" => "Customer updated", "id" => $id]);
    }

    private function respondEmptyData(){
        http_response_code(404);
        echo json_encode(["message" => "Empty data"]);
    }


    private function getValidationErrors(array $data, string $service, string $method){
        $errors = [];

        if($method === "POST"){
            if($service === "reservation"){
                if(empty($data["customer_id"])){
                    $errors[] = "customer_id is required";
                }
                else if(filter_var($data["customer_id"], FILTER_VALIDATE_INT) == false){
                    $errors[] = "customer_id invalid format";
                }

                if(empty($data["checkin"])){
                    $errors[] = "checkin is required";
                }
                else if(!preg_match("^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$", $data["checkin"])){
                    $errors[] = "checkin invalid format";
                }

                if(empty($data["checkout"])){
                    $errors[] = "checkout is required";
                }
                else if(!preg_match("^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$", $data["checkout"])){
                    $errors[] = "checkout invalid format";
                }

                if(empty($data["room_id"])){
                    $errors[] = "room_id is required";
                }
                else if(filter_var($data["room_id"], FILTER_VALIDATE_INT) == false){
                    $errors[] = "room_id invalid format";
                }

                if(empty($data["billed"])){
                    $errors[] = "billed is required";
                }
                else if(filter_var($data["billed"], FILTER_VALIDATE_BOOLEAN) == false){
                    $errors[] = "billed invalid format";
                }

                if(empty($data["breakfast"])){
                    $errors[] = "breakfast is required";
                }
                else if(filter_var($data["breakfast"], FILTER_VALIDATE_BOOLEAN) == false){
                    $errors[] = "breakfast invalid format";
                }

                if(empty($data["lunch"])){
                    $errors[] = "lunch is required";
                }
                else if(filter_var($data["lunch"], FILTER_VALIDATE_BOOLEAN) == false){
                    $errors[] = "lunch invalid format";
                }

                if(empty($data["dinner"])){
                    $errors[] = "dinner is required";
                }
                else if(filter_var($data["dinner"], FILTER_VALIDATE_BOOLEAN) == false){
                    $errors[] = "dinner invalid format";
                }
            }
            else if($service === "room"){
                if(empty($data["number"])){
                    $errors[] = "number is required";
                }else if(filter_var($data["number"], FILTER_VALIDATE_INT) === false){
                    $errors[] = "room number must be an integer";
                }
    
                if(empty($data["type"])){
                    $errors[] = "type is required";
                }
    
                if(empty($data["beds"])){
                    $errors[] = "beds number required";
                }else if(filter_var($data["beds"], FILTER_VALIDATE_INT) === false){
                    $errors[] = "beds number must be an integer";
                }
    
                if(empty($data["cost_per_day"])){
                    $errors[] = "cost_per_day is required";
                }else if(filter_var($data["cost_per_day"], FILTER_VALIDATE_FLOAT) === false){
                    $errors[] = "cost must be a decimal number";
                }
            }
            else if($service === "customer"){
                if(empty($data["firstname"])){
                    $errors[] = "firstname is required";
                }
    
                if(empty($data["lastname"])){
                    $errors[] = "lastname is required";
                }
            }

        }
        else if($method === "PATCH"){
            if($service === "reservation"){
                if(!empty($data["customer_id"])){
                    if(filter_var($data["customer_id"], FILTER_VALIDATE_INT) == false){
                        $errors[] = "customer_id invalid format";
                    }
                }
                if(!empty($data["checkin"])){
                    if(!preg_match("^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$", $data["checkin"])){
                        $errors[] = "checkin invalid format";
                    }
                }
                if(!empty($data["checkout"])){
                    if(!preg_match("^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$", $data["checkout"])){
                        $errors[] = "checkout invalid format";
                    }
                }
                if(!empty($data["room_id"])){
                    if(filter_var($data["room_id"], FILTER_VALIDATE_INT) == false){
                        $errors[] = "room_id invalid format";
                    }
                }
                if(!empty($data["billed"])){
                    if(filter_var($data["billed"], FILTER_VALIDATE_BOOLEAN) == false){
                        $errors[] = "billed invalid format";
                    }
                }
                if(!empty($data["breakfast"])){
                    if(filter_var($data["breakfast"], FILTER_VALIDATE_BOOLEAN) == false){
                        $errors[] = "breakfast invalid format";
                    }
                }
                if(!empty($data["lunch"])){
                    if(filter_var($data["lunch"], FILTER_VALIDATE_BOOLEAN) == false){
                        $errors[] = "lunch invalid format";
                    }
                }
                if(!empty($data["dinner"])){
                    if(filter_var($data["dinner"], FILTER_VALIDATE_BOOLEAN) == false){
                        $errors[] = "dinner invalid format";
                    }
                }
                
            }
            else if($service === "room"){
                if(!empty($data["beds"])){
                    if(filter_var($data["beds"], FILTER_VALIDATE_INT) == false){
                        $errors[] = "beds invalid format";
                    }
                }
                if(!empty($data["cost_per_day"])){
                    if(filter_var($data["cost_per_day"], FILTER_VALIDATE_FLOAT) == false){
                        $errors[] = "cost_per_day invalid format";
                    }
                }
                if(!empty($data["number"])){
                    if(filter_var($data["number"], FILTER_VALIDATE_INT) == false){
                        $errors[] = "number invalid format";
                    }
                }
            }
            //customer table doen't need anything
        }

        return $errors;
    }
}