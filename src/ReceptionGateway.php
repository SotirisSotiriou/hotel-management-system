<?php

class ReceptionGateway{
    private PDO $conn;

    public function __constructor(Database $database){
        $this->conn = $database->getConnection();
    }

//Room features

    public function getAllRooms(): array{
        
    }


    public function getRoomByID(int $id): array | false{
        
    }


    public function createNewRoom(array $data){
        
    }

    
    public function deleteRoom(int $id){

    }


    public function updateRoomInfo(int $id, array $data){
        
    }


//Reservation features

    public function getReservasionInfo(int $id): array | false{

    }     


    public function addReservation(array $data){
        
    }


    public function deleteReservation(int $id){

    }


    public function updateReservationInfo(int $id, array $data){
        
    }

//Customer features

    public function getAllCustomers(): array{

    }


    public function getCustomerByID(int $id): array | false{

    }


    public function newCustomer(array $data){

    }


    public function deleteCustomer(int $id){
        
    }


    public function updateCustomerInfo(int $id, array $data){

    }
}