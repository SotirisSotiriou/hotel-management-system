<?php

class ReceptionController{

    private ReceptionGateway $gateway;
    
    public function __construct(ReceptionGateway $gateway){
        $this->gateway = $gateway;
    }


    public function processRequest(string $method, string $service, ?string $id): void{
        if($id === null){
            if($method === "POST"){
                $data = $_POST;

                if($service === "reservation"){
                    //TODO
                    echo "add reservation call";
                }
                else if($service === "room"){
                    //TODO
                    echo "add room call";
                }
                else if($service === "customer"){
                    //TODO
                    echo "add customer call";
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
                        $reservation = $this->gateway->getReservasionInfo((int)$id);
                        if($reservation === false){
                            $this->respondReservationNotFound($id);
                        }
                        else{
                            echo json_encode($reservation);
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
                        //TODO
                    }
                    else if($service === "room"){
                        //TODO
                    }
                    else if($service === "customer"){
                        //TODO
                    }
                    break;
                case "DELETE":
                    if($service === "reservation"){
                        //TODO
                    }
                    else if($service === "room"){
                        //TODO
                    }
                    else if($service === "customer"){
                        //TODO
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

    private function respondEmptyData(){
        http_response_code(404);
        echo json_encode(["message" => "Empty data"]);
    }


    private function getValidationErrors(array $data, string $service, string $method){
        $errors = [];

        if($method === "POST"){
            if($service === "reservation"){
                //TODO
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
                //TODO
                
            }
            else if($service === "room"){
                //TODO
            }
            else if($service === "customer"){
                //TODO
            }
        }

        return $errors;
    }
}