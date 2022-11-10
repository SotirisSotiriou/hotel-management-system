<?php

class ReceptionController{
    
    public function __construct(private ReceptionGateway $gateway){

    }


    public function processRequest(string $method, string $service, ?string $id): void{
        if($id === null){
            if($method === "POST"){
                if($service === "reservation"){
                    //TODO
                }
                else if($service === "room"){
                    //TODO
                }
                else if($service === "customer"){
                    //TODO
                }
                else{
                    respondServiceNotFound();
                    return;
                }

            }
            else{
                respondMethodNotAllowed("POST");
                return;
            }
        }
        else{
            switch($method){
                case "GET":
                    //TODO
                    break;
                case "PATCH":
                    //TODO
                    break;
                case "DELETE":
                    //TODO
                    break;
                default:
                    respondMethodNotAllowed("GET, PATCH, DELETE");
                    return;
            }
        }

        
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

            //TODO: implementation for GET, PATCH and DELETE methods
        }


        return $errors;
    }
}