<?php

class ReceptionGateway{
    private PDO $conn;

    public function __construct(Database $database){
        $this->conn = $database->getConnection();
    }

//Room features

    public function getAllRooms(): array | false{
        $sql = "SELECT * 
                FROM room";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        if(empty($data)){
            return false;
        }

        return $data;
    }


    public function getRoomByID(int $id): array | false{
        $sql = "SELECT * 
                FROM room 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function getAllAvailableRooms(): array | false{
        //TODO
    }


    public function createNewRoom(array $data): string{
        $sql = "INSERT INTO room (beds, type, cost_per_day) 
                VALUES (:beds, :type, :cost_per_day)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":beds", $data["beds"], PDO::PARAM_INT);
        $stmt->bindValue(":type", $data["type"], PDO::PARAM_STR);
        $stmt->bindValue(":cost_per_day", $data["cost_per_day"], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->conn->lastInsertId();
    }

    
    public function deleteRoom(int $id): int{
        $sql = "DELETE FROM room WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->rowCount();
    }


    public function updateRoomInfo(int $id, array $data): int{
        $fields = [];

        if(array_key_exists("beds", $data)){
            $fields["beds"] = [$data["beds"], PDO::PARAM_INT];
        }

        if(array_key_exists("number", $data)){
            $fields["number"] = [$data["number"], PDO::PARAM_INT];
        }

        if(array_key_exists("type", $data)){
            $fields["type"] = [$data["type"], PDO::PARAM_STR];
        }

        if(array_key_exists("cost_per_day", $data)){
            $fields["cost_per_day"] = [$data["cost_per_day"], PDO::PARAM_STR];
        }

        if(empty($fields)){
            return 0;
        }else{
            $sets = array_map(function($value){
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE room SET " . implode(", ", $sets) . " WHERE id = :id";

            $stmt = $this->prepare($sql);

            $stmt->bindValue(":id", $id);

            foreach($fields as $name => $values){
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }
    }


//Reservation features

    public function getAllReservations(): array | false{
        $sql = "SELECT *
                FROM room_reservation
                ORDER BY id DESC";
        
        $stmt = $this->conn-prepare($sql);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        if(empty($data)){
            return false;
        }
        
        return $data;
    }
    

    public function getReservasionInfo(int $id): array | false{
        $sql = "SELECT * 
                FROM room_reservation 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }     


    public function addReservation(array $data): string{
        $sql = "INSERT INTO room_reservation (customer_id, checkin, checkout, room_id, billed, breakfast, lunch, dinner) 
                VALUES (:customer_id, :checkin, :checkout, :room_id, :billed, :breakfast, :lunch, :dinner)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":customer_id", $data["customer_id"], PDO::PARAM_INT);
        $stmt->bindValue(":checkin", date("Y-m-d H:i:s", strtotime($data["checkin"])), PDO::PARAM_STR);
        $stmt->bindValue(":checkout", date("Y-m-d H:i:s", strtotime($data["checkout"])), PDO::PARAM_STR);
        $stmt->bindValue(":room_id", $data["room_id"], PDO::PARAM_INT);
        $stmt->bindValue(":billed", $data["billed"], PDO::PARAM_BOOL);
        $stmt->bindValue(":breakfast". $data["breakfast"], PDO::PARAM_BOOL);
        $stmt->bindValue(":lunch", $data["lunch"], PDO::PARAM_BOOL);
        $stmt->bindValue(":dinner", $data["dinner"], PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function deleteReservation(int $id): int{
        $sql = "DELETE FROM room_reservation WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function updateReservationInfo(int $id, array $data): int{
        $fields = [];

        if(array_key_exists("customer_id", $data)){
            $fields["customer_id"] = [$data["customer_id"], PDO::PARAM_INT];
        }

        if(array_key_exists("checkin", $data)){
            $fields["checkin"] = [date("Y-m-d H:i:s", $data["checkin"]), PDO::PARAM_STR];
        }

        if(array_key_exists("checkout", $data)){
            $fields["checkout"] = [date("Y-m-d H:i:s", $data["checkout"]), PDO::PARAM_STR];
        }

        if(array_key_exists("room_id")){
            $fields["room_id"] = [$data["room_id"], PDO::PARAM_INT];
        }

        if(array_key_exists("billed", $data)){
            $fields["billed"] = [$data["billed"], PDO::PARAM_BOOL];
        }

        if(array_key_exists("breakfast", $data)){
            $fields["breakfast"] = [$data["breakfast"], PDO::PARAM_BOOL];
        }

        if(array_key_exists("lunch", $data)){
            $fields["lunch"] = [$data["lunch"], PDO::PARAM_BOOL];
        }

        if(array_key_exists("dinner", $data)){
            $fields["dinner"] = [$data["dinner"], PDO::PARAM_BOOL];
        }

        if(empty($fields)){
            return 0;
        }else{
            $sets = array_map(function($value){
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE room_reservation SET " . implode(", ", $sets) . " WHERE id = :id";

            $stmt = $this->prepare($sql);

            $stmt->bindValue(":id", $id);

            foreach($fields as $name => $values){
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }
    }

//Customer features

    public function getAllCustomers(): array{
        $sql = "SELECT * FROM customer";

        $stmt - $this->conn->prepare($sql);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return $data;
    }


    public function getCustomerByID(int $id): array | false{
        $sql = "SELECT * FROM customer WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id);
        
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }


    public function newCustomer(array $data): string{
        $sql = "INSERT INTO customer (fisrtname, latname, phone) VALUES (:firstname, :lastname, :phone)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":firstname", $data["firstname"], PDO::PARAM_STR);
        $stmt->bindValue(":lastname", $data["lastname"], PDO::PARAM_STR);
        $stmt->bindValue(":phone", $data["phone"], PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function deleteCustomer(int $id): int{
        $sql = "DELETE FROM customer WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function updateCustomerInfo(int $id, array $data): int{
        $fields = [];

        if(array_key_exists("firstname", $data)){
            $fields["firstname"] = [$data["firstname"], PDO::PARAM_STR];
        }

        if(array_key_exists("lastname", $data)){
            $fields["lastname"] = [$data["lastname"], PDO::PARAM_STR];
        }

        if(array_key_exists("phone", $data)){
            $fields["phone"] = [$data["phone"], PDO::PARAM_STR];
        }

        if(empty($fields)){
            return 0;
        }else{
            $sets = array_map(function($value){
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE customer SET " . implode(", ", $sets) . " WHERE id = :id";

            $stmt = $this->prepare($sql);

            $stmt->bindValue(":id", $id);

            foreach($fields as $name => $values){
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }
    }
}